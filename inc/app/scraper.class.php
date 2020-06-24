<?php

use duzun\hQuery;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\HttpCache\Store;

/*

INFO

I am using httpClient to do the requests and hQuery by duzun to parse the html.
Some scrapers also require Phanton

*/

abstract class scraper
{
    abstract protected function getEntries();

    private $n;
    protected $config;

    function __construct($client = false)
    {
        /*
        TODO:
        - Add timer here
        - Better error handling between ajax and this instance
        - Add logs!
        */ 

        $this->config["cache_ttl"] = 259200;

        $client = $client ? $client:HttpClient::create(); 
        $store = new Store(DOCR.'cache/');

        $this->client = new CachingHttpClient($client, $store, ["private_headers"=>[],"default_ttl"=>$this->config["cache_ttl"],"debug"=>TRUE, "proxy"=>"http://localhost:7669", "verify_peer"=>FALSE, "verify_host"=>FALSE]); //Http client that uses caching 3 days persistence
    }

    //Get hQuery text without errors
    protected function text($node)
    {
        if($node != null)
        {
            return $node->text();
        }
        else
        {
            return "";
        }
    }

    /*
     *Get hQuery attribute without errors
     */
    protected function attr($node, $attr)
    {
        if($node != null)
        {
            return $node->attr($attr);
        }
        else
        {
            return "";
        }
    }

    public function start()
    {
        if($this->error!=null)
        {
            echo '{"error":"'.$this->error.'"}';
            return;
        }
        $this->n = 0;

        foreach($this->getEntries() as $entry)
        {
            if($this->n!=0)
            {
                echo ', ';
            }
            foreach($entry as $po => $x)
            {
                $entry[$po] = str_replace('"', '', $x);
            }
            echo('["'.join('","',$entry).'"]');
            ob_flush();
            flush();
            $this->n++;

            if($this->n >= $this->limit)
            {
                return $this->stop();
            }
        }
        return $this->stop();
    }

    public function stop()
    {
        /*
         * Results got => protected n
         * Timer stop here
         * Log errors here
         */
    }

}

?>