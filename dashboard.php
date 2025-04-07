<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BENZ Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f0f2f5; }
        .header { background: #2c3e50; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
        .grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .filter { margin-bottom: 15px; }
        input, select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 200px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        button { padding: 8px 16px; background: #2c3e50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #34495e; }
        .button-group { margin-top: 15px; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .action-buttons { display: flex; gap: 10px; justify-content: flex-start; align-items: center; }
        button.view-btn { background: #3498db; }
        button.view-btn:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BENZ PRODUCT DASHBOARD</h1>
        <a href="logout.php" style="color: white;">Logout</a>
    </div>

    <div class="container">
        <div class="grid">
            <!-- Products -->
            <div class="card">
                <h2>Products</h2>
                <div class="filter">
                    <input type="text" id="nameFilter" placeholder="Filter by name..." onkeyup="updateProductList()">
                    <select id="categoryFilter" onchange="updateProductList()">
                        <option value="">All Categories</option>
                    </select>
                </div>

                <div id="message"></div>
                <table>
                    <thead>
                        <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Actions</th></tr>
                    </thead>
                    <tbody id="productList"></tbody>
                </table>
            </div>

            <!-- Orders -->
            <div class="card">
                <h2>Your Orders</h2>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Price</th><th>Date</th></tr>
                    </thead>
                    <tbody id="orderList">
                        <?php
                        $user_id = $_SESSION['user_id'];
                        $result = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY order_date DESC");
                        $total_price = 0;
                        $total_products = 0;
                        while ($row = $result->fetch_assoc()) {
                            $total_price += $row['price'];
                            $total_products++;
                            echo "<tr><td>{$row['id']}</td><td>{$row['product_name']}</td><td>\${$row['price']}</td><td>{$row['order_date']}</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <div style="margin-top: 15px;">
                    <strong id="totalProductDisplay">Total Products: <?= $total_products ?></strong><br>
                    <strong id="totalPriceDisplay">Total Price: $<?= number_format($total_price, 2) ?></strong>
                </div>

                <div class="button-group">
                    <button onclick="downloadReport()">Download Report</button>
                    <button onclick="clearOrders()">Clear All Orders</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const state = {
            products: [],
            categories: new Set(),
            filters: { name: '', category: '' }
        };

        async function init() {
            try {
                const response = await fetch('https://fakestoreapi.com/products');
                state.products = await response.json();
                state.products.forEach(product => state.categories.add(product.category));
                const categorySelect = document.getElementById('categoryFilter');
                state.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category;
                    categorySelect.appendChild(option);
                });
                updateProductList();
            } catch (error) {
                showMessage('Error fetching products', 'error');
            }
        }

        function updateProductList() {
            state.filters.name = document.getElementById('nameFilter').value.toLowerCase();
            state.filters.category = document.getElementById('categoryFilter').value;
            const filtered = state.products.filter(p =>
                p.title.toLowerCase().includes(state.filters.name) &&
                (state.filters.category === '' || p.category === state.filters.category)
            );
            const productList = document.getElementById('productList');
            productList.innerHTML = '';
            filtered.forEach(p => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><img src="${p.image}" alt="${p.title}" width="50"></td>
                    <td>${p.title}</td>
                    <td>${p.category}</td>
                    <td>$${p.price}</td>
                    <td class="action-buttons">
                        <button class="view-btn" onclick="viewProduct(${p.id})">View</button>
                        <button onclick="orderProduct(${p.id}, '${p.title.replace(/'/g, "\\'")}', ${p.price})">Order</button>
                    </td>
                `;
                productList.appendChild(tr);
            });
        }

        function viewProduct(productId) {
            // Updated redirect to view.php
            window.location.href = `view.php?id=${productId}`;
        }

        async function orderProduct(id, name, price) {
            try {
                const response = await fetch('order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: id, product_name: name, price })
                });
                const data = await response.json();
                if (data.success) {
                    const tr = document.createElement('tr');
                    const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
                    tr.innerHTML = `<td>${data.order_id}</td><td>${name}</td><td>$${price}</td><td>${now}</td>`;
                    document.getElementById('orderList').prepend(tr);

                    const totalProductEl = document.getElementById('totalProductDisplay');
                    const totalPriceEl = document.getElementById('totalPriceDisplay');

                    const currentTotal = parseFloat(totalPriceEl.textContent.replace(/[^\d.]/g, '')) || 0;
                    const newTotal = (currentTotal + price).toFixed(2);
                    const currentCount = parseInt(totalProductEl.textContent.replace(/\D/g, '')) || 0;

                    totalPriceEl.textContent = `Total Price: $${newTotal}`;
                    totalProductEl.textContent = `Total Products: ${currentCount + 1}`;

                    showMessage('Order placed successfully', 'success');
                } else {
                    showMessage('Order failed', 'error');
                }
            } catch (error) {
                showMessage('Error placing order', 'error');
            }
        }

        async function clearOrders() {
            if (confirm('Clear all orders?')) {
                try {
                    const response = await fetch('clear_orders.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' }
                    });
                    const data = await response.json();
                    if (data.success) {
                        document.getElementById('orderList').innerHTML = '';
                        document.getElementById('totalProductDisplay').textContent = 'Total Products: 0';
                        document.getElementById('totalPriceDisplay').textContent = 'Total Price: $0.00';
                        showMessage('All orders cleared', 'success');
                    } else {
                        showMessage(data.message || 'Error clearing orders', 'error');
                    }
                } catch (error) {
                    showMessage('Error clearing orders', 'error');
                }
            }
        }

        function showMessage(text, type) {
            const msg = document.getElementById('message');
            msg.textContent = text;
            msg.className = `message ${type}`;
            setTimeout(() => msg.textContent = '', 3000);
        }

        function downloadReport() {
            window.location.href = 'report.php';
        }

        init();
    </script>
</body>
</html>