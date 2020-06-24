<?php

class admin
{

    public static function addKey()
    {
        global $db;
        if(isset($_POST["addkey"]))
        {
            if(user::rankIsMax(2))
            {
                if($_POST["serial_key"]!="")
                {
                    $expires_at = 0;
                    if(isset($_POST["expires"]))
                    {
                        $dt = new DateTime($_POST["expires_at"]);
                        $expires_at = $dt->getTimestamp();
                    }

                    $serial_key = $_POST["serial_key"];
                    $permissions = !empty($_POST["permissions"]) ? join(",",$_POST["permissions"]):"";
                    $query = $db->query("INSERT INTO serial_keys (serial_key, created, status, expire_at, permissions) VALUES (?,?,?,?,?)",$serial_key, time(), "reissued", $expires_at, $permissions);
                    utils::notifAlert("success", "Serial key was added!");
                }
                else
                {
                    utils::notifAlert("danger", "Don't leave serial key empty!");
                }
            }
            else
            {
                utils::notifAlert("danger","You are not logged in");
            }
        }
    }

    public static function addUser()
    {
        global $db;
        if(isset($_POST["adduser"]))
        {
            if(user::rankIsMax(1))
            {
                if($_POST["username"]!="" & $_POST["password"]!="" &  $_POST["rank"] !="")
                {
                    $password = hash("sha256", $_POST["password"]);
                    $query = $db->query("INSERT INTO users (username, password, rank, created) VALUES (?,?,?,?)", $_POST["username"], $password, $_POST["rank"], time());
                    utils::notifAlert("success", "User was added!");
                }
                else
                {
                    utils::notifAlert("danger", "Don't leave anything empty!");
                }
            }
            else
            {
                utils::notifAlert("danger","You don't have permission");
            }
        }
    }

    public static function updateKeyStatus()
    {
        global $db;
        if(isset($_POST["updateKey"]))
        {
            if(user::rankIsMax(2))
            {
                $status = $_POST["status"]; 
                $key = $_POST["serial_key"];
                $query = $db->query("UPDATE serial_keys SET status=? WHERE serial_key=?", $status, $key);
                utils::notifalert("success","Serial key has been updated");
            }
            else
            {
                utils::notifAlert("danger","Your are not logged in");
            }
        }
    }
    public static function getkey()
    {
        global $db;
        if(isset($_GET["key"]))
        {
            if(user::rankIsMax(1))
            {
                $query = $db->query("SELECT * FROM serial_keys WHERE serial_key=?", $_GET["key"]);
                if($query->rowCount()!=0)
                {
                    $f = $query->fetch();
                    return $f;
                }
                else
                {
                    utils::notifAlert("danger", "Serial key could not be found");
                    return null;
                }
            }
            else
            {
                utils::notifAlert("danger","Your are not logged in");
                return null;
            }
        }
    }
    public static function getAllKeys()
    {
        global $db;
        if(user::rankIsMax(1))
        {
            $query = $db->query("SELECT * FROM serial_keys");
            return $query->fetchAll(); 
        }
        else
        {
            utils::notifAlert("danger", "You don't have permission");
            return null;
        }
    }
    public static function getAllTrials()
    {
        global $db;
        if(user::rankIsMax(1))
        {
            $query = $db->query("SELECT * FROM trials");
            return $query->fetchAll(); 
        }
        else
        {
            utils::notifAlert("danger", "You don't have permission");
            return null;
        }
    }
    public static function getAllUsers()
    {
        global $db;
        if(user::rankIsMax(1))
        {
            $query = $db->query("SELECT * FROM users");
            return $query->fetchAll(); 
        }
        else
        {
            utils::notifAlert("danger", "You don't have permission");
            return null;
        }
    }

