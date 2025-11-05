<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartilha notificações de suporte com o header
        View::composer('components.header', function ($view) {
            $novasMensagens = 0;
            
            // Só conta se o usuário for admin ou suporte
            if (Auth::check()) {
                $user = Auth::user();
                if (in_array($user->role, ['admin', 'suporte'])) {
                    $novasMensagens = Message::where('sender_type', 'cliente')
                        ->whereNull('answered_at')
                        ->count();
                }
            }
            
            $view->with('novasMensagens', $novasMensagens);
        });
    }
}
