<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "Running seeder...\n";
Artisan::call('db:seed', ['--class' => 'StorefrontDummySeeder', '--force' => true]);
echo Artisan::output();
echo "\nDone!\n";
