<?php

class scraper
{
    static function start($page)
    {
        if(isset($_SESSION["serial_key"]))
        {
            $page()::_construct();
            $this->start();
        }
    }
}

?>