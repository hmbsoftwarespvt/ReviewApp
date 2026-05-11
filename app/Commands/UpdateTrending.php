<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\TrendingService;

/**
 * UpdateTrending Command
 * 
 * Updates daily trending scores for all apps.
 * Should be scheduled to run at 00:00 UTC daily.
 * 
 * Usage: php spark trending:update
 */
class UpdateTrending extends BaseCommand
{
    protected $group       = 'AppTrust';
    protected $name        = 'trending:update';
    protected $description = 'Update daily trending scores for all apps';
    protected $usage       = 'trending:update';
    
    public function run(array $params)
    {
        CLI::write('Starting trending score update...', 'yellow');
        
        $trendingService = new TrendingService();
        
        try {
            $count = $trendingService->updateDailyTrending();
            
            CLI::write("Successfully updated trending scores for {$count} apps.", 'green');
            CLI::write('Trending cache has been invalidated.', 'green');
            
            return EXIT_SUCCESS;
        } catch (\Exception $e) {
            CLI::error('Error updating trending scores: ' . $e->getMessage());
            return EXIT_ERROR;
        }
    }
}
