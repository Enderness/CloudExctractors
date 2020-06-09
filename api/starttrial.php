<?php 

include("../inc/config.php");
header("Content-type: application/json");

$ip = $_SERVER["REMOTE_ADDR"];
$result = [];

$query = $db->query("SELECT * FROM trials WHERE ip_last=?", $ip);

if($query->rowCount()==0)
{
    $db->query("INSERT INTO trials (status, ip_last, expires_at, started_at) VALUES (?, ?, ?, ?)", "active", $ip, time()+86400, time());
    $result["result"] = "true";
}
else
{
    $result["result"] = "false";
}

echo json_encode($result);
?>