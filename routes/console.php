<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/**
 * Command artisan kustom.
 * Dapat dieksekusi di terminal (php artisan inspire).
 */
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
