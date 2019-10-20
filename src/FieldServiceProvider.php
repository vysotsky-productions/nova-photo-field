<?php

namespace VysotskyProductions\NovaPhotoFiled;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/config/nova-photo-field.php' => config_path('nova-photo-field.php'),
        ], 'nova-photo-field');

        Nova::serving(function (ServingNova $event) {
            Nova::script('NovaPhotoField', __DIR__ . '/../dist/js/field.js');
            Nova::style('NovaPhotoField', __DIR__ . '/../dist/css/field.css');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
