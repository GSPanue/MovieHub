/********************
 * Table of Contents:
 *
 * 1. Global Variables
 *
 * 2. Startup
 *
 * 3. Form Validation
 *
 * 4. Log In & Registration Modal
 * 4.1. Log In & Registration Modal: Helpers
 *
 * 5. Account Modal
 *
 * 6. Product Modal
 *
 * 7. Products
 *
 * 8. Shopping Cart
 *
 * 9. Filters
 * 9.1. Filters: Checkboxes
 * 9.2. Filters: Dropdown
 *
 * 10. Checkout
 * 10.1. Checkout: Shopping Cart
 * 10.2. Checkout: Shipping & Billing
 * 10.3. Checkout: Review & Place Order
 * 10.3.1. Review & Place Order: Helpers
 * 10.4. Checkout: Submission
 * 10.5. Checkout: Helpers
 *
 * 11. CMS
 * 11.1. CMS: Add Tab
 * 11.2. CMS: Remove Tab
 * 11.2.1 Remove Tab: Remove Product
 * 11.2.2. Remove Tab: Edit Product
 * 11.3. CMS: Orders Tab
 * 11.3.1. Orders Tab: View Order
 * 11.4. CMS: Helpers
 *
 * 12. Search
 *
 * 13. Tracking
 *
 * 14. Helpers
 */

/*********************
 * 1. Global Variables
 */

/**
 * registrationID: An array containing all registration form IDs.
 */
var registrationID = [
    'firstName',
    'lastName',
    'emailAddress',
    'password',
    'confirmPassword',
    'mobileNumber',
    'dateOfBirthDay',
    'dateOfBirthMonth',
    'dateOfBirthYear',
    'address1',
    'address2',
    'townOrCity',
    'county',
    'postCode'
];

/**
 * loginID: An array containing all login form IDs.
 */
var loginID = [
    'loginEmailAddress',
    'loginPassword'
];

/**
 * shippingAndBillingID: An array containing all shipping and billing IDs.
 */
var shippingAndBillingID = [
    'shippingFirstName',
    'shippingLastName',
    'shippingMobileNumber',
    'shippingAddress1',
    'shippingAddress2',
    'shippingTownOrCity',
    'shippingCounty',
    'shippingCountry',
    'shippingPostCode',
    'billingFirstName',
    'billingLastName',
    'billingMobileNumber',
    'billingAddress1',
    'billingAddress2',
    'billingTownOrCity',
    'billingCounty',
    'billingCountry',
    'billingPostCode'
];

/**
 * paymentID: An array containg all payment IDs.
 */
var paymentID = [
    'paymentCardNumber',
    'paymentName',
    'paymentExpiry',
    'paymentSecurityCode'
];

/**
 * productID: An array containing all product IDs.
 */
var productID = [
    'title',
    'year',
    'dvdPrice',
    'bluRayPrice',
    'cover',
    'dvdQuantity',
    'bluRayQuantity',
    'description',
    'actors',
    'directors',
    'format',
    'language',
    'subtitles',
    'region',
    'aspectRatio',
    'numberOfDiscs',
    'dvdReleaseDate',
    'runTime',
    'trailer'
];

/**
 * checkoutID: An array containing all checkout IDs.
 */
var checkoutID = [
    'shoppingCartTab',
    'shippingAndBillingTab',
    'paymentTab',
    'reviewAndPlaceOrderTab'
];

/**
 * asyncWait: Used to help synchronise Ajax requests.
 */
var asyncWait = false;

/**
 * currentProductID: Assigned the product ID that the user is viewing.
 */
var currentProductID;

/**
 * products: Assigned an array of products.
 * cart: Assigned an array of products in the user's shopping cart.
 * orders: Assigned an array of orders.
 */
var products = [];
var cart = [];
var orders = [];

/**
 * isRemoving: Used to prevent the user from attempting to remove a product from the cart twice.
 */
var isRemoving = false;


/************
 * 2. Startup
 */

/**
 * Fetches and stores everything required for the website to work, i.e. products, pagination.
 */
$(document).ready(function() {
    var page = getPageName();

    /**
     * If the current page is index, the products are fetched from newest added to oldest.
     */
    if (page === '' || page === 'index') {
        $.ajax({
            url: ((getPageName() === 'panel') ? '../' : './') + 'database/FetchProduct.php',
            method: 'GET',
            data: {sort: true, order: -1, limit: 0},
            dataType: 'json',
            beforeSend: function() {
                /**
                 * Removes all tracked products that have expired.
                 */
                clearExpiredTrackingProducts();
            },
            success: function (response) {
                /**
                 * The products array is assigned an array of products returned.
                 */
                products = response;

                /**
                 * 6 products are displayed per page, and the pagination is added to the page.
                 */
                displayProducts(products, (products.length >= 6) ? 6 : products.length);
                displayPagination(products);
                $('#results').text('Latest Products');

                /**
                 * The user's cart is fetched.
                 */
                $.ajax({
                    url: ((getPageName() === 'panel') ? '../' : './') + 'assets/Cart.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        /**
                         * The cart array is assigned an array of products in the user's cart.
                         * The page overlay is removed afterwards.
                         */
                        cart = ((response === null) ? [] : response);
                        displayPage(true);
                    }
                });
            }
        });
    }
    else if (page === 'checkout') {
        /**
         * The user's cart is fetched and stored in the cart array. The page
         * overlay is removed afterwards.
         */
        updateCart().done(function() {
            displayPage(true);
        });
    }
});

/**
 * displayPage: Displays or removes the page overlay.
 */
function displayPage(boolean) {
    if (boolean) {
        $('#loading').fadeOut('slow', function(){});
        $('#content').fadeIn('slow', function(){});
    }
    else {
        $('#loading').fadeIn('slow', function(){});
        $('#content').fadeOut('slow', function(){});
    }
}


/********************
 * 3. Form Validation
 */

/**
 * All form click, blur and input events are handled.
 */
$('.form-control').on({
    /**
     * Click event: Removes the 'is-invalid' class if it has been added to a field.
     */
    'click': function() {
        var field = this, isRegistrationField = isInArray(field.id, registrationID);
        var isLoginField = isInArray(field.id, loginID);
        var isShippingOrBillingField = isInArray(field.id, shippingAndBillingID);
        var isPaymentField = isInArray(field.id, paymentID);
        var isCurrentPasswordField = isInArray(field.id, ['currentPassword']);
        var isProductField = isInArray(field.id, productID);

        var hasInvalidClass = function(field) {
            return $(field).hasClass('is-invalid');
        };

        if (isRegistrationField || isShippingOrBillingField || isPaymentField || isProductField) {
            if (isShippingOrBillingField) {
                if (hasInvalidClass(field)) {
                    removeInvalidClassAndInvalidFeedbackText(field);
                }

                $('#sameAsShipping').trigger('reset');
                disableNextTabs();
            }
            else {
                if (hasInvalidClass(field)) {
                    removeInvalidClassAndInvalidFeedbackText(field);
                }

                if (isPaymentField) {
                    var currentTabIndex = getCurrentTabIndex();

                    ($('#' + checkoutID[currentTabIndex]).hasClass('active-step')) ? disableNextTabs() : false;
                }
            }

            if (field.id === 'password') {
                var confirmPasswordField = $('#confirmPassword');
                $(confirmPasswordField).val("");

                removeInvalidClassAndInvalidFeedbackText(confirmPasswordField[0]);
            }
        }
        else if (isLoginField) {
            if (hasInvalidClass(field)) {
                removeInvalidClassAndInvalidFeedbackText(field);
            }

            if ($('#invalidLogin')[0].innerHTML.length > 0) {
                for (var i = 0; i < loginID.length; i++) {
                    var field = $('#' + loginID[i]);

                    (i > 0) ? field.val("") : false;

                    removeInvalidClassAndInvalidFeedbackText(field[0]);
                }

                removeInvalidLoginText();
            }
        }
        else if (isCurrentPasswordField) {
            if (hasInvalidClass(field)) {
                removeInvalidClassAndInvalidFeedbackText(field);
            }
        }
    },
    /**
     * Blur event: Adds the 'is-invalid' class to a field and/or invalid feedback text if the field is invalid.
     */
    'blur': function() {
        var field = this, isRegistrationField = isInArray(field.id, registrationID);
        var isLoginField = isInArray(field.id, loginID);
        var isShippingOrBillingField = isInArray(field.id, shippingAndBillingID);
        var isPaymentField = isInArray(field.id, paymentID);
        var isCurrentPasswordField = isInArray(field.id, ['currentPassword']);
        var isProductField = isInArray(field.id, productID);

        if (isRegistrationField || isShippingOrBillingField || isCurrentPasswordField) {
            /**
             * Adds the 'is-invalid' class to a field and invalid feedback text if the field is required and is empty.
             */
            if (validator.isEmpty(field.value.trim())) {
                if (!/address2/i.test(field.id) && !/county/i.test(field.id)) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "You can't leave this empty.");
                }
            }
            /**
             * Validates the first name and last name input by matching them against the following regular expression:
             * (1) The string must not contain two consecutive whitespaces.
             * (2) The string can only contain alphabetic letters, a single whitespace, a comma, full stop, apostrophe and a dash.
             */
            else if (/firstName|lastName/i.test(field.id)) {
                if (!validator.matches(field.value.trim(), /^(?!.* {2})[A-Za-z ,.'-]+$/g)) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "This can only contain letters of the alphabet.");

                    return;
                }

                removeInvalidClassAndInvalidFeedbackText(field);
            }
            /**
             * Validates the e-mail address input by using the validator library.
             */
            else if (field.id === 'emailAddress') {
                if (!validator.isEmail(field.value.trim())) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "This email address format is not recognised.");

                    return;
                }
                else {
                    /**
                     * If the e-mail address input is not empty, the e-mail address is checked to see if it exists
                     * in the database.
                     */
                    asyncWait = true;

                    $.ajax({
                        url: ((getPageName() === 'panel') ? '../' : './') + 'assets/Session.php',
                        method: 'GET',
                        data: {request: 'loggedIn'},
                        success: function (response) {
                            var loggedIn = JSON.parse(response);
                            var emailAddress;

                            /**
                             * If the user is logged in, the user's e-mail address is retrieved.
                             */
                            if (loggedIn) {
                                console.log('reached this!');
                                $.ajax({
                                    url: ((getPageName() === 'panel') ? '../' : './') + 'assets/Session.php',
                                    method: 'GET',
                                    data: {request: 'userInformation'},
                                    success: function (response) {
                                        emailAddress = JSON.parse(response)['emailAddress'];
                                    }
                                }).done(function() {
                                    validateEmailAddress(emailAddress);
                                });
                            }
                            else {
                                validateEmailAddress();
                            }

                            /**
                             * validateEmailAddress: Checks if the e-mail address exists in the database and adds
                             * the 'is-invalid' class to a field as well as invalid feedback text if the e-mail address
                             * exists.
                             */
                            function validateEmailAddress(emailAddress) {
                                if (field.value.trim().toLowerCase() !== emailAddress && loggedIn || !loggedIn) {
                                    $.ajax({
                                        url: ((getPageName() === 'panel') ? '../' : './') + 'database/FetchUser.php',
                                        method: 'GET',
                                        data: {query: {emailAddress: field.value.trim().toLowerCase()}, filter: true},
                                        dataType: 'json',
                                        success: function (response) {
                                            asyncWait = false;

                                            if (response !== null) {
                                                addInvalidClass(field);
                                                addInvalidFeedbackText(field, "This email address is already in use.");
                                            }
                                        }
                                    });
                                }
                                else {
                                    asyncWait = false;
                                }
                            }
                        }
                    });
                }
            }
            /**
             * Validates the password by checking if the length of the input is greater than 5.
             */
            else if (field.id === 'password') {
                if (field.value.length < 6) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "Your password must be at least 6 characters long.");

                    return;
                }

                removeInvalidClassAndInvalidFeedbackText(field);
            }
            /**
             * Validates the confirm password input by checking if it's equal to the password input.
             */
            else if (field.id === 'confirmPassword') {
                if (field.value !== $('#password').val()) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "These passwords don't match. Try again?");

                    return;
                }

                removeInvalidClassAndInvalidFeedbackText(field);
            }
            /**
             * Validates the mobile number input by using the validator library.
             */
            else if (/mobileNumber/i.test(field.id)) {
                if (!validator.isMobilePhone(field.value.trim(), 'en-GB')) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "This mobile number format is not recognised.");

                    return;
                }
                else {
                    /**
                     * If the mobile number input is valid, the mobile number is checked to see if it exists
                     * in the database.
                     */
                    if (!isShippingOrBillingField) {
                        asyncWait = true;

                        $.ajax({
                            url: ((getPageName() === 'panel') ? '../' : './') + 'assets/Session.php',
                            method: 'GET',
                            data: {request: 'loggedIn'},
                            success: function (response) {
                                var loggedIn = JSON.parse(response);
                                var mobileNumber;

                                /**
                                 * If the user is logged in, the user's mobile number is retrieved.
                                 */
                                if (loggedIn) {
                                    $.ajax({
                                        url: ((getPageName() === 'panel') ? '../' : './') + 'assets/Session.php',
                                        method: 'GET',
                                        data: {request: 'userInformation'},
                                        success: function (response) {
                                            mobileNumber = JSON.parse(response)['mobileNumber'];
                                        }
                                    }).done(function () {
                                        validateMobileNumber(mobileNumber);
                                    })
                                }
                                else {
                                    validateMobileNumber();
                                }

                                /**
                                 * validateMobileNumber: Checks if the mobile number exists in the database and adds
                                 * the 'is-invalid' class to a field as well as invalid feedback text if the mobile number
                                 * exists.
                                 */
                                function validateMobileNumber(mobileNumber) {
                                    if (field.value.trim().toLowerCase() !== mobileNumber && loggedIn || !loggedIn) {
                                        $.ajax({
                                            url: ((getPageName() === 'panel') ? '../' : './') + 'database/FetchUser.php',
                                            method: 'GET',
                                            data: {query: {mobileNumber: field.value.trim()}, filter: true},
                                            dataType: 'json',
                                            success: function (response) {
                                                asyncWait = false;

                                                if (response !== null) {
                                                    addInvalidClass(field);
                                                    addInvalidFeedbackText(field, "This mobile number is already in use.");
                                                }
                                            }
                                        });
                                    }
                                    else {
                                        asyncWait = false;
                                    }
                                }
                            }
                        });
                    }
                }
            }
            /**
             * Validates the date of birth options after they have all been selected.
             */
            else if (isInArray(field.id, ['dateOfBirthDay', 'dateOfBirthMonth', 'dateOfBirthYear'])) {
                var isComplete = hasCompletedDateOfBirth();

                /**
                 * hasCompletedDateOfBirth: Checks if all date of birth options have been selected and returns true/false.
                 */
                function hasCompletedDateOfBirth() {
                    for (var i = 6; i < 9; i++) {
                        if ($('#' + registrationID[i]).val() === null) {
                            return false;
                        }
                    }

                    return true;
                }

                /**
                 * The selected month and year is used to check if the selected day is less than or equal
                 * to the number of days in the month. For example, if the user selected 31-November-2018,
                 * the selected options would be invalid as there are 30 days in November.
                 */
                if (isComplete) {
                    var dateOfBirth = {
                        day: $('#dateOfBirthDay'),
                        month: $('#dateOfBirthMonth'),
                        year: $('#dateOfBirthYear')
                    };

                    /**
                     * daysInMonth: Assigned the number of days in the selected month of the selected year.
                     */
                    var daysInMonth = getDaysInMonth(monthToNumber(dateOfBirth.month.val()), parseInt(dateOfBirth.year.val()));

                    /**
                     * Adds the 'is-invalid' class to all date of birth <select> elements if the selected day is greater
                     * than the number of days in the selected month.
                     */
                    if (dateOfBirth.day.val() > daysInMonth) {
                        for (var k in dateOfBirth) {
                            addInvalidClass(dateOfBirth[k]);
                        }
                    }
                    /**
                     * Removes the 'is-invalid' class from all date of birth <select> elements if the selected day
                     * is not greater than the number of days in the selected month.
                     */
                    else {
                        for (var j in dateOfBirth) {
                            removeInvalidClass(dateOfBirth[j]);
                        }
                    }
                }
                else {
                    for (var i = 6; i < 9; i++) {
                        var field = $('#' + registrationID[i]);

                        if (field.val() === null) {
                            addInvalidClass(field[0]);
                        }
                        else {
                            removeInvalidClass(field[0]);
                        }
                    }
                }
            }
            /**
             * Validates the input of both addresses by matching them against the following regular expression:
             * (1) The string must not contain two consecutive whitespaces.
             * (2) The string can only contain alphanumeric characters, a single whitespace, an ampersand and a dash.
             */
            else if (/address1|address2/i.test(field.id)) {
                if (!validator.matches(field.value.trim(), /^(?!.* {2})[A-Za-z0-9 &-]+$/g)) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "This can only contain alphanumeric characters.");

                    return;
                }

                removeInvalidClassAndInvalidFeedbackText(field);
            }
            /**
             * Validates the town/city and county input by matching them against the following regular expression:
             * (1) The string must not contain two consecutive whitespaces.
             * (2) The string can only contain alphabetic letters, a single whitespace, an ampersand and a dash.
             */
            else if (/townOrCity|county/i.test(field.id)) {
                if (!validator.matches(field.value.trim(), /^(?!.* {2})[A-Za-z &-]+$/g)) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "This can only contain letters of the alphabet.");

                    return;
                }

                removeInvalidClassAndInvalidFeedbackText(field);
            }
            /**
             * Validates the postcode input by using the validator library.
             */
            else if (/postCode/i.test(field.id)) {
                if (!validator.isAlphanumeric(validator.blacklist(field.value.trim(), ' '))) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "This post code format is not recognised.");

                    return;
                }

                removeInvalidClassAndInvalidFeedbackText(field);
            }
        }
        else if (isProductField) {
            /**
             * Adds the 'is-invalid' class to a field if the field is empty, otherwise it is removed.
             */
            if (validator.isEmpty(field.value.trim())) {
                addInvalidClass(field);

                return;
            }

            removeInvalidClass(field);
        }
        else if (isLoginField) {
            /**
             * Adds the 'is-invalid' class and invalid feedback text to a field if the field is empty,
             * otherwise it is removed.
             */
            if (validator.isEmpty(field.value.trim())) {
                addInvalidClass(field);
                addInvalidFeedbackText(field, "You can't leave this empty.");

                return;
            }

            removeInvalidClassAndInvalidFeedbackText(field);
        }
        else if (isPaymentField) {
            /**
             * Adds the 'is-invalid' class and invalid feedback text to a field if the field is empty.
             */
            if (validator.isEmpty(field.value.trim())) {
                addInvalidClass(field);
                addInvalidFeedbackText(field, "You can't leave this empty.")
            }

            /**
             * Validates the payment card number by splitting the input by each dash and checking if the
             * length of string is equal to 16.
             */
            else if (field.id === 'paymentCardNumber') {
                var fieldValue = field.value.split(" - ").join("");

                if (fieldValue.length !== (4 * 4) || !validator.isNumeric(fieldValue)) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "This card number format is not recognised.");

                    return;
                }

                removeInvalidClassAndInvalidFeedbackText(field);
            }
            /**
             * Validates the payer's name by matching it against the following regular expression:
             * (1) The string must not contain two consecutive whitespaces.
             * (2) The string can only contain alphabetic letters, a single whitespace, a comma, full stop, apostrophe and a dash.
             */
            else if (field.id === 'paymentName') {
                if (!validator.matches(field.value.trim(), /^(?!.* {2})[A-Za-z ,.'-]+$/g)) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "This can only contain letters of the alphabet.");

                    return;
                }

                removeInvalidClassAndInvalidFeedbackText(field);
            }
            /**
             * Validates the payer's card expiry date.
             */
            else if (field.id === 'paymentExpiry') {
                var fieldValue = field.value.split(" / ").join("");
                var expirationMonth = parseInt(field.value.split(" / ")[0]) - 1, expirationYear = parseInt(field.value.split(" / ")[1]);

                if (expirationMonth < 0 || expirationMonth > 11 || validator.contains(fieldValue, '-') || fieldValue.length < 6) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "This expiration date format is not recognised.");
                }
                else if (!validator.isNumeric(fieldValue) || validator.contains(fieldValue, '-') || fieldValue.length < 6) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "This date format is not recognised.");
                }
                else {
                    var date = new Date();
                    var currentMonth = date.getMonth();
                    var currentYear = date.getFullYear();

                    if ((expirationMonth <= currentMonth && expirationYear <= currentYear) || expirationYear < currentYear) {
                        addInvalidClass(field);
                        addInvalidFeedbackText(field, "This card has expired.");

                        return;
                    }

                    removeInvalidClassAndInvalidFeedbackText(field);
                }
            }
            /**
             * Validates the payer's card security code.
             */
            else if (field.id === 'paymentSecurityCode') {
                var fieldValue = field.value;

                if (!validator.isNumeric(fieldValue) || validator.contains(fieldValue, '-') || fieldValue.length < 3) {
                    addInvalidClass(field);
                    addInvalidFeedbackText(field, "This security code format is not recognised.");

                    return;
                }

                removeInvalidClassAndInvalidFeedbackText(field);
            }
        }
    },
    /**
     * Input event: Handles the input of card details.
     */
    'input': function() {
        var field = this, isPaymentField = isInArray(field.id, paymentID);
        var fieldValue;

        if (isPaymentField) {
            if (field.id === 'paymentCardNumber') {
                var fieldValue = field.value.split(" - ").join("");

                if (fieldValue.length > 0 && fieldValue.length <= (4 * 4)) {
                    fieldValue = fieldValue.match(new RegExp('.{1,4}', 'g')).join(" - ");
                }

                $(field).val(fieldValue);

            }
            else if (field.id === 'paymentExpiry') {
                var fieldValue = field.value.split(" / ").join("");

                if (fieldValue.length < 5) {
                    if (fieldValue.length > 0) {
                        fieldValue = fieldValue.match(new RegExp('.{1,2}', 'g')).join(" / ");
                    }

                    $(field).val(fieldValue);
                }
            }
        }
    }
});

