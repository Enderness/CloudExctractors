<?php

//This page is for formatting json data for ajax requests

require_once($_SERVER["DOCUMENT_ROOT"]."/admin/inc/global.php");

header("Content-type: application/json");

admin::updateEdit();
?>
