<?php

class tradeindia extends scraper
{
    
    public $keyword;
    public $location;
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
        $this->location = (!empty($_GET["Location"])) ? $_GET["Location"]:"";
        $this->limit = ((!empty($_GET["Limit"]) && @is_numeric($_GET["Limit"])) ? (int)$_GET["Limit"]:10);

		$this->headers = [
			"X-Requested-With" => "XMLHttpRequest"
        ];
		$this->url = "https://www.tradeindia.com/search.html?cities=".$this->location."&keyword=".$this->keyword."&page_no=";
    }

    private function getUrls($page)
    {   
        $r = $this->client->request("GET", $this->url.$page, [
            "headers" => $this->headers
        ]);
        $d = hQuery::fromHTML($r->getContent(false));
        unset($r);

        $links = array();

        if(($data = $d->find("div.title > a")) != null)
        {
            foreach($data as $link)
            {
                $links[] = $this->attr($link, "href");
            }
        }

        return $links;
    }

    private function getData($url)
    {
        $r = $this->client->request("GET", "https://www.tradeindia.com/".$url, [
            "headers" => $this->headers
        ]);
        $d = hQuery::fromHTML($r->getContent(false));
        unset($r);

        $product = $this->text($d->find("h1.fp-head"));
        $container = $d->find("div#contact-us");

        if($container == null)
        {
            return null;
        }

        $company = $this->text($container->find("a")->eq(0));
        $person_div = $container->find("span.co-address");

        if($person_div->count() >= 2)
        {
            $contact_name = $this->text($person_div->eq(0));
            $address = $this->text($person_div->eq(1));
        }
        

        $container = $d->find("div.seller-co-no");
        $number = "";
        if($container != null) $this->text($container->find("span")->eq(2));

        $container = $d->find("div.fp-info.mt30");
        $added = "";
        if($container != null)
        {
            foreach($container->find("span") as $row)
            {
                if($row->text() == "ESTABLISHMENT")
                {
                    $added = str_replace(" ", "", str_replace("\n","",$row->nextElementSibling()->text()));
                    break;
                }
            }
        }

        return [$product, $company, $contact_name, $number, $address, $added];
    }

    protected function getEntries()
    {
        $page = 1;

        while (true)
        {
            $links = $this->getUrls($page);
            if(count($links) == 0)
            {
                break;
            }

            foreach($links as $link)
            {
                if(($entry = $this->getData($link)) != null) yield $entry;
            }

            $page++;
        }
    }
}

?>