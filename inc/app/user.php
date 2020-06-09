<?php


class user
{
    public static function logIp()
    {
        global $db;
        if(isset($_SESSION["admin"]))
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
            
            if((int)$f["rank"]==1)
            {
                $payload = array(
                    "iat" => time(),
                    "iss" => $config["domain"],
                    "exp" => time()+ (84000)
                );
                $code =  JWT::encode($payload, APP_SECRET);

                $_SESSION["serial_key"] = $code;
            }
            $_SESSION["admin"] = $f["id"];
            user::logIp();
            header("location: index");
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

    public static function getUser($field)
    {
        global $db;
        if(isset($_SESSION["admin"]))
        {
        $query = $db->query("SELECT * FROM users WHERE id=?", $_SESSION["admin"]);
        $f = $query->fetch();

        if($field=="rank")
        {
            if($f["rank"]==1)
            {
                echo "Master";
            }
            else
            {
                echo "Reseller";
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