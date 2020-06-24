<?php

require("../inc/config.php");
require(DOCR."inc/app/user.class.php");
require(DOCR."inc/app/fields.class.php");

if (!user::rankIsMax(3)) 
{
	header("location: " . $config["url"] . "login");
	die;
}
$page = $_GET["scraper"] ?? "index";
if (!fields::isScraper($page) & $page != "index") {
	header("location: index");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?php echo $page; ?> - scraper</title>
	<link rel="stylesheet" href="/apps/css/core.css">
	<link rel="stylesheet" href="/apps/css/dataTables.bootstrap4.css">
	<link rel="stylesheet" href="/apps/css/sweetalert2.min.css">
	<link rel="stylesheet" href="/apps/css/style_white.css">
	<link rel="stylesheet" href="/apps/css/iconfont.css">
	<link rel="stylesheet" href="/admin/css/chosen.min.css">
	<link rel="stylesheet" href="/apps/css/jquery.tagsinput.min.css" />


<body class="sidebar-dark" data-gr-c-s-loaded="true" cz-shortcut-listen="true">
	<div class="main-wrapper">
		<div class="page-wrapper">

			<?php include("inc/sidebar.php"); ?>

			<div class="page-content">
				<?php if ($page == "index") { ?>
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-body">
									<h6 class="card-title">Frequently Asked Questions</h6>
									<div id="accordion" class="accordion" role="tablist">
										<div class="card">
											<div class="card-header" role="tab" id="headingOne">
												<h6 class="mb-0">
													<a data-toggle="collapse" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="collapsed">
														How get a facebook api token?
													</a>
												</h6>
											</div>
											<div id="collapseOne" class="collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
												<div class="card-body">
													Follow this tutorial: <a target="_blank" href="https://smashballoon.com/custom-facebook-feed/access-token/">here</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<div class="row">
						<div class="col-md-9 grid-margin stretch-card">
							<div class="card">
								<div class="card-body">
									<h5 class="card-title">Data Table</h5>
									<div class="table-responsive">
										<table id="dataTableExample" class="table dataTable no-footer">
											<thead>
												<tr>
													<?php echo fields::format($page, "field"); ?>
												</tr>
											</thead>
											<tbody id="dataresults">

											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div style="border-left: solid 1px black" class="col-md-3 grid-margin stretch-card">
							<div class="card">
								<div class="card-body">
									<h6 class="card-title"><?php echo $page; ?></h6>
									<form method="post" id="scraperForm" class="forms">
										<?php print_r(fields::format($page, "form")); ?>
										<button type="submit" id="startScrape" name="scrape" class="btn btn-primary mr-2">Start</button>
										<button type="button" id="stopScrape" disabled="true" name="stopScrape" class="btn btn-primary mr-2">Stop</button>
										<button type="button" id="clearScrape" name="clearScrape" class="btn btn-primary mr-2">Clear</button>
									</form>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<script>
		var page = "<?php echo $page; ?>";
	</script>
	<script src="/apps/js/core.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
	<script src="/admin/js/chosen.jquery.min.js"></script>
	<script src="/apps/js/dataTables.bootstrap4.js"></script>
	<script src="/apps/js/jquery.tagsinput.min.js"></script>
	<script src="/apps/js/sweetalert2.min.js"></script>
	<script src="/apps/js/jquery.flot.js"></script>
	<script src="/apps/js/jquery.flot.resize.js"></script>
	<script src="/apps/js/template.js"></script>
	<script src="/apps/js/feather.min.js"></script>
	<script>
		$(".chosen-select").chosen();
	</script>