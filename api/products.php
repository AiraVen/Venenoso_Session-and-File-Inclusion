<?php
require_once 'db.php';

// Set CORS headers for API
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

try {
    $pdo = get_pdo();
    
    // Get query parameters
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    // Base query
    $query = "SELECT * FROM products";
    $params = [];
    
    // Add search if provided
    if ($search) {
        $query .= " WHERE product_name LIKE ? OR description LIKE ?";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }
    
    // Add sorting and pagination
    $query .= " ORDER BY date_added DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    // Execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    // Format prices and ensure proper image paths
    foreach ($products as &$product) {
        $product['price'] = floatval($product['price']);
        // Ensure image path starts from root
        if ($product['image_path'] && !str_starts_with($product['image_path'], '/')) {
            $product['image_path'] = '/' . $product['image_path'];
        }
    }
    
    json_response([
        'success' => true,
        'products' => $products
    ]);

} catch (Exception $e) {
    json_response([
        'success' => false,
        'error' => 'Failed to fetch products',
        'debug' => $e->getMessage()
    ], 500);
}