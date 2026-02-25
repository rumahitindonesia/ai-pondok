<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

$now = Carbon::now();
$daysInMonth = $now->daysInMonth;

$dailyData = [];
for ($i = 1; $i <= $daysInMonth; $i++) {
    $dateObj = clone $now;
    $dateObj->day = $i;
    $formattedDate = $dateObj->format('d M');
    $dailyData[$formattedDate] = [
        'date' => $formattedDate,
        'total' => 0
    ];
}

print_r(array_values(array_slice($dailyData, 0, 5)));
?>
