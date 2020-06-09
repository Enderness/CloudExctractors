<?php
//Config v.0.1

require '../vendor/autoload.php';
require '../vendor/firebase/php-jwt/src/JWT.php';

//Debugging 
error_reporting(E_ALL);
ini_set('display_errors', 'On');
set_time_limit(0);

//Dont change
define('APP_SECRET','VWWsiFrZHvPw55rf98SArA4WzP3qVCGz');

//Config
$config["url"] = "http://localhost";
$config["domain"] = "localhost";


class database extends PDO
{
    const PARAM_host = "localhost";
    const PARAM_usr = "root";
    const PARAM_pass = "";
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