<?php

use Symfony\Component\HttpClient\CachingHttpClient;

class facebook extends scraper
{
    public $keyword;
    public $limit;
    public $fb_token;
    public $location;
    public $radius;

    public $error;

    private $url;

    function __construct()
    {
       parent::__construct(); 

        //Main args
        if(empty($_GET["Keyword"]) || empty($_GET["Token"]) || empty($_GET["Location"]))
        {
            $this->error = "Don't leave anything empty!";
            return;
        }

        $this->keyword = $_GET["Keyword"];
        $this->fb_token = $_GET["Token"];
        $this->location = $_GET["Location"];
        $this->radius = ((!empty($_GET["Radius"]) && @is_numeric($_GET["Radius"])) ? (int)($_GET["Radius"]*1000):1000); //FB takes in meters we take in KM
        $this->limit = ((!empty($_GET["Limit"]) && @is_numeric($_GET["Limit"])) ? (int)$_GET["Limit"]:10);

        $fields = "id, category_list, name, location, phone, checkins, website, is_verified, link";
        $coords = $this->getCoords();

        $this->url = "https://graph.facebook.com/search?type=place&q=".$this->keyword."&fields=".$fields."&center=".$coords."&distance=".$this->radius."&access_token=".$this->fb_token;


    }

    private function getCoords()
    {
        $url = "https://nominatim.openstreetmap.org/search/?addressdetails=1&q=".$this->location."&format=json&limit=1";
        $r = $this->client->request("GET",$url);
        try
        {
            return json_decode($r->getContent(false),true)[0]["lat"].",".json_decode($r->getContent(),true)[0]["lon"];
        }
        catch (Exception $e)
        {
            $this->error = "Invalid location!";
            return "";
        }
    }

    private function getData()
    {
        $r = $this->client->request("GET", $this->url);
        $d = json_decode($r->getContent(), true);

        if(@$d["error"]["type"] == "OAuthException")
        {
            $this->error = "Invalid API token!";
            return array();
        }

        foreach($d["data"] as $entry)
        {
            //This fixes some common key errors
            $entry = array_merge([
                "phone" => "",
                "website" => ""
            ], $entry);

            $location = array_merge([
                "street" => "",
                "city" => "",
                "zip" => "",
                "latitude" => "",
                "longitude" => ""
            ], $entry["location"]);

            $address = join(", ", [$location["street"], $location["city"], $location["zip"]]);

            $names = array();
            foreach($entry["category_list"] as $key => $category)
            { 
                $names[$key] = $category["name"];
            }
            $category = join(", ", $names);
            unset($names);

            yield [$entry["id"], $category, $entry["name"], $address, $location["latitude"], $location["longitude"], $entry["phone"], $entry["checkins"], $entry["website"], (bool)$entry["is_verified"] ? "true":"false", $entry["link"]];
        }
    }

    protected function getEntries()
    {
        foreach($this->getData() as $entry)
        {
            yield $entry;
        }
    }
}

?>