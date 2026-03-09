<?php


namespace App\Providers;

use AzureOss\FlysystemAzureBlobStorage\AzureBlobStorageAdapter;
use AzureOss\Storage\Blob\BlobServiceClient;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\PathPrefixing\PathPrefixedAdapter;

class AppServiceProvider extends ServiceProvider
{
    // ...existing code...

    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            
            // Construye la URL hacia la pantalla del frontend
            return $frontendUrl . "/auth/reset-password?token={$token}&email={$notifiable->getEmailForPasswordReset()}";
        });

        Storage::extend('azure', function ($app, $config) {
            $connectionString = $config['connection_string'] ?? '';
            $container = $config['container'] ?? '';

            $serviceClient = BlobServiceClient::fromConnectionString($connectionString);
            $containerClient = $serviceClient->getContainerClient($container);

            $adapter = new AzureBlobStorageAdapter($containerClient);

            $filesystem = new Filesystem($adapter);

            return new FilesystemAdapter($filesystem, $adapter, $config);
        });
    }
}
