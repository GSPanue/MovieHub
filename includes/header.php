<?php

session_start();

/**
 * $root: The root of this website is assigned.
 */
$root = $_SERVER['DOCUMENT_ROOT'];

/**
 * $currentPage: The current page name without the .php extension is assigned.
 */
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

/**
 * $isLoggedIn: A boolean returned by isset is assigned.
 */
$isLoggedIn = isset($_SESSION['userID']);

/**
 * The user's cart is fetched if the current page is the homepage, and carts that are older
 * than 30 minutes are removed from the carts collection.
 */
$db = (($currentPage == 'index') ? new Database() : null);
($db !== null) ? removeExpiredCarts() : false;
$cart = (($db !== null) ? $db->fetchCart(['userID' => ($isLoggedIn) ? $_SESSION['userID'] : $_SESSION['guestID']], false) : null);

/**
 * removeExpiredCarts: Removes carts from the database that were created more than 30 minutes from the current time.
 */
function removeExpiredCarts() {
    global $db;

    /**
     * The current time is obtained and 30 minutes is subtracted from it.
     */
    $currentTime = new MongoDB\BSON\UTCDateTime();
    $currentTime = new DateTime($currentTime->toDateTime()->format('Y-m-d H:i:s'));
    $currentTime = $currentTime->modify('-30 minutes');

    /**
     * All carts are fetched from the database.
     */
    $cart = $db->fetchCart([], true);

    /**
     * Each cart's dateCreated field is compared with the current time. If the creation date of the cart
     * is less than the current time (minus 30 minutes), the cart is removed from the database and the quantity
     * of the items that were in the cart are restored.
     */
    for ($i = 0; $i < sizeof($cart); $i++) {
        /**
         * The creation date of the cart is obtained.
         */
        $dateCreated = $cart[$i]['dateCreated'];
        $dateCreated = new DateTime($dateCreated->toDateTime()->format('Y-m-d H:i:s'));

        if (($dateCreated <= $currentTime)) {
            /**
             * Each product in cart i is iterated and the quantity of it is added to the products document.
             */
            for ($j = 0; $j < sizeof($cart[$i]['items']); $j++) {
                /**
                 * The current product is assigned to $productInCart. The same product is then fetched
                 * from the products collection.
                 */
                $productInCart = $cart[$i]['items'][$j];

                $productID = ['_id' => new \MongoDB\BSON\ObjectID($productInCart['_id'])];
                $product = $db->fetchProduct($productID, false, false, 0);

                /**
                 * The products DVD and Blu-ray quantity is added to the fetched product.
                 */
                $quantity = [$productInCart['dvdQuantity'], $productInCart['bluRayQuantity']];

                $product['dvdQuantity'] = (string)((int)$product['dvdQuantity'] += $quantity[0]);
                $product['bluRayQuantity'] = (string)((int)$product['bluRayQuantity'] += $quantity[1]);

                /**
                 * The product in the products collection is updated with new quantities.
                 */
                $db->updateProduct($product['_id'], $product);
            }

            /**
             * The cart is removed from the database.
             */
            $db->removeCart(['_id' => $cart[$i]['_id']]);
        }
    }
}

/**
 * displaySearchBar: Displays the search bar on the navigation bar.
 */
function displaySearchBar() {
    echo '<!-- Search bar-->
<form class="w-50 navbar-nav mr-auto">
    <div class="input-group">
        <input type="text" class="form-control search" placeholder="Search movies, actors, directors..." id="mainSearch">
        <div class="input-group-append">
            <button class="btn btn-outline-light disabled" type="button">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
</form>';
}

/**
 * displayShoppingCart: Displays the shopping cart button on the navigation bar.
 */
