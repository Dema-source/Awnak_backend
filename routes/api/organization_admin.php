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
  // API: {{baseURL}}/api/organization/countries 
  Route::get('countries/region/{region}', [CountryController::class, 'byRegion']);
  Route::get('countries/{country}/with-cities', [CountryController::class, 'withCities']);
  Route::get('countries/active', [CountryController::class, 'active']);
  Route::apiResource('countries', CountryController::class)->only(['index', 'show']);

  // API: {{baseURL}}/api/organization/cities 
  Route::get('country/{country}/cities', [CityController::class, 'byCountry']);
  Route::get('city/{city}/with-country', [CityController::class, 'withCountry']);
  Route::get('cities/active', [CountryController::class, 'active']);
  Route::apiResource('cities', CountryController::class)->only(['index', 'show']);

  // API: {{baseURL}}/api/organization/locations 
  Route::get('locations/city/{city}', [LocationController::class, 'getByCity']);
  Route::get('locations/country/{country}', [LocationController::class, 'getByCountry']);
  Route::get('locations/within-radius', [LocationController::class, 'withinRadius']);
  Route::get('locations/with-opportunity/{opportunity}', [LocationController::class, 'withOpportunity']);
  Route::apiResource('locations', LocationController::class);

  /*
  |--------------------------------------------------------------------------
  | Document - full access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/organization/volunteer-documents
  Route::get('volunteer-documents/user/{userId}', [VolunteerDocumentController::class, 'getMyDocuments']);
  Route::get('volunteer-documents/{id}/download', [VolunteerDocumentController::class, 'download']);
  Route::get('volunteer-documents/{id}/read', [VolunteerDocumentController::class, 'read']);
  Route::get('volunteer-documents/{id}/url', [VolunteerDocumentController::class, 'getFileUrl']);
  Route::apiResource('volunteer-documents', VolunteerDocumentController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Skill - Read-only access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/organization/skills
  Route::get('skills/popular', [SkillController::class, 'getPopular']);
  Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
  Route::get('skills/recent', [SkillController::class, 'getRecent']);
  Route::apiResource('skills', SkillController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Users
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/organization/users
  Route::apiResource('users', UserController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Profile
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/volunteer/profiles
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








  // Opportunity 
  // API: {{baseURL}}/api/organization/get_opportunities(Related to specific Org)
  // API: {{baseURL}}/api/organization/add_opportunity 
  // API: {{baseURL}}/api/organization/opportunities
  Route::get('get_opportunities', [OrganizationProfileController::class, 'getOrganizationOpportunities']);
  Route::post('add_opportunity', [OpportunityController::class, 'storeOpportunity']);
  Route::apiResource('opportunities', OpportunityController::class)->except(['store']);



  // Opportunity Skills
  // API: {{baseURL}}/api/organization/opportunity-skills
  // API: {{baseURL}}/api/organization/skills-count/opportunity-skills
  // API: {{baseURL}}/api/organization/popular-skills/opportunity-skills
  Route::apiResource('opportunity-skills', OpportunitySkillController::class)->except(['show']);
  Route::get('opportunity-skills/{opportunity}', [OpportunitySkillController::class, 'show']);
  Route::get('opportunity-skills/skill/{skill}/opportunities', [OpportunitySkillController::class, 'getOpportunitiesBySkill']);
  Route::get('opportunity-skills/{opportunity}/skill/{skill}/check', [OpportunitySkillController::class, 'checkSkill']);
  Route::get('skills-count/opportunity-skills', [OpportunitySkillController::class, 'getSkillsCount']);
  Route::get('popular-skills/opportunity-skills', [OpportunitySkillController::class, 'getPopularSkills']);

  // Certificate
  Route::apiResource('certificates', CertificateController::class)->except(['store', 'update', 'delete']);

  // Badge
  Route::apiResource('badges', BadgeController::class)->except(['store', 'update', 'delete']);

  // Location Management - Organizations can manage their own locations
  Route::apiResource('cities', CityController::class)->only(['index', 'show']);
  Route::apiResource('countries', CountryController::class)->only(['index', 'show']);
  // API: {{baseURL}}/api/organization/locations
  Route::apiResource('locations', LocationController::class)->only(['index', 'show']);
  Route::get('city/locations/{city}', [LocationController::class, 'getByCity']);
  Route::get('country/locations/{country}', [LocationController::class, 'getByCountry']);
  Route::get('within-radius/locations', [LocationController::class, 'withinRadius']);
  Route::get('search/locations', [LocationController::class, 'searchByAddress']);
  Route::get('with-opportunity/locations', [LocationController::class, 'withOpportunity']);

  // Location-Opportunity operations for organization admin
  // API: {{baseURL}}/api/organization/location-opportunities
  Route::apiResource('location-opportunities', LocationOpportunityController::class)->only(['index', 'show']);
  Route::get('location-opportunities/location/{location}/opportunities', [LocationOpportunityController::class, 'getOpportunitiesByLocation']);
  Route::post('location-opportunities/{opportunity}', [LocationOpportunityController::class, 'store']);
  Route::put('location-opportunities/{opportunity}', [LocationOpportunityController::class, 'update']);
  Route::delete('location-opportunities/{opportunity}', [LocationOpportunityController::class, 'destroy']);
  Route::get('location-opportunities/{opportunity}/show', [LocationOpportunityController::class, 'showOpportunity']);
  Route::put('location-opportunities/{opportunity}/location/{location}/pivot', [LocationOpportunityController::class, 'updatePivot']);
  Route::get('location-opportunities/{opportunity}/location/{location}/pivot', [LocationOpportunityController::class, 'getPivot']);
