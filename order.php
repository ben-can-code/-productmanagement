<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

// Get and validate posted JSON data
$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['product_name']) || !isset($data['price'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_name = trim($data['product_name']);
$price = floatval($data['price']);
$order_date = date('Y-m-d H:i:s');

// Insert order using prepared statements
$stmt = $conn->prepare("INSERT INTO orders (user_id, product_name, price, order_date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isds", $user_id, $product_name, $price, $order_date);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;

    // Fetch updated totals for the user
    $summary_stmt = $conn->prepare("SELECT COUNT(*) AS total_products, SUM(price) AS total_price FROM orders WHERE user_id = ?");
    $summary_stmt->bind_param("i", $user_id);
    $summary_stmt->execute();
    $summary_result = $summary_stmt->get_result()->fetch_assoc();

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'total_products' => $summary_result['total_products'],
        'total_price' => number_format($summary_result['total_price'], 2)
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
