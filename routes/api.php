<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\OrganizationProfileController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\LocationOpportunityController;
use App\Http\Controllers\Api\OpportunitySkillController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProfileSkillController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VolunteerCertificateController;
use App\Http\Controllers\Api\VolunteerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {

    // Super Administrator routes - full access
    Route::prefix('admin')->middleware('role:super_administrator')->group(function () {
        // Roles & Permissions
        require __DIR__ . '/api/rolesandpermissions.php';

        // User Management
        // API: {{baseURL}}/api/admin/users       
        Route::apiResource('users', UserController::class);

        // Organization
        // API: {{baseURL}}/api/admin/notactive/organizationProfiles
        // API: {{baseURL}}/api/admin/activate/organizationProfile/{id}
        // API: {{baseURL}}/api/admin/organizationProfiles
        Route::get('notactive/organizationProfiles', [OrganizationProfileController::class, 'listNotActive']);
        Route::patch('activate/organizationProfile/{id}', [OrganizationProfileController::class, 'activateOrganization']);
        Route::apiResource('organizationProfiles', OrganizationProfileController::class);

        // Opportunity
        // API: {{baseURL}}/api/admin/opportunities  
        Route::apiResource('opportunities', OpportunityController::class);

        // Skill
        // API: {{baseURL}}/api/admin/skills
        Route::apiResource('skills', SkillController::class);

        // Opportunity Skills
        // API: {{baseURL}}/api/admin/opportunity-skills
        Route::apiResource('opportunity-skills', OpportunitySkillController::class)->except(['show']);
        Route::get('opportunity-skills/{opportunity}', [OpportunitySkillController::class, 'show']);
        Route::get('opportunity-skills/skill/{skill}/opportunities', [OpportunitySkillController::class, 'getOpportunitiesBySkill']);
        Route::get('opportunity-skills/{opportunity}/skill/{skill}/check', [OpportunitySkillController::class, 'checkSkill']);
        Route::get('skills-count/opportunity-skills', [OpportunitySkillController::class, 'getSkillsCount']);
        Route::get('popular-skills/opportunity-skills', [OpportunitySkillController::class, 'getPopularSkills']);

        // Certificate
        Route::apiResource('certificates', CertificateController::class);

        // Badge
        Route::apiResource('badges', BadgeController::class);

        // Location Management - Admin Level
        // API: {{baseURL}}/api/admin/countries
        Route::apiResource('countries', CountryController::class);
        Route::get('active/countries', [CountryController::class, 'active']);
        Route::get('region/countries/{region}', [CountryController::class, 'byRegion']);
        Route::get('search/countries', [CountryController::class, 'search']);
        Route::get('with-cities/countries/{country}', [CountryController::class, 'withCities']);
        Route::get('with-cities-count/countries', [CountryController::class, 'withCitiesCount']);

        // API: {{baseURL}}/api/admin/cities
        Route::apiResource('cities', CityController::class);
        Route::get('active/cities', [CityController::class, 'active']);
        Route::get('country/{country}/cities', [CityController::class, 'byCountry']);
        Route::get('search/cities', [CityController::class, 'search']);
        Route::get('with-country/city/{city}', [CityController::class, 'withCountry']);
        Route::get('with-counts/cities', [CityController::class, 'withCounts']);
        Route::get('within-radius/cities', [CityController::class, 'withinRadius']);

        // API: {{baseURL}}/api/admin/locations - Full access
        Route::apiResource('locations', LocationController::class);
        Route::get('city/locations/{city}', [LocationController::class, 'getByCity']);
        Route::get('country/locations/{country}', [LocationController::class, 'getByCountry']);
        Route::get('within-radius/locations', [LocationController::class, 'withinRadius']);
        Route::get('search/locations', [LocationController::class, 'searchByAddress']);
        Route::get('with-opportunity/locations', [LocationController::class, 'withOpportunity']);

        // Location-Opportunity operations for super administrator
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
        | Profile
        |--------------------------------------------------------------------------
        */
        // API: {{baseURL}}/api/admin/profile
        // API: {{baseURL}}/api/admin/profile/update
        // Route::get('profile', [ProfileController::class, 'getMyProfile']);
        // Route::put('profile/update', [ProfileController::class, 'updateMyProfile']);
        // Profile viewing (full access to other profiles)
        // API: {{baseURL}}/api/admin/profiles
        Route::apiResource('profiles', ProfileController::class);
        // Profile filtering and search
        // API: {{baseURL}}/api/admin/profiles/by-gender
        // API: {{baseURL}}/api/admin/profiles/by-age-range
        // API: {{baseURL}}/api/admin/profiles/search
        // API: {{baseURL}}/api/admin/profiles/by-skills
        Route::get('profiles/by-gender', [ProfileController::class, 'getByGender']);
        Route::get('profiles/by-age-range', [ProfileController::class, 'getByAgeRange']);
        Route::get('profiles/search', [ProfileController::class, 'search']);
        Route::get('profiles/by-skills', [ProfileController::class, 'getBySkills']);
        // Profile relationships
        // API: {{baseURL}}/api/admin/profiles/{id}/with-relations
        // API: {{baseURL}}/api/admin/profiles/with-relations
        Route::get('profiles/{id}/with-relations', [ProfileController::class, 'showWithRelations']);
        Route::get('profiles/with-relations', [ProfileController::class, 'indexWithRelations']);
        // Profile statistics
        // API: {{baseURL}}/api/admin/profiles/statistics
        Route::get('profiles/statistics', [ProfileController::class, 'getStatistics']);

        /*
        |--------------------------------------------------------------------------
        | Skill
        |--------------------------------------------------------------------------
        */
        // API: {{baseURL}}/api/admin/skills
        // API: {{baseURL}}/api/admin/skills/statistics
        // API: {{baseURL}}/api/admin/skills/search
        // API: {{baseURL}}/api/admin/skills/with-relations
        // API: {{baseURL}}/api/admin/skills/with-profiles-count
        // API: {{baseURL}}/api/admin/skills/with-opportunities-count
        // API: {{baseURL}}/api/admin/skills/popular
        // API: {{baseURL}}/api/admin/skills/by-ids
        // API: {{baseURL}}/api/admin/skills/not-in-profile/{profileId}
        // API: {{baseURL}}/api/admin/skills/not-in-opportunity/{opportunityId}
        // API: {{baseURL}}/api/admin/skills/recent
        Route::apiResource('skills', SkillController::class);
        Route::get('skills/statistics', [SkillController::class, 'getStatistics']);
        Route::get('skills/search', [SkillController::class, 'search']);
        Route::get('skills/with-relations', [SkillController::class, 'indexWithRelations']);
        Route::get('skills/{id}/with-relations', [SkillController::class, 'showWithRelations']);
        Route::get('skills/with-profiles-count', [SkillController::class, 'getWithProfilesCount']);
        Route::get('skills/with-opportunities-count', [SkillController::class, 'getWithOpportunitiesCount']);
        Route::get('skills/popular', [SkillController::class, 'getPopular']);
        Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
        Route::get('skills/not-in-profile/{profileId}', [SkillController::class, 'getNotInProfile']);
        Route::get('skills/not-in-opportunity/{opportunityId}', [SkillController::class, 'getNotInOpportunity']);
        Route::get('skills/recent', [SkillController::class, 'getRecent']);
  });

    // System Admin routes - administrative access
    Route::prefix('system')->middleware('role:system_admin')->group(function () {
        // User Management
        // API: {{baseURL}}/api/system/admin/users       
        Route::apiResource('users', UserController::class);
        // API: {{baseURL}}/api/system/organizationProfiles  
        Route::apiResource('organizationProfiles', OrganizationProfileController::class);
        // Opportunity
        Route::apiResource('opportunities', OpportunityController::class);
        // Opportunity Skills
        // API: {{baseURL}}/api/system/opportunity-skills
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

        // Location Management - Read-only access for system admin
        // API: {{baseURL}}/api/system/countries - Read-only access
        Route::apiResource('countries', CountryController::class)->only(['index', 'show']);
        Route::get('countries/active', [CountryController::class, 'active']);
        Route::get('countries/region/{region}', [CountryController::class, 'byRegion']);
        Route::get('countries/search', [CountryController::class, 'search']);
        Route::get('countries/{country}', [CountryController::class, 'show']);
        Route::get('countries/{country}/with-cities', [CountryController::class, 'withCities']);

        // API: {{baseURL}}/api/system/cities - Read-only access
        Route::apiResource('cities', CityController::class)->only(['index', 'show']);
        Route::get('cities/active', [CityController::class, 'active']);
        Route::get('cities/country/{country}', [CityController::class, 'byCountry']);
        Route::get('cities/search', [CityController::class, 'search']);
        Route::get('cities/{city}', [CityController::class, 'show']);
        Route::get('cities/{city}/with-country', [CityController::class, 'withCountry']);
        Route::get('cities/with-counts', [CityController::class, 'withCounts']);
        Route::get('cities/within-radius', [CityController::class, 'withinRadius']);

        // API: {{baseURL}}/api/system/locations - Read-only access
        Route::apiResource('locations', LocationController::class)->only(['index', 'show']);
        Route::get('city/locations/{city}', [LocationController::class, 'getByCity']);
        Route::get('country/locations/{country}', [LocationController::class, 'getByCountry']);
        Route::get('within-radius/locations', [LocationController::class, 'withinRadius']);
        Route::get('search/locations', [LocationController::class, 'searchByAddress']);
        Route::get('with-opportunity/locations', [LocationController::class, 'withOpportunity']);

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
        | Skill
        |--------------------------------------------------------------------------
        */
        // Skills - Read-only access
        // API: {{baseURL}}/api/system/skills
        // API: {{baseURL}}/api/system/skills/statistics
        // API: {{baseURL}}/api/system/skills/with-relations
        // API: {{baseURL}}/api/system/skills/with-profiles-count
        // API: {{baseURL}}/api/system/skills/with-opportunities-count
        // API: {{baseURL}}/api/system/skills/popular
        // API: {{baseURL}}/api/system/skills/by-ids
        // API: {{baseURL}}/api/system/skills/not-in-profile/{profileId}
        // API: {{baseURL}}/api/system/skills/not-in-opportunity/{opportunityId}
        // API: {{baseURL}}/api/system/skills/recent
        Route::apiResource('skills', SkillController::class)->only(['index', 'show']);
        Route::get('skills/statistics', [SkillController::class, 'getStatistics']);
        Route::get('skills/with-relations', [SkillController::class, 'indexWithRelations']);
        Route::get('skills/{id}/with-relations', [SkillController::class, 'showWithRelations']);
        Route::get('skills/with-profiles-count', [SkillController::class, 'getWithProfilesCount']);
        Route::get('skills/with-opportunities-count', [SkillController::class, 'getWithOpportunitiesCount']);
        Route::get('skills/popular', [SkillController::class, 'getPopular']);
        Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
        Route::get('skills/not-in-profile/{profileId}', [SkillController::class, 'getNotInProfile']);
        Route::get('skills/not-in-opportunity/{opportunityId}', [SkillController::class, 'getNotInOpportunity']);
        Route::get('skills/recent', [SkillController::class, 'getRecent']);
    });

    // Organization Admin routes - organization management
    Route::prefix('organization')->middleware('role:organization_admin')->group(function () {
        // Organization Profile
        // API: {{baseURL}}/api/organization/organizationProfiles   
        Route::apiResource('organizationProfiles', OrganizationProfileController::class);

        // Opportunity 
        // API: {{baseURL}}/api/organization/get_opportunities(Related to specific Org)
        // API: {{baseURL}}/api/organization/add_opportunity 
        // API: {{baseURL}}/api/organization/opportunities
        Route::get('get_opportunities', [OrganizationProfileController::class, 'getOrganizationOpportunities']);
        Route::post('add_opportunity', [OpportunityController::class, 'storeOpportunity']);
        Route::apiResource('opportunities', OpportunityController::class)->except(['store']);

        /*
        |--------------------------------------------------------------------------
        | Skill
        |--------------------------------------------------------------------------
        */
        // Skills - Read-only access
        // API: {{baseURL}}/api/organization/skills
        // API: {{baseURL}}/api/organization/skills/statistics
        // API: {{baseURL}}/api/organization/skills/with-relations
        // API: {{baseURL}}/api/organization/skills/with-profiles-count
        // API: {{baseURL}}/api/organization/skills/with-opportunities-count
        // API: {{baseURL}}/api/organization/skills/popular
        // API: {{baseURL}}/api/organization/skills/by-ids
        // API: {{baseURL}}/api/organization/skills/not-in-profile/{profileId}
        // API: {{baseURL}}/api/organization/skills/not-in-opportunity/{opportunityId}
        // API: {{baseURL}}/api/organization/skills/recent
        Route::apiResource('skills', SkillController::class)->only(['index', 'show']);
        Route::get('skills/statistics', [SkillController::class, 'getStatistics']);
        Route::get('skills/with-relations', [SkillController::class, 'indexWithRelations']);
        Route::get('skills/{id}/with-relations', [SkillController::class, 'showWithRelations']);
        Route::get('skills/with-profiles-count', [SkillController::class, 'getWithProfilesCount']);
        Route::get('skills/with-opportunities-count', [SkillController::class, 'getWithOpportunitiesCount']);
        Route::get('skills/popular', [SkillController::class, 'getPopular']);
        Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
        Route::get('skills/not-in-profile/{profileId}', [SkillController::class, 'getNotInProfile']);
        Route::get('skills/not-in-opportunity/{opportunityId}', [SkillController::class, 'getNotInOpportunity']);
        Route::get('skills/recent', [SkillController::class, 'getRecent']);

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

        // Location Management - Read-only access for organization admin
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
    });


































    // Opportunity Manager routes - opportunity management
    Route::prefix('opportunity')->middleware('role:opportunity_manager')->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */
        // API: {{baseURL}}/api/opportunity/profile
        // API: {{baseURL}}/api/opportunity/profile/update
        Route::get('profile', [ProfileController::class, 'getMyProfile']);
        Route::put('profile/update', [ProfileController::class, 'updateMyProfile']);
        // Profile viewing (read-only access to other profiles)
        // API: {{baseURL}}/api/opportunity/profiles
        Route::apiResource('profiles', ProfileController::class)->only(['index', 'show']);
        // Profile filtering and search
        // API: {{baseURL}}/api/opportunity/profiles/by-gender
        // API: {{baseURL}}/api/opportunity/profiles/by-age-range
        // API: {{baseURL}}/api/opportunity/profiles/search
        // API: {{baseURL}}/api/opportunity/profiles/by-skills
        Route::get('profiles/by-gender', [ProfileController::class, 'getByGender']);
        Route::get('profiles/by-age-range', [ProfileController::class, 'getByAgeRange']);
        Route::get('profiles/search', [ProfileController::class, 'search']);
        Route::get('profiles/by-skills', [ProfileController::class, 'getBySkills']);
        // Profile relationships
        // API: {{baseURL}}/api/opportunity/profiles/{id}/with-relations
        // API: {{baseURL}}/api/opportunity/profiles/with-relations
        Route::get('profiles/{id}/with-relations', [ProfileController::class, 'showWithRelations']);
        Route::get('profiles/with-relations', [ProfileController::class, 'indexWithRelations']);
        /*
        |--------------------------------------------------------------------------
        | Skill
        |--------------------------------------------------------------------------
        */
        // Skills - Limited access
        // API: {{baseURL}}/api/opportunity/skills
        // API: {{baseURL}}/api/opportunity/skills/with-relations
        // API: {{baseURL}}/api/opportunity/skills/with-profiles-count
        // API: {{baseURL}}/api/opportunity/skills/with-opportunities-count
        // API: {{baseURL}}/api/opportunity/skills/popular
        // API: {{baseURL}}/api/opportunity/skills/by-ids
        // API: {{baseURL}}/api/opportunity/skills/not-in-profile/{profileId}
        // API: {{baseURL}}/api/opportunity/skills/not-in-opportunity/{opportunityId}
        // API: {{baseURL}}/api/opportunity/skills/recent
        Route::apiResource('skills', SkillController::class)->only(['index', 'show']);
        Route::get('skills/with-relations', [SkillController::class, 'indexWithRelations']);
        Route::get('skills/{id}/with-relations', [SkillController::class, 'showWithRelations']);
        Route::get('skills/with-profiles-count', [SkillController::class, 'getWithProfilesCount']);
        Route::get('skills/with-opportunities-count', [SkillController::class, 'getWithOpportunitiesCount']);
        Route::get('skills/popular', [SkillController::class, 'getPopular']);
        Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
        Route::get('skills/not-in-profile/{profileId}', [SkillController::class, 'getNotInProfile']);
        Route::get('skills/not-in-opportunity/{opportunityId}', [SkillController::class, 'getNotInOpportunity']);
        Route::get('skills/recent', [SkillController::class, 'getRecent']);
    });

    // Volunteer Coordinator routes - volunteer management
    Route::prefix('coordinator')->middleware('role:volunteer_coordinator')->group(function () {
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
    });

    // Performance Evaluator routes - evaluation management
    Route::prefix('evaluator')->middleware('role:performance_evaluator')->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */
        // API: {{baseURL}}/api/evaluator/profile
        // API: {{baseURL}}/api/evaluator/profile/update
        Route::get('profile', [ProfileController::class, 'getMyProfile']);
        Route::put('profile/update', [ProfileController::class, 'updateMyProfile']);
        // Profile viewing (read-only access to other profiles)
        // API: {{baseURL}}/api/evaluator/profiles
        Route::apiResource('profiles', ProfileController::class)->only(['index', 'show']);
        // Profile filtering and search
        // API: {{baseURL}}/api/evaluator/profiles/by-gender
        // API: {{baseURL}}/api/evaluator/profiles/by-age-range
        // API: {{baseURL}}/api/evaluator/profiles/search
        // API: {{baseURL}}/api/evaluator/profiles/by-skills
        Route::get('profiles/by-gender', [ProfileController::class, 'getByGender']);
        Route::get('profiles/by-age-range', [ProfileController::class, 'getByAgeRange']);
        Route::get('profiles/search', [ProfileController::class, 'search']);
        Route::get('profiles/by-skills', [ProfileController::class, 'getBySkills']);
        // Profile relationships
        // API: {{baseURL}}/api/evaluator/profiles/{id}/with-relations
        // API: {{baseURL}}/api/evaluator/profiles/with-relations
        Route::get('profiles/{id}/with-relations', [ProfileController::class, 'showWithRelations']);
        Route::get('profiles/with-relations', [ProfileController::class, 'indexWithRelations']);
        /*
        |--------------------------------------------------------------------------
        | Skill
        |--------------------------------------------------------------------------
        */
        // Skills - Limited access
        // API: {{baseURL}}/api/evaluator/skills
        // API: {{baseURL}}/api/evaluator/skills/with-relations
        // API: {{baseURL}}/api/evaluator/skills/with-profiles-count
        // API: {{baseURL}}/api/evaluator/skills/with-opportunities-count
        // API: {{baseURL}}/api/evaluator/skills/popular
        // API: {{baseURL}}/api/evaluator/skills/by-ids
        // API: {{baseURL}}/api/evaluator/skills/not-in-profile/{profileId}
        // API: {{baseURL}}/api/evaluator/skills/not-in-opportunity/{opportunityId}
        // API: {{baseURL}}/api/evaluator/skills/recent
        Route::apiResource('skills', SkillController::class)->only(['index', 'show']);
        Route::get('skills/with-relations', [SkillController::class, 'indexWithRelations']);
        Route::get('skills/{id}/with-relations', [SkillController::class, 'showWithRelations']);
        Route::get('skills/with-profiles-count', [SkillController::class, 'getWithProfilesCount']);
        Route::get('skills/with-opportunities-count', [SkillController::class, 'getWithOpportunitiesCount']);
        Route::get('skills/popular', [SkillController::class, 'getPopular']);
        Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
        Route::get('skills/recent', [SkillController::class, 'getRecent']);
    });

    // Volunteer routes - basic volunteer access
    Route::prefix('volunteer')->middleware('role:volunteer')->group(function () {
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
        | Skill
        |--------------------------------------------------------------------------
        */
        // Skills - Limited access
        // API: {{baseURL}}/api/volunteer/skills
        // API: {{baseURL}}/api/volunteer/skills/with-relations
        // API: {{baseURL}}/api/volunteer/skills/with-profiles-count
        // API: {{baseURL}}/api/volunteer/skills/with-opportunities-count
        // API: {{baseURL}}/api/volunteer/skills/popular
        // API: {{baseURL}}/api/volunteer/skills/by-ids
        // API: {{baseURL}}/api/volunteer/skills/not-in-profile/{profileId}
        // API: {{baseURL}}/api/volunteer/skills/not-in-opportunity/{opportunityId}
        // API: {{baseURL}}/api/volunteer/skills/recent
        Route::apiResource('skills', SkillController::class)->only(['index', 'show']);
        Route::get('skills/with-relations', [SkillController::class, 'indexWithRelations']);
        Route::get('skills/{id}/with-relations', [SkillController::class, 'showWithRelations']);
        Route::get('skills/with-profiles-count', [SkillController::class, 'getWithProfilesCount']);
        Route::get('skills/with-opportunities-count', [SkillController::class, 'getWithOpportunitiesCount']);
        Route::get('skills/popular', [SkillController::class, 'getPopular']);
        Route::get('skills/by-ids', [SkillController::class, 'getByIds']);
        Route::get('skills/recent', [SkillController::class, 'getRecent']);
    });
});


require __DIR__ . '/auth.php';
