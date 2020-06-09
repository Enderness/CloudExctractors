<?php

//This page is for formatting json data for ajax requests

include("../../inc/config.php");
include("../../inc/app/utils.php");
include("../../inc/app/admin.php");

header("Content-type: application/json");

admin::updateEdit();
?>
