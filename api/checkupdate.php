<?php

include("../inc/config.php");

header("Content-type: application/json");

$result = [];

if(isset($_POST["v"]) & isset($_POST["a"]))
{
    $a = $_POST["a"];
    $v = $_POST["v"];

    $query = $db->query("SELECT * FROM apps WHERE name=?", $a);
    if($query->rowCount()==0)
    {
        $result["result"] = "error";
    }
    else
    {
        $f = $query->fetch();

        if($v==$f["version"])
        {
            $result["result"] = "false";
        }
        else
        {
            $result["result"] = "true";
            $result["update"] = $f["update"];
        }
    }
}
else
{
    $result["result"] = "error";
}

echo json_encode($result);
?>