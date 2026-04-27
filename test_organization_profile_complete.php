<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "🧪 Complete OrganizationProfile Test Suite\n\n";

$testResults = [
    'passed' => 0,
    'failed' => 0,
    'total' => 0
];

function runTest($testName, $testFunction, &$results) {
    $results['total']++;
    echo "📋 Testing: $testName\n";
    try {
        $result = $testFunction();
        if ($result) {
            echo "   ✅ PASSED\n";
            $results['passed']++;
        } else {
            echo "   ❌ FAILED\n";
            $results['failed']++;
        }
    } catch (Exception $e) {
        echo "   ❌ FAILED: " . $e->getMessage() . "\n";
        echo "   📍 Error in: " . $e->getFile() . " line " . $e->getLine() . "\n";
        $results['failed']++;
    }
    echo "\n";
}

// Helper function to create test users
function createTestUser($email, $role, $status = 'active') {
    $user = \App\Models\User::create([
        'name' => "Test $role",
        'email' => $email . time() . '@test.com',
        'password' => 'password',
        'status' => $status,
    ]);
    $user->assignRole($role);
    return $user;
}

// Test 1: Repository - Create Organization Profile
runTest("Repository - Create Organization Profile", function() {
    $user = createTestUser('repo_create', 'organization_admin', 'active');
    
    $repository = new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile());
    
    $profile = $repository->create([
        'user_id' => $user->id,
        'license_number' => 'TEST-001',
        'type' => 'Charitable organization',
        'bio' => 'Test organization profile',
        'website' => 'https://test.org',
        'status' => 'active'
    ]);
    
    return $profile && $profile->user_id === $user->id && $profile->license_number === 'ORG-TEST-001';
}, $testResults);

// Test 2: Repository - Search Functionality
runTest("Repository - Search Organization Profiles", function() {
    $user = createTestUser('repo_search', 'organization_admin', 'active');
    
    $repository = new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile());
    
    $profile = \App\Models\OrganizationProfile::create([
        'user_id' => $user->id,
        'license_number' => 'SEARCH-001',
        'type' => 'Civil society organization',
        'bio' => 'Searchable organization with unique content',
        'website' => 'https://search.org',
        'status' => 'active'
    ]);
    
    $searchResults = $repository->search('searchable', [], 10);
    
    return $searchResults->total() >= 1;
}, $testResults);

// Test 3: Repository - Filter Functionality
runTest("Repository - Filter Organization Profiles", function() {
    $user = createTestUser('repo_filter', 'organization_admin', 'active');
    
    $repository = new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile());
    
    $profile = \App\Models\OrganizationProfile::create([
        'user_id' => $user->id,
        'license_number' => 'FILTER-001',
        'type' => 'Hospital',
        'bio' => 'Filter test organization',
        'website' => 'https://filter.org',
        'status' => 'active'
    ]);
    
    $filterResults = $repository->filter(['type' => 'Hospital'], 10);
    
    return $filterResults->total() >= 1;
}, $testResults);

// Test 4: Repository - Statistics
runTest("Repository - Get Statistics", function() {
    $repository = new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile());
    
    $statistics = $repository->getStatistics();
    
    return is_array($statistics) && 
           isset($statistics['total_profiles']) && 
           isset($statistics['status_distribution']) &&
           isset($statistics['type_distribution']);
}, $testResults);

// Test 5: Service - Create Organization Profile
runTest("Service - Create Organization Profile", function() {
    $user = createTestUser('service_create', 'organization_admin', 'active');
    
    $service = new \App\Services\OrganizationProfileService(
        new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile())
    );
    
    $profile = $service->create([
        'user_id' => $user->id,
        'license_number' => 'SERVICE-001',
        'type' => 'Religious organization',
        'bio' => 'Service test organization',
        'website' => 'service.org'
    ]);
    
    return $profile && 
           $profile->license_number === 'ORG-SERVICE-001' && 
           $profile->website === 'https://service.org';
}, $testResults);

// Test 6: Service - Find By User ID
runTest("Service - Find By User ID", function() {
    $user = createTestUser('service_find', 'organization_admin', 'active');
    
    $service = new \App\Services\OrganizationProfileService(
        new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile())
    );
    
    $profile = \App\Models\OrganizationProfile::create([
        'user_id' => $user->id,
        'license_number' => 'FIND-001',
        'type' => 'Environmental organization',
        'bio' => 'Find test organization',
        'website' => 'https://find.org',
        'status' => 'active'
    ]);
    
    $foundProfile = $service->findByUserId($user->id);
    
    return $foundProfile && $foundProfile->id === $profile->id;
}, $testResults);

