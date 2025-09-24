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
        'App\Models\SiscaV2\Equipment' => 'App\Policies\SiscaV2\EquipmentPolicy',
        'App\Models\SiscaV2\EquipmentType' => 'App\Policies\SiscaV2\EquipmentTypePolicy',
        'App\Models\SiscaV2\ChecksheetTemplate' => 'App\Policies\SiscaV2\ChecksheetTemplatePolicy',
        'App\Models\SiscaV2\Company' => 'App\Policies\SiscaV2\CompanyPolicy',
        'App\Models\SiscaV2\User' => 'App\Policies\SiscaV2\UserPolicy',
        'App\Models\SiscaV2\PeriodCheck' => 'App\Policies\SiscaV2\PeriodCheckPolicy',
        'App\Models\SiscaV2\Area' => 'App\Policies\SiscaV2\AreaPolicy',
        'App\Models\SiscaV2\Location' => 'App\Policies\SiscaV2\LocationPolicy',
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
