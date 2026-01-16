<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\FonnteService;

$fonnte = new FonnteService();
$result = $fonnte->send('085624568440', 'Halo Fauzan! Ini adalah pesan tes dari sistem PSB Dar Al Tauhid.');

echo "Result:\n";
print_r($result);
