     <?php

    use App\Http\Controllers\Api\BadgeController;
    use App\Http\Controllers\Api\CertificateController;
    use App\Http\Controllers\Api\CityController;
    use App\Http\Controllers\Api\CountryController;
    use App\Http\Controllers\Api\LocationController;
    use App\Http\Controllers\Api\LocationOpportunityController;
    use App\Http\Controllers\Api\OpportunityController;
    use App\Http\Controllers\Api\OpportunitySkillController;
    use App\Http\Controllers\Api\OrganizationProfileController;
    use App\Http\Controllers\Api\ProfileController;
    use App\Http\Controllers\Api\ProfileSkillController;
    use App\Http\Controllers\Api\SkillController;
    use App\Http\Controllers\Api\UserController;
    use Illuminate\Support\Facades\Route;
        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */        // API: {{baseURL}}/api/coordinator/profile
        // API: {{baseURL}}/api/coordinator/profile/update
        Route::get('profile', [ProfileController::class, 'getMyProfile']);
        Route::put('profile/update', [ProfileController::class, 'updateMyProfile']);
        // Profile viewing (read-only access to other profiles)
        // API: {{baseURL}}/api/coordinator/profiles
        Route::apiResource('profiles', ProfileController::class)->only(['index', 'show']);
        // Profile filtering and search
        // API: {{baseURL}}/api/coordinator/profiles/by-gender
        // API: {{baseURL}}/api/coordinator/profiles/by-age-range
        // API: {{baseURL}}/api/coordinator/profiles/search
        // API: {{baseURL}}/api/coordinator/profiles/by-skills
        Route::get('profiles/by-gender', [ProfileController::class, 'getByGender']);
        Route::get('profiles/by-age-range', [ProfileController::class, 'getByAgeRange']);
        Route::get('profiles/search', [ProfileController::class, 'search']);
        Route::get('profiles/by-skills', [ProfileController::class, 'getBySkills']);
        // Profile relationships
        // API: {{baseURL}}/api/coordinator/profiles/{id}/with-relations
        // API: {{baseURL}}/api/coordinator/profiles/with-relations
        Route::get('profiles/{id}/with-relations', [ProfileController::class, 'showWithRelations']);
        Route::get('profiles/with-relations', [ProfileController::class, 'indexWithRelations']);