<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$reviews = \App\Models\Review::whereNotNull('media')->get();
foreach($reviews as $r) {
    $old = $r->media;
    $new = str_replace(',assets/reviews/', '|assets/reviews/', $old);
    if ($old !== $new) {
        $r->media = $new;
        $r->save();
        echo "Updated Review {$r->id}\n";
    }
}
echo "Done";
