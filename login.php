<?php

require($_SERVER["DOCUMENT_ROOT"]."/inc/config.php");
include(DOCR."inc/app/utils.class.php");
include(DOCR."inc/app/user.class.php");

if(isset($_SESSION["user"]))
{
  if(user::rankIsMax(2))
  {
    header("location: ".$config["url"]."admin");
  }
  else
  {
    header("location: ".$config["url"]."apps");
  }
}

?>
<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Login - CloudExtractors</title>
	<link rel="stylesheet" href="/admin/css/core.css">
	<link rel="stylesheet" href="/admin/css/style.css">
  <link rel="stylesheet" href="/admin/css/iconfont.css">
  <link rel="stylesheet" href="/admin/css/flag-icon.min.css">
  <link rel="stylesheet" href="/apps/css/sweetalert2.min.css">
</head>
<body class="sidebar-dark" data-gr-c-s-loaded="true" cz-shortcut-listen="true">      
	<div class="main-wrapper">
		<div class="page-wrapper full-page">
			<div class="page-content d-flex align-items-center justify-content-center">

				<div class="row w-100 mx-0 auth-page">
					<div class="col-md-8 col-xl-6 mx-auto">
						<div class="card">
							<div class="row">
                <div class="col-md-4 pr-md-0">
                  <div class="auth-left-wrapper">

                  </div>
                </div>
                <div class="col-md-8 pl-md-0">
                  <div class="auth-form-wrapper px-4 py-5">
                    <a href="#" class="noble-ui-logo d-block mb-2">Cloud <span>Extractors</span></a>
                    <h5 class="text-muted font-weight-normal mb-4">Welcome back! Log in to your account.</h5>
                    <form method="post" class="forms-sample">
                      <div class="form-group">
                        <label for="username">Username</label>
                        <input name="username" type="text" class="form-control" id="username" placeholder="Username">
                      </div>
                      <div class="form-group">
                        <label for="password">Password</label>
                        <input name="password" type="password" class="form-control" id="password" autocomplete="current-password" placeholder="Password">
                      </div>
                      <div class="mt-3">
                        <input name="login" type="submit" class="btn btn-primary mr-2 mb-2 mb-md-0 text-white" value="Login"/>

                      </div>
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

<script src="/apps/js/core.js"></script>
<script src="/admin/js/feather.min.js"></script>
<script src="/apps/js/sweetalert2.min.js"></script>
<?php user::login(); ?>
</body></html>