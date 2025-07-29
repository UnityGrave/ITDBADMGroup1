<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * ExchangeRateService for EPIC 8: Multi-Currency Support
 * 
 * Handles fetching and updating exchange rates from exchangerate.host API
 */
class ExchangeRateService
{
    private string $apiKey;
    private string $baseUrl;
    private string $baseCurrency;

    public function __construct()
    {
        // Using a free API - no API key required
        $this->apiKey = config('currency.exchange_rate_api_key', '');
        $this->baseUrl = config('currency.exchange_rate_api_url', 'https://api.fxratesapi.com');
        $this->baseCurrency = config('currency.base_currency', 'USD');
    }

    /**
     * Fetch latest exchange rates from the API
     * 
     * @param array $currencies List of currency codes to fetch rates for
     * @return array Exchange rates keyed by currency code
     * @throws Exception
     */
    public function fetchExchangeRates(array $currencies = []): array
    {
        if (empty($currencies)) {
            $currencies = $this->getActiveCurrencyCodes();
        }

        // Remove base currency from the list since it should always be 1.0
        $currencies = array_filter($currencies, fn($code) => $code !== $this->baseCurrency);

        if (empty($currencies)) {
            return [$this->baseCurrency => 1.0];
        }

        try {
            Log::info('Fetching exchange rates from API', [
                'currencies' => $currencies,
                'base_currency' => $this->baseCurrency
            ]);

            // Build query parameters for the free API
            $params = [
                'base' => $this->baseCurrency,
            ];
            
            // Add symbols parameter if we have specific currencies
            if (!empty($currencies)) {
                $params['symbols'] = implode(',', $currencies);
            }

            // Prepare the request using the free exchangerate.host API
            $response = Http::timeout(30)
                ->retry(3, 1000) // Retry 3 times with 1 second delay
                ->get($this->baseUrl . '/latest', $params);

            if (!$response->successful()) {
                throw new Exception("API request failed with status: " . $response->status());
            }

            $data = $response->json();

            // Check for API errors (fxratesapi.com format)
            if (!$data) {
                throw new Exception("Empty API response received");
            }

            // Check for API success indicator
            if (isset($data['success']) && !$data['success']) {
                $error = $data['message'] ?? 'Unknown API error';
                throw new Exception("API returned error: " . $error);
            }

            // The API structure: {"success":true,"base":"USD","rates":{"EUR":0.85,...}}
            if (!isset($data['rates']) || !is_array($data['rates'])) {
                throw new Exception("Invalid API response format: missing or invalid rates data");
            }

            // Get the rates and ensure base currency is included
            $rates = $data['rates'];
            $baseCurrency = $data['base'] ?? $this->baseCurrency;
            $rates[$baseCurrency] = 1.0;

            Log::info('Successfully fetched exchange rates', [
                'rates_count' => count($rates),
                'timestamp' => $data['timestamp'] ?? null
            ]);

            return $rates;

        } catch (Exception $e) {
            Log::error('Failed to fetch exchange rates', [
                'error' => $e->getMessage(),
                'currencies' => $currencies
            ]);
            throw $e;
        }
    }

