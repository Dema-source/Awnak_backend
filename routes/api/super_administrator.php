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
  use App\Http\Controllers\Api\RolesPermissions\RoleController;
  use App\Http\Controllers\Api\RolesPermissions\RolePermissionController;
  use App\Http\Controllers\Api\SkillController;
  use App\Http\Controllers\Api\UserController;
  use App\Http\Controllers\Api\VolunteerDocumentController;
  use App\Http\Controllers\Api\OpportunityDocumentController;
  use Illuminate\Support\Facades\Route;

  /*
  |--------------------------------------------------------------------------
  | Roles & Permissions - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/roles
  Route::post('assign-permission-to-role', [RolePermissionController::class, 'assignPermissionToRole']);
  Route::post('remove-permission-from-role', [RolePermissionController::class, 'removePermissionFromRole']);
  Route::post('assign-role-to-user', [RolePermissionController::class, 'assignRoleToUser']);
  Route::post('remove-role-from-user', [RolePermissionController::class, 'revokeRoleFromUser']);
  Route::post('assign-permission-to-user', [RolePermissionController::class, 'assignPermissionToUser']);
  Route::post('remove-permission-from-user', [RolePermissionController::class, 'revokePermissionFromUser']);
  Route::get('check-permission', [RolePermissionController::class, 'checkPermission']);
  Route::get('get-user-permissions/{user}', [RolePermissionController::class, 'getUserPermissions']);
  Route::apiResource('roles', RoleController::class);

  /*
  |--------------------------------------------------------------------------
  | Location Management - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/countries
  Route::get('countries/active', [CountryController::class, 'active']);
  Route::get('countries/region/{region}', [CountryController::class, 'byRegion']);
  Route::get('countries/{country}/with-cities', [CountryController::class, 'withCities']);
  Route::get('countries/with-cities-count', [CountryController::class, 'withCitiesCount']);
  Route::apiResource('countries', CountryController::class);

  // API: {{baseURL}}/api/admin/cities
  Route::get('cities/active', [CityController::class, 'active']);
  Route::get('country/{country}/cities', [CityController::class, 'byCountry']);
  Route::get('city/{city}/with-country', [CityController::class, 'withCountry']);
  Route::get('cities/with-counts', [CityController::class, 'withCounts']);
  Route::get('cities/within-radius', [CityController::class, 'withinRadius']);
  Route::apiResource('cities', CityController::class);

  // API: {{baseURL}}/api/admin/locations
  Route::get('locations/city/{city}', [LocationController::class, 'getByCity']);
  Route::get('locations/country/{country}', [LocationController::class, 'getByCountry']);
  Route::get('locations/within-radius', [LocationController::class, 'withinRadius']);
  Route::get('locations/with-opportunity', [LocationController::class, 'withOpportunity']);
  Route::apiResource('locations', LocationController::class);

  /*
  |--------------------------------------------------------------------------
  | Document - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/volunteer-documents
  Route::get('volunteer-documents/user/{userId}', [VolunteerDocumentController::class, 'getMyDocuments']);
  Route::get('volunteer-documents/{id}/download', [VolunteerDocumentController::class, 'download']);
  Route::get('volunteer-documents/{id}/read', [VolunteerDocumentController::class, 'read']);
  Route::get('volunteer-documents/{id}/url', [VolunteerDocumentController::class, 'getFileUrl']);
  Route::apiResource('volunteer-documents', VolunteerDocumentController::class);

  // API: {{baseURL}}/api/admin/opportunity-documents
  Route::get('opportunity-documents/search', [OpportunityDocumentController::class, 'search']);
  Route::get('opportunity-documents/type/{type}', [OpportunityDocumentController::class, 'getByType']);
  Route::get('opportunity-documents/opportunity/{opportunity}', [OpportunityDocumentController::class, 'getByOpportunity']);
  Route::get('opportunity-documents/organization/{organizationId}', [OpportunityDocumentController::class, 'getMyOpportunityDocuments']);
  Route::apiResource('opportunity-documents', OpportunityDocumentController::class);

  // API: {{baseURL}}/api/admin/documents (all documents)
  Route::get('documents/search', [VolunteerDocumentController::class, 'search']);
  Route::get('documents/type/{type}', [VolunteerDocumentController::class, 'getByType']);
  Route::get('documents/volunteer/{userId}', [VolunteerDocumentController::class, 'getMyDocuments']);
  Route::get('documents/opportunity/{opportunity}', [OpportunityDocumentController::class, 'getByOpportunity']);
  Route::get('documents/organization/{organizationId}', [OpportunityDocumentController::class, 'getMyOpportunityDocuments']);

  /*
  |--------------------------------------------------------------------------
  | Skill - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/skills
  Route::get('skills/with-relations', [SkillController::class, 'indexWithRelations']);
  Route::get('skills/{id}/with-relations', [SkillController::class, 'showWithRelations']);
  Route::get('skills/with-profiles-count', [SkillController::class, 'getWithProfilesCount']);
  Route::get('skills/with-opportunities-count', [SkillController::class, 'getWithOpportunitiesCount']);
  Route::get('skills/popular', [SkillController::class, 'getPopular']);
  Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
  Route::get('skills/not-in-profile/{profileId}', [SkillController::class, 'getNotInProfile']);
  Route::get('skills/not-in-opportunity/{opportunityId}', [SkillController::class, 'getNotInOpportunity']);
  Route::get('skills/recent', [SkillController::class, 'getRecent']);
  Route::get('skills/statistics', [SkillController::class, 'getStatistics']);
  Route::apiResource('skills', SkillController::class);

  /*
  |--------------------------------------------------------------------------
  | Users - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/users
  Route::patch('users/{user}/inactive', [UserController::class, 'inactive']);
  Route::apiResource('users', UserController::class);

  /*
  |--------------------------------------------------------------------------
  | Profile - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/profiles
  Route::get('profiles/{id}/with-relations', [ProfileController::class, 'showWithRelations']);
  Route::get('profiles/with-relations', [ProfileController::class, 'indexWithRelations']);
  Route::get('profiles/statistics', [ProfileController::class, 'getStatistics']);
  Route::apiResource('profiles', ProfileController::class);

  /*
  |--------------------------------------------------------------------------
  | Organization Profiles
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/organization-profiles
  Route::get('organization-profiles/statistics', [OrganizationProfileController::class, 'getStatistics']);
  Route::get('organization-profiles/list-active', [OrganizationProfileController::class, 'listActive']);
  Route::get('organization-profiles/list-not-active', [OrganizationProfileController::class, 'listNotActive']);
  Route::get('organization-profiles/with-relations', [OrganizationProfileController::class, 'indexWithRelations']);
  Route::get('organization-profiles/{id}/with-relations', [OrganizationProfileController::class, 'showWithRelations']);
  Route::post('organization-profiles/{id}/activate', [OrganizationProfileController::class, 'activate']);
  Route::post('organization-profiles/{id}/deactivate', [OrganizationProfileController::class, 'deactivate']);
  Route::get('organization-profiles/user/{userId}', [OrganizationProfileController::class, 'getByUserId']);
  Route::get('organization-profiles/check-user/{userId}', [OrganizationProfileController::class, 'userHasProfile']);
  Route::apiResource('organization-profiles', OrganizationProfileController::class);





  /*
  |--------------------------------------------------------------------------
  | Profile-Skill
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/profile-skills
  Route::apiResource('profile-skills', ProfileSkillController::class)->except(['show']);
  Route::get('profile-skills/{profile}', [ProfileSkillController::class, 'show']);


  /*
|--------------------------------------------------------------------------
| Location-Opportunity
|--------------------------------------------------------------------------
*/
  // API: {{baseURL}}/api/admin/location-opportunities
  Route::apiResource('location-opportunities', LocationOpportunityController::class)->only(['index', 'show']);
  Route::get('location-opportunities/location/{location}/opportunities', [LocationOpportunityController::class, 'getOpportunitiesByLocation']);
  Route::post('location-opportunities/{opportunity}', [LocationOpportunityController::class, 'store']);
  Route::put('location-opportunities/{opportunity}', [LocationOpportunityController::class, 'update']);
  Route::delete('location-opportunities/{opportunity}', [LocationOpportunityController::class, 'destroy']);
  Route::get('location-opportunities/{opportunity}/show', [LocationOpportunityController::class, 'showOpportunity']);
  Route::put('location-opportunities/{opportunity}/location/{location}/pivot', [LocationOpportunityController::class, 'updatePivot']);
  Route::get('location-opportunities/{opportunity}/location/{location}/pivot', [LocationOpportunityController::class, 'getPivot']);
  // API: {{baseURL}}/api/admin/location-opportunities
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
  // API: {{baseURL}}/api/admin/opportunity-skills
  Route::apiResource('opportunity-skills', OpportunitySkillController::class)->except(['show']);
  Route::get('opportunity-skills/{opportunity}', [OpportunitySkillController::class, 'show']);
  Route::get('opportunity-skills/skill/{skill}/opportunities', [OpportunitySkillController::class, 'getOpportunitiesBySkill']);
  Route::get('opportunity-skills/{opportunity}/skill/{skill}/check', [OpportunitySkillController::class, 'checkSkill']);
  Route::get('skills-count/opportunity-skills', [OpportunitySkillController::class, 'getSkillsCount']);
  Route::get('popular-skills/opportunity-skills', [OpportunitySkillController::class, 'getPopularSkills']);

  // =========================================//
  // =========================================//
  // =========================================//




  // Opportunity
  // API: {{baseURL}}/api/admin/opportunities  
  // Route::apiResource('opportunities', OpportunityController::class);



  // // Certificate
  // Route::apiResource('certificates', CertificateController::class);

  // // Badge
  // Route::apiResource('badges', BadgeController::class);
