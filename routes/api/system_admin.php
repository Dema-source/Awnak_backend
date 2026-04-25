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
    | Skill - Limited access - Read only access
    |--------------------------------------------------------------------------
    */
  // API: {{baseURL}}/api/system/skills
  // API: {{baseURL}}/api/system/skills/popular
  // API: {{baseURL}}/api/system/skills/by-ids
  // API: {{baseURL}}/api/system/skills/recent
  // API: {{baseURL}}/api/system/skills/statistics

  Route::get('skills/popular', [SkillController::class, 'getPopular']);
  Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
  Route::get('skills/recent', [SkillController::class, 'getRecent']);
  Route::get('skills/statistics', [SkillController::class, 'getStatistics']);
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
  // API: {{baseURL}}/api/system/profiles
  Route::apiResource('profiles', ProfileController::class)->only(['index', 'show']);
  // Profile filtering and search
  // API: {{baseURL}}/api/system/profiles/by-gender
  // API: {{baseURL}}/api/system/profiles/by-age-range
  // API: {{baseURL}}/api/system/profiles/search
  // API: {{baseURL}}/api/system/profiles/by-skills
  Route::get('profiles/by-gender', [ProfileController::class, 'getByGender']);
  Route::get('profiles/by-age-range', [ProfileController::class, 'getByAgeRange']);
  Route::get('profiles/search', [ProfileController::class, 'search']);
  Route::get('profiles/by-skills', [ProfileController::class, 'getBySkills']);
  // Profile relationships
  // API: {{baseURL}}/api/system/profiles/{id}/with-relations
  // API: {{baseURL}}/api/system/profiles/with-relations
  Route::get('profiles/{id}/with-relations', [ProfileController::class, 'showWithRelations']);
  Route::get('profiles/with-relations', [ProfileController::class, 'indexWithRelations']);

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
