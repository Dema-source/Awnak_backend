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
  use App\Http\Controllers\Api\VolunteerController;
  use App\Http\Controllers\Api\VolunteerDocumentController;
  use Illuminate\Support\Facades\Route;

  /*
  |--------------------------------------------------------------------------
  | Location Management - Limited access - Read only access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/system/countries 
  Route::get('countries/active', [CountryController::class, 'active']);
  Route::get('countries/region/{region}', [CountryController::class, 'byRegion']);
  Route::get('countries/{country}/with-cities', [CountryController::class, 'withCities']);
  Route::get('countries/with-cities-count', [CountryController::class, 'withCitiesCount']);
  Route::apiResource('countries', CountryController::class)->only(['index', 'show']);

  // API: {{baseURL}}/api/system/cities 
  Route::get('cities/active', [CityController::class, 'active']);
  Route::get('country/{country}/cities', [CityController::class, 'byCountry']);
  Route::get('city/{city}/with-country', [CityController::class, 'withCountry']);
  Route::get('cities/with-counts', [CityController::class, 'withCounts']);
  Route::get('cities/within-radius', [CityController::class, 'withinRadius']);
  Route::apiResource('cities', CityController::class)->only(['index', 'show']);

  // API: {{baseURL}}/api/system/locations 
  Route::get('locations/city/{city}', [LocationController::class, 'getByCity']);
  Route::get('locations/country/{country}', [LocationController::class, 'getByCountry']);
  Route::get('locations/within-radius', [LocationController::class, 'withinRadius']);
  Route::get('locations/with-opportunity', [LocationController::class, 'withOpportunity']);
  Route::apiResource('locations', LocationController::class);

  /*
  |--------------------------------------------------------------------------
  | Document - full access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/system/volunteer-documents
  Route::get('volunteer-documents/user/{userId}', [VolunteerDocumentController::class, 'getMyDocuments']);
  Route::get('volunteer-documents/{id}/download', [VolunteerDocumentController::class, 'download']);
  Route::get('volunteer-documents/{id}/read', [VolunteerDocumentController::class, 'read']);
  Route::get('volunteer-documents/{id}/url', [VolunteerDocumentController::class, 'getFileUrl']);
  Route::apiResource('volunteer-documents', VolunteerDocumentController::class);

  /*
  |--------------------------------------------------------------------------
  | Skill - Limited access - Read only access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/system/skills
  Route::get('skills/popular', [SkillController::class, 'getPopular']);
  Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
  Route::get('skills/recent', [SkillController::class, 'getRecent']);
  Route::get('skills/statistics', [SkillController::class, 'getStatistics']);
  Route::apiResource('skills', SkillController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Users
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/system/users
  Route::apiResource('users', UserController::class);

  /*
  |--------------------------------------------------------------------------
  | Profile
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/system/profiles
  Route::get('profiles/{id}/with-relations', [ProfileController::class, 'showWithRelations']);
  Route::get('profiles/with-relations', [ProfileController::class, 'indexWithRelations']);
  Route::get('profiles/statistics', [ProfileController::class, 'getStatistics']);
  Route::apiResource('profiles', ProfileController::class);

  /*
  |--------------------------------------------------------------------------
  | Organization Profiles
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/system/organization-profiles
  Route::get('organization-profiles/statistics', [OrganizationProfileController::class, 'statistics']);
  Route::get('organization-profiles/list-active', [OrganizationProfileController::class, 'listActive']);
  Route::get('organization-profiles/list-not-active', [OrganizationProfileController::class, 'listNotActive']);
  Route::get('organization-profiles/with-relations', [OrganizationProfileController::class, 'indexWithRelations']);
  Route::get('organization-profiles/{id}/with-relations', [OrganizationProfileController::class, 'showWithRelations']);
  Route::patch('organization-profiles/{id}/activate', [OrganizationProfileController::class, 'activate']);
  Route::patch('organization-profiles/{id}/deactivate', [OrganizationProfileController::class, 'deactivate']);
  Route::get('organization-profiles/user/{userId}', [OrganizationProfileController::class, 'getByUserId']);
  Route::get('organization-profiles/check-user/{userId}', [OrganizationProfileController::class, 'userHasProfile']);
  Route::apiResource('organization-profiles', OrganizationProfileController::class);

  /*
  |--------------------------------------------------------------------------
  | Volunteer
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/system/volunteers
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











 

  /*
    |--------------------------------------------------------------------------
    | Location-Opportunity 
    |--------------------------------------------------------------------------
    */
  // Location-Opportunity operations for organization admin
  // API: {{baseURL}}/api/system/location-opportunities
  Route::apiResource('location-opportunities', LocationOpportunityController::class)->only(['index', 'show']);
  Route::get('location-opportunities/location/{location}/opportunities', [LocationOpportunityController::class, 'getOpportunitiesByLocation']);
  Route::post('location-opportunities/{opportunity}', [LocationOpportunityController::class, 'store']);
  Route::put('location-opportunities/{opportunity}', [LocationOpportunityController::class, 'update']);
  Route::delete('location-opportunities/{opportunity}', [LocationOpportunityController::class, 'destroy']);
  Route::get('location-opportunities/{opportunity}/show', [LocationOpportunityController::class, 'showOpportunity']);
  Route::put('location-opportunities/{opportunity}/location/{location}/pivot', [LocationOpportunityController::class, 'updatePivot']);
  Route::get('location-opportunities/{opportunity}/location/{location}/pivot', [LocationOpportunityController::class, 'getPivot']);

  /*
    |--------------------------------------------------------------------------
    | Opportunity-Skill
    |--------------------------------------------------------------------------
    */
  // API: {{baseURL}}/api/system/opportunity-skills
  Route::apiResource('opportunity-skills', OpportunitySkillController::class)->except(['show']);
  Route::get('opportunity-skills/{opportunity}', [OpportunitySkillController::class, 'show']);
  Route::get('opportunity-skills/skill/{skill}/opportunities', [OpportunitySkillController::class, 'getOpportunitiesBySkill']);
  Route::get('opportunity-skills/{opportunity}/skill/{skill}/check', [OpportunitySkillController::class, 'checkSkill']);
  Route::get('skills-count/opportunity-skills', [OpportunitySkillController::class, 'getSkillsCount']);
  Route::get('popular-skills/opportunity-skills', [OpportunitySkillController::class, 'getPopularSkills']);

  /*
    |--------------------------------------------------------------------------
    | Profile-Skill
    |--------------------------------------------------------------------------
    */
  // API: {{baseURL}}/api/system/profile-skills
  Route::apiResource('profile-skills', ProfileSkillController::class)->except(['show']);
  Route::get('profile-skills/{profile}', [ProfileSkillController::class, 'show']);
  Route::get('profile-skills/skill/{skill}/profiles', [ProfileSkillController::class, 'getProfilesBySkill']);
  Route::get('profile-skills/{profile}/skill/{skill}/check', [ProfileSkillController::class, 'checkSkill']);
  Route::get('skills-count/profile-skills', [ProfileSkillController::class, 'getSkillsCount']);
  Route::get('popular-skills/profile-skills', [ProfileSkillController::class, 'getPopularSkills']);

  // =========================================//
  // =========================================//
  // =========================================//









  // User Management
  // API: {{baseURL}}/api/system/admin/users       
  Route::apiResource('users', UserController::class);
  // API: {{baseURL}}/api/system/organizationProfiles  
  Route::apiResource('organizationProfiles', OrganizationProfileController::class);
  // Opportunity
  Route::apiResource('opportunities', OpportunityController::class);

  // Certificate
  Route::apiResource('certificates', CertificateController::class)->except(['store', 'update', 'delete']);

  // Badge
  Route::apiResource('badges', BadgeController::class)->except(['store', 'update', 'delete']);
