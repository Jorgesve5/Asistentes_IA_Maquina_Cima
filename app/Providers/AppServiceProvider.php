<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                $provider = \App\Models\SystemSetting::get('active_api_provider');
                if ($provider) {
                    $groqKey = \App\Models\SystemSetting::get('groq_api_key');
                    $geminiKey = \App\Models\SystemSetting::get('gemini_api_key');
                    $openaiKey = \App\Models\SystemSetting::get('openai_api_key');

                    config([
                        'services.groq.key' => $provider === 'groq' ? $groqKey : null,
                        'services.gemini.key' => $provider === 'gemini' ? $geminiKey : null,
                        'services.openai.key' => $provider === 'openai' ? $openaiKey : null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silence exceptions to avoid breaking migrations or CLI commands
        }
    }
}