/********************************
 * 4. Log In & Registration Modal
 */

/**
 * Restores the modal back to its initial state when closed.
 */
$('#accountModal').on('hide.bs.modal', function() {
    /**
     * Checks if the user is logged in.
     */
    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'assets/Session.php',
        method: 'GET',
        data: {request: 'loggedIn'},
        dataType: 'json',
        success: function (response) {
            /**
             * If the user is logged in, the edit profile button and change password form is reset.
             */
            if (response) {
                wait().done(function() {
                    (validator.contains($('#editProfile')[0].classList.value, 'btn-outline-dark')) ? false : getElementById('cancelEdit').click();
                    resetForm(['currentPassword', 'password', 'confirmPassword'], 'changePasswordForm');
                });
            }
            else {
                /**
                 * If the user is not logged in, the registration form is reset.
                 */
                if ($('#registrationForm').length !== 0) {

                    /**
                     * resetAllFormInput: Removes all form input.
                     */
                    var resetAllFormInput = function () {
                        $('#registrationForm')[0].reset();
                        $('#loginForm')[0].reset();
                    };

                    /**
                     * resetAllFormElements: Removes the 'is-invalid' class and invalid feedback text from all form fields.
                     */
                    var resetAllFormElements = function () {
                        for (var i = 0; i < registrationID.length; i++) {
                            var field = $('#' + registrationID[i])[0];

                            removeInvalidClassAndInvalidFeedbackText(field);

                            if (i < loginID.length) {
                                field = $('#' + loginID[i])[0];

                                removeInvalidClassAndInvalidFeedbackText(field);
                            }
                        }
                    };

                    resetAllFormInput();
                    resetAllFormElements();
                    removeInvalidLoginText();

                    /**
                     * The 'log in' tab becomes the active tab.
                     */
                    $('#logInTab').tab('show');
                }
            }
        }
    });
});

/**
 * Appends the day, month and year options to the date of birth <select> elements.
 */
$('#createAccountTab').on('click', function() {
    if (!hasSelectOptions()) {
        var months = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];

        for (var i = 0; i < 31; i++) {
            $('#dateOfBirthDay').append('<option value=' + ("" + (i + 1)) + '>' + ("" + (i + 1)) + '</option>');

            if (i < months.length) {
                $('#dateOfBirthMonth').append('<option value=' + ("" + months[i]) + '>' + ("" + months[i]) + '</option>');
            }
        }

        for (var i = 2018; i > 1899; i--) {
            $('#dateOfBirthYear').append('<option value=' + ("" + i) + '>' + ("" + i) + '</option>');
        }
    }
});

/**
 * Validates the login form field and logs in a user when all fields are valid.
 */
$('#submitLoginForm').on('click', function() {
    var isComplete = true;

    /**
     * The 'is-invalid' class is added to any field that has an empty value.
     */
    for (var i = 0; i < loginID.length; i++) {
        var field = $('#' + loginID[i])[0], isEmpty = validator.isEmpty(field.value);

        /**
         * If a field is empty and doesn't have the 'is-invalid' class, isComplete is assigned false
         * and the 'is-invalid' class is added to the field.
         */
        if (isEmpty && !validator.contains(field.classList.value, 'is-invalid')) {
            isComplete = false;

            addInvalidClass(field);
            addInvalidFeedbackText(field, "You can't leave this empty.");
        }
        /**
         * isComplete is assigned false if a field has the 'is-invalid' class.
         */
        else if (validator.contains(field.classList.value, 'is-invalid')) {
            isComplete = false;
        }
    }

    /**
     * The users login credentials are validated when isComplete is true.
     */
    if (isComplete) {
        var emailAddress = $('#loginEmailAddress').val(), password = $('#loginPassword').val();

        logIn(emailAddress, password);
    }

    return false;
});

/**
 * logIn: Logs a user in by checking if the submitted e-mail address and password is valid.
 */
function logIn(emailAddress, password) {
    $('#submitLoginForm').html('<div class="fa fa-circle-o-notch fa-spin"></div>');

    /**
     * The submitted e-mail address and password is sent to LogIn.php.
     * A boolean is returned indicating whether or not the credentials have been matched.
     */
    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'assets/LogIn.php',
        method: 'POST',
        data: {emailAddress: emailAddress, password: hash(password).toString()},
        dataType: 'json',
        success: function(response) {
            if (response) {
                /**
                 * The page is reloaded if the credentials are valid.
                 */
                location.reload();
            }
            else {
                /**
                 * An error message is displayed if the credentials are invalid.
                 */
                $('#submitLoginForm').html('Sign In');

                for (var i = 0; i < loginID.length; i++) {
                    var field = $('#' + loginID[i])[0];

                    addInvalidClass(field);
                    addInvalidFeedbackText(field);
                }

                addInvalidLoginText();
            }
        }
    });

    return false;
}

/**
 * Logs a user out.
 */
$('a[href="#logoutTab"]').on('click', function() {
    $('#signOutSpinner').addClass('fa fa-circle-o-notch fa-spin logout-spinner pull-right');

    wait().done(function() {
        $.ajax({
            url: ((getPageName() === 'panel') ? '../' : './') + 'assets/LogOut.php',
            method: 'POST',
            success: function (response) {
                if (response) {
                    /**
                     * The page is reloaded after the PHP session variables have been changed.
                     */
                    location.reload();
                }
            }
        });
    });
});

/**
 * Stores the registration form data in the database when all form fields are valid.
 */
$('#submitRegistrationForm').on('click', function() {
    var isComplete = true;
    var user = {};

    $('#submitRegistrationForm').html('<div class="fa fa-circle-o-notch fa-spin"></div>');


    var interval = setInterval(function() {
        if (!asyncWait) {
            clearInterval(interval);

            /**
             * Checks if all form fields are valid.
             */
            for (var i = 0; i < registrationID.length; i++) {
                var field = $('#' + registrationID[i])[0], isEmpty = validator.isEmpty(field.value);
                user[field.id] = field.value;

                /**
                 * isComplete is assigned false if a field is empty, or if date of birth has not been set.
                 */
                if ((isEmpty && field.id !== 'address2' && field.id !== 'county') || isInArray(field.value, ['Day', 'Month', 'Year'])) {
                    $('#submitRegistrationForm').html('Register');
                    isComplete = false;

                    /**
                     * Adds the 'is-invalid' class if it doesn't exist.
                     */
                    if (!validator.contains(field.classList.value, 'is-invalid')) {
                        if (!isInArray(field.id, ['country', 'address2'])) {
                            addInvalidClass(field);

                            /**
                             * Adds invalid feedback text if the field ID does not contain 'dateofbirth'.
                             */
                            if (!validator.contains(field.id.toLowerCase(), 'dateofbirth')) {
                                addInvalidFeedbackText(field, "You can't leave this empty.");
                            }
                        }
                    }
                }
                /**
                 * isComplete is assigned false if the current field has the 'is-invalid' class.
                 */
                else if (validator.contains(field.classList.value, 'is-invalid')) {
                    $('#submitRegistrationForm').html('Register');
                    isComplete = false;
                }
            }

            /**
             * The registration data is stored in the database when isComplete is true.
             */
            if (isComplete) {
                function prepareUserObject() {
                    delete user['confirmPassword'];
                    user['country'] = $('#country')[0].value;
                    user['password'] = hash(user['password']).toString();
                }

                prepareUserObject();

                $.ajax({
                    url: ((getPageName() === 'panel') ? '../' : './') + 'assets/Register.php',
                    method: 'POST',
                    data: {userInput: JSON.stringify(user)},
                    success: function() {
                        $('#logInTab').tab('show');
                        $('#registrationForm')[0].reset();
                        $('#submitRegistrationForm').html('Register');
                    }
                });
            }
        }
    }, 250);
});

