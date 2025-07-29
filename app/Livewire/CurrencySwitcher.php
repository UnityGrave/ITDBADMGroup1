<?php

namespace App\Livewire;

use App\Models\Currency;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CurrencySwitcher extends Component
{
    public $selectedCurrency;
    public $currencies;

    public function mount()
    {
        // Load all active currencies
        $this->currencies = Currency::active()->orderBy('code')->get();
        
        // Set initial currency based on priority: user preference > session > default
        $this->selectedCurrency = $this->getEffectiveCurrency();
    }

    /**
     * Get the effective currency for the current user/session
     */
    public function getEffectiveCurrency(): string
    {
        // If user is authenticated, check their preference first
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasPreferredCurrency()) {
                return $user->getPreferredCurrencyCode();
            }
        }

        // Then check session
        if (session()->has('currency')) {
            return session('currency');
        }

        // Finally, fall back to default
        return config('currency.base_currency', 'USD');
    }

    /**
     * Handle currency change
     */
    public function changeCurrency($currencyCode)
    {
        $currencyCode = strtoupper($currencyCode);
        
        // Validate currency exists and is active
        $currency = Currency::where('code', $currencyCode)->active()->first();
        if (!$currency) {
            $this->addError('currency', 'Invalid currency selected');
            return;
        }

        // Update the selected currency
        $this->selectedCurrency = $currencyCode;

        // Store in session for all users
        session(['currency' => $currencyCode]);

        // If user is authenticated, also update their preference
        if (Auth::check()) {
            Auth::user()->setPreferredCurrency($currencyCode);
        }

        // Dispatch global event to update all components
        $this->dispatch('currency-changed', currency: $currencyCode);

        // Show success message
        $this->js("
            if (typeof window.showToast === 'function') {
                window.showToast('Currency changed to {$currency->name}', 'success');
            }
        ");
    }

    /**
     * Get the current currency object
     */
    public function getCurrentCurrency()
    {
        return Currency::where('code', $this->selectedCurrency)->first();
    }

    /**
     * Get the currency symbol for display
     */
    public function getCurrentSymbol(): string
    {
        $currency = $this->getCurrentCurrency();
        return $currency ? $currency->symbol : '$';
    }

    /**
     * Listen for auth state changes to update currency
     */
    protected $listeners = [
        'user-logged-in' => 'handleUserLogin',
        'user-logged-out' => 'handleUserLogout',
    ];

    /**
     * Handle user login - update to their preferred currency
     */
    public function handleUserLogin()
    {
        if (Auth::check()) {
            $userCurrency = Auth::user()->getEffectiveCurrency();
            if ($userCurrency !== $this->selectedCurrency) {
                $this->selectedCurrency = $userCurrency;
                session(['currency' => $userCurrency]);
                $this->dispatch('currency-changed', currency: $userCurrency);
            }
        }
    }

    /**
     * Handle user logout - fall back to session or default
     */
    public function handleUserLogout()
    {
        $effectiveCurrency = $this->getEffectiveCurrency();
        if ($effectiveCurrency !== $this->selectedCurrency) {
            $this->selectedCurrency = $effectiveCurrency;
            $this->dispatch('currency-changed', currency: $effectiveCurrency);
        }
    }

    public function render()
    {
        return view('livewire.currency-switcher');
    }
}
