<?php
class utils
{
    public static function notifAlert($type, $text)
    {
        if($type == "danger")
        {
            $type = "error";
        }
        echo '<script>
              var notification = ["'.$type.'","'.$text.'"];
              </script>'; 
    }

    public static function ipCountry($ip)
    {
        duzun\hQuery::$cache_path = DOCR."cache";
        duzun\hQuery::$cache_expires = 999999;
        $d = duzun\hQuery::fromURL("http://ip-api.com/json/".$ip);
        return @json_decode($d, true)["country"];
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