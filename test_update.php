<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create(
        '/profile', 'PUT',
        ['name' => 'Ashish Gupta', 'dob' => '2018-02-08', 'anniversary' => '2026-06-05', 'fb_consent' => true]
    )
);
echo $response->getContent();
