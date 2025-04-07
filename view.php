<?php
if (!isset($_GET['id'])) {
    echo "No product ID specified.";
    exit;
}
$productId = intval($_GET['id']);
$productUrl = "https://fakestoreapi.com/products/$productId";

$productData = file_get_contents($productUrl);
if ($productData === FALSE) {
    echo "Error fetching product details.";
    exit;
}

$product = json_decode($productData, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['title']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f0f2f5; }
        .container { max-width: 800px; margin: 50px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        img { max-width: 200px; float: left; margin-right: 20px; }
        h1 { margin-top: 0; }
        .info { overflow: auto; }
        .price { font-size: 20px; font-weight: bold; color: #2c3e50; margin-top: 10px; }
        .category { color: #888; margin-top: 5px; }
        .rating { margin-top: 10px; color: #e67e22; }
        .rating span { font-weight: bold; }
        a.button { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #2c3e50; color: white; text-decoration: none; border-radius: 4px; }
        a.button:hover { background: #34495e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="info">
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
            <h1><?= htmlspecialchars($product['title']) ?></h1>
            <div class="category">Category: <?= htmlspecialchars($product['category']) ?></div>
            <div class="price">$<?= number_format($product['price'], 2) ?></div>
            <div class="rating">
                Rating: <span><?= number_format($product['rating']['rate'], 1) ?></span>/5 
                (<?= $product['rating']['count'] ?> reviews)
            </div>
            <p><?= htmlspecialchars($product['description']) ?></p>
        </div>
        <a href="dashboard.php" class="button">Back to Dashboard</a>
    </div>
</body>
</html>