/*******************************************
 * 4.1. Log In & Registration Modal: Helpers
 */

/**
 * getInvalidFeedbackID: Returns the result of concatenating 'invalid' with a field ID.
 */
function getInvalidFeedbackID(fieldID) {
    return "invalid" + fieldID.charAt(0).toUpperCase() + fieldID.slice(1);
}

/**
 * addInvalidClass: Adds the 'is-invalid' class to a field.
 */
function addInvalidClass(field) {
    if ($(field).length !== 0) {
        $(field).addClass('is-invalid');
    }
}

/**
 * addInvalidFeedbackText: Adds invalid feedback text to a field.
 */
function addInvalidFeedbackText(field, text) {
    var invalidFeedbackID = '#' + getInvalidFeedbackID(field.id);

    if ($(invalidFeedbackID).length !== 0) {
        $(invalidFeedbackID).text(text);
    }
}

/**
 * addInvalidLoginText: Adds invalid login text.
 */
function addInvalidLoginText() {
    $('#invalidLogin').text("The e-mail address or password you entered is incorrect.");
}

/**
 * removeInvalidLoginText: Removes invalid login text.
 */
function removeInvalidLoginText() {
    $('#invalidLogin').text("");
}

/**
 * removeInvalidClass: Removes the 'is-invalid' class from a field.
 */
function removeInvalidClass(field) {
    if ($(field).length !== 0) {
        $(field).removeClass('is-invalid');
    }
}

/**
 * removeInvalidFeedbackText: Removes invalid feedback text from a field.
 */
function removeInvalidFeedbackText(field) {
    var invalidFeedbackID = '#' + getInvalidFeedbackID(field.id);

    if ($(invalidFeedbackID).length !== 0) {
        $(invalidFeedbackID).text("");
    }
}

/**
 * removeInvalidClassAndInvalidFeedbackText: Removes the 'is-invalid' class from a field
 * as well as the invalid feedback text.
 */
function removeInvalidClassAndInvalidFeedbackText(field) {
    removeInvalidClass(field);
    removeInvalidFeedbackText(field);
}

/**
 * hasSelectOptions: Returns true if all date of birth <select> elements have more than 1 option, otherwise false.
 */
function hasSelectOptions() {
    var IDs = ['dateOfBirthDay', 'dateOfBirthMonth', 'dateOfBirthYear'];

    for (var i = 0; i < IDs.length; i++) {
        if ($('#' + IDs[i] +'> option').length <= 1) {
            return false;
        }
    }

    return true;
}

/**
 * monthToNumber: Takes a month in the form of a string (e.g. "January") and returns the month in
 * the form of an integer. For example, "February" will return 2.
 */
function monthToNumber(month) {
    return "JanFebMarAprMayJunJulAugSepOctNovDec".indexOf(month.substring(0, 3)) / 3 + 1;
}

/**
 * Returns the number of days in a given month.
 */
function getDaysInMonth(month, year) {
    return new Date(year, month, 0).getDate();
}

/**
 * hash: Takes a string and returns it hashed using MD5.
 */
function hash(password) {
    return CryptoJS.MD5(password);
}

/******************
 * 5. Account Modal
 */

/**
 * Resets the edit profile button when the tab has changed.
 */
$('#profileTab').on('hidden.bs.tab', function() {
    wait().done(function() {
        (validator.contains($('#editProfile')[0].classList.value, 'btn-outline-success')) ? getElementById('cancelEdit').click() : false;
    })
});

/**
 * Resets the change password form when the tab has changed.
 */
$('#passwordTab').on('hidden.bs.tab', function() {
    wait().done(function() {
        resetForm(['currentPassword', 'password', 'confirmPassword'], 'changePasswordForm');
    });
});

/**
 * The user is redirected to the CMS panel page.
 */
$('#cmsTab').on('shown.bs.tab', function() {
    $(location).attr('href', ((getPageName() === 'panel') ? './panel.php' : './cms/panel.php'));
});

/**
 * Handles the edit and cancel profile button click events.
 */
$('#editProfile, #cancelEdit').on('click', function() {
    /**
     * Displays the 'cancel' and 'save changes' buttons.
     */
    if (validator.contains(this.classList.value, 'btn-outline-dark')) {
        editProfile(true);
    }
    else {
        var currentButtonID = this.id;
        var currentButtonHTML = $('#' + currentButtonID).html();
        var profileFieldID = registrationID.slice();
        var isSaveChanges = (this.id === 'editProfile');

        showSpinner(currentButtonID);

        /**
         * Out of the two buttons, the one that isn't clicked is hidden whilst processing the changes.
         */
        (isSaveChanges) ? $('#cancelEdit').attr('hidden', true) : $('#editProfile').attr('hidden', true);

        var isComplete;

        wait().done(function() {
            /**
             * The profile form fields are checked to see if they are all valid.
             */
            isComplete = hasNoInvalidInput(profileFieldID, false, [
                'dateOfBirthDay', 'dateOfBirthMonth', 'dateOfBirthYear', 'country'
            ]);

            /**
             * If the fields are all valid, the user's profile changes are saved or cancelled.
             */
            if (isComplete || !isSaveChanges) {
                (isSaveChanges) ? saveProfileChanges() : cancelProfileChanges();

                wait().done(function() {
                    /**
                     * The edit profile feature is disabled.
                     */
                    removeSpinner(currentButtonID, currentButtonHTML);
                    editProfile(false);

                    $('#editProfile').attr('hidden', false);
                })
            }
            else {
                removeSpinner(currentButtonID, currentButtonHTML);
                (isSaveChanges) ? $('#cancelEdit').attr('hidden', false) : false;
            }
        });
    }
});

/**
 * Updates a user's password.
 */
$('#updatePassword').on('click', function() {
    var updateButtonID = this.id;
    var updateButtonHTML = $(this).html();

    /**
     * The change password fields are checked to see if they are all valid.
     */
    var isComplete = hasNoInvalidInput(['currentPassword', 'password', 'confirmPassword'], true);

    if (isComplete) {
        showSpinner(updateButtonID);

        /**
         * The current password and new password is hashed.
         */
        var data = {
            'currentPassword': hash($('#currentPassword').val()).toString(),
            'newPassword': hash($('#password').val()).toString()
        };

        wait().done(function() {
            asyncWait = true;

            /**
             * The submitted password data is sent to UpdateUser.php. If the current password entered
             * does not match the user's account password, an error is displayed. Otherwise,
             * the form is reset.
             */
            $.ajax({
                url: ((getPageName() === 'panel') ? '../' : './') + 'database/UpdateUser.php',
                method: 'POST',
                data: {update: data, updatePassword: true},
                dataType: 'json',
                success: function (response) {
                    if (!response) {
                        addInvalidClass($('#currentPassword')[0]);
                        addInvalidFeedbackText($('#currentPassword')[0], "The password you entered is incorrect.");
                    }

                    $('#changePasswordForm')[0].reset();
                    removeSpinner('updatePassword', updateButtonHTML);

                    asyncWait = false;
                }
            });
        });
    }
});

/**
 * editProfile: Enables/disables the ability to edit a profile.
 */
function editProfile(boolean) {
    var fieldID = registrationID.slice();
    fieldID.push('country');

    /**
     * Enables or disables the profile fields.
     */
    for (var i = 0; i < fieldID.length; i++) {
        (/dateofbirth/i.test(fieldID[i]) || isInArray(fieldID[i], ['password', 'confirmPassword'])) ? false : $('#' + fieldID[i]).attr('disabled', !boolean);
    }

    /**
     * Displays or hides the save changes and cancel buttons.
     */
    $('#cancelEdit').attr('hidden', !boolean);
    $('#editProfile').removeClass((boolean) ? 'btn-outline-dark' : 'btn-outline-success')
        .addClass((boolean) ? 'btn-outline-success' : 'btn-outline-dark')
        .html('<i class="' + ((boolean) ? 'fa fa-save' : 'fa fa-edit') + '">' + '</i> ' +
            ((boolean) ? 'Save Changes' : 'Edit Profile'));
}

/**
 * saveProfileChanges: Updates the user's profile information.
 */
function saveProfileChanges() {
    asyncWait = true;
    var fieldID = registrationID.slice(), data = {};
    fieldID.push('country');

    /**
     * All input except the date of birth is retrieved.
     */
    for (var i = 0; i < fieldID.length; i++) {
        if (!/dateofbirth/i.test(fieldID[i]) && !/password/i.test(fieldID[i])) {
            data[fieldID[i]] = $('#' + fieldID[i]).val();
        }
    }

    /**
     * The input is stored in the database.
     */
    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'database/UpdateUser.php',
        method: 'POST',
        data: {update: data},
        dataType: 'json',
        success: function (response) {
            (response) ? asyncWait = false : (asyncWait = false, cancelProfileChanges());
        }
    });
}

/**
 * cancelProfileChanges: Reverts all changes made to the user's profile.
 */
function cancelProfileChanges() {
    asyncWait = true;

    /**
     * The user's account information is retrieved from the userInformation session variable
     * and set as the input for each profile field.
     */
    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'assets/Session.php',
        method: 'GET',
        data: {request: 'userInformation'},
        success: function (response) {
            var userInformation = JSON.parse(response);

            for (var key in userInformation) {
                var field = $('#' + key);
                if (!/dateofbirth/i.test(key)) {
                    $(field).val(userInformation[key]);
                    removeInvalidClassAndInvalidFeedbackText(field[0]);
                }
            }

            asyncWait = false;
        }
    });
}

/**
 * viewOrderHistory: Retrieves the HTML for a specific order and displays it.
 */
function viewOrderHistory(order) {
    var orderID = order.id;
    showSpinner(orderID);

    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'assets/OrderHistory.php',
        method: 'GET',
        data: {orderID: orderID},
        success: function(response) {
            $('#orderHistory').html(response);
        }
    });
}

/**
 * restoreOrderHistory: Reverts the order history tab content back to its initial state.
 */
function restoreOrderHistory(button) {
    showSpinner(button);

    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'assets/OrderHistory.php',
        method: 'GET',
        success: function(response) {
            $('#orderHistory').html(response);
        }
    });
}

/******************
 * 6. Product Modal
 */

/**
 * Restores the product modal back to its initial state.
 */
$('#productModal').on('hidden.bs.modal', function() {
    var restoreModal = function() {
        var quantity = $('#quantity'), format = $('#productFormat');

        /**
         * The quantity and format text is set as the text of the first child of each dropdown.
         */
        quantity.text($('#dropdownQuantity').children(":first").text());
        format.text($('#dropdownFormat').children(":first").text());

        /**
         * The product modal description, information and trailer text is removed, and the description tab
         * is set as the active tab.
         */
        setProductModal([], true);
        $('#descriptionTab').tab('show');

        currentProductID = undefined;
    };

    restoreModal();
});

/**
 * Handles quantity changes.
 */
$(document).on('click', '.quantity-option', function() {
    $('#productQuantity').text(this.innerHTML);
});

/**
 * Handles format changes.
 */
$(document).on('click', '.format-option', function() {
    var text = $('#productFormat').text();

    if (!$(this).hasClass('disabled')) {
        updateQuantity(this.innerHTML);
        $('#productFormat').text(this.innerHTML);
        $(this).text(text);
    }

    if (text.includes('Out of Stock') && !$(this).hasClass('disabled')) {
        $(this).addClass('disabled');
    }
});

/**
 * Adds an item to the cart.
 */
