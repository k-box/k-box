<?php

namespace KBox\Geo\Providers;

use KBox\Geo\GeoService;
use KBox\Plugins\Plugin;
use KBox\Events\FileDeleting;
use KBox\Plugins\PluginManager;
use KBox\Documents\DocumentType;
use KBox\Documents\Facades\Files;
use KBox\Documents\Facades\Previews;
use Illuminate\Support\Facades\Route;
use KBox\Documents\Facades\Thumbnails;
use KBox\Geo\Actions\ProcessGeodata;
use KBox\Geo\Previews\GeodataPreviewDriver;
use KBox\Geo\Listeners\RemoveFileFromGeoserver;
use KBox\Geo\TypeIdentifiers\KmlTypeIdentifier;
use KBox\Geo\TypeIdentifiers\GpxTypeIdentifier;
use KBox\Geo\Thumbnails\GeoTiffThumbnailGenerator;
use KBox\DocumentsElaboration\Actions\AddToSearch;
use KBox\Geo\TypeIdentifiers\GeoJsonTypeIdentifier;
use KBox\Geo\TypeIdentifiers\GeoTiffTypeIdentifier;
use KBox\Geo\Thumbnails\ShapefileThumbnailGenerator;
use KBox\Geo\Thumbnails\GeoPackageThumbnailGenerator;
use KBox\Geo\TypeIdentifiers\ShapefileTypeIdentifier;
use KBox\Geo\TypeIdentifiers\GeoPackageTypeIdentifier;
use KBox\Geo\Thumbnails\GeoJsonGpxKmlThumbnailGenerator;
use KBox\DocumentsElaboration\Facades\DocumentElaboration;

class GeoServiceProvider extends Plugin
{
    /**
     * Bootstrap the plugin services.
     *
     * @return void
     */
    public function boot()
    {

        if (! $this->app->routesAreCached()) {
            Route::middleware('web')
                ->namespace('KBox\Geo\Http\Controllers')
                ->prefix('geoplugin')
                ->as('plugins.k-box-kbox-plugin-geo.')
                ->group(__DIR__.'/../../routes/routes.php');
        }

        // Translation loading
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'geo');

        // Views loading
        $this->loadViewsFrom(__DIR__.'/../../views', 'geo');

        // registering event listeners
        $this->registerEventListener(FileDeleting::class, RemoveFileFromGeoserver::class);
    }

    /**
     * Register the plugin offered services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GeoService::class, function ($app) {
            return new GeoService(app(PluginManager::class));
        });

        // Register the plugin default configuration as application configuration
        $this->mergeConfigFrom(__DIR__.'/../../config/default.php', GeoService::PLUGIN_ID);
        
        // register the custom step in the elaboration pipeline
        DocumentElaboration::register(ProcessGeodata::class, AddToSearch::class);

        $this->registerFileTypes();
        
        $this->registerThumbnailGenerators();
        
        $this->registerPreviewDrivers();
    }

    private function registerFileTypes()
    {
        Files::register('application/vnd.google-earth.kml+xml', DocumentType::GEODATA, 'kml', KmlTypeIdentifier::class);
        Files::register('application/vnd.google-earth.kmz', DocumentType::GEODATA, 'kmz', KmlTypeIdentifier::class);
        Files::register('application/geopackage+sqlite3', DocumentType::GEODATA, 'gpkg', GeoPackageTypeIdentifier::class);
        Files::register('application/octet-stream', DocumentType::GEODATA, 'shp', ShapefileTypeIdentifier::class);
        Files::register('application/geo+json', DocumentType::GEODATA, 'geojson', GeoJsonTypeIdentifier::class);
        Files::register('application/gpx+xml', DocumentType::GEODATA, 'gpx', GpxTypeIdentifier::class);
        Files::register('application/zip', DocumentType::GEODATA, 'zip', ShapefileTypeIdentifier::class);
        Files::register('image/tiff', DocumentType::GEODATA, 'tiff', GeoTiffTypeIdentifier::class);
    }

    private function registerThumbnailGenerators()
    {
        Thumbnails::register(GeoJsonGpxKmlThumbnailGenerator::class);
        Thumbnails::register(GeoPackageThumbnailGenerator::class);
        Thumbnails::register(ShapefileThumbnailGenerator::class);
        Thumbnails::register(GeoTiffThumbnailGenerator::class);
    }

    private function registerPreviewDrivers()
    {
        Previews::register(GeodataPreviewDriver::class);
    }
}
