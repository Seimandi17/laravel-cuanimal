<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Products;
use App\Services\ProductGeocoder;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('products:geocode {--force : Recalcular también servicios que ya tienen coordenadas}', function () {
    /** @var ClosureCommand $this */
    $geocoder = app(ProductGeocoder::class);
    $force = (bool) $this->option('force');
    $updated = 0;
    $skipped = 0;

    $query = Products::query()
        ->when(!$force, function ($query) {
            $query->where(function ($query) {
                $query->whereNull('lat')->orWhereNull('lng');
            });
        });

    $total = $query->count();

    if ($total === 0) {
        $this->info('No hay servicios pendientes de geocodificar.');
        return Command::SUCCESS;
    }

    $this->info("Geocodificando {$total} servicios...");

    $query->orderBy('id')->chunkById(50, function ($products) use ($geocoder, &$updated, &$skipped) {
        foreach ($products as $product) {
            $coords = $geocoder->resolve([
                'address' => $product->address,
                'city' => $product->city,
                'codigo_postal' => $product->codigo_postal,
                'province' => $product->province,
            ]);

            if (empty($coords)) {
                $skipped += 1;
                $this->warn("Sin coordenadas para #{$product->id}: {$product->name}");
                usleep(1100000);
                continue;
            }

            $product->forceFill($coords)->save();
            $updated += 1;
            $this->line("Actualizado #{$product->id}: {$product->name}");

            // Nominatim recomienda limitar la frecuencia de peticiones.
            usleep(1100000);
        }
    });

    $this->info("Finalizado. Actualizados: {$updated}. Sin coordenadas: {$skipped}.");

    return Command::SUCCESS;
})->purpose('Geocodificar coordenadas de servicios existentes');
