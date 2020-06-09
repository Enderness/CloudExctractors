<?php

include("../inc/config.php");

header("Content-type: application/json");

function checkExpired($id, $expire_at)
{
    if ((int)$expire_at-time()<0 and $expire_at!="0")
    {
        return True;
    }
    else
    {
        return False;
    }
}
function logIp($id)
{
    global $db;
    $ip = $_SERVER["REMOTE_ADDR"];
    $db->query("UPDATE serial_keys SET ip_last=?, status='active' WHERE id=?", $ip, $id);
}

$result = [];

if(isset($_POST["key"]))
{
    $key = $_POST["key"];
    $query = $db->query("SELECT * FROM serial_keys WHERE serial_key=?", $key);
    
    if($query->rowCount()!=0)
    {
        $f = $query->fetch(); 
        $status = $f['status'];
        
        if($status=="expired")
        {
            $result["result"] = "expired";
        }
        elseif(checkExpired($f["id"], $f["expire_at"]))
        {
            $result["result"] = "expired";
        }
        elseif($status=="suspended")
        {
            $result["result"] = "suspended";
        }
        else
        {
            if($status=="reissued")
            {
                logIp($f["id"]);
            }

            $result["result"] = "true";
        }
    }
    else
    {
        $result["result"] = "false";
    }
}
else 
{
    $result["result"] = "error";
}

echo json_encode($result);
?>