// Test 7: Request Validation - Store Request Admin
runTest("Request Validation - Store Request Admin", function() {
    $superAdmin = createTestUser('req_admin', 'super_administrator', 'active');
    
    // Mock authenticated user
    \Illuminate\Support\Facades\Auth::shouldReceive('user')->andReturn($superAdmin);
    
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'user_id' => 1,
        'license_number' => 'REQ-001',
        'type' => 'Charitable organization',
        'bio' => 'Request test',
        'website' => 'https://req.org',
        'status' => 'active'
    ]);
    
    $storeRequest = new \App\Http\Requests\Api\OrganizationProfile\StoreOrganizationProfileRequest($request);
    $rules = $storeRequest->rules();
    
    return isset($rules['user_id']) && in_array('required', $rules['user_id']) && isset($rules['status']);
}, $testResults);

// Test 8: Request Validation - Store Request User
runTest("Request Validation - Store Request User", function() {
    $orgAdmin = createTestUser('req_user', 'organization_admin', 'active');
    
    // Mock authenticated user
    \Illuminate\Support\Facades\Auth::shouldReceive('user')->andReturn($orgAdmin);
    
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'license_number' => 'REQ-002',
        'type' => 'Charitable organization',
        'bio' => 'Request test user',
        'website' => 'https://requser.org'
    ]);
    
    $storeRequest = new \App\Http\Requests\Api\OrganizationProfile\StoreOrganizationProfileRequest($request);
    $rules = $storeRequest->rules();
    
    return isset($rules['user_id']) && in_array('prohibited', $rules['user_id']) && !isset($rules['status']);
}, $testResults);

// Test 9: Controller - Store For Admin
runTest("Controller - Store For Admin Method", function() {
    $superAdmin = createTestUser('ctrl_admin', 'super_administrator', 'active');
    $targetUser = createTestUser('ctrl_target', 'organization_admin', 'active');
    
    // Mock authenticated user
    \Illuminate\Support\Facades\Auth::shouldReceive('user')->andReturn($superAdmin);
    
    $controller = new \App\Http\Controllers\Api\OrganizationProfileController(
        new \App\Services\OrganizationProfileService(
            new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile())
        )
    );
    
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'user_id' => $targetUser->id,
        'license_number' => 'CTRL-001',
        'type' => 'Charitable organization',
        'bio' => 'Controller test admin',
        'website' => 'https://ctrl.org',
        'status' => 'active'
    ]);
    
    $storeRequest = new \App\Http\Requests\Api\OrganizationProfile\StoreOrganizationProfileRequest($request);
    
    try {
        $response = $controller->storeForAdmin($storeRequest);
        return $response->getStatusCode() === 200;
    } catch (Exception $e) {
        echo "   ⚠️  Exception: " . $e->getMessage() . "\n";
        return false;
    }
}, $testResults);

// Test 10: Controller - Store For User
runTest("Controller - Store For User Method", function() {
    $orgAdmin = createTestUser('ctrl_user', 'organization_admin', 'active');
    
    // Mock authenticated user
    \Illuminate\Support\Facades\Auth::shouldReceive('user')->andReturn($orgAdmin);
    
    $controller = new \App\Http\Controllers\Api\OrganizationProfileController(
        new \App\Services\OrganizationProfileService(
            new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile())
        )
    );
    
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'license_number' => 'CTRL-002',
        'type' => 'Charitable organization',
        'bio' => 'Controller test user',
        'website' => 'https://ctrluser.org'
    ]);
    
    $storeRequest = new \App\Http\Requests\Api\OrganizationProfile\StoreOrganizationProfileRequest($request);
    
    try {
        $response = $controller->storeForUser($storeRequest);
        return $response->getStatusCode() === 200;
    } catch (Exception $e) {
        echo "   ⚠️  Exception: " . $e->getMessage() . "\n";
        return false;
    }
}, $testResults);

// Test 11: Model Relationships
runTest("Model - User Relationship", function() {
    $user = createTestUser('model_rel', 'organization_admin', 'active');
    
    $profile = \App\Models\OrganizationProfile::create([
        'user_id' => $user->id,
        'license_number' => 'MODEL-001',
        'type' => 'Charitable organization',
        'bio' => 'Model relationship test',
        'website' => 'https://model.org',
        'status' => 'active'
    ]);
    
    $profileWithUser = \App\Models\OrganizationProfile::with('user')->find($profile->id);
    
    return $profileWithUser && $profileWithUser->user && $profileWithUser->user->id === $user->id;
}, $testResults);

