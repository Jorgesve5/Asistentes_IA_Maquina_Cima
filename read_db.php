<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$machine = App\Models\Machine::find('zeta-633l');
if ($machine) {
    $text = $machine->manual_content;
    echo "String: " . $text . "\n";
    for ($i = 0; $i < mb_strlen($text); $i++) {
        $char = mb_substr($text, $i, 1);
        echo $char . ":" . bin2hex($char) . " ";
    }
    echo "\n";
}
