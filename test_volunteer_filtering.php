<?php

/**
 * Comprehensive Volunteer Filtering Test Script
 * Tests all filtering scenarios for volunteer index and show endpoints
 */

echo "=== VOLUNTEER FILTERING TESTS ===\n\n";

$baseUrl = 'http://127.0.0.1:8000/api/admin/volunteers';

// Test configurations
$tests = [
    // Basic tests
    '1. Get all volunteers (no filters)' => [
        'url' => $baseUrl,
        'description' => 'Should return all volunteers'
    ],
    
    // Experience filtering
    '2. Filter by experience_years=1' => [
        'url' => $baseUrl . '?experience_years=1',
        'description' => 'Should return volunteers with 1 year experience'
    ],
    
    '3. Filter by experience_years=3' => [
        'url' => $baseUrl . '?experience_years=3',
        'description' => 'Should return volunteers with 3 years experience'
    ],
    
    // Languages filtering - String format
    '4. Filter by single language (string)' => [
        'url' => $baseUrl . '?languages=English',
        'description' => 'Should return volunteers who speak English'
    ],
    
    '5. Filter by single language (arabic)' => [
        'url' => $baseUrl . '?languages=Arabic',
        'description' => 'Should return volunteers who speak Arabic'
    ],
    
    // Languages filtering - Array format
    '6. Filter by multiple languages (array)' => [
        'url' => $baseUrl . '?languages[]=English&languages[]=Arabic',
        'description' => 'Should return volunteers who speak English OR Arabic'
    ],
    
    '7. Filter by three languages' => [
        'url' => $baseUrl . '?languages[]=English&languages[]=Spanish&languages[]=French',
        'description' => 'Should return volunteers who speak English OR Spanish OR French'
    ],
    
    // Availability filtering - String format
    '8. Filter by single availability (string)' => [
        'url' => $baseUrl . '?availability=weekends',
        'description' => 'Should return volunteers available on weekends'
    ],
    
    '9. Filter by single availability (evenings)' => [
        'url' => $baseUrl . '?availability=evenings',
        'description' => 'Should return volunteers available in evenings'
    ],
    
    // Availability filtering - Array format
    '10. Filter by multiple availability (array)' => [
        'url' => $baseUrl . '?availability[]=weekends&availability[]=evenings',
        'description' => 'Should return volunteers available on weekends OR evenings'
    ],
    
    '11. Filter by three availability times' => [
        'url' => $baseUrl . '?availability[]=weekends&availability[]=evenings&availability[]=mornings',
        'description' => 'Should return volunteers available on weekends OR evenings OR mornings'
    ],
    
    // Combined filtering
    '12. Combined: experience + single language' => [
        'url' => $baseUrl . '?experience_years=3&languages=English',
        'description' => 'Should return volunteers with 3 years experience who speak English'
    ],
    
    '13. Combined: experience + multiple languages' => [
        'url' => $baseUrl . '?experience_years=2&languages[]=English&languages[]=Arabic',
        'description' => 'Should return volunteers with 2 years experience who speak English OR Arabic'
    ],
    
    '14. Combined: experience + language + availability' => [
        'url' => $baseUrl . '?experience_years=3&languages=English&availability=weekends',
        'description' => 'Should return volunteers with 3 years experience, speak English, available weekends'
    ],
    
    '15. Combined: multiple languages + multiple availability' => [
        'url' => $baseUrl . '?languages[]=English&languages[]=Arabic&availability[]=weekends&availability[]=evenings',
        'description' => 'Should return volunteers who speak English OR Arabic AND available weekends OR evenings'
    ],
    
    // Status filtering
    '16. Filter by status=active' => [
        'url' => $baseUrl . '?status=active',
        'description' => 'Should return only active volunteers'
    ],
    
    '17. Filter by status=pending' => [
        'url' => $baseUrl . '?status=pending',
        'description' => 'Should return only pending volunteers'
    ],
    
    // Search functionality
    '18. Search functionality' => [
        'url' => $baseUrl . '?search=volunteer',
        'description' => 'Should return volunteers matching search term'
    ],
    
    // Combined with search
    '19. Search + language filter' => [
        'url' => $baseUrl . '?search=teacher&languages=English',
        'description' => 'Should return volunteers matching search term who speak English'
    ],
    
    // Pagination
    '20. Pagination test' => [
        'url' => $baseUrl . '?page=1&per_page=5',
        'description' => 'Should return first 5 volunteers with pagination'
    ],
    
    // Complex combined
    '21. Complex combined filter' => [
        'url' => $baseUrl . '?experience_years=3&languages[]=English&languages[]=Arabic&availability[]=weekends&status=active&page=1&per_page=10',
        'description' => 'Should return active volunteers with 3 years experience, speaking English/Arabic, available weekends'
    ]
];