$(document).on('click', '.add-to-cart-btn, #modalAddToCart', function() {
    var button = this;
    var html = this.innerHTML, isCardButton = (this.id === 'cardAddToCart');
    var id = (isCardButton) ? $(this).closest('div[id]')[0].id : null;
    var hasSpinner = $(this).children().hasClass('fa-spin');
    var productID = (isCardButton) ? id : currentProductID;

    if (!hasSpinner) {
        showSpinner((id !== null) ? this : this.id);
        (id === null) ? $('#closeModalTop, #closeModal').hide() : $(this).addClass('text-center');

        /**
         * The product that the user has added to his/her cart is fetched.
         */
        $.ajax({
            url: ((getPageName() === 'panel') ? '../' : './') + 'database/FetchProduct.php',
            method: 'GET',
            data: {query: {_id: productID}},
            dataType: 'json',
            success: function (response) {
                /**
                 * The product in the products array is updated with the response.
                 */
                var productIndex = getProductIndex(productID, products);
                products[productIndex] = response;

                /**
                 * The following variables decide if the user selected the card or modal add to cart button, the quantity, and
                 * what format.
                 */
                var isBluRayCard = (parseInt(products[productIndex]['dvdQuantity']) === 0 && $(button).hasClass('add-to-cart-btn'));

                var format = (isCardButton) ? 'dvd' : $('#productFormat').text().split(" ")[0].toLowerCase();
                (isBluRayCard) ? format = 'blu-ray' : false;
                var quantity = parseInt((isCardButton) ?  '1' : $('#productQuantity').text());
                var quantityKey = (format.includes('dvd')) ? 'dvdQuantity' : 'bluRayQuantity';
                var productQuantity = parseInt(products[productIndex][quantityKey]);

                /**
                 * If the product has a DVD/Blu-ray quantity greater than 0, and the
                 * selected quantity is less than or equal to 5, the item is added to the user's cart.
                 */
                if (productQuantity > 0 && (quantity <= productQuantity && quantity <= 5)) {
                    /**
                     * The product quantity is reduced by the selected quantity.
                     */
                    products[productIndex][quantityKey] = (productQuantity - quantity).toString();

                    var update = {};
                    update[quantityKey] = products[productIndex][quantityKey];

                    /**
                     * The product is updated with a new quantity.
                     */
                    $.ajax({
                        url: ((getPageName() === 'panel') ? '../' : './') + 'database/UpdateProduct.php',
                        method: 'POST',
                        data: {_id: productID, product: update, single: true},
                        dataType: 'json',
                        success: function(response) {
                            /**
                             * The product is added to the user's cart.
                             */
                            if (response) {
                                $.ajax({
                                    url: ((getPageName() === 'panel') ? '../' : './') + 'database/UpdateCart.php',
                                    method: 'POST',
                                    data: {product: {_id: productID, format: format, quantity: parseInt(quantity)}, action: 'add'},
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response) {
                                            /**
                                             * The user's cart is fetched and the cart is displayed with the new item.
                                             */
                                            updateCart().done(function() {
                                                /**
                                                 * The product modal and card is reset.
                                                 */
                                                $('#closeModalTop, #closeModal').show();
                                                removeSpinner($(button), html);
                                                updateCard($('#' + productID).find('#cardAddToCart'), products[getProductIndex(productID, products)]);
                                                $('#productModal').modal('hide');

                                                setCartContent();
                                                animateShoppingCartButton();
                                            });
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
                else {
                    /**
                     * The product modal and card is reset if the selected quantity is not in stock.
                     */
                    $('#closeModalTop, #closeModal').show();
                    setProductModal(products[getProductIndex(productID, products)], false);
                    removeSpinner($(button), html);
                    updateCard($('#' + productID).find('#cardAddToCart'), products[getProductIndex(productID, products)]);
                }
            }
        });
    }

    /**
     * Displays an animation on the shopping cart icon.
     */
    var animateShoppingCartButton = function() {
        $('#shoppingCartButton').addClass('animated pulse product-added');

        setTimeout(function() {
            $('#shoppingCartButton').removeClass('animated pulse product-added');
        }, 800);
    };
});

/*************
 * 7. Products
 */

/**
 * displayProducts: Displays each product on the page.
 */
function displayProducts(products, max) {
    var html = '';

    if (products.length === 0) {
        html += '<div class="product-group">No products were found.</div>';
    }
    else {
        for (var i = 0; i < max; i++) {
            var inStock = (parseInt(products[i]['dvdQuantity']) > 0 || parseInt(products[i]['bluRayQuantity']) > 0);
            var bluRayOnly = (parseInt(products[i]['dvdQuantity']) === 0 && parseInt(products[i]['bluRayQuantity']) > 0);

            var addToCartButton = '<div class="col-7">' +
                ((inStock) ? 'Add to Cart' : 'Out of Stock') +
                '</div>' +
                '<div class="col-5 text-right">' +
                ((inStock && !bluRayOnly) ? '£' + products[i]['dvdPrice'] : (bluRayOnly) ? '£' + products[i]['bluRayPrice'] : '') +
                '</div>';

            html += '<div class="col-4 mb-3 card-container" id="' + products[i]['_id']['$oid'] + '">' +
                '<div class="card thumbnail">' +
                '<img class="card-img-top" src="./img/products/' + products[i]['cover'] + '">' +
                '<div class="card-body">' +
                '<div class="row">' +
                '<div class="col-12">' +
                '<h6 class="card-title">' + products[i]['title'] + '</h6>' +
                '</div></div></div>' +
                '<ul class="list-group list-group-flush">' +
                '<button type="button" class="list-group-item list-group-item-action btn view-product-btn view-product" data-toggle="modal" data-target="#productModal">' +
                'View Product' +
                '</button>' +
                '<button class="list-group-item list-group-item-action active ' + ((inStock) ? 'add-to-cart-btn' : 'out-of-stock-btn') + '" id="cardAddToCart">' +
                '<div class="row">'
                + addToCartButton +
                '</div></button></ul></div></div>';
        }
    }

    getElementById('products').innerHTML = html;
}

/**
 * The content of the product modal is set, and the product is tracked.
 */
$(document).on('click', '.view-product', function() {
    currentProductID = $(this).closest('div[id]')[0].id;
    var product = products[getProductIndex(currentProductID, products)];

    trackItem(currentProductID);
    setProductModal(product, false);
});

/**
 * The products trailer url is set.
 */
$('#productModal').on('shown.bs.modal', function() {
    var product = products[getProductIndex(currentProductID, products)];

    var url = 'http://www.youtube.com/embed/' + product['trailer'].split('?v=')[1] + '?hd=1&iv_load_policy=3';
    $('#productTrailer').attr('src', url);
});

/**
 * setProductModal: Displays the product modal description and information text.
 */
function setProductModal(product, unset) {
    var productInformationID = [
        'actors',
        'directors',
        'format',
        'language',
        'subtitles',
        'region',
        'aspectRatio',
        'numberOfDiscs',
        'dvdReleaseDate',
        'runTime'
    ];

    var productBodyID, i;

    /**
     * If unset is true, the description and information text is cleared.
     */
    if (unset) {
        productBodyID = ['productTitle', 'productBodyTitle', 'productPrice', 'productStock', 'navDescription'];

        for (i = 0; i < productInformationID.length; i++) {
            if (i < productBodyID.length) {
                $('#' + productBodyID[i]).text('');
            }

            $('#' + productInformationID[i]).text('');
        }

        $('#productImage').attr('src', '');
        $('#productTrailer').attr('src', '');

        getElementById('dropdownFormat').innerHTML = '';
    }
    else {
        productBodyID = {
            productTitle: product['title'] + ' (' + product['year'] + ')',
            productBodyTitle: product['title'] + ' (' + product['year'] + ')',
            productPrice: 'From £' + product['dvdPrice'],
            productStock: ((parseInt(product['dvdQuantity']) > 0 || parseInt(product['bluRayQuantity']) > 0) ? 'In Stock' : 'Out of Stock'),
            navDescription: product['description']
        };

        /**
         * The product's image is displayed.
         */
        $('#productImage').attr('src', ('./img/products/' + product['cover']));

        /**
         * Displays the product title, body title, price, stock and description.
         */
        for (var key in productBodyID) {
            $('#' + key).text(productBodyID[key]);
        }

        /**
         * Displays the product information.
         */
        for (i = 0; i < productInformationID.length; i++) {
            var text = product[productInformationID[i]];
            (productInformationID[i] === 'runTime') ? text += ' Minutes' : '';

            $('#' + productInformationID[i]).text(text);
        }

        /**
         * If the product is out of stock, the add to cart button is hidden.
         */
        (productBodyID['productStock'] === 'Out of Stock') ? $('#modalAddToCart').hide() : $('#modalAddToCart').show();

        /**
         * The format and quantity dropdown options are displayed.
         */
        setFormat();
        setQuantity();

        /**
         * setFormat: Displays the format dropdown button.
         */
        function setFormat() {
            var formatOption = '';

            if (parseInt(product['bluRayQuantity']) > 0) {
                var bluRayPrice = (parseFloat(product['bluRayPrice']) - parseFloat(product['dvdPrice'])).toFixed(2);
                formatOption += '<button class="dropdown-item format-option">Blu-ray (+ £' + bluRayPrice + ')</button>';
            }
            else {
                formatOption += '<button class="dropdown-item format-option disabled">Blu-ray (Out of Stock)</button>';
            }

            $('#productFormat').text((parseInt(product['dvdQuantity']) === 0) ? 'DVD (Out of Stock)' : 'DVD');

            getElementById('dropdownFormat').innerHTML = formatOption;
        }

        /**
         * setQuantity: Displays the quantity dropdown button.
         */
        function setQuantity() {
            var quantityOptions = '', maxQuantityOptions = (parseInt(product['dvdQuantity']) >= 5) ? 5 : parseInt(product['dvdQuantity']);

            if (maxQuantityOptions !== 0) {
                for (i = 0; i < maxQuantityOptions; i++) {
                    quantityOptions += '<button class="dropdown-item quantity-option">' + (i + 1) + '</button>';
                }

                getElementById('dropdownQuantity').innerHTML = quantityOptions;
            }
            else {
                getElementById('dropdownQuantity').innerHTML = '';
            }

            getElementById('productQuantity').innerHTML = (maxQuantityOptions > 0) ? 1 : 0;
        }
    }
}

/**
 * updateQuantity: Updates the quantity dropdown button with new quantity options.
 */
function updateQuantity(format) {
    var product = products[getProductIndex(currentProductID, products)];

    var maxQuantityOptions, quantityOptions = '';
    var productFormat = (format.includes('Blu-ray')) ? 'bluRayQuantity' : 'dvdQuantity';

    maxQuantityOptions = (parseInt(product[productFormat]) >= 5 ? 5 : parseInt(product[productFormat]));

    for (var i = 0; i < maxQuantityOptions; i++) {
        quantityOptions += '<button class="dropdown-item quantity-option">' + (i + 1) + '</button>';
    }

    getElementById('dropdownQuantity').innerHTML = quantityOptions;
    getElementById('productQuantity').innerHTML = 1;
}

/**
 * getProductIndex: Returns the index of a product in the products array.
 */
function getProductIndex(id, array) {
    for (var i = 0; i < array.length; i++) {
        if (array[i]['_id']['$oid'] === id) {
            return i;
        }
    }
}

/**
 * updateCard: Updates a product's card content.
 */
function updateCard(id, product) {
    var inStock = (parseInt(product['dvdQuantity']) > 0 || parseInt(product['bluRayQuantity']) > 0);
    var bluRayOnly = (parseInt(product['dvdQuantity']) === 0 && parseInt(product['bluRayQuantity']) > 0);

    var html = '<div class="row">' +
        '<div class="col-7">' +
        ((inStock) ? 'Add to Cart' : 'Out of Stock') +
        '</div>' +
        '<div class="col-5 text-right">' +
        ((inStock && !bluRayOnly) ? '£' + product['dvdPrice'] : (bluRayOnly) ? '£' + product['bluRayPrice'] : '') +
        '</div>';

    if (inStock) {
        ($(id).hasClass('out-of-stock-btn')) ? $(id).removeClass('out-of-stock-btn') : false;
        $(id).addClass('add-to-cart-btn');
    }
    else if (!inStock) {
        ($(id).hasClass('add-to-cart-btn')) ? $(id).removeClass('add-to-cart-btn') : false;
        $(id).addClass('out-of-stock-btn');
    }

    $(id).html(html);
}

/**
 * displayPagination: Displays pagination for the products.
 */
function displayPagination(array) {
    var numberOfPages = Math.ceil(array.length / 6);
    (numberOfPages === 0) ? numberOfPages = 1 : false;

    var html = '<ul class="pagination justify-content-center">' +
        '<li class="page-item products-page disabled">' +
        '<a class="page-link">Previous</a>' +
        '</li>';

    for (var i = 0; i < numberOfPages; i++) {
        html += '<li class="page-item products-page' +  ((i === 0) ? " active" : "") + '"><a class="page-link">' + (i + 1) + '</a></li>';

        if (i + 1 === numberOfPages) {
            html += '<li class="page-item products-page' + ((numberOfPages === 1) ? " disabled" : "") + '">' +
                '<a class="page-link">Next</a>' +
                '</li>';
        }
    }

    html += '</ul>';

    getElementById('productPagination').innerHTML = html;
}

/**
 * Handles click events for the product pagination.
 */
$(document).on('click', '.products-page', function() {
    var id = 'productPagination';
    var value = $(this).children()[0].text;
    var nextPage = parseInt(value);
    var numberOfPages = Math.ceil(products.length / 6);

    /**
     * If the user clicks a non-disabled previous or next button, nextPage is incremented or decremented.
     */
    if (!$(this).hasClass('disabled') && isInArray($(this).children()[0].text, ['Previous', 'Next'])) {
        nextPage = parseInt($('#' + id).find('.active').children()[0].text);
        (value === 'Previous') ? nextPage-- : nextPage++;
    }

    /**
     * If nextPage is less than or equal to numberOfPages, the button clicked is added the 'active' class.
     */
    if (nextPage <= numberOfPages && !$(this).hasClass('active')) {
        $('#' + id).find('.active').removeClass('active');

        /**
         * If the user clicked the previous or next button, the next numbered button is added the
         * 'active' class.
         */
        if (isInArray(value, ['Previous', 'Next'])) {
            $('#' + id + ', li>a:contains(' + nextPage + ')').parent().addClass('active');
        }
        else {
            $(this).addClass('active');
        }

        if (nextPage > 1) {
            /**
             * The previous button is enabled if nextPage is greater than 1.
             */
            $('#' + id).find('.disabled').removeClass('disabled');

            /**
             * If nextPage is equal to the number of pages, the next button is disabled.
             * Otherwise, it is enabled.
             */
            if (nextPage === numberOfPages) {
                (value === 'Next') ? $(this).addClass('disabled') : $(this).next().addClass('disabled');
            }
            else {
                $('#' + id + ', li>a:contains("Next")').parent().removeClass('disabled');
            }
        }
        else {
            /**
             * The previous and next buttons are disabled if nextPage is less than 1.
             */
            $('#' + id + ', li>a:contains("Previous")').parent().addClass('disabled');
            $('#' + id + ', li>a:contains("Next")').parent().removeClass('disabled');
        }

        /**
         * Assigns the products for the next page.
         */
        var next = getNextPage(filterBy(retrieveSelectedFilters(), true));

        /**
         * The products for the next page are displayed.
         */
        displayProducts(next, (next.length >= 6) ? 6 : next.length);

        /**
         * getNextPage: Returns the products for the next page.
         */
        function getNextPage(array) {
            return array.slice((6 * (nextPage - 1)), 6 * nextPage);
        }
    }

    $("html, body").animate({
        scrollTop: 0
    }, "slow");
});

/******************
 * 8. Shopping Cart
 */

/**
 * Prevents the shopping cart menu from closing when a click event occurs within the dropdown container.
 */
$("#shoppingCartMenu").on('click', function(event) {
    event.stopPropagation();
});

/**
 * Adds a style to the shopping cart button when a show event occurs and
 * removes the style when a hide event occurs.
 */
$('#shoppingCart').on({
    'show.bs.dropdown': function() {
        $('#shoppingCartButton').css({
            'background-color': '#f8f9fa',
            'border-color': '#f8f9fa',
            'color': '#343a40'
        });
    },
    'hide.bs.dropdown': function() {
        $('#shoppingCartButton').removeAttr('style');
    }
});

/**
 * Redirects the user to the checkout page.
 */
$('#checkout').on('click', function() {
    $(location).attr('href', './checkout.php');
});

/**
 * setCartContent: Updates the content of the user's shopping cart.
 */
function setCartContent() {
    $('#cartContent').empty();

    var html = '';
    var numberOfItems = 0;
    var totalCost = 0;

    /**
     * If the user's cart has at least one product in it, the shopping cart table is displayed.
     */
    if (cart.length > 0) {
        html += '<div class="dropdown-divider dropdown-divider-margin-a"></div>' +
            '<table class="table table-hover shopping-cart-table">' +
            '<thead>' +
            '<tr class="shopping-cart-row">' +
            '<th scope="col" class="shopping-cart-row">Product</th>' +
            '<th scope="col" class="shopping-cart-row">Format</th>' +
            '<th scope="col" class="shopping-cart-row">Quantity</th>' +
            '<th scope="col" class="shopping-cart-row">Price</th>' +
            '<th scope="col" class="shopping-cart-row">Delete</th>' +
            '</tr></thead><tbody>';

        /**
         * A row is displayed for each product and the products format, e.g.
         * 1 x Product A [DVD] and 1 x Product A [Blu-ray] are 2 seperate rows.
         */
        for (var i = 0; i < cart.length; i++) {
            var id = cart[i]['_id']['$oid'];
            var title = cart[i]['title'] + ' (' + cart[i]['year'] + ')';
            var price = [cart[i]['dvdPrice'], cart[i]['bluRayPrice']];
            var quantity = [parseInt(cart[i]['dvdQuantity']), parseInt(cart[i]['bluRayQuantity'])];

            for (var j = 0; j < quantity.length; j++) {
                var isDVD = (j === 0);

                if (quantity[j] > 0) {
                    totalCost += (quantity[j] * parseFloat(price[j]));
                    numberOfItems++;

                    html += '<tr id="' + id + '"> <th scope="row" class="shopping-cart-row">' + title + '</th>' +
                        '<td valign="middle" class="shopping-cart-row">' + ((isDVD) ? 'DVD' : 'Blu-Ray') + '</td>' +
                        '<td valign="middle" class="shopping-cart-row">' + quantity[j] + '</td>' +
                        '<td valign="middle" class="shopping-cart-row">' + '£' + price[j] + '</td>' +
                        '<td valign="middle" align="center" class="shopping-cart-row">' +
                        '<button type="button" class="close delete-button" onclick="removeFromCart(this)">' +
                        '<span>&times;</span>' +
                        '</button></td></tr>';
                }
            }
        }

        html += '</tbody></table>';

        /**
         * The shopping cart price breakdown is calculated.
         */
        var priceBreakdown = {
            'Subtotal (excl. VAT):': '£' + toFixed(totalCost, 2),
            'VAT (20%):': '£' + toFixed((0.20 * totalCost), 2),
            'Post & Packaging:': 'Free',
            'Grand Total:': '£' + toFixed((totalCost + (0.20 * totalCost)), 2)
        };

        html += '<div class="dropdown-divider dropdown-divider-margin-b"></div>';

        for (var key in priceBreakdown) {
            html += '<div class="row m-0"><div class="col pl-0">' +
                '<h6 class="dropdown-header header-sm">' + key + '</h6>' +
                '</div><div class="col text-right pr-0">' +
                '<h6 class="dropdown-header header-rhs header-sm">' + priceBreakdown[key] + '</h6>' +
                '</div></div>';
        }
    }
    else {
        html += '<div class="dropdown-divider"></div>' +
            '<div class="col">' +
            '<div class="empty-cart">Your shopping cart is empty!</div>' +
            '</div>';
    }

    /**
     * The number of items in the user's cart as well as the user's shopping cart is displayed.
     */
    $('#cartQuantity').text(numberOfItems);
    $('#cartContent').append(html);
}

/**
 * removeFromCart: Removes a product from the user's cart.
 */
function removeFromCart(item) {
    var productID = $(item).closest('tr[id]')[0].id;
    var format = $($(item).parent().parent().children()[1]).text();
    var quantity = $($(item).parent().parent().children()[2]).text();

    /**
     * If isRemoving is false, the product is removed from the user's cart.
     */
    if (!isRemoving) {
        isRemoving = true;

        $.ajax({
            url: ((getPageName() === 'panel') ? '../' : './') + 'database/UpdateCart.php',
            method: 'POST',
            data: {
                product: {_id: productID, quantity: parseInt(quantity), format: format.toLowerCase()}, action: 'remove'
            },
            dataType: 'json',
            success: function (response) {
                /**
                 * The cart and products array are updated.
                 */
                cart = ((response[0] === null) ? [] : response[0]);
                products = response[1];

                var dropdownFilters = {
                    'Latest': 'latest',
                    'Price: Low to High': 'lowest',
                    'Price: High to Low': 'highest'
                };

                /**
                 * The products are sorted by whichever dropdown filter has been selected, and the filter checkboxes
                 * are reset.
                 */
                sortBy(dropdownFilters[$('#dropdownFilter').text()], false);
                resetCheckboxes();

                /**
                 * The shopping cart content is updated.
                 */
                setCartContent();

                isRemoving = false;
            }
        });
    }
}

/**
 * updateCart: Fetches and stores a user's cart in the cart array.
 */
function updateCart() {
    var deferred = new $.Deferred();

    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'database/FetchCart.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            cart = ((response === null) ? [] : response);

            deferred.resolve(true);

        }
    });

    return deferred.promise();
}

/************
 * 9. Filters
 */

/**************************
 * 9.1. Filters: Checkboxes
 */

/**
 * Handles filter checkbox click events.
 */
$('.product-filter').on('click', function() {
    var checkbox = $(this).find('input[type=checkbox]');
    var isPriceFilter = (checkbox[0].id.includes('lessThan')) || (checkbox[0].id.includes('moreThan'));

    /**
     * Checks the checkbox if it isn't already checked.
     * If the checkbox is a price filter, the other price filters are unchecked.
     */
    setCheckbox(checkbox, !(checkbox).is(':checked'));
    (isPriceFilter) ? resetPriceCheckboxes(checkbox) : false;

    /**
     * The selected filters are retrieved.
     */
    var selectedFilters = retrieveSelectedFilters();

    /**
     * The products are filtered by the selected filters.
     */
    filterBy(selectedFilters);
});

/**
 * retrieveSelectedFilters: Returns an object containing all selected filters.
 */
function retrieveSelectedFilters() {
    var selectedFilters = {};

    $('.product-filter > input[type=checkbox]').filter(':checked').each(function() {
        selectedFilters[this.id] = this.value;
    });

    return selectedFilters;
}

/**
 * resetPriceCheckboxes: Sets all price checkboxes, except for a given ID, to false.
 */
function resetPriceCheckboxes(id) {
    $('#priceFilter > div > input[type=checkbox]').filter(':checked').each(function() {
        if (id === undefined) {
            setCheckbox($(this), false);
        }
        else if (id[0].id !== this.id && id !== undefined) {
            setCheckbox($(this), false);
        }
    });
}

/**
 * resetCheckboxes: Sets all checkboxes to false.
 */
function resetCheckboxes() {
    $('.product-filter > input[type=checkbox]').filter(':checked').each(function() {
        setCheckbox(this, false);
    });
}

/**
 * setCheckbox: Sets a checkbox to checked or unchecked.
 */
function setCheckbox(id, boolean) {
    $(id).prop('checked', boolean);
}

/**
 * filterBy: Filters products by the selected filters.
 */
function filterBy(selectedFilters, isReturned) {
    var dropdownFilters = {
        'Latest': 'latest',
        'Price: Low to High': 'lowest',
        'Price: High to Low': 'highest'
    };

    /**
     * If the selectedFilters object is empty, it will be sorted by the dropdown filter, e.g. latest.
     */
    if ($.isEmptyObject(selectedFilters)) {
        if (isReturned) {
            return sortBy(dropdownFilters[$('#dropdownFilter').text()], true);
        }
        else {
            sortBy(dropdownFilters[$('#dropdownFilter').text()]);
        }
    }
    else {
        /**
         * The products are sorted by the dropdown filter and assigned to sortedProducts.
         */
        var sortedProducts = sortBy(dropdownFilters[$('#dropdownFilter').text()], true);
        var hasFilteredByPrice = (Object.keys(selectedFilters)[0].includes('lessThan') || Object.keys(selectedFilters)[0].includes('moreThan'));

        var priceRange = {
            'lessThan5': [0, 5],
            'lessThan10': [5, 10],
            'lessThan15': [10, 15],
            'lessThan20': [15, 20],
            'moreThan20': [20, null]
        };

        /**
         * The products are sorted by price.
         */
        if (hasFilteredByPrice) {
            var isMoreThanFilter = (Object.keys(selectedFilters)[0].includes('moreThan'));

            var min = priceRange[Object.keys(selectedFilters)[0]][0];
            var max = (!isMoreThanFilter) ? priceRange[Object.keys(selectedFilters)[0]][1] : null;

            var tempArray = [];

            for (var i = 0; i < sortedProducts.length; i++) {
                var dvdPrice = parseInt(sortedProducts[i]['dvdPrice']);

                if ((dvdPrice >= min && dvdPrice < max) && !isMoreThanFilter) {
                    tempArray.push(sortedProducts[i]);
                }
                else if (dvdPrice >= min && isMoreThanFilter) {
                    tempArray.push(sortedProducts[i]);
                }
            }

            sortedProducts = tempArray;
        }

        var years = [];

        for (var key in selectedFilters) {
            if (key.includes('year')) {
                years.push(parseInt(selectedFilters[key]));
            }
        }

        /**
         * If the years array is not empty, the products are sorted by decade.
         */
        if (years.length > 0) {
            var tempArray = sortedProducts.slice();

            for (var i = 0; i < tempArray.length; i++) {
                var productYear = parseInt(tempArray[i]['year']);
                var index = sortedProducts.indexOf(tempArray[i]);

                var flag = false;

                for (var element in years) {
                    if (productYear >= years[element] && productYear <= (years[element] + 9)) {
                        flag = true;
                    }
                }

                /**
                 * Removes a product from tempArray if the product's year is not within any of
                 * the selected decades.
                 */
                if (!flag) {
                    sortedProducts.splice(index, 1);
                }
            }
        }

        if (isReturned) {
            return sortedProducts;
        }
        else {
            if (sortedProducts.length === 0) {
                $('#results').text('No products were found. Try using a different filter!');
            }

            displayProducts(sortedProducts, (sortedProducts.length >= 6) ? 6 : sortedProducts.length);
            displayPagination(sortedProducts);
        }
    }
}

/************************
 * 9.2. Filters: Dropdown
 */

/**
 * Handles dropdown filter changes.
 */
$('#dropdownFilterOptions button').on('click', function() {
    $('#dropdownFilter').text(this.innerHTML);

    /**
     * The products are sorted by whichever filter is selected, and the filter checkboxes are reset.
     */
    sortBy(this.id);
    resetCheckboxes();
});

/**
 * sortBy: Sorts all products by latest or price.
 */
function sortBy(type, isReturned) {
    var isEmptySearch = validator.isEmpty($('#mainSearch').val().trim());
    var array = (isEmptySearch) ? products.slice() : search(products, $('#mainSearch').val().trim().toLowerCase(), 'products', isEmptySearch);

    if (type === 'latest') {
        $('#results').text((isEmptySearch) ? 'Latest Products' : 'Results (' + array.length + ')');

        if (isReturned) {
            return array;
        }
        else {
            displayProducts(array, (array.length >= 6) ? 6 : array.length);
            displayPagination(array);
        }
    }
    else {
        var isLowestToHighest = (type === 'lowest');
        var emptySearchText = (isEmptySearch && type === 'lowest') ? 'Price: Low to High' : 'Price: High to Low';

        /**
         * Sorts the product array by DVD price in descending order.
         */
        array.sort(function (a, b) {
            return a.dvdPrice - b.dvdPrice;
        });

        $('#results').text((isEmptySearch) ? emptySearchText : 'Results (' + array.length + ') - ' + 'Price: ' + ((isLowestToHighest) ? 'Low to High' : 'High to Low'));

        /**
         * The array is reversed if sorting by price from highest to lowest.
         */
        (!isLowestToHighest) ? array.reverse() : false;

        if (isReturned) {
            return array;
        }
        else {
            displayProducts(array, (array.length >= 6) ? 6 : array.length);
            displayPagination(array);
        }
    }
}

/**************
 * 10. Checkout
 */

/**
 * Shows the next available tab and updates the next tab button.
 */
$('#nextCheckoutTab').on({
    'click': function () {
        var currentTabIndex = getCurrentTabIndex(), hasNextTab = (currentTabIndex + 1 < checkoutID.length);
        var validForms = true;
        var validPayment = true;

        /**
         * Validates the shipping and billing forms.
         */
        var validateForms = function() {
            var shippingForm = shippingAndBillingID.slice(0, shippingAndBillingID.length / 2);
            var billingForm = shippingAndBillingID.slice(shippingAndBillingID.length / 2, shippingAndBillingID.length);

            validForms = (hasNoInvalidInput(shippingForm) && hasNoInvalidInput(billingForm));

            (validForms) ? enableNextTab() : false;
        };

        /**
         * Validates the payment form.
         */
        var validatePayment = function() {
            validPayment = hasNoInvalidInput(paymentID);

            (validPayment) ? enableNextTab() : false;
        };

        (currentTabIndex + 1 === 2) ? validateForms() : false;
        (currentTabIndex + 1 === 3) ? validatePayment() : false;
        (currentTabIndex + 1 === 4) ? processOrder(this) : false;

        /**
         * Updates the next tab button.
         */
        $(this).trigger('update');

        /**
         * Transitions the user to the next tab when clicking the next tab button.
         */
        (hasNextTab && validForms && validPayment) ? $('#' + checkoutID[currentTabIndex + 1]).tab('show') : false;
    },
    'update': function() {
        var currentTabIndex = getCurrentTabIndex();
        console.log(getCurrentTabIndex());

        /**
         * The button text for the next tab button is changed.
         */
        if (currentTabIndex > 0) {
            if (currentTabIndex < checkoutID.length - 1) {
                $(this).text('Continue');
            }
            else {
                if ($(this).text() === 'Continue') {
                    $(this).text('Place Order');
                }
                else if ($(this).text() === 'Place Order') {
                    showSpinner(this.id);
                }
            }
        }
        else {
            $(this).text('Checkout');
        }
    }
});

/*******************************
 * 10.1. Checkout: Shopping Cart
 */

/**
 * Redirects the user to the homepage.
 */
$('#continueShopping').on('click', function() {
    $(location).attr('href', './');
});

/************************************
 * 10.2. Checkout: Shipping & Billing
 */

/**
 * Handles the 'same as shipping' checkbox click event.
 */
$('#sameAsShipping').on({
    'click': function (e) {
        /**
         * Disables the billing form fields if the shipping fields are valid.
         */
        var disableBilling = function () {
            var shippingForm = shippingAndBillingID.slice(0, shippingAndBillingID.length / 2);

            if (hasNoInvalidInput(shippingForm)) {
                for (var i = shippingAndBillingID.length / 2; i < shippingAndBillingID.length; i++) {
                    var field = $('#' + shippingAndBillingID[i]);

                    $(field).prop('disabled', true);
                    removeInvalidClassAndInvalidFeedbackText(field[0]);
                }

                $('#sameAsShipping').trigger('update');

                return;
            }

            e.preventDefault();
        };

        /**
         * Enables the billing form fields.
         */
        var enableBilling = function () {
            for (var i = shippingAndBillingID.length / 2; i < shippingAndBillingID.length; i++) {
                $('#' + shippingAndBillingID[i]).prop('disabled', false);
            }

            $('#billingForm')[0].reset();

            disableNextTabs();
        };

        ($(this).is(':checked')) ? disableBilling() : enableBilling();
    },
    /**
     * The billing form fields are set the same values as the shipping form fields.
     */
    'update': function() {
        for (var i = 0, j = shippingAndBillingID.length / 2; i < shippingAndBillingID.length / 2; i++, j++) {
            var shippingField = $('#' + shippingAndBillingID[i]);
            var billingField = $('#' + shippingAndBillingID[j]);

            (billingField.val() !== shippingField) ? $(billingField).val(shippingField.val()) : false;
        }
    },
    /**
     * The billing form fields are reset.
     */
    'reset': function() {
        if ($(this).is(':checked')) {
            $(this).prop('checked', false);

            for (var i = shippingAndBillingID.length / 2; i < shippingAndBillingID.length; i++) {
                $('#' + shippingAndBillingID[i]).prop('disabled', false);
            }

            $('#billingForm')[0].reset();

            disableNextTabs();
        }
    }
});

/**************************************
 * 10.3. Checkout: Review & Place Order
 */

/**
 * populateReviewAndPlaceOrder: Sets the content of the review & place order tab.
 */
function populateReviewAndPlaceOrder() {
    var shippingInformation = retrieveShippingInformation();
    var billingInformation = ($('#sameAsShipping').is(':checked')) ? true : retrieveBillingInformation();
    var keys = ['firstName', 'lastName', 'mobileNumber', 'address1', 'address2', 'townOrCity', 'county', 'country', 'postCode'];

    var paymentCardNumber = $('#paymentCardNumber').val();

    /**
     * The shipping and billing information is cleared.
     */
    clearShippingAndBillingInformation();

    var isEmptyCounty = validator.isEmpty(shippingInformation[keys[6]].trim());
    var isEmptyAddress2 = validator.isEmpty(shippingInformation[keys[4]].trim());

    var shipping = [
        {'reviewShippingName': [shippingInformation[keys[0]] + " " + shippingInformation[keys[1]]]},
        {'reviewShippingAddress1': [shippingInformation[keys[3]]]},
        {'reviewShippingAddress2': [(isEmptyAddress2) ? "" : shippingInformation[keys[4]]]},
        {'reviewShippingTownOrCity': [shippingInformation[keys[5]]]},
        {'reviewShippingCountyAndPostCode': [isEmptyCounty ? [shippingInformation[keys[8]]] : shippingInformation[keys[6]] + ", " + shippingInformation[keys[8]]]},
        {'reviewShippingCountry': [shippingInformation[keys[7]]]}
    ];

    /**
     * The text for each ID in shipping is set.
     */
    for (var shippingID in shipping) {
        var key = Object.keys(shipping[shippingID])[0];
        var value = shipping[shippingID][key][0];

        $('#' + key).text(value);
    }

    if (typeof billingInformation !== 'object') {
        $('#reviewBillingSameAsShipping').text('Same as shipping address');
    }
    /**
     * If billingInformation is not of type object, the text for each billing ID is set.
     */
    else {
        isEmptyCounty = validator.isEmpty(billingInformation[keys[6]].trim());
        isEmptyAddress2 = validator.isEmpty(billingInformation[keys[4]].trim());

        var billing = [
            {'reviewBillingName': [billingInformation[keys[0]] + " " + billingInformation[keys[1]]]},
            {'reviewBillingAddress1': [billingInformation[keys[3]]]},
            {'reviewBillingAddress2': [(isEmptyAddress2) ? "" : billingInformation[keys[4]]]},
            {'reviewBillingTownOrCity': [billingInformation[keys[5]]]},
            {'reviewBillingCountyAndPostCode': [isEmptyCounty ? [billingInformation[keys[8]]] : billingInformation[keys[6]] + ", " + billingInformation[keys[8]]]},
            {'reviewBillingCountry': [billingInformation[keys[7]]]}
        ];

        for (var billingID in billing) {
            var key = Object.keys(billing[billingID])[0];
            var value = billing[billingID][key][0];

            $('#' + key).text(value);
        }
    }

    /**
     * The last 4 digits of the user's card number is displayed.
     */
    $('#billingCardNumber').text("Card ending in " + paymentCardNumber.substring(paymentCardNumber.length - 4));
}

/***************************************
 * 10.3.1. Review & Place Order: Helpers
 */

/**
 * retrieveShippingInformation: Returns the user's shipping information.
 */
function retrieveShippingInformation() {
    var shippingForm = shippingAndBillingID.slice(0, shippingAndBillingID.length / 2);
    var keys = ['firstName', 'lastName', 'mobileNumber', 'address1', 'address2', 'townOrCity', 'county', 'country', 'postCode'];
    var tempObject = {};

    for (var i = 0; i < shippingForm.length; i++) {
        tempObject[keys[i]] = $('#' + shippingForm[i]).val().trim();
    }

    return tempObject;
}

/**
 * retrieveBillingInformation: Returns the user's billing information.
 */
function retrieveBillingInformation() {
    var billingForm = shippingAndBillingID.slice(shippingAndBillingID.length / 2, shippingAndBillingID.length);
    var keys = ['firstName', 'lastName', 'mobileNumber', 'address1', 'address2', 'townOrCity', 'county', 'country', 'postCode'];
    var tempObject = {};

    for (var i = 0; i < billingForm.length; i++) {
        tempObject[keys[i]] = $('#' + billingForm[i]).val().trim();
    }

    return tempObject;
}

/****************************
 * 10.4. Checkout: Submission
 */

/**
 * processOrder: Retrieves order information and stores it in the database.
 */
function processOrder() {
    var orderInformation = {
        shipping: retrieveShippingInformation(),
        billing: retrieveBillingInformation(),
        delivery: 'FREE Next Day Delivery',
        summary: {}
    };

    orderInformation['billing']['cardNumber'] = $('#paymentCardNumber').val().split(' - ').pop();
    orderInformation['billing']['sameAsShipping'] = $('#sameAsShipping').is(':checked');

    /**
     * The order is stored in the database.
     */
    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'database/AddOrder.php',
        method: 'POST',
        data: orderInformation,
        dataType: 'json',
        success: function(response) {
            var orderNumber = response['orderNumber'];

            /**
             * The confirmation page is retrieved and displayed on the page.
             */
            $.ajax({
                url: './assets/OrderConfirmation.php',
                method: 'GET',
                success: function(response) {
                    $('#checkoutSteps').remove();
                    $('#nextCheckoutTab').remove();
                    $('#navReviewAndPlaceOrder').html(response);
                    $('#orderNumber').text(orderNumber);
                }
            });
        }
    });
}

/*************************
 * 10.5. Checkout: Helpers
 */

/**
 * enableNextTab: Enables the next checkout tab.
 */
function enableNextTab() {
    var currentTabIndex = getCurrentTabIndex();

    $('#' + checkoutID[currentTabIndex + 1]).removeClass('disabled');
}

/**
 * disableNextTabs: Disables all tabs after the current tab.
 */
function disableNextTabs() {
    for (var i = getCurrentTabIndex() + 1; i < checkoutID.length; i++) {
        var tab = $('#' + checkoutID[i])[0];

        if (!validator.contains(tab.classList.value, 'disabled')) {
            $(tab).removeClass('active-step');

            $(tab).addClass('inactive-step disabled');
        }
    }
}

/**
 * getCurrentTabIndex: Returns the current tabs index.
 */
function getCurrentTabIndex() {
    for (var i = 0; i < checkoutID.length; i++) {
        var ID = checkoutID[i].split("Tab")[0];
        ID = ID.charAt(0).toUpperCase() + ID.slice(1);

        var tab = $('#nav' + ID)[0];

        if (validator.contains(tab.classList.value, 'active')) {
            return i;
        }
    }
}

/**
 * clearShippingAndBillingInformation: Resets the shipping and billing information in the review & place order tab.
 */
function clearShippingAndBillingInformation() {
    var ID = ['Name', 'Address1', 'Address2', 'TownOrCity', 'CountyAndPostCode', 'Country'];
    $('#reviewBillingSameAsShipping').text("");

    for (var i = 0; i < ID.length; i++) {
        $('#reviewShipping' + ID[i]).text("");
        $('#reviewBilling' + ID[i]).text("");
    }
}

/*********
 * 11. CMS
 */

/**
 * Adds the 'inactive-step' class to tab when hidden.
 */
$('a[data-toggle="tab"]').on('hide.bs.tab', function() {
    var step = this;

    if (getPageName() === 'panel') {
        if (validator.contains(step.classList.value, 'active-step')) {
            $(step).removeClass('active-step');
            $(step).addClass('inactive-step');
        }
    }
});

/********************
 * 11.1. CMS: Add Tab
 */

/**
 * Resets the add product tab by clearing the form field and removing all fields with the is-invalid class.
 */
$('#addProductTab').on('hidden.bs.tab', function() {
    $('#addProductForm')[0].reset();
    $('#fileName').text("Choose a cover...");

    for (var i = 0; i < productID.length; i++) {
        if ($('#' + productID[i]).hasClass('is-invalid')) {
            removeInvalidClass($('#' + productID[i]));
        }
    }
});

/**
 * Handles the add product button click event.
 */
$('#addProduct').on('click', function() {
    var currentButtonID = this.id;
    var currentButtonHTML = $('#' + currentButtonID).html();

    showSpinner(currentButtonID);

    var formData = new FormData();
    var isComplete = hasNoInvalidInput(productID, false, [], true);

    /**
     * If the add product form field is complete, the user input is appended to formData.
     */
    if (isComplete) {
        for (var i = 0; i < productID.length; i++) {
            var field = $('#' + productID[i]);

            formData.append(field[0].id, field.val());
        }

        /**
         * The product cover is also appended to formData.
         */
        formData.append('cover', $('#cover').prop('files')[0]);

        asyncWait = true;

        /**
         * The product is added to the database.
         */
        $.ajax({
            url: ((getPageName() === 'panel') ? '../' : './') + 'database/AddProduct.php',
            method: 'POST',
            contentType: false,
            processData: false,
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response) {
                    removeSpinner(currentButtonID, currentButtonHTML);

                    /**
                     * The add product form is reset back to its initial state.
                     */
                    $('#addProductForm')[0].reset();
                    $('#fileName').text("Choose a cover...");

                    asyncWait = false;
                }
            }
        });
    }
    else {
        removeSpinner(currentButtonID, currentButtonHTML);
    }

    return false;
});