function displayShoppingCart() {
    global $cart;

    $isEmptyCart = (sizeOf($cart['items']) === 0);

    /**
     * getCartQuantity: Returns the number of products in the user's cart.
     */
    function getCartQuantity($cart) {
        $numberOfItems = 0;

        /**
         * Iterates over each product in the user's cart. If the DVD quantity or Blu-ray quantity is greater
         * than zero, $numberOfItems is incremented.
         */
        for ($i = 0; $i < sizeof($cart); $i++) {
            $quantity = ['dvdQuantity' => (int)$cart[$i]['dvdQuantity'], 'bluRayQuantity' => (int)$cart[$i]['bluRayQuantity']];

            foreach ($quantity as $key => $value) {
                ($value > 0) ? $numberOfItems++ : false;
            }
        }

        return $numberOfItems;
    }

    /**
     * getEmptyCart: Returns html for an empty cart.
     */
    function getEmptyCart() {
        return '<div class="dropdown-divider"></div><div class="col"><div class="empty-cart">Your shopping cart is empty!</div></div>';
    }

    /**
     * getTable: Returns html for a table containing the user's cart items.
     */
    function getTable($cart) {
        $html = '<div class="dropdown-divider dropdown-divider-margin-a"></div>
                 <table class="table table-hover shopping-cart-table">';

        $html .= '<thead>
                    <tr class="shopping-cart-row">
                        <th scope="col" class="shopping-cart-row">Product</th>
                        <th scope="col" class="shopping-cart-row">Format</th>
                        <th scope="col" class="shopping-cart-row">Quantity</th>
                        <th scope="col" class="shopping-cart-row">Price</th>
                        <th scope="col" class="shopping-cart-row">Delete</th>
                    </tr>
                  </thead><tbody>';

        /**
         * Iterates over the user's cart and appends any product that has a quantity greater than zero to $html.
         * Additionally, the DVD and Blu-ray quantity for each product are treated as separate rows.
         */
        for ($i = 0; $i < sizeof($cart); $i++) {
            /**
             * The title, price and quantity of the current product in the user's cart is obtained.
             */
            $title = $cart[$i]['title'] . ' (' . $cart[$i]['year'] . ')';
            $price = ['dvdPrice' => $cart[$i]['dvdPrice'], 'bluRayPrice' => $cart[$i]['bluRayPrice']];
            $quantity = ['dvdQuantity' => (int)$cart[$i]['dvdQuantity'], 'bluRayQuantity' => (int)$cart[$i]['bluRayQuantity']];

            foreach ($quantity as $key => $value) {
                $isDVD = ($key) === 'dvdQuantity';

                /**
                 * If the value of a key in quantity is greater than zero, the product is appended to $html.
                 */
                if ($value > 0) {
                    $html .= '<tr id="' . $cart[$i]['_id'].'">
                    <th scope="row" class="shopping-cart-row">' . $title . '</th>
                    <td valign="middle" class="shopping-cart-row">' . (($isDVD) ? 'DVD' : 'Blu-Ray') . '</td>
                    <td valign="middle" class="shopping-cart-row">' . $value . '</td>
                    <td valign="middle" class="shopping-cart-row">' . '£' . (($isDVD) ? $price['dvdPrice'] : $price['bluRayPrice']) . '</td>
                    <td valign="middle" align="center" class="shopping-cart-row">
                        <button type="button" class="close delete-button remove-from-cart" onclick="removeFromCart(this)">
                            <span>&times;</span>
                        </button>
                    </td>
                </tr>';
                }
            }
        }

        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * getPrice: Returns html for the price section of the cart.
     */
    function getPrice($cart) {
        $totalCost = 0;

        /**
         * Sums the cost of each product in the user's cart.
         */
        for ($i = 0; $i < sizeof($cart); $i++) {
            $quantity = ['dvdQuantity' => (int)$cart[$i]['dvdQuantity'], 'bluRayQuantity' => (int)$cart[$i]['bluRayQuantity']];

            foreach ($quantity as $key => $value) {
                $isDVD = ($key) === 'dvdQuantity';

                if ($value > 0) {
                    $totalCost += ($value * (float)$cart[$i][($isDVD) ? 'dvdPrice' : 'bluRayPrice']);
                }
            }
        }

        /**
         * The total cost summary is calculated.
         */
        $priceBreakdown = ['Subtotal (excl. VAT):' => '£' . bcdiv($totalCost, 1, 2),
            'VAT (20%):' => '£' . bcdiv((0.20 * $totalCost), 1, 2),
            'Post & Packaging:' => 'Free',
            'Grand Total:' => '£' . bcdiv(($totalCost + (0.20 * $totalCost)), 1, 2)
        ];

        $html = '<div class="dropdown-divider dropdown-divider-margin-b"></div>';

        /**
         * Each key-value pair in $priceBreakdown is concatenated in the following foreach loop and appended
         * to $html.
         */
        foreach ($priceBreakdown as $key => $value) {
            $html .= '<div class="row m-0">
                    <div class="col pl-0">
                        <h6 class="dropdown-header header-sm">' . $key . '</h6>
                    </div>
                    <div class="col text-right pr-0">
                        <h6 class="dropdown-header header-rhs header-sm">'. $value . '</h6>
                    </div>
                </div>';
        }

        return $html;
    }

    echo '<!-- Shopping Cart -->
    <li class="nav-item">
        <div id="shoppingCart">
            <button class="btn btn-outline-light" type="button" data-toggle="dropdown" id="shoppingCartButton">
                <i class="fa fa-shopping-cart">
                    <span class="badge" id="cartQuantity">' . (($isEmptyCart) ? 0 : getCartQuantity($cart['items'])) . '</span>
                </i>
            </button>
    
            <!-- Dropdown Menu -->
            <div class="dropdown-menu dropdown-menu-right" id="shoppingCartMenu">
    
                <!-- Header & Checkout button -->
                <div class="row m-0">
                    <div class="col pl-0">
                        <h6 class="dropdown-header">Shopping Cart</h6>
                    </div>
                    <div class="col text-right">
                        <button type="button" class="btn btn-success btn-sm checkout-btn mr-0" id="checkout">Checkout</button>
                    </div>
                </div><div id="cartContent">' . (($isEmptyCart) ? getEmptyCart() : getTable($cart['items'])) .
                ((!$isEmptyCart) ? getPrice($cart['items']) : null) . '</div>' .
            '</div>
        </div>
    </li>';
}

?>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <!-- Brand -->
    <a class="navbar-brand" href="<?php echo ($currentPage == 'panel') ? '../' : './'; ?>">MovieHub</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Account & Shopping Cart -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <?php

        /**
         * Displays the search bar if on the homepage.
         */
        ($currentPage == 'index') ? displaySearchBar() : false;

        ?>
        <ul class="navbar-nav ml-auto">
            <?php

            /**
             * Displays the account modal if the current page is not checkout.
             */
            if ($currentPage !== 'checkout') {
                echo '<li class="nav-item">
                <button class="btn btn-outline-light mr-2" type="button" data-toggle="modal"
                        data-target="#accountModal">
                    <i class="fa fa-user"></i>
                </button>

                <!-- Modal -->
                <div class="modal fade" id="accountModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">

                            <!-- Header -->
                            <div class="modal-header">
                                <div class="container modal-header-container">
                                    <div class="row align-items-start">
                                        <div class="col">
                                            <h5 class="modal-title">' . (($isLoggedIn) ? 'My Account' : 'MovieHub') . '</h5>
                                        </div>
                                        <div class="col">
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Body -->
                            <div class="modal-body" id="accountModalBody">';
                ($isLoggedIn) ? include($root . '/profile.php') : include($root . '/log-in.php');

                echo '</div></div></div></div></li>';

                /**
                 * Displays the shopping cart button if on the homepage.
                 */
                ($currentPage == 'index') ? displayShoppingCart() : false;
            }

            ?>
        </ul>
    </div>
</nav>