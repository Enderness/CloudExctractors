<?php
class utils
{
    public static function notifAlert($type, $text)
    {
        echo "<div class='alert alert-".$type."' role='alert'>".$text."</div>"; 
    }
    public static function formatDate($date)
    {
        if($date==0)
        {
            return "Never";
        }
        else
        {
            return date("Y-m-d",$date);
        }
    }
}

$utils = new utils;
?>