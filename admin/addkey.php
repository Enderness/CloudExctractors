<?php

require("inc/global.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Admin panel</title>

	<?php include("inc/styles.html"); ?>

<body class="sidebar-dark" data-gr-c-s-loaded="true" cz-shortcut-listen="true">
	<div class="main-wrapper">
		<div class="page-wrapper">

			<?php include("inc/sidebar.php"); ?>

			<!-- partial:partials/_navbar.html -->
			<nav class="navbar">
				<a href="#" class="sidebar-toggler">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
						<line x1="3" y1="12" x2="21" y2="12"></line>
						<line x1="3" y1="6" x2="21" y2="6"></line>
						<line x1="3" y1="18" x2="21" y2="18"></line>
					</svg>
				</a>
				<div class="navbar-content">
					<ul class="navbar-nav">
						<li class="nav-item dropdown nav-profile">
							<a class="nav-link dropdown-toggle" href="https://www.nobleui.com/html/template/demo_3/dashboard-one.html#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<img src="imgs/profile.png" alt="profile">
							</a>
							<div class="dropdown-menu" aria-labelledby="profileDropdown">
								<div class="dropdown-header d-flex flex-column align-items-center">
									<div class="figure mb-3">
										<img src="imgs/profile.png" alt="">
									</div>
									<div class="info text-center">
										<p class="name font-weight-bold mb-0"><?php user::getUser("username") ?></p>
										<p class="email text-muted mb-3"><?php user::getUser("rank") ?></p>
									</div>
								</div>
								<div class="dropdown-body">
									<ul class="profile-nav p-0 pt-3">
										<li class="nav-item">
											<a href="logout" class="nav-link">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
													<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
													<polyline points="16 17 21 12 16 7"></polyline>
													<line x1="21" y1="12" x2="9" y2="12"></line>
												</svg>
												<span>Log Out</span>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</nav>
			<!-- partial -->

			<div class="page-content">

				<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
					<div>
						<h4 class="mb-3 mb-md-0">Add a serial key<h4>
					</div>
				</div>

				<?php admin::addkey(); ?>
				<div class="row">
					<div class="col-md-6 grid-margin stretch-card">
						<div class="card">
							<div class="card-body">
								<h6 class="card-title">Add serial key:</h6>
								<form class="forms-sample" method="post">
									<div class="form-group">
										<label>Serial key <a href="#" id="generateRand">(Generate)</a></label>
										<input type="text" name="serial_key" class="form-control" id="serial_key_input" autocomplete="off" placeholder="Serial key">
									</div>
									<div class="form-group">
										<div class="form-check">
											<label class="form-check-label">
												Expires
												<input type="checkbox" checked="" name="expires" class="form-check-input" onclick="var input = document.getElementById('expires_at'); if(this.checked){ input.disabled = false; input.focus();}else{input.disabled=true;}">
										</div>
										<input type="date" id="expires_at" name="expires_at" value="<?php echo date("Y-m-d", strtotime("+1 week")); ?>">
									</div>
									<div class="form-group">
										<label class="form-label" for="permissions">
											Permissions:
										</label>
										<br>
										<select id="permissions" name="permissions[]" multiple>
											<option value="1">Justdial</option>
											<option value="2">Indiamart</option>
											<option value="3">Tradeindia</option>
											<option value="4">Linkedin</option>
											<option value="5">Google Maps</option>
											<option value="6">Companyleads</option>
											<option value="7">Facebook</option>
										</select>
										<button type="button" class="btn btn-primary mr-2 btn-sm chosen-toggle select">Select all</button>
										<button type="button" class="btn btn-primary mr-2 btn-sm chosen-toggle deselect">Deselect all</button>
									</div>
									<br>
									<input name="addkey" type="submit" class="btn btn-primary mr-2" value="Create" />
								</form>
							</div>
						</div>
					</div>
				</div> <!-- row -->
			</div>



		</div>
	</div>

	<?php
	include("inc/scripts.html");
	?>
	<script>
		$("#permissions").chosen({
			"width": "300px"
		});

		$('.chosen-toggle').each(function(index) {
			console.log(index);
			$(this).on('click', function() {
				$(this).parent().find('option').prop('selected', $(this).hasClass('select')).parent().trigger('chosen:updated');
			});
		});
	</script>
	<svg id="SvgjsSvg1001" width="2" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;">
		<defs id="SvgjsDefs1002"></defs>
		<polyline id="SvgjsPolyline1003" points="0,0"></polyline>
		<path id="SvgjsPath1004" d="M0 0 "></path>
	</svg>
</body>

</html>