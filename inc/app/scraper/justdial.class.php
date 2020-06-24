<?php

use duzun\hQuery;
use Symfony\Component\HttpClient\CachingHttpClient;

class justdial extends scraper
{

    public $keyword;
    public $location;
    public $limit;
    public $emails; // Boolean if use email scraping

    public $error;

    private $url;
    private $headers;

    function __construct()
    {

        parent::__construct();

        //Main args
        if(empty($_GET["Keyword"]))
        {
            $this->error = "Keyword is empty!";
            return;
        }

        $this->keyword = $_GET["Keyword"];
        $this->location = $_GET["Location"];
        $this->emails = (@$_GET["Emails"]=="on");
        $this->limit = ((!empty($_GET["Limit"]) && @is_numeric($_GET["Limit"])) ? (int)$_GET["Limit"]:10);

        $this->url = "https://www.justdial.com/functions/ajxsearch.php?national_search=0&act=pagination&city=".$this->location."&search=".$this->keyword."&page=";
        $this->headers = [
            "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36",
            "Referer" => "https://www.justdial.com/".$this->location."/".$this->keyword,
            "Accept-Language" => "en-EN"
        ];
        $this->getCookies();
        

    }

    private function getCookies()
    {
        $r = $this->client->request("GET", "https://www.justdial.com/", [
            "headers" => $this->headers
        ]);
        $cookies = $r->getHeaders(false)["set-cookie"];
        $this->headers["cookie"] = explode(";",$cookies[0])[0]."; ".explode(";",$cookies[1])[0];

    }

    private function getUrls($page)
    {
        $r = $this->client->request("GET", $this->url.$page, [
            "headers" => $this->headers
        ]);

        $d = hQuery::fromHTML(json_decode($r->getContent(),true)["markup"]);
        unset($r);

        $links = array();
        foreach($d->find("li.cntanr") as $po => $link)
        {
            $links[$po] = $link->attr("data-href");
        }

        return $links;
    }

    /*
     * Justdial tries to hide phone numbers with a weird icon class randomizer.
     * This method returns a dictionary with the real numbers of each icon
     */
    private function getNumbers($html)
    {
        preg_match("/grayscale}(.*?).mobilesv{/", $html, $match);
        $codes = explode(".", $match[1]);

        $decode = array();
        for ($i=0; $i < 11; $i++) 
        { 
            preg_match("/(.*?):bef/", $codes[$i+1], $match);

            if($i < 10)
            {
                $decode[$match[1]] = (string)$i;
            }
            else
            {
                $decode[$match[1]] = "+";
            }
        }
        return $decode;
    }

    private function getCoords($location)
    {
        $url = "https://nominatim.openstreetmap.org/search/?addressdetails=1&q=".$location."&format=json&limit=1";
        $r = $this->client->request("GET",$url);

        if(count($data = json_decode($r->getContent(false), true)) != 0)
        {
            return $data[0];
        }
        else
        {
            return ["lat" => "","lon" => ""];
        }
    }

    private function getData($url)
    {
        $r = $this->client->request("GET", $url, [
            "headers" => $this->headers
        ]);
        $d = hQuery::fromHTML($r->getContent(false));
        unset($r);

        $decode = $this->getNumbers($d->find("style")->eq(1)->text());

        $contact_div = $d->find("div.paddingR0");

        $category = array();
        foreach($contact_div->find("a.lng_also_lst1") as $po => $cat)
        {
            $category[$po] = $cat->attr("title");
        }
        $category = join(", ", $category);

        $company = $d->find("span.fn")->eq(0)->text();
        $address = $contact_div->find("span.lng_add")->eq(0)->text();

        $numbers = array();
        foreach($contact_div->find("a.tel") as $po => $num)
        {
            $buffer = array();
            foreach($num->find("span.mobilesv") as $po1 => $num1)
            {
                $buffer[$po1] = $decode[explode(" ",$num1->attr("class"))[1]];
            }
            $numbers[$po] = join("",$buffer);
        }
        $numbers = join(", ", $numbers);

        $coords = $this->getCoords(str_replace("\t","",$d->find("span.lng_add")->eq(0)->text()));
        $latitude = $coords["lat"];
        $longitude = $coords["lon"];
        unset($coords);

        $votes = str_replace(" ", "", $d->find("span.votes")->eq(0)->text());
        $rating = $d->find("span.value-titles")->eq(0)->text();
        $verified = ($d->find("span.jd_verified") != null) ? "true":"false";
        $trusted = ($d->find("span.jd_trusted") != null) ? "true":"false";

        $website = $contact_div->find("i.web_ic");
        if($website != null)
        {
            $website = $website->nextElementSibling()->children()[0]->attr("href");
        }


        $email = "";
        if($this->emails && $website != "")
        {
            $r = $this->client->request("GET", $website, [
                "headers" => $this->headers
            ]);
            preg_match("/[a-z0-9\.\-+_]+@[a-z0-9\.\-+_]+\.[a-z]+/", $r->getContent(false), $match);
            $email = @join(", ", $match);
        }

        return [$category, $company, $address, $email, $numbers, $latitude, $longitude, $rating, $votes, $verified, $trusted, $website];
    }

    protected function getEntries()
    {
        $page = 1;
        while(true)
        {
            $links = $this->getUrls($page);
            if(count($links)==0)
            {
                return [];
            }

            foreach($links as $link)
            {
                yield $this->getData($link);
            }
            $page++;
        }
    }
}

?>