/***********************
 * 11.2. CMS: Remove Tab
 */

/**
 * Resets the remove product recently added text and  when the tab is hidden.
 */
$('#removeProductTab').on('hide.bs.tab', function() {
    $('#recentlyAddedTitle').text('Recently Added');
    $('#productSearch').val('');
});

/**
 * Displays all of the products for the remove product tab.
 */
$('a[href="#navRemoveProduct"]').on('click', function() {
    var id = 'recentlyAdded';
    $('#recentlyAddedTitle').text('Recently Added');
    getElementById(id).innerHTML = '';
    getElementById('removeProductPagination').innerHTML = '';
    $('#productSearch').val('');

    showSpinner(id + 'Spinner');

    /**
     * The products are fetched from the database.
     */
    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'database/FetchProduct.php',
        method: 'GET',
        data: {sort: true, order: -1, limit: 0},
        dataType: 'json',
        success: function (response) {
            products = response;
            removeSpinner(id + 'Spinner', '');

            $('#recentlyAddedTitle').text('Recently Added (' + response.length + ')');
            getElementById(id).innerHTML = productList(response, (response.length > 5) ? 5 : response.length);

            displayCMSPagination(response, 'removeProductPagination');
        }
    });
});

/***********************************
 * 11.2.1 Remove Tab: Remove Product
 */

