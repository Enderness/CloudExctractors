<?php

include("../inc/config.php");
header("Content-type: application/json");

function checkExpired($id,$expires_at)
{
    global $db;
    if((int)$expires_at-time()<0 and $expires_at!="0")
    {
        $query = $db->query("UPDATE trials SET status='expired' WHERE id=?", $id);
        return "true";
    }
    else
    {
        return "false";
    }


}

$result = [];
$ip = $_SERVER["REMOTE_ADDR"];

$query = $db->query("SELECT * FROM trials WHERE ip_last=?", $ip);
$f = $query->fetch();

if($query->rowCount()==0)
{
    $result["result"] = "false";
}
else
{
    $result["result"] = "true";
    $result["expired"] = checkExpired($f["id"], $f["expires_at"]);
}

echo json_encode($result);
?>