<?php

use duzun\hQuery;
use Symfony\Component\HttpClient\CachingHttpClient;

class companyleads Extends scraper
{

    public $keyword;
    public $limit;

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
        $this->limit = ((!empty($_GET["Limit"]) && @is_numeric($_GET["Limit"])) ? (int)$_GET["Limit"]:10);

        //Misc
        $this->url = "https://www.indiancompany.info/company/search/".$this->keyword."/";
        $this->headers = ["User-Agent" => "Mozilla/5.0"];

        if(!empty(apcu_fetch("companyCookie")))
        {
            $this->headers["cookie"] = apcu_fetch("companyCookie");
        }
        else
        {
            $this->setLimit();
        }
    }

    //IndiaCompany by default only shows 25 results, but with a simple post we can get 100
    private function setLimit()
    {
        $r = $this->client->request("GET", $this->url, [
            "headers" => $this->headers,
            "extra" => ["no_cache" => true]
            ]);
        


        $d = hQuery::fromHTML($r->getContent());

        $csrf_token = $d->find("input[name='csrf_token']")->val();

        $data = [
            "page_size_select" => "100",
            "search_text" => $this->keyword,
            "csrf_token" => $csrf_token
        ];

        $this->headers["referer"] = $this->url;
        $this->headers["cookie"] = $r->getHeaders()["set-cookie"][0];

        $r = $this->client->request("POST", "https://www.indiancompany.info/company/_set_page_size/", [
            "headers" => $this->headers,
            "max_redirects" => 0,
            "body" => $data,
            "extra" => ["no_cache" => true]
            ]);

        $cookie = $r->getHeaders(false)["set-cookie"][0];
        $this->headers["cookie"] = $cookie;
        apcu_store("companyCookie",$cookie, $this->config["cache_ttl"]);

        unset($this->headers["referer"]);
    }

    private function getUrls($page)
    {
        $links = array();

        if($page!=1)
        {
            $pagestring = "page/".$page."/";
        }
        else
        {
            $pagestring = "";
        }

        $r = $this->client->request("GET", $this->url.$pagestring, [
            "headers" => $this->headers
        ]);

        $d = hQuery::fromHTML($r->getContent());
        $hrefs = $d->find("tbody > tr > td.break-word > a");
        foreach ($hrefs as $po => $a)
        {
            $links[$po] = "https://www.indiancompany.info".$a->attr("href");
        }

        return $links;
    }

    private function getData($link)
    {
        $r = $this->client->request("GET", $link, [
            "headers" => $this->headers
        ]);
        
        $d = hQuery::fromHTML($r->getContent());

        $type = explode(" ", $d->find("h3[class='details-section-header']")->text())[0]; //LLP or Company

        $itable = $d->find("table.details-table-custom")[0]; //Info table
        $ctable = $d->find("table.details-table-custom")[1]; //Contact table

        switch($type)
        {
            case "Company":
                $cin = $itable->find("th[title='Company CIN - Corporate Identification Number']")->nextElementSibling()->text();
                $name = $itable->find("th[title='Registered Name of the Company']")->nextElementSibling()->text();
                $roc = $itable->find("th[title='Registrar of Companies (RoC) where Company is registered']")->nextElementSibling()->text();
                $category = $itable->find("th[title='Category of the Company']")->nextElementSibling()->text();
                $subcategory = $itable->find("th[title='Sub-Category of the Company']")->nextElementSibling()->text();
                $class = $itable->find("th[title='Class of the Company']")->nextElementSibling()->text();
                $status = $itable->find("th[title='e-Filing Status of the Company']")->nextElementSibling()->text();
                $authcap = $itable->find("th[title='Authorized Capital of the Company']")->nextElementSibling()->text();
                $paidcap = $itable->find("th[title='Paid Up Capital of the Company']")->nextElementSibling()->text();
                $dateinc = $itable->find("th[title='Date of Incorporation of the Company']")->nextElementSibling()->text();

                $email = $ctable->find("th[title='Contact Email Address of the Company']")->nextElementSibling()->text();
                $address = $ctable->find("th[title='Registered Address of the Company']")->nextElementSibling()->text();

                break;

            case "LLP":
                $cin = $itable->find("th[title='LLPIN - LLP Identification Number']")->nextElementSibling()->text();
                $name = $itable->find("th[title='Registered Name of the LLP']")->nextElementSibling()->text();
                $roc = $itable->find("th[title='Registrar of Companies (RoC) where LLP is registered']")->nextElementSibling()->text();
                $category = "LLP";
                $subcategory = $itable->find("th[title='Main Business Activity Description']")->nextElementSibling()->text();
                $class = "Public";
                $status = $itable->find("th[title='Status of the LLP']")->nextElementSibling()->text();
                $authcap = $itable->find("th[title='Total Obligation of Contribution of the LLP']")->nextElementSibling()->text();
                $paidcap = "";
                $dateinc = $itable->find("th[title='Date of Incorporation of the LLP']")->nextElementSibling()->text();

                $email = $ctable->find("th[title='Contact Email Address of the LLP']")->nextElementSibling()->text();
                $address = $ctable->find("th[title='Registered Address of the LLP']")->nextElementSibling()->text();

                break;

        }

        return [$cin, $name, $email, $address, $roc, $category, $subcategory, $class, $status, $authcap, $paidcap, $dateinc];
    }

    protected function getEntries()
    {
        $page = 1;
        while(true)
        {
            $links = $this->getUrls($page);
            foreach($links as $link)
            {
                yield $this->getData($link);
            }

            if(count($links)==100)
            {
                $page++;
            }
            else
            {
                break;
            }
        }
    }
}

?>