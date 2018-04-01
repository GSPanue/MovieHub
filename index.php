<?php

require './database/vendor/autoload.php';
require './database/Database.php';

session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS, Styles, Font Awesome & Animate -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="libraries/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="libraries/animate/animate.css">

    <title>MovieHub | Shop for the latest movies</title>
</head>
<body>

<?php require 'includes/header.php';?>

<div class="loading" id="loading"><i class="fa fa-circle-o-notch fa-spin"></i></div>

<!-- Sidebar & Products-->
<div class="container content mt-3" style="display: none" id="content">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-3 mr-5">
            <!-- Filter by Price -->
            <div class="mb-3">
                <div class="row sidebar-header 5 mb-2">
                    <h6>Price</h6>
                </div>
                <div id="priceFilter">
                    <div class="custom-control custom-checkbox product-filter">
                        <input type="checkbox" class="custom-control-input" id="lessThan5" value="5">
                        <label class="custom-control-label" id="lessThan5Label">
                            Less than £5
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox product-filter">
                        <input type="checkbox" class="custom-control-input" id="lessThan10" value="10">
                        <label class="custom-control-label" id="lessThan10Label">
                            £5 - £9.99
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox product-filter">
                        <input type="checkbox" class="custom-control-input" id="lessThan15" value="15">
                        <label class="custom-control-label" id="lessThan15Label">
                            £10 - £14.99
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox product-filter">
                        <input type="checkbox" class="custom-control-input" id="lessThan20" value="20">
                        <label class="custom-control-label" id="lessThan20Label">
                            £15 - £19.99
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox product-filter">
                        <input type="checkbox" class="custom-control-input" id="moreThan20" value="0">
                        <label class="custom-control-label" id="moreThan20Label">
                            £20+
                        </label>
                    </div>
                </div>
            </div>

            <!-- Filter by Decade -->
            <div class="mb-3">
                <div class="row sidebar-header 5 mb-2">
                    <h6>Decade</h6>
                </div>
                <div class="custom-control custom-checkbox product-filter">
                    <input type="checkbox" class="custom-control-input" id="year2010" value="2010">
                    <label class="custom-control-label" id="year2010Label">
                        2010s
                    </label>
                </div>
                <div class="custom-control custom-checkbox product-filter">
                    <input type="checkbox" class="custom-control-input" id="year2000" value="2000">
                    <label class="custom-control-label" id="year2000Label">
                        2000s
                    </label>
                </div>
                <div class="custom-control custom-checkbox product-filter">
                    <input type="checkbox" class="custom-control-input" id="year1990" value="1990">
                    <label class="custom-control-label" id="year1990Label">
                        1990s
                    </label>
                </div>
                <div class="custom-control custom-checkbox product-filter">
                    <input type="checkbox" class="custom-control-input" id="year1980" value="1980">
                    <label class="custom-control-label" id="year1980Label">
                        1980s
                    </label>
                </div>
                <div class="custom-control custom-checkbox product-filter">
                    <input type="checkbox" class="custom-control-input" id="year1970" value="1970">
                    <label class="custom-control-label" id="year1970Label">
                        1970s
                    </label>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col">
            <!-- Product Sorting Options -->
            <div class="row sort-container">
                <div class="w-100">
                    <h6 class="results pull-left" id="results"></h6>
                    <div class="pull-right">
                        <div class="col">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle btn-sm sort-btn"
                                    data-toggle="dropdown" id="dropdownFilter">Latest</button>
                            <div class="dropdown-menu dropdown-menu-right" id="dropdownFilterOptions">
                                <button class="dropdown-item" type="button" id="latest">Latest</button>
                                <button class="dropdown-item" type="button" id="lowest">Price: Low to High</button>
                                <button class="dropdown-item" type="button" id="highest">Price: High to Low</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Listings -->
            <div class="row mt-3">
                <div class="row mb-3 product-group" id="products"></div>
            </div>

            <!-- Product Modal -->
            <div class="modal fade" id="productModal" tabindex="-1" role="dialog"
                 data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header">
                            <!-- Product Title & Year -->
                            <div class="container modal-header-container">
                                <div class="row align-items-start">
                                    <div class="col">
                                        <h5 class="modal-title" id="productTitle"></h5>
                                    </div>
                                    <div class="col">
                                        <button type="button" class="close" data-dismiss="modal" id="closeModalTop">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                            <!-- Product Thumbnail -->
                            <div class="row product-thumbnail">
                                <div class="col-5">
                                    <img id="productImage" class="img-thumbnail">
                                </div>

                                <!-- Product Title, Price & Stock Status -->
                                <div class="col-7">
                                    <div class="row">
                                        <h4 id="productBodyTitle"></h4>
                                    </div>
                                    <div class="row">
                                        <h5 id="productPrice"></h5>
                                    </div>
                                    <div class="row mb-3">
                                        <h6 id="productStock"></h6>
                                    </div>

                                    <!-- Product Format -->
                                    <div class="row mr-1 mb-3 product-options">
                                        <div class="col-3">
                                            <h6>Format:</h6>
                                        </div>
                                        <!-- Product Format Dropdown Button -->
                                        <div class="col-3 product-dropdown-btn">
                                            <div class="btn-group">
                                                <button class="btn btn-outline-dark btn-sm dropdown-toggle"
                                                        type="button" data-toggle="dropdown">
                                                    <span id="productFormat"></span>
                                                </button>
                                                <div class="dropdown-menu" id="dropdownFormat"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Quantity -->
                                    <div class="row mr-1 mb-4 product-options">
                                        <div class="col-3">
                                            <h6>Quantity:</h6>
                                        </div>
                                        <!-- Product Quantity Dropdown Button -->
                                        <div class="col-3 product-dropdown-btn">
                                            <div class="btn-group">
                                                <button class="btn btn-outline-dark btn-sm dropdown-toggle"
                                                        type="button" data-toggle="dropdown">
                                                    <span id="productQuantity"></span>
                                                </button>
                                                <div class="dropdown-menu" id="dropdownQuantity"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Information, Description & Trailer Tabs -->
                                    <div class="row">
                                        <div class="col product-tab tab-inactive">
                                            <nav>
                                                <!-- Product Description Tab -->
                                                <div class="nav nav-tabs nav-justified mb-3"
                                                     id="productModalTabs" role="tablist">
                                                    <a class="nav-item nav-link nav-tab-btn active"
                                                       id="descriptionTab" data-toggle="tab"
                                                       href="#navDescription" role="tab">
                                                        Description
                                                    </a>
                                                    <!-- Product Information Tab -->
                                                    <a class="nav-item nav-link nav-tab-btn"
                                                       id="informationTab" data-toggle="tab"
                                                       href="#navInformation" role="tab">
                                                        Information
                                                    </a>
                                                    <!-- Product Trailer Tab -->
                                                    <a class="nav-item nav-link nav-tab-btn"
                                                       id="trailerTab" data-toggle="tab"
                                                       href="#navTrailer" role="tab">
                                                        Trailer
                                                    </a>
                                                </div>
                                            </nav>

                                            <!-- Tab Content -->
                                            <div class="tab-content">
                                                <!-- Product Description -->
                                                <div class="tab-pane fade show active"
                                                     id="navDescription" role="tabpanel">
                                                </div>

                                                <!-- Product Information -->
                                                <div class="tab-pane fade" id="navInformation"
                                                     role="tabpanel">
                                                    <dl class="row">
                                                        <dt class="col-5 h6">Actors:</dt>
                                                        <dd class="col-7" id="actors"></dd>
                                                        <dt class="col-5 h6">Directors:</dt>
                                                        <dd class="col-7" id="directors"></dd>
                                                        <dt class="col-5 h6">Format:</dt>
                                                        <dd class="col-7" id="format"></dd>
                                                        <dt class="col-5 h6">Language:</dt>
                                                        <dd class="col-7" id="language"></dd>
                                                        <dt class="col-5 h6">Subtitles:</dt>
                                                        <dd class="col-7" id="subtitles"></dd>
                                                        <dt class="col-5 h6">Region:</dt>
                                                        <dd class="col-7" id="region"></dd>
                                                        <dt class="col-5 h6">Aspect Ratio:</dt>
                                                        <dd class="col-7" id="aspectRatio"></dd>
                                                        <dt class="col-5 h6">Number of Discs:</dt>
                                                        <dd class="col-7" id="numberOfDiscs"></dd>
                                                        <dt class="col-5 h6">DVD Release Date:</dt>
                                                        <dd class="col-7" id="dvdReleaseDate"></dd>
                                                        <dt class="col-5 h6">Run Time:</dt>
                                                        <dd class="col-7" id="runTime"></dd>
                                                    </dl>
                                                </div>

                                                <!-- Product Trailer -->
                                                <div class="tab-pane fade" id="navTrailer"
                                                     role="tabpanel">
                                                    <div class="embed-responsive embed-responsive-16by9">
                                                        <iframe class="embed-responsive-item"
                                                                allowfullscreen="allowfullscreen" id="productTrailer">
                                                        </iframe>
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
                            <button type="button" class="btn btn-dark" data-dismiss="modal" id="closeModal">
                                Close
                            </button>
                            <button type="button" class="btn btn-success" id="modalAddToCart">
                                Add to cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="mb-4" id="productPagination"></nav>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>

<!-- jQuery, Scripts, Validator, CryptoJS, Popper.js & Bootstrap JS -->
<script src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/scripts.js"></script>
<script type="text/javascript" src="libraries/validator/validator.min.js"></script>
<script type="text/javascript" src="libraries/cryptojs/core.js"></script>
<script type="text/javascript" src="libraries/cryptojs/md5.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>
</html>