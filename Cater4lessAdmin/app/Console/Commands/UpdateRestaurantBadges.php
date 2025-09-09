<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Restaurant;
use App\Services\BadgeCriteriaService;

class UpdateRestaurantBadges extends Command
{
    protected $signature = 'badges:update';
    protected $description = 'Update restaurant badges based on performance';

    public function handle()
    {
        $service = new BadgeCriteriaService();
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            $badge = $service->evaluateBadge($restaurant);
            $restaurant->badge = $badge;
            $restaurant->save();
        }

        $this->info("Badges updated successfully");
    }
}
