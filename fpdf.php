<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Fake order data for demo purposes
// In real use, you’d get this from session, DB, or GET params
$order_id = $_GET['order_id'] ?? '123456';
$product_name = $_GET['product_name'] ?? 'Sample Product';
$price = $_GET['price'] ?? '29.99';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view this page.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt - PDF Preview</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .receipt {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .details p {
            font-size: 16px;
            margin: 10px 0;
        }

        button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="receipt" id="receipt">
    <h2>Order Receipt</h2>
    <div class="details">
        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?></p>
        <p><strong>Product Name:</strong> <?php echo htmlspecialchars($product_name); ?></p>
        <p><strong>Price:</strong> $<?php echo htmlspecialchars($price); ?></p>
        <p><strong>Customer ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
    </div>
</div>

<button onclick="window.print()">Download / Print as PDF</button>

</body>
</html>
