<?php
session_start();
require_once __DIR__ . '/../app/config.php';

// ✅ Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ✅ Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update quantity
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['qty'] as $productId => $quantity) {
            $productId = (int)$productId;
            $quantity = max(1, (int)$quantity);
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] = $quantity;
            }
        }
    }

    // Remove item
    if (isset($_POST['remove'])) {
        $removeId = (int)$_POST['remove'];
        unset($_SESSION['cart'][$removeId]);
    }

    // Empty cart
    if (isset($_POST['empty_cart'])) {
        $_SESSION['cart'] = [];
    }

    header("Location: cart.php");
    exit;
}

// ✅ Load product details
$items = [];
$total = 0.0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $stmt = $pdo->query("SELECT id, title, price, image_path FROM catalog_items WHERE id IN ($ids) AND status = 'published'");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $pid = $product['id'];
        $qty = $_SESSION['cart'][$pid];
        $lineTotal = $product['price'] * $qty;
        $total += $lineTotal;

        $items[] = [
            'id' => $pid,
            'title' => $product['title'],
            'price' => $product['price'],
            'qty' => $qty,
            'line_total' => $lineTotal,
            'image' => $product['image_path'] ?? ''
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart</title>
<style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 40px; }
    h1 { text-align: center; color: #0056b3; }
    table { width: 80%; margin: 20px auto; border-collapse: collapse; background: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
    th { background: #007bff; color: white; }
    img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
    .actions button { background: #dc3545; color: white; border: none; padding: 8px 10px; border-radius: 4px; cursor: pointer; }
    .actions button:hover { background: #b52a35; }
    .update-btn { background: #28a745; }
    .update-btn:hover { background: #1e7e34; }
    .checkout { text-align: center; margin-top: 30px; }
    .checkout a { text-decoration: none; background: #007bff; color: #fff; padding: 12px 20px; border-radius: 5px; }
    .checkout a:hover { background: #0056b3; }
    .empty-cart { text-align: center; margin-top: 10px; }
</style>
</head>
<body>

<h1>🛒 Your Cart</h1>

<?php if (empty($items)): ?>
    <p style="text-align:center;">Your cart is empty.</p>
    <p style="text-align:center;"><a href="catalog.php">← Back to Catalog</a></p>
<?php else: ?>
<form method="post" action="">
<table>
    <tr>
        <th>Product</th>
        <th>Image</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Line Total</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($items as $item): ?>
    <tr>
        <td><?= htmlspecialchars($item['title']) ?></td>
        <td>
            <?php if (!empty($item['image'])): ?>
                <img src="../public/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
            <?php else: ?>
                <span>—</span>
            <?php endif; ?>
        </td>
        <td>$<?= number_format($item['price'], 2) ?></td>
        <td>
            <input type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['qty'] ?>" min="1" style="width:60px;">
        </td>
        <td>$<?= number_format($item['line_total'], 2) ?></td>
        <td class="actions">
            <button type="submit" name="remove" value="<?= $item['id'] ?>">Remove</button>
        </td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="4" style="text-align:right; font-weight:bold;">Grand Total:</td>
        <td colspan="2" style="font-weight:bold;">$<?= number_format($total, 2) ?></td>
    </tr>
</table>

<div style="text-align:center; margin-top:15px;">
    <button type="submit" name="update_cart" class="update-btn">Update Cart</button>
</div>
</form>

<div class="checkout">
    <a href="checkout.php">Proceed to Checkout →</a>
</div>

<div class="empty-cart">
    <form method="post">
        <button type="submit" name="empty_cart" class="actions">Empty Cart</button>
    </form>
    <p><a href="catalog.php">← Continue Shopping</a></p>
</div>
<?php endif; ?>

</body>
</html>
