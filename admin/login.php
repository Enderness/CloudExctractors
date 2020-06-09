<?php

require("../inc/config.php");
include("../inc/app/utils.php");
include("../inc/app/user.php");

if(isset($_SESSION["admin"]))
{
  header("location:index");
}
user::login();


?>
<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Login - Admin panel</title>
	<link rel="stylesheet" href="css/core.css">
	<link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/iconfont.css">
  <link rel="stylesheet" href="css/flag-icon.min.css">
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
                        <label for="exampleInputEmail1">Username</label>
                        <input name="username" type="text" class="form-control" id="exampleInputEmail1" placeholder="Username">
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input name="password" type="password" class="form-control" id="exampleInputPassword1" autocomplete="current-password" placeholder="Password">
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

<script src="js/feather.min.js"></script>

</body></html>