    /**
     * Update exchange rates in the database
     * 
     * @param array $rates Exchange rates keyed by currency code
     * @return array Results of the update operation
     */
    public function updateExchangeRates(array $rates): array
    {
        $results = [
            'updated' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        foreach ($rates as $currencyCode => $rate) {
            try {
                $currency = Currency::where('code', $currencyCode)->first();

                if (!$currency) {
                    $results['skipped']++;
                    $results['errors'][] = "Currency {$currencyCode} not found in database";
                    continue;
                }

                if (!$currency->is_active) {
                    $results['skipped']++;
                    Log::debug("Skipping inactive currency: {$currencyCode}");
                    continue;
                }

                // Validate rate
                if (!is_numeric($rate) || $rate <= 0) {
                    $results['errors'][] = "Invalid exchange rate for {$currencyCode}: {$rate}";
                    continue;
                }

                // Update the currency
                $oldRate = $currency->exchange_rate;
                $currency->updateExchangeRate((float) $rate);
                
                $results['updated']++;
                
                Log::info("Updated exchange rate for {$currencyCode}", [
                    'old_rate' => $oldRate,
                    'new_rate' => $rate,
                    'change_percent' => $oldRate > 0 ? round((($rate - $oldRate) / $oldRate) * 100, 4) : 0
                ]);

            } catch (Exception $e) {
                $results['errors'][] = "Failed to update {$currencyCode}: " . $e->getMessage();
                Log::error("Error updating currency {$currencyCode}", [
                    'error' => $e->getMessage(),
                    'rate' => $rate
                ]);
            }
        }

        return $results;
    }

    /**
     * Fetch and update all exchange rates
     * 
     * @return array Results of the operation
     */
    public function syncExchangeRates(): array
    {
        try {
            // Check if we've synced recently (within the last hour) to avoid excessive API calls
            $cacheKey = 'exchange_rates_last_sync';
            $lastSync = Cache::get($cacheKey);
            
            if ($lastSync && now()->diffInMinutes($lastSync) < 60) {
                Log::info('Exchange rates were synced recently, skipping', [
                    'last_sync' => $lastSync,
                    'minutes_ago' => now()->diffInMinutes($lastSync)
                ]);
                
                return [
                    'status' => 'skipped',
                    'message' => 'Rates were updated recently',
                    'last_sync' => $lastSync
                ];
            }

            $rates = $this->fetchExchangeRates();
            $results = $this->updateExchangeRates($rates);
            
            // Cache the sync time
            Cache::put($cacheKey, now(), now()->addHours(2));
            
            $results['status'] = 'success';
            $results['synced_at'] = now();
            
            Log::info('Exchange rate synchronization completed', $results);
            
            return $results;

        } catch (Exception $e) {
            Log::error('Exchange rate synchronization failed', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'updated' => 0,
                'skipped' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Get active currency codes from the database
     * 
     * @return array
     */
    private function getActiveCurrencyCodes(): array
    {
        return Currency::active()->pluck('code')->toArray();
    }

    /**
     * Check if the API is reachable
     * 
     * @return bool
     */
    public function isApiReachable(): bool
    {
        try {
            // Use the free API endpoint to check connectivity
            $response = Http::timeout(10)->get($this->baseUrl . '/latest', [
                'base' => 'USD',
                'symbols' => 'EUR' // Just get one rate for testing
            ]);
            
            if (!$response->successful()) {
                return false;
            }
            
            // Check if the response has the expected format
            $data = $response->json();
            return isset($data['rates']) && is_array($data['rates']);
            
        } catch (Exception $e) {
            Log::warning('Exchange rate API health check failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get the last synchronization time
     * 
     * @return \Carbon\Carbon|null
     */
    public function getLastSyncTime(): ?\Carbon\Carbon
    {
        return Cache::get('exchange_rates_last_sync');
    }

    /**
     * Force a synchronization regardless of cache
     * 
     * @return array
     */
    public function forceSyncExchangeRates(): array
    {
        Cache::forget('exchange_rates_last_sync');
        return $this->syncExchangeRates();
    }

    /**
     * Get exchange rate for a specific currency
     * 
     * @param string $currencyCode
     * @return float|null
     */
    public function getExchangeRate(string $currencyCode): ?float
    {
        $currency = Currency::where('code', $currencyCode)->first();
        return $currency ? (float) $currency->exchange_rate : null;
    }

    /**
     * Check if exchange rates are outdated
     * 
     * @param int $hoursThreshold
     * @return bool
     */
    public function areRatesOutdated(int $hoursThreshold = 24): bool
    {
        $oldestUpdate = Currency::active()
            ->whereNotNull('rate_updated_at')
            ->min('rate_updated_at');
            
        if (!$oldestUpdate) {
            return true; // No rates have been updated
        }
        
        return now()->diffInHours($oldestUpdate) > $hoursThreshold;
    }
} 