    public static function deleteKey()
    {
        global $db;
        if(isset($_POST["delete_key"]))
        {
            if(user::rankIsMax(1))
            {
                $query = $db->query("DELETE FROM serial_keys WHERE id=?", $_POST["delete_key"]);
            }
            else
            {
                utils::notifAlert("danger", "You don't have permission");
            }
        }
    }
    public static function deleteTrial()
    {
        global $db;
        if(isset($_POST["delete_trial"]))
        {
            if(user::rankIsMax(1))
            {
                $query = $db->query("DELETE FROM trials WHERE id=?", $_POST["delete_trial"]);
            }
            else
            {
                utils::notifAlert("danger", "You don't have permission");
            }
        }
    }
    public static function deleteUser()
    {
        global $db;
        if(isset($_POST["delete_user"]))
        {
            if(user::rankIsMax(1))
            {
                $query = $db->query("DELETE FROM users WHERE id=?", $_POST["delete_user"]);
            }
            else
            {
                utils::notifAlert("danger", "You don't have permission");
            }
        }
    }

    public static function updateEdit()
    {
        global $db;
        if(isset($_POST["update_edit_key"]))
        {
            if(user::rankIsMax(1))
            {
                $data = [];

                $query = $db->query("SELECT * FROM serial_keys WHERE id=?", $_POST["update_edit_key"]);
                $f = $query->fetch();

                $data["id"] = $f["id"];
                $data["serial_key"] = $f["serial_key"];
                $data["status"] = $f["status"];
                $data["expire_at"] = utils::formatDate($f["expire_at"]);

                echo json_encode($data);
            }
            else
            {
                utils::notifAlert("danger", "You don't have permission");
            }
        }
        elseif(isset($_POST["update_edit_trial"]))
        {
            if(user::rankIsMax(1))
            {
                $data = [];

                $query = $db->query("SELECT * FROM trials WHERE id=?", $_POST["update_edit_trial"]);
                $f = $query->fetch();

                $data["id"] = $f["id"];
                $data["status"] = $f["status"];
                $data["expires_at"] = utils::formatDate($f["expires_at"]);

                echo json_encode($data);
            }
            else
            {
                utils::notifAlert("danger", "You don't have permission");
            }
        }
        elseif(isset($_POST["update_edit_user"]))
        {
            if(user::rankIsMax(1))
            {
                $data = [];

                $query = $db->query("SELECT * FROM users WHERE id=?", $_POST["update_edit_user"]);
                $f = $query->fetch();

                $data["id"] = $f["id"];
                $data["username"] = $f["username"];
                $data["rank"] = $f["rank"]; 

                echo json_encode($data);
            }
            else
            {
                utils::notifAlert("danger", "You don't have permission");
            }
        }
    }

    public static function updateKey()
    {
        global $db;
        if(isset($_POST["editkey"]))
        {
            if(user::rankIsMax(1))
            {
                if($_POST["expires_at"] != "")
                {
                    $dt = new DateTime($_POST["expires_at"]);
                    $expires_at = $dt->getTimestamp();
                }
                else
                {
                    $expires_at = 0;
                }

                $query = $db->query("UPDATE serial_keys SET serial_key=?, status=?, expire_at=? WHERE id=?", $_POST["serial_key"], $_POST["status"], $expires_at, $_POST["id"]);
            }
            else
            {
                utils::notifAlert("danger", "You don't have permission");
            }
        }
    }
    public static function updateTrial()
    {
        global $db;
        if(isset($_POST["edittrial"]))
        {
            if(user::rankIsMax(1))
            {
                if($_POST["expires_at"] != "")
                {
                    $dt = new DateTime($_POST["expires_at"]);
                    $expires_at = $dt->getTimestamp();
                }
                else
                {
                    $expires_at = 0;
                }

                $query = $db->query("UPDATE trials SET status=?, expires_at=? WHERE id=?", $_POST["status"], $expires_at, $_POST["id"]);
            }
            else
            {
                utils::notifAlert("danger", "You don't have permission");
            }
        }
    }
    public static function updateUser()
    {
        global $db;
        if(isset($_POST["edituser"]))
        {
            if(user::rankIsMax(1))
            {
                $query = $db->query("UPDATE users SET username=?, rank=? WHERE id=?", $_POST["username"], $_POST["rank"], $_POST["id"]);

                if($_POST["password"]!="")
                {
                $password = hash("sha256", $_POST["password"]);
                $query = $db->query("UPDATE users SET password=? WHERE id=?", $password, $_POST["id"]);
                }
            }
            else
            {
                utils::notifAlert("danger", "You don't have permission");
            }
        }
    }


}

?>