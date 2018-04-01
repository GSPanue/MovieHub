<?php

$isAdmin = $_SESSION['isAdmin'];

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

/**
 * The user's orders are fetched in descending order.
 */
$orders = (($currentPage === 'index' || $currentPage === 'checkout') ?
    $db->fetchOrder(['userID' => $_SESSION['userID']], true, -1, 0) : null);

/**
 * getOrders: Returns a list of orders.
 */
function getOrders($orders) {
    $html = '';

    for ($i = 0; $i < sizeof($orders); $i++) {
        $order = $orders[$i];

        $dateCreated = $order['dateCreated'];
        $dateCreated = new DateTime($dateCreated->toDateTime()->format('Y-m-d H:i:s'));

        $html .= '<tr>
                    <td class="order-row">' . $dateCreated->format('d/m/Y') . '</td>
                    <td class="order-row">' . $order['orderNumber'] . '</td>
                    <td class="order-row">' . '£' . $order['summary']['grandTotal'] . '</td>
                    <td class="order-row">' . $order['status'] . '</td>
                    <td align="middle" class="order-row">
                        <button class="btn btn-outline-dark btn-sm view-order" id="' . $order['_id'] . '" onclick="viewOrderHistory(this)">
                            View
                        </button>
                    </td>
                  </tr>';
    }

    return $html;
}

