<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearProductCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-product-caches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing product-related caches...');
        
        // Clear filter option caches
        cache()->forget('sets_list');
        cache()->forget('categories_list');
        cache()->forget('rarities_list');
        
        // Clear price calculation caches
        $this->info('Clearing price calculation caches...');
        $keys = cache()->get('price_cache_keys', []);
        foreach ($keys as $key) {
            cache()->forget($key);
        }
        cache()->forget('price_cache_keys');
        
        $this->info('All product caches cleared successfully!');
    }
}
