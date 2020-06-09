<?php
//Config v.0.1


require $_SERVER['DOCUMENT_ROOT'] .'/vendor/firebase/php-jwt/src/JWT.php';

//Debugging 
error_reporting(0);
ini_set('display_errors', 'On');
set_time_limit(0);

//Dont change
define('APP_SECRET','VWWsiFrZHvPw55rf98SArA4WzP3qVCGz');

//Config
$config["url"] = "http://cloudextractors.online";
$config["domain"] = "cloudextractors.online";

$config["api_url"] = "http://cloudextractors.online:8000";

class database extends PDO
{
    const PARAM_host = "localhost";
    const PARAM_usr = "root";
    const PARAM_pass = "ultimate123";
    const PARAM_db = "datascraper";

    public function __construct($options=null)
    {
        parent::__construct('mysql:host='.database::PARAM_host.';dbname='.database::PARAM_db, database::PARAM_usr,database::PARAM_pass, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
        ));
    }

    public function query($query)
    { 
        //SQLi secured values
        $args = func_get_args();
        array_shift($args); 

        $response = parent::prepare($query);
        $response->execute($args);
        return $response;

    }
}
session_start();
$db = new database();
?>