<?php

header("content-type:text/html");

require($_SERVER["DOCUMENT_ROOT"]."/inc/config.php");
require($_SERVER["DOCUMENT_ROOT"]."/inc/app/user.class.php");

if(isset($_GET["scrape"]) && user::isPermitted($_GET["scrape"]))
{
    $page = $_GET["scrape"];

    require("../../inc/app/scraper.class.php");
    require("../../inc/app/scraper/".$page.".class.php");

    $scraper = new $page();
    $scraper->start();
}

?>