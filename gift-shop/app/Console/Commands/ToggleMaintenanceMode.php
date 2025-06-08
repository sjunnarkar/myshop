<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ToggleMaintenanceMode extends Command
{
    protected $signature = 'maintenance {action : The action to perform (on/off)}';
    protected $description = 'Toggle maintenance mode for the application';

    public function handle()
    {
        $action = $this->argument('action');
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            $this->error('.env file not found!');
            return 1;
        }

        $envContent = File::get($envFile);
        
        if ($action === 'on') {
            if (strpos($envContent, 'MAINTENANCE_MODE=true') === false) {
                $envContent = str_replace(
                    'MAINTENANCE_MODE=false',
                    'MAINTENANCE_MODE=true',
                    $envContent
                );
                File::put($envFile, $envContent);
                $this->info('Maintenance mode has been enabled.');
            } else {
                $this->info('Maintenance mode is already enabled.');
            }
        } elseif ($action === 'off') {
            if (strpos($envContent, 'MAINTENANCE_MODE=false') === false) {
                $envContent = str_replace(
                    'MAINTENANCE_MODE=true',
                    'MAINTENANCE_MODE=false',
                    $envContent
                );
                File::put($envFile, $envContent);
                $this->info('Maintenance mode has been disabled.');
            } else {
                $this->info('Maintenance mode is already disabled.');
            }
        } else {
            $this->error('Invalid action. Use "on" or "off".');
            return 1;
        }

        return 0;
    }
} 