<?php
session_start();
require_once './Basket.php';
include('./functions.php');

// Define the product catalogue
$products = include('./products.php');

if (!isset($_SESSION['basket']) || !is_string($_SESSION['basket'])) {
    $_SESSION['basket'] = serialize(new Basket($products));
}
if (is_string($_SESSION['basket'])) {
    try {
        $basket = unserialize($_SESSION['basket']);
    } catch (Exception $e) {
        echo 'Error: ', $e->getMessage();
        exit; // Stop further execution if there is an error
    }
} else {
    echo 'Session data is not a valid serialized string.';
    exit;
}

// add to the cart
if (isset($_POST['add_product_code'])) {
    try {
        $basket->add($_POST['add_product_code']);
        $_SESSION['basket'] = serialize($basket);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

// remove from the cart
if (isset($_POST['remove_product_code'])) {
    try {
        $basket->remove($_POST['remove_product_code']);
        $_SESSION['basket'] = serialize($basket); // Update session with the modified basket
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

$cartItems = $basket->getCart();
$total = $basket->calculatePrice();
$duties = $basket->calculateDuties();
$r1offer = 0;
if (isset($cartItems) && isset($cartItems['R01'])) {
    $r1offer = redWidgetOffer($cartItems['R01'], $products);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acme Widget Co - Product Selection</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <div class="container">
        <h1>Acme Widget Co</h1>
        <h2>Products</h2>
        <form action="clear_session.php" method="post">
            <button type="submit" name="clear_session">Clear Session</button>
        </form>
        <div id="product-list">
            <?php foreach ($products as $code => $product) : ?>
                <div class="product">
                    <h3><?php echo $product['name']; ?></h3>
                    <p>Code: <?php echo $code; ?></p>
                    <p>Price: <?php echo formatPrice($product['price']); ?></p>
                    <form method="post" action=".">
                        <input type="hidden" name="add_product_code" value="<?php echo $code; ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        <h2>Shopping Cart</h2>
        <div id="cart">
            <?php if (empty($cartItems)) : ?>
                <p class="center">Your cart is empty.</p>
            <?php else : ?>
                <?php foreach ($cartItems as $code => $item) : ?>
                    <div class="cart-item">
                        <h4><?php echo $item['name']; ?></h4>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                        <p>Price: <?php echo formatPrice($item['price'], 2); ?></p>
                        <p>Total: <?php echo formatPrice($item['price'] * $item['quantity']); ?></p>
                        <form method="post" action=".">
                            <input type="hidden" name="remove_product_code" value="<?php echo htmlspecialchars($code); ?>">
                            <button type="submit">-</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($cartItems)) : ?>
            <div id="total">
                <p>Price: <?php echo formatPrice($total); ?></p>
                <p>Delivery Charges: <?php echo formatPrice($duties); ?></p>
                <p>R1 Offer: <?php echo formatPrice($r1offer); ?></p>
                <hr>
                <h3>Total Amount: <?php echo formatPrice($total + $duties - $r1offer); ?></h3>
            </div>
        <?php endif; ?>

    </div>
</body>

</html>