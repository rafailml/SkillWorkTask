<?php

namespace App\Providers;

use App\Interfaces\CompanyInfo\CompanyInfoInterface;
use App\Services\CompanyInfo\ClearbitCompanyInfoService;
use Illuminate\Support\ServiceProvider;

class CompanyInfoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CompanyInfoInterface::class, ClearbitCompanyInfoService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
