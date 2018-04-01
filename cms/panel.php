<?php

session_start();

/**
 * Redirects the user back to the homepage if the user is not an administrator.
 */
(!$_SESSION['isAdmin']) ? header('Location:' . '../') : false;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS, Styles & Font Awesome-->
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <link rel="stylesheet" type="text/css" href="../libraries/font-awesome/css/font-awesome.min.css">

    <title>MovieHub | CMS Panel</title>
</head>
<body>

<?php require '../includes/header.php'; ?>

<!-- Add Products, Remove Product & Orders Tab -->
<div class="panel-steps" id="panelSteps">
    <div class="container w-75">
        <ul class="nav justify-content-center">
            <!-- Add Product Tab -->
            <li class="nav-item">
                <a class="nav-link active-step active show" id="addProductTab" href="#navAddProduct" role="tab"
                   data-toggle="tab">
                    <div class="panel-icon-border mr-2">
                        1
                    </div>
                    Add Product
                </a>
            </li>
            <!-- Remove Product Tab -->
            <li class="nav-item">
                <a class="nav-link inactive-step" id="removeProductTab" href="#navRemoveProduct" role="tab"
                   data-toggle="tab">
                    <div class="panel-icon-border mr-2">
                        2
                    </div>
                    Remove Product
                </a>
            </li>
            <!-- Orders Tab -->
            <li class="nav-item">
                <a class="nav-link inactive-step" id="viewOrdersTab" href="#navViewOrders" role="tab"
                   data-toggle="tab">
                    <div class="panel-icon-border mr-2">
                        3
                    </div>
                    Orders
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Tab Content -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-7">
            <div class="tab-content mt-3 mb-3">
                <!-- Add Product -->
                <div class="tab-pane active" id="navAddProduct" role="tabpanel">
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-1">
                                            <h5 class="ml-0">Add Product</h5>
                                        </div>
                                        <!-- Product -->
                                        <form class="col" id="addProductForm">
                                            <h6>Product</h6>
                                            <div class="form-row">
                                                <div class="col-7">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Title</span>
                                                        </div>
                                                        <input id="title" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-5">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Release Date</span>
                                                        </div>
                                                        <input id="year" type="text" class="form-control" placeholder="Year">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Cover</span>
                                                        </div>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" id="cover">
                                                            <label class="custom-file-label" id="fileName">
                                                                Choose a cover...
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">DVD Price (£)</span>
                                                        </div>
                                                        <input id="dvdPrice" type="text" class="form-control"
                                                               placeholder="9.99">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Blu-ray Price (£)</span>
                                                        </div>
                                                        <input id="bluRayPrice" type="text" class="form-control"
                                                               placeholder="12.99">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Stock -->
                                            <h6 class="mt-1">Stock</h6>
                                            <div class="form-row">
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">DVD Quantity</span>
                                                        </div>
                                                        <input id="dvdQuantity" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Blu-ray Quantity</span>
                                                        </div>
                                                        <input id="bluRayQuantity" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Description -->
                                            <h6 class="mt-1">Description</h6>
                                            <div class="form-row">
                                                <div class="col h-100">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text text-area-title">Description</span>
                                                        </div>
                                                        <textarea id="description" class="form-control no-resize"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Information -->
                                            <h6 class="mt-1">Information</h6>
                                            <div class="form-row">
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Actors</span>
                                                        </div>
                                                        <input id="actors" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Directors</span>
                                                        </div>
                                                        <input id="directors" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Format</span>
                                                        </div>
                                                        <input id="format" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Language</span>
                                                        </div>
                                                        <input id="language" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Subtitles</span>
                                                        </div>
                                                        <input id="subtitles" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Region</span>
                                                        </div>
                                                        <input id="region" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Aspect Ratio</span>
                                                        </div>
                                                        <input id="aspectRatio" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Number of Discs</span>
                                                        </div>
                                                        <input id="numberOfDiscs" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">DVD Release Date</span>
                                                        </div>
                                                        <input id="dvdReleaseDate" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Run Time</span>
                                                        </div>
                                                        <input id="runTime" type="text" class="form-control" placeholder="180">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Trailer -->
                                            <h6 class="mt-1">Trailer</h6>
                                            <div class="form-row">
                                                <div class="col">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">URL</span>
                                                        </div>
                                                        <input id="trailer" type="text" class="form-control"
                                                               placeholder="https://www.youtube.com/watch?v=twuScTcDP_Q">
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="btn btn-outline-success pull-right" id="addProduct">
                                                Add Product
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remove Product -->
                <div class="tab-pane" id="navRemoveProduct" role="tabpanel">
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-1">
                                            <h5 class="ml-0">Remove Product</h5>
                                        </div>
                                        <div class="col">
                                            <!-- Search -->
                                            <h6>Search</h6>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control admin-search"
                                                               placeholder="Search movies, actors, directors..." id="productSearch">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-dark disabled" type="button">
                                                                <i class="fa fa-search"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Recently Added -->
                                            <h6 class="mt-1" id="recentlyAddedTitle">Recently Added</h6>
                                            <div class="text-center" id="recentlyAddedSpinner"></div>
                                            <div id="recentlyAdded"></div>
                                            <nav class="mt-3" id="removeProductPagination"></nav>

                                            <!-- Edit Product Modal -->
                                            <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog">
                                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <!-- Header -->
                                                        <div class="modal-header">
                                                            <div class="container modal-header-container">
                                                                <div class="row align-items-start">
                                                                    <div class="col">
                                                                        <h5 class="modal-title" id="editProductModalTitle">Moon (2009)</h5>
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
                                                        <div class="modal-body">
                                                            <div id="editProductID" hidden></div>
                                                            <div class="row product-thumbnail">
                                                                <div class="col-5">
                                                                    <img id="productImage"
                                                                         class="img-thumbnail">
                                                                </div>
                                                                <div class="col-7 pl-0 pr-0">
                                                                    <form class="col pl-0" id="editProductForm">
                                                                        <h6>Product</h6>
                                                                        <div class="form-row">
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Title</span>
                                                                                    </div>
                                                                                    <input id="editTitle" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Release Date</span>
                                                                                    </div>
                                                                                    <input id="editYear" type="text" class="form-control" placeholder="Year">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-row">
                                                                            <div class="col">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Cover</span>
                                                                                    </div>
                                                                                    <div class="custom-file">
                                                                                        <input type="file" class="custom-file-input" id="editCover">
                                                                                        <label class="custom-file-label" id="editFileName">
                                                                                            Choose a cover...
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-row">
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">DVD Price (£)</span>
                                                                                    </div>
                                                                                    <input id="editDvdPrice" type="text" class="form-control"
                                                                                           placeholder="9.99">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Blu-ray Price (£)</span>
                                                                                    </div>
                                                                                    <input id="editBluRayPrice" type="text" class="form-control"
                                                                                           placeholder="12.99">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Stock -->
                                                                        <h6 class="mt-1">Stock</h6>
                                                                        <div class="form-row">
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">DVD Quantity</span>
                                                                                    </div>
                                                                                    <input id="editDvdQuantity" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Blu-ray Quantity</span>
                                                                                    </div>
                                                                                    <input id="editBluRayQuantity" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- Description -->
                                                                        <h6 class="mt-1">Description</h6>
                                                                        <div class="form-row">
                                                                            <div class="col h-100">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text text-area-title">Description</span>
                                                                                    </div>
                                                                                    <textarea id="editDescription" class="form-control no-resize"></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- Information -->
                                                                        <h6 class="mt-1">Information</h6>
                                                                        <div class="form-row">
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Actors</span>
                                                                                    </div>
                                                                                    <input id="editActors" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Directors</span>
                                                                                    </div>
                                                                                    <input id="editDirectors" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-row">
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Format</span>
                                                                                    </div>
                                                                                    <input id="editFormat" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Language</span>
                                                                                    </div>
                                                                                    <input id="editLanguage" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-row">
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Subtitles</span>
                                                                                    </div>
                                                                                    <input id="editSubtitles" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Region</span>
                                                                                    </div>
                                                                                    <input id="editRegion" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-row">
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Aspect Ratio</span>
                                                                                    </div>
                                                                                    <input id="editAspectRatio" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Number of Discs</span>
                                                                                    </div>
                                                                                    <input id="editNumberOfDiscs" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-row">
                                                                            <div class="col-7">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">DVD Release Date</span>
                                                                                    </div>
                                                                                    <input id="editDvdReleaseDate" type="text" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-5">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">Run Time</span>
                                                                                    </div>
                                                                                    <input id="editRunTime" type="text" class="form-control" placeholder="180">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- Trailer -->
                                                                        <h6 class="mt-1">Trailer</h6>
                                                                        <div class="form-row">
                                                                            <div class="col">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">URL</span>
                                                                                    </div>
                                                                                    <input id="editTrailer" type="text" class="form-control"
                                                                                           placeholder="https://www.youtube.com/watch?v=twuScTcDP_Q">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <button class="btn btn-outline-success pull-right" id="updateProduct">
                                                                            Save Changes
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders -->
                <div class="tab-pane" id="navViewOrders" role="tabpanel">
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-1">
                                            <h5 class="ml-0">Orders</h5>
                                        </div>
                                        <div class="col">
                                            <!-- Search -->
                                            <h6>Search</h6>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control admin-search"
                                                               placeholder="Search order numbers, status, names..." id="orderSearch">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-dark disabled" type="button">
                                                                <i class="fa fa-search"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Recent Orders -->
                                            <h6 class="mt-1" id="recentOrdersTitle">Recent Orders</h6>
                                            <div class="text-center" id="recentOrdersSpinner"></div>
                                            <div id="recentOrders"></div>
                                            <nav class="mt-3" id="recentOrdersPagination"></nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Modal -->
                <div class="modal fade" id="orderModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <!-- Header -->
                            <div class="modal-header">
                                <div class="container modal-header-container">
                                    <div class="row align-items-start">
                                        <div class="col">
                                            <h5 class="modal-title" id="orderModalTitle">Order (#823244)</h5>
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
                            <div class="modal-body">
                                <div class="row m-0 mb-3">
                                    <!-- Date of Purchase & Status -->
                                    <div class="card w-100 mb-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <h6>Date of Purchase:</h6>
                                                    <div id="dateOfPurchase"></div>
                                                </div>
                                                <div class="col">
                                                    <h6>Status:</h6>
                                                    <div id="status"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Shipping & Billing Information-->
                                    <div class="card w-100 mb-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col" id="shippingAddress"></div>
                                                <div class="col" id="billingAddress"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Shipping Speed -->
                                    <div class="card w-100 mb-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <h6>Shipping Speed:</h6>
                                                    <div id="delivery"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Package -->
                                    <div class="card w-100 mb-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <h6>Package:</h6>
                                                    <ul class="history-list" id="package"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Order Summary -->
                                    <div class="card w-100">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <h6>Order Summary:</h6>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div>Subtotal (excl. VAT):</div>
                                                        </div>
                                                        <div class="col text-right">
                                                            <div id="subTotal"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div>VAT (20%):</div>
                                                        </div>
                                                        <div class="col text-right">
                                                            <div id="vat"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div>Post & Packaging:</div>
                                                        </div>
                                                        <div class="col text-right">
                                                            <div id="postAndPackaging"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2 history-total">
                                                        <div class="col">
                                                            <div>Grand Total:</div>
                                                        </div>
                                                        <div class="col text-right">
                                                            <div id="grandTotal"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-dark" data-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>

<!-- jQuery, Scripts, Validator, Popper.js & Bootstrap JS -->
<script src="../js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="../js/scripts.js"></script>
<script type="text/javascript" src="../libraries/validator/validator.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.min.js"></script>

</body>
</html>