/**
 * Handles the remove product button click event.
 */
$(document).on('click', '.remove-product', function() {
    var id = 'recentlyAdded';
    getElementById(id).innerHTML = '';
    getElementById('removeProductPagination').innerHTML = '';
    $('#recentlyAddedTitle').text('Recently Added');
    $('#productSearch').val('');

    showSpinner(id + 'Spinner');

    /**
     * Retrieves the product ID.
     */
    var productID = $(this).closest('div[id]')[0].id;

    /**
     * The product is removed from the database.
     */
    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'database/RemoveProduct.php',
        method: 'POST',
        data: {query: {_id: productID}},
        dataType: 'json',
        success: function() {
            /**
             * All products are fetched from the database and displayed on the page.
             */
            $.ajax({
                url: ((getPageName() === 'panel') ? '../' : './') + 'database/FetchProduct.php',
                method: 'GET',
                data: {sort: true, order: -1, limit: 0},
                dataType: 'json',
                success: function(response) {
                    products = response;
                    removeSpinner(id + 'Spinner', '');

                    $('#productSearch').val('');
                    $('#recentlyAddedTitle').text('Recently Added (' + response.length + ')');
                    getElementById(id).innerHTML = productList(response, (response.length > 5) ? 5 : response.length);
                    displayCMSPagination(products, 'removeProductPagination');
                }
            });
        }
    });
});

/**
 * productList: Returns a list of products for the remove product tab.
 */
