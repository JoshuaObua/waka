<!doctype html>
<html class="no-js " lang="en">


<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">

<title>{{ config('company.name') }} - Sign In</title>
<!-- Favicon-->
<link rel="icon" href="{{ asset(config('company.favicon_url')) }}" type="image/x-icon">
<!-- Custom Css -->
<link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/style.min.css">    
</head>

<body class="theme-blush">

<div class="authentication">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-sm-12">
                <form class="card auth_form" action="/login" method="POST">
                    @csrf
                    <div class="header">
                        <img class="logo" src="assets/images/logo.svg" alt="">
                        <h5>{{ config('company.name') }}</h5>
                        <p class="text-muted">{{ config('company.tagline') }}</p>
                    </div>
                    <div class="body">
                        @if($errors->has('login'))
                            <div class="alert alert-danger">{{ $errors->first('login') }}</div>
                        @endif
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="zmdi zmdi-account-circle"></i></span>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" id="password-input" class="form-control" placeholder="Password" required>
                            <div class="input-group-append">                                
                                <span class="input-group-text"><a href="javascript:void(0);" id="toggle-password" class="forgot" title="Toggle Password Visibility"><i class="zmdi zmdi-eye" id="toggle-password-icon"></i></a></span>
                            </div>                            
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="checkbox mb-0">
                                <input id="remember_me" type="checkbox">
                                <label for="remember_me" class="mb-0">Remember Me</label>
                            </div>
                            <a href="forgot-password.html" class="forgot text-muted" title="Forgot Password">Forgot Password?</a>
                        </div>
                        <button type="submit" id="submit-btn" class="btn btn-primary btn-block waves-effect waves-light">SIGN IN</button>                        
                        <div class="signin_with mt-3">
                            <p class="mb-0">or Sign Up using</p>
                            <button type="button" class="btn btn-primary btn-icon btn-icon-mini btn-round facebook"><i class="zmdi zmdi-facebook"></i></button>
                            <button type="button" class="btn btn-primary btn-icon btn-icon-mini btn-round twitter"><i class="zmdi zmdi-twitter"></i></button>
                            <button type="button" class="btn btn-primary btn-icon btn-icon-mini btn-round google"><i class="zmdi zmdi-google-plus"></i></button>
                        </div>
                    </div>
                </form>
                <div class="copyright text-center">
                    &copy;
                    <script>document.write(new Date().getFullYear())</script>,
                    <span><a href="#">{{ config('company.name') }}</a></span>
                </div>
            </div>
            <div class="col-lg-8 col-sm-12">
                <div class="card">
                    <img src="assets/images/signin.svg" alt="Sign In"/>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Jquery Core Js -->
<script src="assets/bundles/libscripts.bundle.js"></script>
<script src="assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Show/Hide Password Toggle
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password-input');
    const passwordIcon = document.getElementById('toggle-password-icon');

    if (togglePassword && passwordInput && passwordIcon) {
        togglePassword.addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('zmdi-eye');
                passwordIcon.classList.add('zmdi-eye-off');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('zmdi-eye-off');
                passwordIcon.classList.add('zmdi-eye');
            }
        });
    }

    // Double Submission Protection
    const authForm = document.querySelector('form.auth_form');
    const submitBtn = document.getElementById('submit-btn');

    if (authForm && submitBtn) {
        authForm.addEventListener('submit', function (e) {
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> SIGNING IN...';
        });
    }
});
</script>
</body>


</html>