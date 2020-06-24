<?php
//@author = Viljami Ranta
//@copyright = CloudExtractors

//Debugging 
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//Misc
set_time_limit(0);
session_start();

//Constants
define('APP_SECRET','VWWsiFrZHvPw55rf98SArA4WzP3qVCGz'); // Dont change
define('DOCR',$_SERVER["DOCUMENT_ROOT"]."/");

//Requires
require DOCR.'vendor/autoload.php';

//Config
$config["url"] = "http://localhost/";
$config["domain"] = "localhost";

//Database
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
$db = new database();
?>