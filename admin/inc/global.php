<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/inc/config.php");
require_once(DOCR."inc/app/utils.class.php");
require_once(DOCR."inc/app/user.class.php");
require_once(DOCR."inc/app/admin.class.php");

if(!user::rankIsMax(2))
{
    header("location: ".$config["url"]."login");
    die;
}
?>