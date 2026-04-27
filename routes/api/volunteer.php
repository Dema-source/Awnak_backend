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
  use App\Http\Controllers\Api\VolunteerDocumentController;
  use Illuminate\Support\Facades\Route;

  /*
  |--------------------------------------------------------------------------
  | Location Management - Limited access - Read only access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/volunteer/countries 
  Route::get('countries/region/{region}', [CountryController::class, 'byRegion']);
  Route::get('countries/{country}/with-cities', [CountryController::class, 'withCities']);
  Route::get('countries/active', [CountryController::class, 'active']);
  Route::apiResource('countries', CountryController::class)->only(['index', 'show']);

  // API: {{baseURL}}/api/volunteer/cities 
  Route::get('country/{country}/cities', [CityController::class, 'byCountry']);
  Route::get('city/{city}/with-country', [CityController::class, 'withCountry']);
  Route::get('cities/active', [CountryController::class, 'active']);
  Route::apiResource('cities', CountryController::class)->only(['index', 'show']);

  // API: {{baseURL}}/api/volunteer/locations 
  Route::get('locations/city/{city}', [LocationController::class, 'getByCity']);
  Route::get('locations/country/{country}', [LocationController::class, 'getByCountry']);
  Route::get('locations/within-radius', [LocationController::class, 'withinRadius']);
  Route::get('locations/with-opportunity', [LocationController::class, 'withOpportunity']);
  Route::apiResource('locations', LocationController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Documents - Volunteer can manage their own documents
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/volunteer/documents
  Route::get('documents/my-documents', [VolunteerDocumentController::class, 'getMyDocuments']);
  Route::get('volunteer-documents/{id}/download', [VolunteerDocumentController::class, 'download']);
  Route::get('volunteer-documents/{id}/read', [VolunteerDocumentController::class, 'read']);
  Route::get('volunteer-documents/{id}/url', [VolunteerDocumentController::class, 'getFileUrl']);
  Route::apiResource('volunteer-documents', VolunteerDocumentController::class);

  /*
  |--------------------------------------------------------------------------
  | Skill - Limited access - Read only access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/volunteer/skills
  Route::get('skills/popular', [SkillController::class, 'getPopular']);
  Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
  Route::get('skills/recent', [SkillController::class, 'getRecent']);
  Route::get('skills/my-skills', [SkillController::class, 'getMySkills']);
  Route::apiResource('skills', SkillController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Users - Limited access - Read only access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/volunteer/users
  Route::apiResource('users', UserController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Profile
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/volunteer/profiles
  Route::get('profiles/my-profile', [ProfileController::class, 'getMyProfile']);
  Route::put('profile/update/my-profile', [ProfileController::class, 'updateMyProfile']);
  Route::get('profiles/{id}/with-relations', [ProfileController::class, 'showWithRelations']);
  Route::get('profiles/with-relations', [ProfileController::class, 'indexWithRelations']);
  Route::apiResource('profiles', ProfileController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Organization Profiles
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/organization-profiles
  Route::get('organization-profiles/with-relations', [OrganizationProfileController::class, 'indexWithRelations']);
  Route::get('organization-profiles/{id}/with-relations', [OrganizationProfileController::class, 'showWithRelations']);
  Route::get('organization-profiles/check-user/{userId}', [OrganizationProfileController::class, 'userHasProfile']);
  Route::apiResource('organization-profiles', OrganizationProfileController::class)->only(['index', 'show']);