?>
<!-- Profile, Order History, Change Password, CMS & Logout Tabs -->
<div class="row">
    <div class="col-4">
        <div class="nav flex-column nav-pills" id="accountModalTabs" role="tablist">
            <!-- Profile Tab -->
            <a class="nav-link active" id="profileTab" data-toggle="pill" href="#navProfile" role="tab">Profile</a>
            <!-- Order History, Change Password & CMS Tabs -->
            <?php

            /**
             * The order history and password tab is hidden if in the CMS panel.
             */
            if ($currentPage !== 'panel') {
                echo '<a class="nav-link" id="ordersTab" data-toggle="pill" href="#navOrders" role="tab">Order History</a>';
                echo '<a class="nav-link" id="passwordTab" data-toggle="pill" href="#navPassword" role="tab">Change Password</a>';
            }
            /**
             * Displays the CMS tab/button if the user is an administrator.
             */
            echo ($isAdmin) ? '<a class="nav-link" id="cmsTab" data-toggle="pill" href="#" role="tab">Go to CMS</a>' : false;

            ?>
            <!-- Logout Tab -->
            <a class="nav-link logout" id="logoutTab" data-toggle="pill" href="#logoutTab" role="tab">
                <div class="row">
                    <div class="col">
                        Sign Out <i id="signOutSpinner"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="col-8">
        <div class="tab-content">
            <!-- Profile -->
            <div class="tab-pane fade show active mr-3 mb-3" id="navProfile" role="tabpanel">
                <!-- Edit Profile/Save Changes & Cancel Buttons-->
                <div class="row mb-3 align-items-center">
                    <div class="col">
                        <h5 class="m-0">My Profile</h5>
                    </div>
                    <div class="col" id="profileButtons">
                        <button class="btn btn-outline-dark btn-sm pull-right ml-2" id="editProfile">
                            <i class="fa fa-edit" id="editProfileIcon"></i>
                            Edit Profile
                        </button>

                        <button class="btn btn-outline-danger btn-sm pull-right" id="cancelEdit" hidden>
                            <i class="fa fa-undo"></i>
                            Cancel
                        </button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <!-- Profile Form -->
                        <form id="profileForm">
                            <div class="form-row">
                                <div class="col-6">
                                    <label for="firstName">First Name</label>
                                    <input type="text" class="form-control" id="firstName" value="<?php echo $_SESSION['userInformation']['firstName'] ?>" disabled>
                                    <div class="invalid-feedback" id="invalidFirstName"></div>
                                </div>
                                <div class="col-6">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" value="<?php echo $_SESSION['userInformation']['lastName'] ?>" disabled>
                                    <div class="invalid-feedback" id="invalidLastName"></div>
                                </div>
                            </div>
                            <div class="form-row mb-3">
                                <div class="col-12">
                                    <label for="emailAddress">Email Address</label>
                                    <input type="text" class="form-control" id="emailAddress"
                                           value="<?php echo $_SESSION['userInformation']['emailAddress'] ?>" disabled>
                                    <div class="invalid-feedback" id="invalidEmailAddress"></div>
                                </div>
                            </div>
                            <div class="form-row mb-3">
                                <div class="col-4">
                                    <label for="mobileNumber">Mobile Number</label>
                                    <input type="text" class="form-control" id="mobileNumber" value="<?php echo $_SESSION['userInformation']['mobileNumber'] ?>"
                                           disabled>
                                    <div class="invalid-feedback" id="invalidMobileNumber"></div>
                                </div>
                                <div class="col-8">
                                    <label for="dateOfBirthDay">Date of Birth</label>
                                    <div class="form-row">
                                        <div class="col-3">
                                            <select class="custom-select form-control" id="dateOfBirthDay" disabled>
                                                <option disabled selected><?php echo $_SESSION['userInformation']['dateOfBirthDay'] ?></option>
                                            </select>
                                        </div>
                                        <div class="col-5">
                                            <select class="custom-select form-control" id="dateOfBirthMonth" disabled>
                                                <option disabled selected><?php echo $_SESSION['userInformation']['dateOfBirthMonth'] ?></option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select class="custom-select form-control" id="dateOfBirthYear" disabled>
                                                <option disabled selected><?php echo $_SESSION['userInformation']['dateOfBirthYear'] ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row mb-3">
                                <div class="col-6">
                                    <label for="address1">Address (Line 1)</label>
                                    <input type="text" class="form-control" id="address1"
                                           value="<?php echo $_SESSION['userInformation']['address1'] ?>" disabled>
                                    <div class="invalid-feedback" id="invalidAddress1"></div>
                                </div>
                                <div class="col-6">
                                    <label for="address2">Address (Line 2)</label>
                                    <input type="text" class="form-control" id="address2" value="<?php echo $_SESSION['userInformation']['address2'] ?>" disabled>
                                    <div class="invalid-feedback" id="invalidAddress2"></div>
                                </div>
                            </div>
                            <div class="form-row mb-3">
                                <div class="col-6">
                                    <label for="townOrCity">Town/City</label>
                                    <input type="text" class="form-control" id="townOrCity" value="<?php echo $_SESSION['userInformation']['townOrCity'] ?>" disabled>
                                    <div class="invalid-feedback" id="invalidTownOrCity"></div>
                                </div>
                                <div class="col-6">
                                    <label for="county">County</label>
                                    <input type="text" class="form-control" id="county" value="<?php echo $_SESSION['userInformation']['county'] ?>" disabled>
                                    <div class="invalid-feedback" id="invalidCounty"></div>
                                </div>
                            </div>
                            <div class="form-row mb-2">
                                <div class="col-8">
                                    <label for="country">Country</label>
                                    <select class="custom-select" id="country" disabled>
                                        <option selected><?php echo $_SESSION['userInformation']['country'] ?></option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="postCode">Post Code</label>
                                    <input type="text" class="form-control" id="postCode" value="<?php echo $_SESSION['userInformation']['postCode'] ?>" disabled>
                                    <div class="invalid-feedback" id="invalidPostCode"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order History-->
            <div class="tab-pane fade mr-3" id="navOrders" role="tabpanel">
                <div id="orderHistory">
                    <div class="row mb-3 align-items-center">
                        <div class="col">
                            <h5 class="m-0">Order History</h5>
                        </div>
                    </div>
                    <?php

                    /**
                     * If the user has not placed any orders, an alert is displayed. Otherwise,
                     * a table containing the user's order history is displayed.
                     */
                    if (sizeof($orders) === 0) {
                        echo '<div class="alert alert-danger" role="alert">Your order history is empty.</div>';
                    }
                    else {
                        echo '<table class="table order-table">
                        <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Order #</th>
                            <th scope="col">Total</th>
                            <th scope="col">Status</th>
                            <th scope="col">Details</th>
                        <tbody>';

                        /**
                         * Displays a list of orders.
                         */
                        echo getOrders($orders);

                    }
                    ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Change Password -->
            <div class="tab-pane fade" id="navPassword" role="tabpanel">
                <div class="row mb-3 align-items-center">
                    <div class="col">
                        <h5 class="m-0">Change Password</h5>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <form id="changePasswordForm">
                            <div class="form-row mb-3">
                                <div class="col-12">
                                    <label for="currentPassword">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword">
                                    <div class="invalid-feedback" id="invalidCurrentPassword"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-6">
                                    <label for="password">New Password</label>
                                    <input type="password" class="form-control" id="password">
                                    <div class="invalid-feedback" id="invalidPassword"></div>
                                </div>
                                <div class="col-6">
                                    <label for="confirmPassword">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmPassword">
                                    <div class="invalid-feedback" id="invalidConfirmPassword"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Update Changes Button -->
                <div class="btn btn-outline-success btn-sm mb-2 pull-right" id="updatePassword">
                    <i class="fa fa-save"></i>
                    Update
                </div>
            </div>
        </div>
    </div>
</div>