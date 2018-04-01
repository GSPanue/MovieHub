<!-- Log In & Create Account Tabs -->
<nav>
    <div class="nav nav-tabs nav-justified mb-3" id="signInModalTabs" role="tablist">
        <!-- Log In Tab -->
        <a class="nav-item nav-link login-tab active" id="logInTab" data-toggle="tab" href="#navLogIn" role="tab">
            Sign In
        </a>
        <!-- Create Account Tab -->
        <a class="nav-item nav-link login-tab" id="createAccountTab" data-toggle="tab" href="#navCreateAccount" role="tab">
            Create Account
        </a>
    </div>
</nav>

<!-- Tab Content -->
<div class="tab-content">
    <!-- Log In -->
    <div class="tab-pane fade show active" id="navLogIn" role="tabpanel">
        <!-- Log In Form -->
        <form class="login-container" id="loginForm">
            <div class="mb-3">
                <label for="loginEmailAddress">Email Address</label>
                <input type="text" class="form-control" placeholder="example@gmail.com" id="loginEmailAddress" required>
                <div class="invalid-feedback" id="invalidLoginEmailAddress"></div>
            </div>
            <div class="mb-3">
                <label for="loginPassword">Password</label>
                <input type="password" class="form-control" id="loginPassword" required>
                <div class="invalid-feedback" id="invalidLoginPassword"></div>
            </div>
            <div class="mb-3 text-center invalid-login" id="invalidLogin"></div>
            <button class="btn btn-primary login-btn float-right" id="submitLoginForm">Sign In</button>
        </form>
    </div>

    <!-- Create Account -->
    <div class="tab-pane fade" id="navCreateAccount" role="tabpanel">
        <!-- Create Account Form -->
        <form class="pl-5 pr-5" id="registrationForm">
            <div class="form-row mb-3">
                <div class="col-6">
                    <label for="firstName">First Name</label>
                    <input type="text" class="form-control" id="firstName" required>
                    <div class="invalid-feedback" id="invalidFirstName"></div>
                </div>
                <div class="col-6">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="lastName" required>
                    <div class="invalid-feedback" id="invalidLastName"></div>
                </div>
            </div>
            <div class="form-row mb-3">
                <div class="col">
                    <label for="emailAddress">Email Address</label>
                    <input type="text" class="form-control" id="emailAddress" required>
                    <div class="invalid-feedback" id="invalidEmailAddress"></div>
                </div>
            </div>
            <div class="form-row mb-3">
                <div class="col-6">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" required>
                    <div class="invalid-feedback" id="invalidPassword"></div>
                </div>
                <div class="col-6">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" required>
                    <div class="invalid-feedback" id="invalidConfirmPassword"></div>
                </div>
            </div>
            <div class="form-row mb-3">
                <div class="col-6">
                    <label for="mobileNumber">Mobile Number</label>
                    <input type="text" class="form-control" id="mobileNumber" required>
                    <div class="invalid-feedback" id="invalidMobileNumber"></div>
                </div>
                <div class="col-6">
                    <label for="dateOfBirthDay">Date of Birth</label>
                    <div class="form-row">
                        <div class="col-3">
                            <select class="custom-select form-control" id="dateOfBirthDay" required>
                                <option disabled selected>Day</option>
                            </select>
                        </div>
                        <div class="col-5">
                            <select class="custom-select form-control" id="dateOfBirthMonth" required>
                                <option disabled selected>Month</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <select class="custom-select form-control" id="dateOfBirthYear" required>
                                <option disabled selected>Year</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row mb-3">
                <div class="col-6">
                    <label for="address1">Address (Line 1)</label>
                    <input type="text" class="form-control" id="address1" required>
                    <div class="invalid-feedback" id="invalidAddress1"></div>
                </div>
                <div class="col-6">
                    <label for="address2">Address (Line 2)</label>
                    <input type="text" class="form-control" id="address2">
                    <div class="invalid-feedback" id="invalidAddress2"></div>
                </div>
            </div>
            <div class="form-row mb-3">
                <div class="col-5">
                    <label for="townOrCity">Town/City</label>
                    <input type="text" class="form-control" id="townOrCity" required>
                    <div class="invalid-feedback" id="invalidTownOrCity"></div>
                </div>
                <div class="col-7">
                    <label for="county">County</label>
                    <input type="text" class="form-control" id="county">
                    <div class="invalid-feedback" id="invalidCounty"></div>
                </div>
            </div>
            <div class="form-row mb-3">
                <div class="col-7">
                    <label for="country">Country</label>
                    <select class="custom-select" id="country" required>
                        <option selected>United Kingdom</option>
                    </select>
                </div>
                <div class="col-5">
                    <label for="postCode">Post Code</label>
                    <input type="text" class="form-control" id="postCode" required>
                    <div class="invalid-feedback" id="invalidPostCode"></div>
                </div>
            </div>
            <!-- Submit Form Button -->
            <button type="button" class="btn btn-primary login-btn float-right" id="submitRegistrationForm">Register</button>
        </form>
    </div>
</div>