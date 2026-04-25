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
      use App\Http\Controllers\Api\DocumentController;
      use App\Http\Controllers\Api\VolunteerDocumentController;
      use Illuminate\Support\Facades\Route;

      /*
    |--------------------------------------------------------------------------
    | Location Management - Limited access - Read only access
    |--------------------------------------------------------------------------
    */
      // API: {{baseURL}}/api/system/countries 
      Route::get('countries/region/{region}', [CountryController::class, 'byRegion']);
      Route::get('countries/{country}/with-cities', [CountryController::class, 'withCities']);
      Route::get('countries/active', [CountryController::class, 'active']);
      Route::apiResource('countries', CountryController::class)->only(['index', 'show']);

      // API: {{baseURL}}/api/system/cities 
      Route::get('country/{country}/cities', [CityController::class, 'byCountry']);
      Route::get('city/{city}/with-country', [CityController::class, 'withCountry']);
      Route::get('cities/active', [CountryController::class, 'active']);
      Route::apiResource('cities', CountryController::class)->only(['index', 'show']);

      // API: {{baseURL}}/api/system/locations 
      Route::get('locations/city/{city}', [LocationController::class, 'getByCity']);
      Route::get('locations/country/{country}', [LocationController::class, 'getByCountry']);
      Route::get('locations/within-radius', [LocationController::class, 'withinRadius']);
      Route::get('locations/with-opportunity', [LocationController::class, 'withOpportunity']);
      Route::apiResource('locations', LocationController::class)->only(['index', 'show']);

      /*
    |--------------------------------------------------------------------------
    | Skill - Limited access - Read only access
    |--------------------------------------------------------------------------
    */
      // API: {{baseURL}}/api/system/skills
      // API: {{baseURL}}/api/system/skills/popular
      // API: {{baseURL}}/api/system/skills/by-ids
      // API: {{baseURL}}/api/system/skills/recent
      Route::get('skills/popular', [SkillController::class, 'getPopular']);
      Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
      Route::get('skills/recent', [SkillController::class, 'getRecent']);
      Route::get('skills/my-skills', [SkillController::class, 'getMySkills']);
      Route::apiResource('skills', SkillController::class)->only(['index', 'show']);


      /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */
      // API: {{baseURL}}/api/volunteer/profile
      // API: {{baseURL}}/api/volunteer/profile/update
      Route::get('profile', [ProfileController::class, 'getMyProfile']);
      Route::put('profile/update', [ProfileController::class, 'updateMyProfile']);
      // Profile viewing (read-only access to other profiles)
      // API: {{baseURL}}/api/volunteer/profiles
      Route::apiResource('profiles', ProfileController::class)->only(['index', 'show']);
      // Profile filtering and search
      // API: {{baseURL}}/api/volunteer/profiles/by-gender
      // API: {{baseURL}}/api/volunteer/profiles/by-age-range
      // API: {{baseURL}}/api/volunteer/profiles/search
      // API: {{baseURL}}/api/volunteer/profiles/by-skills
      Route::get('profiles/by-gender', [ProfileController::class, 'getByGender']);
      Route::get('profiles/by-age-range', [ProfileController::class, 'getByAgeRange']);
      Route::get('profiles/search', [ProfileController::class, 'search']);
      Route::get('profiles/by-skills', [ProfileController::class, 'getBySkills']);
      // Profile relationships
      // API: {{baseURL}}/api/volunteer/profiles/{id}/with-relations
      // API: {{baseURL}}/api/volunteer/profiles/with-relations
      Route::get('profiles/{id}/with-relations', [ProfileController::class, 'showWithRelations']);
      Route::get('profiles/with-relations', [ProfileController::class, 'indexWithRelations']);

      /*
        |--------------------------------------------------------------------------
        | Documents - Volunteer can manage their own documents
        |--------------------------------------------------------------------------
        */
      // API: {{baseURL}}/api/volunteer/documents
      Route::get('documents/my-documents', [VolunteerDocumentController::class, 'getMyDocuments']);
      Route::get('documents/search', [VolunteerDocumentController::class, 'search']);
      Route::get('documents/type/{type}', [VolunteerDocumentController::class, 'getByType']);
      Route::apiResource('documents', VolunteerDocumentController::class);