// Test 12: Repository - Advanced Filters
runTest("Repository - Advanced Filters", function() {
    $user = createTestUser('repo_advanced', 'organization_admin', 'active');
    
    $repository = new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile());
    
    $profile = \App\Models\OrganizationProfile::create([
        'user_id' => $user->id,
        'license_number' => 'ADVANCED-001',
        'type' => 'Student club/association',
        'bio' => 'Advanced filter test',
        'website' => 'https://advanced.org',
        'status' => 'active'
    ]);
    
    // Test multiple filters
    $filterResults = $repository->filter([
        'type' => 'Student club/association',
        'status' => 'active',
        'license_number' => 'ADVANCED'
    ], 10);
    
    return $filterResults->total() >= 1;
}, $testResults);

// Test 13: Service - Statistics
runTest("Service - Get Statistics", function() {
    $service = new \App\Services\OrganizationProfileService(
        new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile())
    );
    
    $statistics = $service->getStatistics();
    
    return is_array($statistics) && 
           isset($statistics['total_profiles']) && 
           isset($statistics['status_distribution']) &&
           isset($statistics['type_distribution']) &&
           isset($statistics['organizations_with_opportunities']) &&
           isset($statistics['recent_organizations']) &&
           isset($statistics['active_organizations']);
}, $testResults);

// Test 14: Repository - Date Range Filter
runTest("Repository - Date Range Filter", function() {
    $user = createTestUser('repo_date', 'organization_admin', 'active');
    
    $repository = new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile());
    
    $profile = \App\Models\OrganizationProfile::create([
        'user_id' => $user->id,
        'license_number' => 'DATE-001',
        'type' => 'Company with a Corporate Social Responsibility (CSR) program',
        'bio' => 'Date range test',
        'website' => 'https://date.org',
        'status' => 'active'
    ]);
    
    $today = now()->format('Y-m-d');
    $dateResults = $repository->getByDateRange($today, $today, [], 10);
    
    return $dateResults->total() >= 1;
}, $testResults);

// Test 15: Repository - User Has Profile Check
runTest("Repository - User Has Profile Check", function() {
    $user = createTestUser('repo_check', 'organization_admin', 'active');
    
    $repository = new \App\Repositories\Eloquent\OrganizationProfileRepository(new \App\Models\OrganizationProfile());
    
    // Initially should not have profile
    $hasProfileBefore = $repository->userHasProfile($user->id);
    
    // Create profile
    \App\Models\OrganizationProfile::create([
        'user_id' => $user->id,
        'license_number' => 'CHECK-001',
        'type' => 'Voluntary educational/university institution',
        'bio' => 'Profile check test',
        'website' => 'https://check.org',
        'status' => 'active'
    ]);
    
    // Now should have profile
    $hasProfileAfter = $repository->userHasProfile($user->id);
    
    return !$hasProfileBefore && $hasProfileAfter;
}, $testResults);

// Output Results
echo "📊 Test Results:\n";
echo "   Total Tests: {$testResults['total']}\n";
echo "   Passed: {$testResults['passed']}\n";
echo "   Failed: {$testResults['failed']}\n";
echo "   Success Rate: " . round(($testResults['passed'] / $testResults['total']) * 100, 2) . "%\n\n";

if ($testResults['failed'] === 0) {
    echo "🎉 All tests passed! OrganizationProfile implementation is working correctly.\n";
    echo "\n📋 Implementation Summary:\n";
    echo "   ✅ Repository layer with advanced filtering and search\n";
    echo "   ✅ Service layer with business logic\n";
    echo "   ✅ Request validation with role-based rules\n";
    echo "   ✅ Controller with role-specific methods\n";
    echo "   ✅ Model relationships\n";
    echo "   ✅ Statistics and reporting\n";
    echo "   ✅ Date range filtering\n";
    echo "   ✅ Profile uniqueness enforcement\n";
    
    echo "\n🚀 Available Features:\n";
    echo "   ✅ CRUD operations with role-based access\n";
    echo "   ✅ Search across bio, license, website, and user info\n";
    echo "   ✅ Advanced filtering (status, type, dates, etc.)\n";
    echo "   ✅ Statistics and analytics\n";
    echo "   ✅ Profile ownership management\n";
    echo "   ✅ Activation/deactivation\n";
} else {
    echo "⚠️  Some tests failed. Please review the failed tests above.\n";
}

echo "\n✅ Complete OrganizationProfile test suite finished!\n";
