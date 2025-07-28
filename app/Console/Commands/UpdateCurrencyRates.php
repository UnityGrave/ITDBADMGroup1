<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateService;
use Illuminate\Console\Command;
use Exception;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update-rates 
                           {--force : Force update even if rates were recently synced}
                           {--currencies=* : Specific currencies to update (default: all)}
                           {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency exchange rates from the API';

    /**
     * The exchange rate service instance.
     */
    private ExchangeRateService $exchangeRateService;

    /**
     * Create a new command instance.
     */
    public function __construct(ExchangeRateService $exchangeRateService)
    {
        parent::__construct();
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Starting exchange rate update...');
        
        try {
            // Check if this is a dry run
            if ($this->option('dry-run')) {
                return $this->handleDryRun();
            }

            // Check API connectivity first
            if (!$this->exchangeRateService->isApiReachable()) {
                $this->error('❌ Exchange rate API is not reachable. Please check your internet connection and API key.');
                return Command::FAILURE;
            }

            // Determine which method to use based on options
            if ($this->option('force')) {
                $results = $this->exchangeRateService->forceSyncExchangeRates();
            } else {
                $results = $this->exchangeRateService->syncExchangeRates();
            }

            // Handle the results
            return $this->displayResults($results);

        } catch (Exception $e) {
            $this->error('❌ Failed to update exchange rates: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Handle dry run mode
     */
    private function handleDryRun(): int
    {
        $this->info('🔍 DRY RUN MODE - No changes will be made');
        
        try {
            $currencies = $this->option('currencies') ?: [];
            $rates = $this->exchangeRateService->fetchExchangeRates($currencies);
            
            $this->info('📊 Exchange rates that would be updated:');
            
            $headers = ['Currency', 'Current Rate', 'New Rate', 'Change %'];
            $rows = [];
            
            foreach ($rates as $code => $newRate) {
                $currentRate = $this->exchangeRateService->getExchangeRate($code);
                $changePercent = 0;
                
                if ($currentRate && $currentRate > 0) {
                    $changePercent = round((($newRate - $currentRate) / $currentRate) * 100, 4);
                }
                
                $rows[] = [
                    $code,
                    $currentRate ? number_format($currentRate, 8) : 'N/A',
                    number_format($newRate, 8),
                    $changePercent > 0 ? "+{$changePercent}%" : "{$changePercent}%"
                ];
            }
            
            $this->table($headers, $rows);
            $this->info('✅ Dry run completed successfully');
            
            return Command::SUCCESS;
            
        } catch (Exception $e) {
            $this->error('❌ Dry run failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display the results of the exchange rate update
     */
    private function displayResults(array $results): int
    {
        switch ($results['status']) {
            case 'success':
                $this->info('✅ Exchange rates updated successfully!');
                
                if ($results['updated'] > 0) {
                    $this->info("📈 Updated {$results['updated']} currencies");
                }
                
                if ($results['skipped'] > 0) {
                    $this->warn("⏭️  Skipped {$results['skipped']} currencies");
                }
                
                if (!empty($results['errors'])) {
                    $this->warn('⚠️  Some errors occurred:');
                    foreach ($results['errors'] as $error) {
                        $this->line("   • {$error}");
                    }
                }
                
                if (isset($results['synced_at'])) {
                    $this->info("🕐 Synced at: {$results['synced_at']->format('Y-m-d H:i:s')}");
                }
                
                return Command::SUCCESS;
                
            case 'skipped':
                $this->info('⏭️  Exchange rates were updated recently');
                if (isset($results['last_sync'])) {
                    $this->info("   Last sync: {$results['last_sync']->format('Y-m-d H:i:s')}");
                }
                $this->info('   Use --force to update anyway');
                return Command::SUCCESS;
                
            case 'error':
                $this->error('❌ Exchange rate update failed');
                $this->error("   Error: {$results['message']}");
                
                if (!empty($results['errors'])) {
                    $this->error('   Additional errors:');
                    foreach ($results['errors'] as $error) {
                        $this->line("   • {$error}");
                    }
                }
                
                return Command::FAILURE;
                
            default:
                $this->error('❌ Unknown status returned from exchange rate service');
                return Command::FAILURE;
        }
    }

    /**
     * Show additional information about the command
     */
    public function getHelp(): string
    {
        return <<<HELP

This command updates exchange rates for all active currencies from the exchangerate.host API.

Examples:
  php artisan currency:update-rates                    # Update all currencies
  php artisan currency:update-rates --force            # Force update (ignore cache)
  php artisan currency:update-rates --dry-run          # Preview changes without updating
  php artisan currency:update-rates --currencies=EUR,GBP  # Update specific currencies

The command includes built-in rate limiting to prevent excessive API calls.
It will skip updates if rates were synced within the last hour unless --force is used.

HELP;
    }
}
