<?php

namespace App\Providers;

use App\Domain\Repositories\CategoriaRepositoryInterface;
use App\Infrastructure\Repositories\EloquentCategoriaRepository;
use App\Domain\Repositories\MarcaRepositoryInterface;
use App\Infrastructure\Repositories\EloquentMarcaRepository;
use App\Domain\Repositories\ProductoRepositoryInterface;
use App\Infrastructure\Repositories\EloquentProductoRepository;
use App\Domain\Repositories\ClienteRepositoryInterface;
use App\Infrastructure\Repositories\EloquentClienteRepository;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Infrastructure\Repositories\EloquentUsuarioRepository;
use App\Domain\Repositories\VentaRepositoryInterface;
use App\Infrastructure\Repositories\EloquentVentaRepository;
use App\Domain\Repositories\CotizacionRepositoryInterface;
use App\Infrastructure\Repositories\EloquentCotizacionRepository;
use App\Infrastructure\Services\GeminiAIService;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            CategoriaRepositoryInterface::class,
            EloquentCategoriaRepository::class
        );

        $this->app->bind(
            MarcaRepositoryInterface::class,
            EloquentMarcaRepository::class
        );

        $this->app->bind(
            ProductoRepositoryInterface::class,
            EloquentProductoRepository::class
        );

        $this->app->bind(
            ClienteRepositoryInterface::class,
            EloquentClienteRepository::class
        );

        $this->app->bind(
            UsuarioRepositoryInterface::class,
            EloquentUsuarioRepository::class
        );
        
        $this->app->bind(
            VentaRepositoryInterface::class,
            EloquentVentaRepository::class
        );
        
        $this->app->bind(
            CotizacionRepositoryInterface::class,
            EloquentCotizacionRepository::class
        );

        // Registrar GeminiAIService como singleton
        $this->app->singleton(GeminiAIService::class, function ($app) {
            return new GeminiAIService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}