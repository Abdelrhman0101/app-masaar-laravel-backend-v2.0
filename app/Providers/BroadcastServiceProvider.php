<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // هنا نقوم بتعريف مسارات المصادقة للبث.
        // ونحدد أن هذه المسارات يجب أن تكون محمية بنفس
        // طريقة حماية الـ API الخاص بنا (auth:sanctum).
        // Broadcast::routes(['middleware' => ['api', 'auth:sanctum']]);

        require base_path('routes/channels.php');
    }
}