<?php

use \Firebase\JWT\JWT;

class user
{

    public static function rankIsMax($i)
    {
        global $db;
        if(isset($_SESSION["user"]))
        {
            $query = $db->query("SELECT `rank` FROM users WHERE id=?", $_SESSION["user"]);
            $f = $query->fetch();
            return ($f["rank"] <= $i);
        }
        else
        {
            return false;
        }
    }
    public static function logIp()
    {
        global $db;
        if(isset($_SESSION["user"]))
        {
            $db->query("UPDATE users SET ip_last=? WHERE id=?", $_SERVER["REMOTE_ADDR"], $_SESSION["admin"]);
        }
        else
        {
            utils::notifAlert("danger", "Not logged in");
        }
    }
    public static function login()
    {
        global $db;
        global $config;

        if(isset($_POST["login"]))
        {
        $username = $_POST["username"] ?? "";
        $password = hash("sha256", $_POST["password"] ?? "");
        
        if($username != "" | $_POST["password"] != "")
        {
            $query = $db->query("SELECT * FROM users WHERE username=? AND password=?", $username, $password);
            if($query->rowCount()>0)
            {
                $f = $query->fetch();
                $_SESSION["user"] = $f["id"];
                user::logIp();
                if($f["rank"] < 3)
                {
                    header("location: ".$config["url"]."admin/index");
                }
                else
                {
                    header("location: ".$config["url"]."apps/index");
                }
            }
            else
            {
            utils::notifAlert("danger","Wrong login");
            }
        }
        else
        {
            utils::notifAlert("danger","Please don't leave anything empty");
        }
        }
    }

    public static function isPermitted($page)
    {
        global $db;
        if(isset($_SESSION["user"]))
        {
            $q = $db->query("SELECT `permissions` FROM users WHERE id=?", $_SESSION["user"]);
            $f = $q->fetch();

            switch($page)
            {
                case "justdial":
                    $page = "1";
                    break;
                case "indiamart":
                    $page = "2";
                    break;
                case "tradeindia":
                    $page = "3";
                    break;
                case "linkedin":
                    $page = "4";
                    break;
                case "googlemaps":
                    $page = "5";
                    break;
                case "companyleads":
                    $page = "6";
                    break;
                case "facebook":
                    $page = "7";
                    break;
            }

            $perms = explode(",",$f["permissions"]);
            return in_array($page, $perms);
        }
        else
        {
            return false;
        }
    }

    public static function getUser($field)
    {
        global $db;
        if(isset($_SESSION["user"]))
        {
            $query = $db->query("SELECT * FROM users WHERE id=?", $_SESSION["user"]);
            $f = $query->fetch();
            if($field == "rank")
            {
                switch($f["rank"])
                {
                    case 1:
                        echo "Master";
                        break;
                    case 2:
                        echo "Reseller";
                        break;
                    case 3:
                        echo "Regular";
                        break;
                }
            }
            else
            {
                echo $f[$field];
            }
        }
        else
        {
            utils::notifAlert("danger","You dont have permission");
        }

    }
}
?>