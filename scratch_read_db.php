<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$machines = App\Models\Machine::all();
foreach ($machines as $m) {
    echo "ID: {$m->id} | Slug: {$m->slug} | Name: {$m->name}\n";
    echo "MANUAL: " . var_export($m->manual_content, true) . "\n";
    echo "FAQ: " . var_export($m->faq_content, true) . "\n";
    echo "-------------------------------------\n";
}
