<?php

require("inc/global.php");

if (!user::rankIsMax(1)) {
  header("location: " . $config["url"] . "login");
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
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </a>
        <div class="navbar-content">
          <ul class="navbar-nav">
            <li class="nav-item dropdown nav-profile">
              <a class="nav-link dropdown-toggle" href="" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
            <h4 class="mb-3 mb-md-0">Manage Users<h4>
          </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="editKey" tabindex="-1" role="dialog" aria-labelledby="editKey" aria-hidden="true">
          <form method="post" class="forms-sample">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Edit user</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <?php
                  admin::updateUser();
                  ?>
                  <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                      <div class="card-body">
                        <h6 class="card-title">Manage user</h6>
                        <div class="form-group">
                          <label for="exampleInputUsername1">Username</label>
                          <input id="user_id_edit" type="hidden" name="id" />
                          <input type="text" name="username" class="form-control" id="username_edit" autocomplete="off" placeholder="">
                        </div>
                        <div class="form-group">
                          <label for="exampleInputUsername1">Password</label>
                          <input type="text" name="password" class="form-control" autocomplete="off" placeholder="New password">
                        </div>
                        <div class="form-group">
                          <div class="form-group">
                            <label>Rank</label>
                            <select id="rank_edit" name="rank" class="form-control">
                              <option value="1">Master</option>
                              <option value="2">Reseller</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <input type="submit" class="btn btn-primary" value="Save changes" name="edituser" />
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Manage all users</h6>
          <p class="card-description">Here you can manage all the users. This page is only available for master rank.</p>
          <div class="table-responsive">
            <div id="dataTableExample_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
              <div class="row">
                <div class="col-sm-12 col-md-6">
                  <div class="dataTables_length" id="dataTableExample_length"></div>
                </div>
                <div class="col-sm-12 col-md-6"></div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <table id="dataTableExample" class="table dataTable no-footer" role="grid" aria-describedby="dataTableExample_info">
                  <thead>
                    <tr role="row">
                      <th class="sorting_asc" tabindex="0" aria-controls="dataTableExample" rowspan="1" colspan="1" aria-sort="ascending" style="width: 263px;">Id</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTableExample" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 403px;">Serial Key</th>
                      <th class="sorting_asc" tabindex="0" aria-controls="dataTableExample" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 263px;">Last ip</th>
                      <th class="sorting_asc" tabindex="0" aria-controls="dataTableExample" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 263px;">Country</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTableExample" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 194px;">rank</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTableExample" rowspan="1" colspan="1" aria-label="Start date: activate to sort column ascending" style="width: 190px;">Created at</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTableExample" rowspan="1" colspan="1" aria-label="Salary: activate to sort column ascending" style="width: 150px;">Manage</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    admin::deleteUser();
                    foreach (admin::getAllUsers() as $row) {
                    ?>
                      <tr id="row<?php echo $row["id"]; ?>" role="row" class="odd">
                        <td class="sorting_1"><?php echo $row["id"]; ?></td>
                        <td><?php echo $row["username"]; ?></td>
                        <td><?php echo $row["ip_last"]; ?></td>
                        <td><?php echo utils::ipCountry($row["ip_last"]); ?></td>
                        <td><?php echo $row["rank"]; ?></td>
                        <td><?php echo utils::formatDate($row["created"]); ?></td>
                        <td>
                          <button type="button" onclick="delete_user(<?php echo $row['id']; ?>)" class="btn btn-danger btn-icon">
                            <i data-feather="trash-2"></i>
                          </button>
                          <button type="button" onclick="update_edit_user(<?php echo $row['id']; ?>)" data-toggle="modal" data-target="#editKey" class="btn btn-primary btn-icon">
                            <i data-feather="edit"></i>
                          </button>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> <!-- row -->

  </div>



  </div>
  </div>

  <?php include("inc/scripts.html"); ?>

  <svg id="SvgjsSvg1001" width="2" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;">
    <defs id="SvgjsDefs1002"></defs>
    <polyline id="SvgjsPolyline1003" points="0,0"></polyline>
    <path id="SvgjsPath1004" d="M0 0 "></path>
  </svg>
</body>

</html>