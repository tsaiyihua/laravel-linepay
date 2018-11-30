<?php
namespace TsaiYiHua\LinePay;

use Illuminate\Support\ServiceProvider;

class LinePayServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerConfigs();
        }
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    protected function registerConfigs()
    {
        $this->publishes([
            __DIR__ . '/../config/linepay.php' => config_path('linepay.php')
        ], 'linepay');
    }
}