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
  use App\Http\Controllers\Api\VolunteerController;
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
  // API: {{baseURL}}/api/volunteer/organization-profiles
  Route::get('organization-profiles/with-relations', [OrganizationProfileController::class, 'indexWithRelations']);
  Route::get('organization-profiles/{id}/with-relations', [OrganizationProfileController::class, 'showWithRelations']);
  Route::apiResource('organization-profiles', OrganizationProfileController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Volunteer
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/volunteer/volunteers
  // Route::get('volunteers/statistics', [VolunteerController::class, 'getStatistics']);
  // Route::get('volunteers/list-active', [VolunteerController::class, 'listActive']);
  // Route::get('volunteers/list-inactive', [VolunteerController::class, 'listInactive']);
  // Route::get('volunteers/list-pending', [VolunteerController::class, 'listPending']);
  // Route::get('volunteers/list-blocked', [VolunteerController::class, 'listBlocked']);
  // Route::patch('volunteers/{id}/activate', [VolunteerController::class, 'activate']);
  // Route::patch('volunteers/{id}/deactivate', [VolunteerController::class, 'deactivate']);
  // Route::patch('volunteers/{id}/block', [VolunteerController::class, 'block']);
  // Route::get('volunteers/{id}/with-relations', [VolunteerController::class, 'showWithRelations']);
  // Route::get('volunteers/with-relations', [VolunteerController::class, 'indexWithRelations']);
  // Route::get('volunteers/profile/{profileId}', [VolunteerController::class, 'getByProfileId']);
  // Route::get('volunteers/user/{userId}', [VolunteerController::class, 'getByUserId']);
  // Route::get('volunteers/check-user/{userId}', [VolunteerController::class, 'userHasVolunteer']);
  // Route::apiResource('volunteers', VolunteerController::class);
