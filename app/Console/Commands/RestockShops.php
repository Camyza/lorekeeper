<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Shop\ShopStock;
use Illuminate\Console\Command;

class RestockShops extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restock-shops';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restocks shops.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $stocks = ShopStock::where('is_limited_stock', 1)->where('restock', 1)->get();
        foreach ($stocks as $stock) {
            if ($stock->restock_interval == 2) {
                // check if it's start of week
                $now = Carbon::now();
                $day = $now->dayOfWeek;
                if ($day != 1) {
                    continue;
                }
            } else if ($stock->restock_interval == 3) {
                // check if it's start of month
                $now = Carbon::now();
                $day = $now->day;
                if ($day != 1) {
                    continue;
                }
            }

            // if the stock is random, restock from the stock type
            if ($stock->is_random) {
                $model = getAssetModelString(strtolower($this->stock_type));
            }

            $stock->quantity = $stock->range ? mt_rand(1, $stock->restock_quantity) : $stock->restock_quantity;
            $stock->save();
        }
    }
}