function productList(array, max) {
    var html = '';

    if (array.length === 0) {
        html += '<div>No products were found.</div>';
    }
    else {
        for (var i = 0; i < max; i++) {
            html += '<div class="row' + ((i + 1 === max) ? "" : " mb-3") + '" id="' + array[i]['_id']['$oid'] + '">' +
                '<div class="col">' +
                '<div class="card">' +
                '<div class="card-body">' +
                '<div class="row">' +
                '<div class="col recently-added-title">' +
                array[i]['title'] + ' (' + array[i]['year'] + ')' +
                '</div>' +
                '<div class="row mr-0 text-right">' +
                '<div class="col edit-product-btn">' +
                '<button class="btn btn-outline-success btn-sm edit-product" data-toggle="modal" data-target="#editProductModal">' +
                '<i class="fa fa-pencil"></i>' +
                ' Edit' +
                '</button>' +
                '</div>' +
                '<div class="col">' +
                '<button class="btn btn-outline-danger btn-sm remove-product">' +
                '<i class="fa fa-trash"></i>' +
                ' Remove' +
                '</button>' +
                '</div></div></div></div></div></div></div>';
        }
    }

    return html;
}

/**********************************
 * 11.2.2. Remove Tab: Edit Product
 */

/**
 * Removes the product's cover when the edit product modal is closed.
 */
$('#editProductModal').on('hide.bs.modal', function() {
    $('#productImage').attr('src', '');
});

/**
 * Handles the edit product button click event.
 */
$(document).on('click', '.edit-product', function() {
    var productID = $(this).closest('div[id]')[0].id;
    var product;

    /**
     * The products array is iterated over until the product is found.
     */
    for (var i = 0; i < products.length; i++) {
        if (products[i]['_id']['$oid'] === productID) {
            product = products[i];

            break;
        }
    }

    $('#editProductModalTitle').text(product['title'] + " (" + product['year'] + ")");

    /**
     * The product information is displayed on the modal.
     */
    for (var key in product) {
        if (!isInArray(key, ['_id', 'dateCreated'])) {
            if (key === 'cover') {
                var text = (product[key] === null) ? 'Choose a cover...' : product[key];

                $('#edit' + removeFromString('fileName', '', false)).text(text);

                continue;
            }

            $('#edit' + removeFromString(key, '', false)).val(product[key]);
        }
    }

    $('#editProductID').text(productID);
    (product['cover'] !== null) ? $('#productImage').attr('src', ('../img/products/' + product['cover'])) : false;
});

/**
 * Handles the save changes button click event.
 */
$('#updateProduct').on('click', function() {
    var currentButtonID = this.id;
    var currentButtonHTML = $('#' + currentButtonID).html();

    showSpinner(currentButtonID);

    var formData = new FormData();

    /**
     * The user input is appended to formData.
     */
    for (var i = 0; i < productID.length; i++) {
        var field = $('#edit' + removeFromString(productID[i], '', false));

        if (field[0].id === 'editCover' && field.val().length < 1) {
            if ($('#editFileName').text().length > 0 && $('#editFileName').text() !== 'Choose a cover...') {
                formData.append(removeFromString(field[0].id, 'edit', true), $('#editFileName').text());
            }

            continue;
        }

        formData.append(removeFromString(field[0].id, 'edit', true), field.val());
    }

    /**
     * The product cover is also appended to formData if set.
     */
    if ($('#editCover').prop('files').length > 0) {
        formData.append('cover', $('#editCover').prop('files')[0]);
    }

    formData.append('_id', $('#editProductID').text());

    asyncWait = true;

    /**
     * The product is updated in the database.
     */
    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'database/UpdateProduct.php',
        method: 'POST',
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response) {
                removeSpinner(currentButtonID, currentButtonHTML);

                /**
                 * The edit product form field is reset.
                 */
                $('#editProductForm')[0].reset();
                $('#editFileName').text("");
                $('#editProductID').text("");
                $('#productImage').attr('src', '');

                $('#editProductModal').modal('hide');

                /**
                 * The remove product tab is clicked so that the products are fetched.
                 */
                getElementById('removeProductTab').click();

                asyncWait = false;
            }
        }
    });

    return false;
});

/***********************
 * 11.3. CMS: Orders Tab
 */

/**
 * Displays all the products for the orders tab.
 */
$('a[href="#navViewOrders"]').on('click', function() {
    var id = 'recentOrders';
    $('#recentOrdersTitle').text('Recent Orders');
    getElementById(id).innerHTML = '';
    getElementById('recentOrdersPagination').innerHTML = '';
    $('#orderSearch').val('');

    showSpinner(id + 'Spinner');

    /**
     * The products are fetched from the database and displayed on the page.
     */
    wait().done(function() {
        $.ajax({
            url: ((getPageName() === 'panel') ? '../' : './') + 'database/FetchOrder.php',
            method: 'GET',
            data: {sort: true, order: -1, limit: 0},
            dataType: 'json',
            success: function (response) {
                orders = response;
                removeSpinner(id + 'Spinner', '');

                $('#recentOrdersTitle').text('Recent Orders (' + response.length + ')');
                getElementById(id).innerHTML = orderList(response, (response.length > 5) ? 5 : response.length);

                displayCMSPagination(response, 'recentOrdersPagination');
            }
        });
    });
});

/**
 * Handles order status change events.
 */
$(document).on('change', '#orderStatusOptions', function() {
    var orderID = $(this).closest('div[id]')[0].id;

    asyncWait = true;

    /**
     * Updates the order with a new order status.
     */
    $.ajax({
        url: ((getPageName() === 'panel') ? '../' : './') + 'database/UpdateOrder.php',
        method: 'POST',
        data: {query: orderID, update: {status: this.value}},
        dataType: 'json',
        beforeSend: function() {
            /**
             * The orders tab is clicked so that the products are fetched with updated information.
             */
            getElementById('viewOrdersTab').click();
        },
        success: function (response) {
            if (response) {
                asyncWait = false;
            }
        }
    });
});

/**
 * orderList: Returns the HTML for the orders.
 */
function orderList(array, max) {
    var html = '';

    if (array.length === 0) {
        html += '<div>No orders were found.</div>';
    }
    else {
        for (var i = 0; i < max; i++) {
            var orderStatusOptions = remove(['Pending', 'Dispatched', 'Delivered'], [array[i]['status']]);

            html += '<div class="row' + ((i + 1 === max) ? "" : " mb-3") + '" id="' + array[i]['_id']['$oid'] + '">' +
                '<div class="col">' +
                '<div class="card">' +
                '<div class="card-body">' +
                '<div class="row">' +
                '<div class="col order-title">' +
                '<div class="row">' +
                '<div class="col-12">' +
                '[Order # ' + array[i]['orderNumber'] + ']: ' + getDate(array[i]['dateCreated']['$date']['$numberLong']) +
                '</div>' +
                '<div class="col-6 order-status mt-2">' +
                '<select class="form-control" id="orderStatusOptions">' +
                '<option selected>' + array[i]['status'] + '</option>' +
                '<option>' + orderStatusOptions[0] + '</option>' +
                '<option>' + orderStatusOptions[1] + '</option>' +
                '</select>' +
                '</div></div></div>' +
                '<div class="col text-right">' +
                '<button class="btn btn-outline-success btn-sm view-order-information" data-toggle="modal" data-target="#orderModal">' +
                '<i class="fa fa-eye"></i>' +
                ' View' +
                '</button>' +
                '</div></div></div></div></div></div>';
        }
    }

    return html;
}

/********************************
 * 11.3.1. Orders Tab: View Order
 */

/**
 * Handles the view order button click event.
 */
$(document).on('click', '.view-order-information', function() {
    var orderID = $(this).closest('div[id]')[0].id;

    /**
     * The order information is displayed on the modal.
     */
    displayOrderInformation(orderID);
});

/**
 * displayOrderInformation: Sets the view order modal content.
 */
function displayOrderInformation(id) {
    var data = [];

    /**
     * Iterates over the orders array and assigns an order to data if the given order ID is found.
     */
    for (var i = 0; i < orders.length; i++) {
        if (orders[i]['_id']['$oid'] === id) {
            data = orders[i];
        }
    }

    $('#orderModalTitle').text('Order #' + data['orderNumber'] + ' - ' + data['shipping']['firstName'] + ' ' + data['shipping']['lastName']);
    $('#dateOfPurchase').text(getDate(data['dateCreated']['$date']['$numberLong']));
    $('#status').text(data['status']);
    $('#shippingAddress').html(
        '<h6>Shipping Address:</h6>' +
        '<div>' + data['shipping']['firstName'] + ' ' + data['shipping']['lastName'] + '</div>' +
        '<div>' + data['shipping']['address1'] + '</div>' +
        ((data['shipping']['address2'] !== '') ? '<div>' + data['shipping']['address2'] + '</div>' : '') +
        '<div>' + data['shipping']['townOrCity'] + '</div>' +
        '<div>' + ((data['shipping']['county'] !== '') ? (data['shipping']['county'] + ', ') : '') + data['shipping']['postCode'] + '</div>' +
        '<div>' + data['shipping']['country'] + '</div>'
    );
    $('#billingAddress').html(
        '<h6>Billing Information:</h6>' +
        '<div class="mb-2">Card ending in ' + data['billing']['cardNumber'] + '</div>' +
        '<h6>Billing Address:</h6>' +
        ((data['billing']['sameAsShipping'] === 'true') ? '<div>Same as shipping address</div>' :
                '<div>' + data['billing']['firstName'] + ' ' + data['billing']['lastName'] + '</div>' +
                '<div>' + data['billing']['address1'] + '</div>' +
                ((data['billing']['address2'] !== '') ? '<div>' + data['billing']['address2'] + '</div>' : '') +
                '<div>' + data['billing']['townOrCity'] + '</div>' +
                '<div>' + ((data['billing']['county'] !== '') ? (data['billing']['county'] + ', ') : '') + data['billing']['postCode'] + '</div>' +
                '<div>' + data['billing']['country'] + '</div>'
        )
    );
    $('#delivery').text(data['delivery']);

    var html = '';

    /**
     * Iterates over the order's package array and appends a row in HTML form for each product in the order's package.
     */
    for (var i = 0; i < data['package'].length; i++) {
        var product = data['package'][i];
        var productName = product['title'] + ' (' + product['year'] + ')';
        var quantity = {'DVD': product['dvdQuantity'], 'Blu-ray': product['bluRayQuantity']};
        var price = {'DVD': product['dvdPrice'], 'Blu-ray': product['bluRayPrice']};

        for (var key in quantity) {
            if (parseInt(quantity[key]) > 0) {
                html += '<li>' + quantity[key] + ' x ' + productName + ' [Format: ' + key + '] ' + '[Price: £' + price[key] + ']' + '</li>';
            }
        }
    }

    $('#package').html(html);
    $('#subTotal').text('£' + data['summary']['subTotal']);
    $('#vat').text('£' + data['summary']['vat']);
    $('#postAndPackaging').text((data['summary']['postAndPackaging'] !== 'Free') ?'£ ' + data['summary']['postAndPackaging'] : 'Free');
    $('#grandTotal').text('£' + data['summary']['grandTotal']);
}

/**
 * Resets the view order modal content when it's closed.
 */
$('#orderModal').on('hide.bs.modal', function() {
    var IDs = [
        'orderModalTitle',
        'dateOfPurchase',
        'status',
        'shippingAddress',
        'billingAddress',
        'delivery',
        'package',
        'subTotal',
        'vat',
        'postAndPackaging',
        'grandTotal'
    ];

    for (var i = 0; i < IDs.length; i++) {
        $('#' + IDs[i]).html('');
    }
});

/********************
 * 11.4. CMS: Helpers
 */

/**
 * Displays the file name of a selected product cover.
 */
$('#cover, #editCover').on('change', function(e) {
    var file = e.target.files[0];
    var fileNameID = (this.id === 'cover')? 'fileName' : 'editFileName';

    (file !== undefined) ? ($('#' + fileNameID).text(file.name), removeInvalidClass(this)) : $('#' + fileNameID).text("Choose a cover...");
});

/**
 * Handles click events for the product pagination.
 */
$(document).on('click', '.remove-product-page, .view-orders-page', function() {
    var isRemoveProductPage = $(this).hasClass('remove-product-page');
    var id = (isRemoveProductPage) ? 'removeProductPagination' : 'recentOrdersPagination';
    var value = $(this).children()[0].text;
    var nextPage = parseInt(value);
    var numberOfPages = Math.ceil((isRemoveProductPage) ? products.length / 5 : orders.length / 5);

    /**
     * If the user clicks a non-disabled previous or next button, nextPage is incremented or decremented.
     */
    if (!$(this).hasClass('disabled') && isInArray($(this).children()[0].text, ['Previous', 'Next'])) {
        nextPage = parseInt($('#' + id).find('.active').children()[0].text);
        (value === 'Previous') ? nextPage-- : nextPage++;
    }

    /**
     * If nextPage is less than or equal to numberOfPages, the button clicked is added the 'active' class.
     */
    if (nextPage <= numberOfPages & !$(this).hasClass('active')) {
        $('#' + id).find('.active').removeClass('active');

        /**
         * If the user clicked the previous or next button, the next numbered button is added the
         * 'active' class.
         */
        if (isInArray(value, ['Previous', 'Next'])) {
            $('#' + id + ', li>a:contains(' + nextPage + ')').parent().addClass('active');
        }
        else {
            $(this).addClass('active');
        }

        if (nextPage > 1) {
            /**
             * The previous button is enabled if nextPage is greater than 1.
             */
            $('#' + id).find('.disabled').removeClass('disabled');

            /**
             * If nextPage is equal to the number of pages, the next button is disabled.
             * Otherwise, it is enabled.
             */
            if (nextPage === numberOfPages) {
                (value === 'Next') ? $(this).addClass('disabled') : $(this).next().addClass('disabled');
            }
            else {
                $('#' + id + ', li>a:contains("Next")').parent().removeClass('disabled');
            }
        }
        else {
            /**
             * The previous and next buttons are disabled if nextPage is less than 1.
             */
            $('#' + id + ', li>a:contains("Previous")').parent().addClass('disabled');
            $('#' + id + ', li>a:contains("Next")').parent().removeClass('disabled');
        }

        /**
         * Assigns the products for the next page.
         */
        var next = getNextPage((isRemoveProductPage) ? products : orders);

        /**
         * Assigns the HTML for the product list.
         */
        var list = ((isRemoveProductPage) ? productList(next, (next.length < 5) ? next.length : 5) : orderList(next, (next.length < 5) ? next.length : 5));

        /**
         * The products for the next page are displayed.
         */
        getElementById((isRemoveProductPage) ? 'recentlyAdded' : 'recentOrders').innerHTML = list;

        /**
         * getNextPage: Returns the products for the next page.
         */
        function getNextPage(array) {
            return array.slice((5 * (nextPage - 1)), 5 * nextPage);
        }
    }

    $("html, body").animate({
        scrollTop: 0
    }, "slow");
});

