<?php

require("inc/global.php");

if(!user::rankIsMax(1))
{
    header("location: ".$config["url"]."login");
    die;
}
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
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
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
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
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
            <h4 class="mb-3 mb-md-0">Add a user<h4>
          </div>
        </div>

      <?php admin::adduser(); ?>
        <div class="row">
          <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
								<h6 class="card-title">Add a user:</h6>
								<form class="forms-sample" method="post">
									<div class="form-group">
										<label>Username</label>
										<input type="text" name="username" class="form-control"  autocomplete="off" placeholder="Username">
									</div>
									<div class="form-group">
										<label>Password</label>
										<input type="password" name="password" class="form-control"  autocomplete="off" placeholder="Password">
									</div>
									<div class="form-group">
                                       <div class="form-check">
                                            Rank
                                            <select id="rank" name="rank" class="form-control">
											                <option value="1">Master</option>
											                <option value="2">Reseller</option>
										    </select>
									    </div>
                  <br>
                  <input name="adduser" type="submit" class="btn btn-primary mr-2" value="Create"/>
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
    $data = [];
    for ($i=0; $i < 11; $i++) 
    { 
      $day = 86400;
      $now = time()+1-$day*$i;
      $query = $db->query("SELECT created FROM serial_keys WHERE created BETWEEN 0 AND ?", $now);
      array_push($data, $query->rowCount());
    }
    $total = $data[0];
    $data = array_reverse($data);
  ?>
  <script>
  // Apex chart1 start
$(document).ready(function(){ 
  $("#serial_keys_amount").html("<?php echo $total;?>");
  if($('#apexChart1').length) {
    var options1 = {
      chart: {
        type: "line",
        height: 60,
        sparkline: {
          enabled: !0
        }
      },
      series: [{
          data: [<?php echo implode(",",$data); ?>]
      }],
      stroke: {
        width: 2,
        curve: "smooth"
      },
      markers: {
        size: 0
      },
      colors: ["#727cf5"],
      tooltip: {
        fixed: {
          enabled: !1
        },
        x: {
          show: !1
        },
        y: {
          title: {
            formatter: function(e) {
              return ""
            }
          }
        },
        marker: {
          show: !1
        }
      }
    };
    new ApexCharts(document.querySelector("#apexChart1"),options1).render();
  }
  // Apex chart1 end

<?php

$data = [];
$query = $db->query("SELECT count(*) FROM trials");
$total = $query->rowCount();

for ($i=1; $i < 12; $i++)
{ 
  $day = 86400;
  $then = time()-$day*$i;
  $now = time()+1-$day*($i-1);
  $query = $db->query("SELECT created FROM serial_keys WHERE created BETWEEN ? AND ?", $then, $now);
  array_push($data, $query->rowCount());
}
  $data = array_reverse($data);
?>

  $("#trials_amount").html("<?php echo $total; ?>")
  // Apex chart2 start
  if($('#apexChart2').length) {
    var options2 = {
      chart: {
        type: "bar",
        height: 60,
        sparkline: {
          enabled: !0
        }
      },
      plotOptions: {
        bar: {
          columnWidth: "60%"
        }
      },
      colors: ["#727cf5"],
      series: [{
        data: [<?php echo implode(",",$data);?>]
      }],
      labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
      xaxis: {
        crosshairs: {
          width: 1
        }
      },
      tooltip: {
        fixed: {
          enabled: !1
        },
        x: {
          show: !1
        },
        y: {
          title: {
            formatter: function(e) {
              return ""
            }
          }
        },
        marker: {
          show: !1
        }
      }
    };
    new ApexCharts(document.querySelector("#apexChart2"),options2).render();
  }
  // Apex chart2 end
});
</script>
	<!-- end custom js for this page -->

    <svg id="SvgjsSvg1001" width="2" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;"><defs id="SvgjsDefs1002"></defs><polyline id="SvgjsPolyline1003" points="0,0"></polyline><path id="SvgjsPath1004" d="M0 0 "></path></svg></body></html>