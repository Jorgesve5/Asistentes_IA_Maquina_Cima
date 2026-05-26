<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$groqKey = config('services.groq.key');
echo 'Groq Key: ' . ($groqKey ? substr($groqKey, 0, 10) . '...' : 'NULL') . PHP_EOL;

try {
    $response = Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $groqKey,
    ])->post('https://api.groq.com/openai/v1/chat/completions', [
        'model' => 'llama-3.1-8b-instant',
        'messages' => [['role' => 'user', 'content' => 'hello']],
    ]);
    
    echo 'Status: ' . $response->status() . PHP_EOL;
    echo 'Body: ' . $response->body() . PHP_EOL;
} catch (\Exception $e) {
    echo 'Exception: ' . $e->getMessage() . PHP_EOL;
}
