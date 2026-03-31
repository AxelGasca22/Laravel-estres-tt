<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = \App\Models\Actividad::orderBy('id')->get(['id', 'nombre', 'descripcion', 'tipo']);

foreach ($rows as $row) {
    $payload = [
        'id' => $row->id,
        'nombre' => $row->nombre,
        'descripcion' => $row->descripcion,
        'tipo' => $row->tipo,
    ];

    try {
        json_encode($payload, JSON_THROW_ON_ERROR);
    } catch (\Throwable $e) {
        echo "BAD_UTF8 id={$row->id} nombre={$row->nombre} error={$e->getMessage()}" . PHP_EOL;
    }
}

echo "done" . PHP_EOL;
