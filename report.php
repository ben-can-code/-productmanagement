<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config.php';

$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="orders_report.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Order ID', 'Product Name', 'Price', 'Order Date']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['id'], $row['product_name'], $row['price'], $row['order_date']]);
}

fclose($output);
exit();
?>
