<?php

include("../../inc/config.php");
include("../../inc/app/scrapers.php");

header("Content-type: application/json");

if(isset($_GET["scrape"]))
{
    $output = array(
        "data" => scrapers::formatResults()
    );
    echo json_encode($output);
}
else
{
    echo '{"data":[]}';
}

?>