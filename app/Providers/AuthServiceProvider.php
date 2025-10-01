<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        'App\Models\Equipment' => 'App\Policies\EquipmentPolicy',
        'App\Models\EquipmentType' => 'App\Policies\EquipmentTypePolicy',
        'App\Models\ChecksheetTemplate' => 'App\Policies\ChecksheetTemplatePolicy',
        'App\Models\Company' => 'App\Policies\CompanyPolicy',
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Models\PeriodCheck' => 'App\Policies\PeriodCheckPolicy',
        'App\Models\Area' => 'App\Policies\AreaPolicy',
        'App\Models\Location' => 'App\Policies\LocationPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