/**
 * displayCMSPagination: Displays pagination for the CMS panel products.
 */
function displayCMSPagination(array, id) {
    var numberOfPages = Math.ceil(array.length / 5);
    var liClass = (id === 'removeProductPagination') ? 'remove-product-page' : 'view-orders-page';

    var html = '<ul class="pagination cms-pagination justify-content-center">' +
        '<li class="page-item ' + liClass +  ' disabled">' +
        '<a class="page-link">Previous</a>' +
        '</li>';

    for (var i = 0; i < numberOfPages; i++) {
        html += '<li class="page-item ' + liClass +  ((i === 0) ? " active" : "") + '"><a class="page-link">' + (i + 1) + '</a></li>';

        if (i + 1 === numberOfPages) {
            html += '<li class="page-item ' + liClass + ((numberOfPages === 1) ? " disabled" : "") + '">' +
                '<a class="page-link">Next</a>' +
                '</li>';
        }
    }

    html += '</ul>';

    getElementById(id).innerHTML = html;
}

/************
 * 12. Search
 */

/**
 * Handles all search bar input.
 */
$(document).ready(function() {
    /**
     * Handles the CMS panel remove product search bar input.
     */
    $('#productSearch').keyup(function() {
        var id = 'recentlyAdded', isEmptyInput = validator.isEmpty($(this).val().trim());

        getElementById(id).innerHTML = '';
        getElementById('removeProductPagination').innerHTML = '';

        showSpinner(id + 'Spinner');

        /**
         * Retrieves the user input and searches for products that match the input.
         */
        var found = search(products, $(this).val().trim().toLowerCase(), 'products', isEmptyInput);

        getElementById(id).innerHTML = productList(found, (isEmptyInput) ? ((products.length >= 5) ? 5 : products.length) : found.length);

        /**
         * The results are displayed on the page.
         */
        (!isEmptyInput) ? $('#recentlyAddedTitle').text('Results (' + found.length + ')') : $('#recentlyAddedTitle').text('Recently Added (' + products.length + ')');
        (isEmptyInput) ? displayCMSPagination(products, 'removeProductPagination') : false;
        removeSpinner(id + 'Spinner', '');
    });

    /**
     * Handles the CMS panel orders search bar input.
     */
    $('#orderSearch').keyup(function() {
        var id = 'recentOrders', isEmptyInput = validator.isEmpty($(this).val().trim());

        getElementById(id).innerHTML = '';
        getElementById('recentOrdersPagination').innerHTML = '';

        showSpinner(id + 'Spinner');

        /**
         * Retrieves the user input and searches for products that match the input.
         */
        var found = search(orders, $(this).val().trim().toLowerCase(), 'orders', isEmptyInput);

        getElementById(id).innerHTML = orderList(found, (isEmptyInput) ? ((orders.length >= 5) ? 5 : orders.length) : found.length);

        /**
         * The results are displayed on the page.
         */
        $('#recentOrdersTitle').text((!isEmptyInput) ? ('Results (' + found.length + ')') : 'Recent Orders (' + orders.length + ')');
        (isEmptyInput) ? displayCMSPagination(orders, 'recentOrdersPagination') : false;
        removeSpinner(id + 'Spinner', '');

    });

    /**
     * Handles the homepage search bar input.
     */
    $('#mainSearch').keyup(function() {
        var id = 'products', isEmptyInput = validator.isEmpty($(this).val().trim());
        $('#dropdownFilter').text('Latest');
        resetCheckboxes();

        getElementById(id).innerHTML = '';
        getElementById('productPagination').innerHTML = '';

        /**
         * Retrieves the user input and searches for products that match the input.
         */
        var found = search(products, $(this).val().trim().toLowerCase(), 'products', isEmptyInput);

        /**
         * The results are displayed on the page.
         */
        $('#results').text((!isEmptyInput) ? ('Results (' + found.length + ')') : 'Latest Products');
        displayProducts(found, (found.length >= 6) ? 6 : found.length);
        displayPagination(found);
    });
});

/**
 * search: Returns an array of products that match the given user input.
 */
function search(array, input, type, isEmptyInput) {
    var found = [];
    var max = array.length;

    var keys = (type === 'products') ? ['title', 'actors', 'year', 'directors'] : ['orderNumber', 'status'];

    /**
     * Iterates over each product and checks if any user input matches a value in products using a key found in keys.
     */
    for (var i = 0; i < max; i++) {
        if (!isEmptyInput) {
            var words = "";

            /**
             * Converts the values returned by using an element in keys as the key to a string and concatenates
             * it to words in lowercase format.
             */
            for (var j = 0; j < keys.length; j++) {
                if (j + 1 < keys.length) {
                    words += String(array[i][keys[j]]).trim().toLowerCase() + " ";
                }
                else {
                    words += String(array[i][keys[j]]).trim().toLowerCase();
                }
            }

            /**
             * If the words string contains the input string, the product is pushed to the found array.
             */
            if (words.includes(input)) {
                found.push(array[i]);
            }
            else {
                /**
                 * The user input is split by whitespace and commas.
                 */
                var inputArray = input.split(/[ ,]+/);

                /**
                 * Iterates over inputArray and checks to see if an element in inputArray is contained in the
                 * words string.
                 */
                for (var k = 0; k < inputArray.length; k++) {
                    if (inputArray[k].trim().length !== 0) {
                        /**
                         * If the current inputArray element isn't empty and is found within the words string,
                         * the product is pushed to the found array provided that it isn't in the array already.
                         */
                        if (words.includes(inputArray[k].trim()) && !validator.isEmpty(inputArray[k].trim())) {
                            isInArray(array[i], found) ? false : found.push(array[i]);
                        }
                        else if (type === 'orders') {
                            /**
                             * If the type is 'orders', the inputArray element is checked to see if it's
                             * within the products shipping name/billing name.
                             */
                            var shippingName = array[i]['shipping']['firstName'].toLowerCase() + " " + array[i]['shipping']['lastName'].toLowerCase();
                            var billingName = array[i]['billing']['firstName'].toLowerCase() + " " + array[i]['billing']['lastName'].toLowerCase();

                            if (shippingName.includes(inputArray[k]) || billingName.includes(inputArray[k])) {
                                isInArray(array[i], found) ? false : found.push(array[i]);
                            }
                        }
                    }
                }
            }
        }
        else {
            /**
             * The product is pushed to the found array if the user input is an empty string.
             */
            found.push(array[i]);
        }
    }

    return found;
}

/**************
 * 13. Tracking
 */

/**
 * trackItem: Adds a product's ID to localStorage, and increments the number of times the
 * product has been viewed. The tracked items in localStorage are also sorted in descending order.
 * Items that have been stored in localStorage for more than 30 minutes are removed.
 */
function trackItem(id) {
    /**
     * Expired items are removed from localStorage.
     */
    clearExpiredTrackingProducts();

    /**
     * The tracking item is retrieved from localStorage. If the item does not exist in localStorage (it is null,
     * initialiseTracking() is called to set a tracking item in localStorage.
     */
    var tracking = JSON.parse(localStorage.getItem('tracking')), index;
    (tracking === null) ? tracking = initialiseTracking() : false;

    /**
     * If the product exists in localStorage, the viewCount is incremented by 1. Otherwise, the product is added
     * to localStorage with a default viewCount of 1.
     */
    if ((index = isTracking(id)) !== null) {
        tracking[index]['viewCount'] = tracking[index]['viewCount'] + 1;
    }
    else {
        addNewProduct(id);
    }

    /**
     * The updated tracking item is stored in localStorage.
     */
    setTracking();

    /**
     * addNewItem: Pushes an item object to tracking.
     */
    function addNewProduct() {
        tracking.push({
            dateCreated: new Date(),
            productID: id,
            viewCount: 1
        });
    }

    /**
     * initialiseTracking: Adds a tracking item to localStorage and returns an empty array.
     */
    function initialiseTracking() {
        localStorage.setItem('tracking', JSON.stringify([]));

        return [];
    }

    /**
     * setTracking: Sorts the tracking array by viewCount in descending order and updates
     * the tracking item in localStorage.
     */
    function setTracking() {
        localStorage.setItem('tracking', JSON.stringify(tracking.sort(sortByViewCount)));
    }

    /**
     * sortByViewCount: Sorts the products by viewCount.
     */
    function sortByViewCount(a, b) {
        return b.viewCount - a.viewCount;
    }

    /**
     * isTracking: Checks if a product exists in the tracking array.
     */
    function isTracking() {
        if (tracking.length > 0) {
            for (var i in tracking) {
                if (tracking[i].productID.toString() === id) {
                    return i;
                }
            }
        }

        return null;
    }
}

/**
 * clearExpiredTrackingProducts: Removes all tracked products that have been stored in localStorage
 * for more than 30 minutes.
 */
function clearExpiredTrackingProducts() {
    var tracking = JSON.parse(localStorage.getItem('tracking'));

    if (tracking !== null) {
        var date = new Date();

        var i = tracking.length;

        while (i--) {
            if ((date - Date.parse(tracking[i]['dateCreated'])) > (30 * 60 * 1000)) {
                tracking.splice(i, 1);
            }
        }

        localStorage.setItem('tracking', JSON.stringify(tracking));
    }
}

/*************
 * 14. Helpers
 */

/**
 * getElementById: Returns an element.
 */
function getElementById(id) {
    return document.getElementById(id);
}

/**
 * isInArray: Checks if a given value is found in a given array and returns true or false.
 */
function isInArray(value, array) {
    return array.indexOf(value) > -1;
}

/**
 * remove: Removes an element from an array.
 */
function remove(array, elements) {
    array = array.slice();

    for (var i = 0; i < elements.length; i++) {
        var element = elements[i], position = array.indexOf(element);

        while (position !== -1) {
            array.splice(position, 1);
            position = array.indexOf(elements);
        }
    }

    return array;
}

/**
 *
 * resetForm: Resets a form by removing the is-invalid class, invalid feedback text and input.
 */
function resetForm(formID, formContainerID, ignore) {
    (formContainerID !== undefined) ? $('#' + formContainerID)[0].reset() : false;

    for (var i = 0; i < formID.length; i++) {
        var field = $('#' + formID[i])[0];

        removeInvalidClassAndInvalidFeedbackText(field);
    }

}

/**
 * hasNoInvalidInput: Checks if a form field has valid input.
 */
function hasNoInvalidInput(formID, isRegistrationForm, ignore, invalidClass) {
    (!isRegistrationForm) ? formID = remove(formID, ['password', 'confirmPassword']) : false;
    (ignore !== undefined) ? formID = remove(formID, ignore) : false;

    var isValid = true;

    /**
     * Iterates over each ID in formID and checks if the field is empty or has the is-invalid class.
     */
    for (var i = 0; i < formID.length; i++) {
        var field = $('#' + formID[i]);
        var isEmpty = (typeof field.val() !== 'string') ? validator.isEmpty(field.val().toString()) : validator.isEmpty(field.val());

        /**
         * If the field is empty or has the is-invalid class, isValid is assigned false.
         */
        if (isEmpty || field.hasClass('is-invalid')) {
            /**
             * The address 2 and county fields are ignored as they are optional fields.
             */
            if ((/address2/i.test(field[0].id) || /county/i.test(field[0].id)) && isEmpty) {
                continue;
            }

            isValid = false;

            (invalidClass) ? addInvalidClass(field) : false;
        }
    }

    return isValid;
}

/**
 * getPageName: Returns the current page.
 */
function getPageName() {
    var index = window.location.href.lastIndexOf("/") + 1,
        filenameWithExtension = window.location.href.substr(index),
        filename = filenameWithExtension.split(".")[0];

    return filename;
}

/**
 * removeFromString: Removes a specific part of a string from a string and converts the first character to
 * uppercase or lowercase.
 */
function removeFromString(string, remove, firstCharToLowercase) {
    string = string.replace(remove, '');
    (firstCharToLowercase) ? string = (string.charAt(0).toLowerCase() + string.slice(1)) :
        string = (string.charAt(0).toUpperCase() + string.slice(1));

    return string;
}

/**
 * getDate: Converts MongoDB date to a string.
 */
function getDate(d) {
    d = '/Date(' + d + ')/';

    var m = d.match(/\/Date\((\d+)\)\//);
    return m ? (new Date(+m[1])).toLocaleDateString('en-GB', {year: '2-digit', month: '2-digit', day: '2-digit'}) : d;
}

/**
 *
 * toFixed: Returns a float to 2 decimal places.
 */
function toFixed(num, fixed) {
    var re = new RegExp('^-?\\d+(?:\.\\d{0,' + (fixed || -1) + '})?');

    return parseFloat(num.toString().match(re)[0]).toFixed(fixed);
}

/**
 * Returns a deferred object and is used to synchronise Ajax requests.
 */
var wait = function() {
    var deferred = new $.Deferred();

    waitForChange();

    function waitForChange() {
        if (asyncWait) {
            setTimeout(waitForChange, 250);
        }
        else {
            deferred.resolve(true);
        }
    }

    return deferred.promise();
};

/**
 * showSpinner: Displays the spinner icon on an element.
 */
function showSpinner(id) {
    var html = '<div class="fa fa-circle-o-notch fa-spin"></div>';
    (typeof id !== 'string') ? $(id).html(html) : $('#' + id).html(html);
}

/**
 * removeSpinner: Removes the spinner icon from an element.
 */
function removeSpinner(id, html) {
    (typeof id !== 'string') ? $(id).html(html) : $('#' + id).html(html);
}

/**
 * Adds the 'active-step' class to an active tab and updates the next page button.
 */
$('a[data-toggle="tab"]').on('shown.bs.tab', function () {
    var step = this;

    if (validator.contains(step.classList.value, 'inactive-step')) {
        $(step).removeClass('inactive-step');
        $(step).addClass('active-step');
    }

    if (getPageName() !== 'panel') {
        (this.id === 'reviewAndPlaceOrderTab') ? populateReviewAndPlaceOrder() : false;

        $('#nextCheckoutTab').trigger('update');
    }
});