// Run tests
foreach ($tests as $testName => $test) {
    echo "=== $testName ===\n";
    echo "URL: {$test['url']}\n";
    echo "Description: {$test['description']}\n";
    
    // Execute curl request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer YOUR_TOKEN_HERE' // Replace with actual token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ ERROR: $error\n";
    } else {
        echo "✅ HTTP Status: $httpCode\n";
        
        // Parse JSON response
        $data = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($data['success']) && $data['success']) {
                $total = $data['meta']['total'] ?? 0;
                $currentPage = $data['meta']['current_page'] ?? 1;
                $perPage = $data['meta']['per_page'] ?? 15;
                
                echo "✅ SUCCESS: Found $total volunteer(s)\n";
                echo "📄 Page: $currentPage of {$data['meta']['last_page'] ?? 1} (Per page: $perPage)\n";
                
                // Show first few results
                if (isset($data['data']) && count($data['data']) > 0) {
                    echo "📋 Sample results:\n";
                    $count = 0;
                    foreach ($data['data'] as $volunteer) {
                        if ($count >= 3) break;
                        echo "  • ID: {$volunteer['id']}, Languages: " . json_encode($volunteer['languages']) . ", Experience: {$volunteer['experience_years']}\n";
                        $count++;
                    }
                    if (count($data['data']) > 3) {
                        echo "  ... and " . (count($data['data']) - 3) . " more\n";
                    }
                } else {
                    echo "📋 No results found\n";
                }
            } else {
                echo "❌ API Error: " . ($data['message'] ?? 'Unknown error') . "\n";
            }
        } else {
            echo "❌ JSON Parse Error: " . json_last_error_msg() . "\n";
            echo "Raw Response: " . substr($response, 0, 200) . "...\n";
        }
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}

echo "=== SHOW ENDPOINT TESTS ===\n\n";

// Show endpoint tests
$showTests = [
    '1. Basic show endpoint' => [
        'url' => $baseUrl . '/1',
        'description' => 'Should show volunteer details'
    ],
    
    '2. Show with relations (default)' => [
        'url' => $baseUrl . '/1/with-relations',
        'description' => 'Should show volunteer with default relations'
    ],
    
    '3. Show with specific relations' => [
        'url' => $baseUrl . '/1/with-relations?relations[]=profile&relations[]=user&relations[]=applications',
        'description' => 'Should show volunteer with specific relations'
    ],
    
    '4. Show with all relations' => [
        'url' => $baseUrl . '/1/with-relations?relations[]=profile&relations[]=user&relations[]=evaluation&relations[]=applications&relations[]=tasks&relations[]=certificates&relations[]=badges',
        'description' => 'Should show volunteer with all relations'
    ]
];

foreach ($showTests as $testName => $test) {
    echo "=== $testName ===\n";
    echo "URL: {$test['url']}\n";
    echo "Description: {$test['description']}\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer YOUR_TOKEN_HERE' // Replace with actual token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ ERROR: $error\n";
    } else {
        echo "✅ HTTP Status: $httpCode\n";
        
        $data = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($data['success']) && $data['success']) {
                echo "✅ SUCCESS: Volunteer retrieved\n";
                
                if (isset($data['data'])) {
                    $volunteer = $data['data'];
                    echo "📋 Volunteer Info:\n";
                    echo "  • ID: {$volunteer['id']}\n";
                    echo "  • Languages: " . json_encode($volunteer['languages']) . "\n";
                    echo "  • Availability: " . json_encode($volunteer['availability']) . "\n";
                    echo "  • Experience: {$volunteer['experience_years']}\n";
                    echo "  • Status: {$volunteer['status']}\n";
                    
                    // Show loaded relations
                    if (isset($volunteer['profile'])) {
                        echo "  • Profile: Loaded\n";
                    }
                    if (isset($volunteer['user'])) {
                        echo "  • User: Loaded\n";
                    }
                    if (isset($volunteer['applications'])) {
                        echo "  • Applications: " . count($volunteer['applications']) . " items\n";
                    }
                }
            } else {
                echo "❌ API Error: " . ($data['message'] ?? 'Unknown error') . "\n";
            }
        } else {
            echo "❌ JSON Parse Error: " . json_last_error_msg() . "\n";
            echo "Raw Response: " . substr($response, 0, 200) . "...\n";
        }
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}

echo "=== TEST COMPLETE ===\n";
echo "Replace 'YOUR_TOKEN_HERE' with actual authentication token to run tests.\n";
