<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <!-- important -->
    <?php include_once '../../init.php'; ?>

    <title><?= APP_NAME ?> | Login </title>

    <base href="<?= BASE_URL ?>">
    <meta name="base_url" content="<?= BASE_URL ?>" />
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('sneat/img/favicon/favicon.ico'); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="<?= asset('sneat/assets/fonts/boxicons.css'); ?>" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= asset('sneat/assets/css/core.css'); ?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?= asset('sneat/assets/css/theme-default.css'); ?>" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?= asset('sneat/css/demo.css'); ?>" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= asset('sneat/assets/libs/perfect-scrollbar/perfect-scrollbar.css'); ?>" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="<?= asset('sneat/assets/css/pages/page-auth.css'); ?>" />
    <!-- Helpers -->
    <script src="<?= asset('sneat/assets/js/helpers.js'); ?>"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?= asset('sneat/js/config.js'); ?>"></script>

    <script src="<?= asset('sneat/assets/libs/jquery/jquery.js'); ?>"></script>

    <link rel="stylesheet" href="<?= asset('general/css/toastr.min.css'); ?>">

    <script src="<?= asset('general/js/axios.min.js'); ?>"></script>
    <script src="<?= asset('general/js/jquery.min.js'); ?>"></script>
    <script src="<?= asset('general/js/helper.js'); ?>"></script>
    <script src="<?= asset('general/js/toastr.min.js'); ?>"></script>
    <script src="<?= asset('general/js/block-ui.js'); ?>"></script>
    <script src="<?= asset('general/js/validationJS.js'); ?>"></script>

    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>

	<!-- google -->
	<!-- <script src="https://apis.google.com/js/platform.js" async defer></script> -->
	<!-- <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback" async defer></script> -->
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="javascript:void(0)" class="app-brand-link gap-2 justify-content-center">
                  <img src="<?= asset('sneat/img/logo.png') ?>" class="app-brand-logo demo img-fluid" width="45%">
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2">Welcome to CT Demo! 👋</h4>
              <p class="mb-4">Please sign-in to your account</p>
              <form id="formAuthentication" class="mb-3" method="POST">
                <div class="mb-3">
                  <label for="email" class="form-label">Email or Username</label>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    placeholder="Enter your email or username"
                    autofocus
                  />
                </div>
                <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                  </div>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password"
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                </div>
                <div class="mb-3"></div>
                <div class="mb-3">
                  <!-- <div class="g-recaptcha" data-sitekey="<?= $recaptchKey ?>" data-size="invisible" data-callback="setResponse"></div> -->
                  <!-- <input type="hidden" id="captcha-response" name="g-recaptcha-response" class="form-control" /> -->
                  <input type="hidden"name="action" value="authorize" class="form-control" />
                  <button id="loginBtn" class="btn btn-primary w-100" type="submit">Sign in</button>
                </div>

                <div class="mt-4 text-center">
                    <div class="signin-other-title">
                        <h5 class="fs-13 mb-4 title"> OR </h5>
                    </div>
                    <div>
                        <button type="button" class="btn btn-danger btn-icon waves-effect waves-light w-100 google-signin" onclick="googleLogin()" disabled>
                            <i class="ri-google-fill fs-16"></i> &nbsp; Sign In with Google
                        </button>
                    </div>
                </div>

              </form>
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="<?= asset('sneat/assets/libs/popper/popper.js'); ?>"></script>
    <script src="<?= asset('sneat/assets/js/bootstrap.js'); ?>"></script>
    <script src="<?= asset('sneat/assets/libs/perfect-scrollbar/perfect-scrollbar.js'); ?>"></script>

    <script src="<?= asset('sneat/assets/js/menu.js'); ?>"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="<?= asset('sneat/js/main.js'); ?>"></script>
    
    <!-- Page JS -->
	<script type="text/javascript">
		$(document).ready(function() {
			// setTimeout(function() {
			// 	googleLogin();
			// }, 10);
		});

		$("#formAuthentication").submit(async function(event) {
			event.preventDefault();
			var username = $('#username').val();
			var password = $('#password').val();
			var response = $('#captcha-response').val();

			if (validateData()) {

                const res = await loginApi('AuthController', 'formAuthentication');

				if (isSuccess(res)) {
					const data = res.data;
					const resCode = parseInt(data.code);
					noti(resCode, data.message);

					if (isSuccess(resCode)) {
						setTimeout(function() {
							window.location.href = data.redirectUrl;
						}, 450);
					} 
				} 

			} else {
				validationJsError('toastr', 'multi'); // single or multi
			}

			grecaptcha.reset();
			onloadCallback();

		});

		function validateData() {

			const rules = {
				'password': 'required',
				'username': 'required',
			};

			return validationJs(rules);
		}
	</script>
  </body>
</html>