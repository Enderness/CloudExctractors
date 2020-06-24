<?php

use duzun\hQuery;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;


class googlemaps extends scraper
{

    public $keyword;
    public $location;
    public $limit;
    public $error;

    protected $callback;

    private $url;
    private $driver;
    private $responses;
    private $memory;

    function __construct()
    {
        $this->responses = array();
        $this->callback = function ($method, $url, $body)
        {
            if(@$this->responses[$url] == null)
            {
                return new MockResponse("miss", ["http_code"=>666]);
            }
            else
            {
                return $this->responses[$url];
            }
        };

        parent::__construct(new MockHttpClient($this->callback));

        //Main args
        if(empty($_GET["Keyword"]) || empty($_GET["Location"]))
        {
            $this->error = "Input is empty!";
            return;
        }

        $this->keyword = $_GET["Keyword"];
        $this->location = $_GET["Location"];
        $this->limit = ((!empty($_GET["Limit"]) && @is_numeric($_GET["Limit"])) ? (int)$_GET["Limit"]:10);

        $this->url = "https://www.google.com/maps/search/".$this->keyword."+in+".$this->location."/&hl=en";
        $this->memory = array();
        
        $this->driver = \Symfony\Component\Panther\Client::createChromeClient();
        

    }


    private function waitRefresh($in = false)
    {
        if($in)
        {
            $by = Facebook\WebDriver\WebDriverBy::cssSelector(".section-refresh-overlay");
            $this->driver->wait(30, 250)->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated($by));
        }
        $by = Facebook\WebDriver\WebDriverBy::cssSelector(".section-refresh-overlay");
        $this->driver->wait(30, 250)->until(Facebook\WebDriver\WebDriverExpectedCondition::invisibilityOfElementLocated($by));
    }

    private function store($url, $body)
    {
        $this->responses[$url] = new MockResponse($body);
        $r = $this->client->request("GET", $url);
    }

    private function nextPage()
    {

        $crawler = $this->driver->waitFor("#n7lv7yjyC35__section-pagination-button-next");
        $next = $crawler->filter("#n7lv7yjyC35__section-pagination-button-next");

        if($next->attr("disabled") != true)
        {

            try
            {
                $next->click();
            }
            catch(Facebook\WebDriver\Exception\ElementClickInterceptedException $e)
            {
                $this->waitRefresh();
                $next = $this->driver->refreshCrawler()->filter("#n7lv7yjyC35__section-pagination-button-next");
                $next->click();

            }
            $this->waitRefresh(true);
            return true;
        }
        else
        {
            return false;
        }

    }


    private function loadData($crawler)
    {
        $place = urlencode($crawler->attr("aria-label"));
        if(in_array($place, $this->memory))
        {
            return false;
        }
        $r = $this->client->request("GET", "https://www.google.com/maps/place/".$place);
        if($r->getStatusCode() == 666)
        {
            try
            {
                $crawler->click();
            }
            catch (Facebook\WebDriver\Exception\ElementClickInterceptedException $e)
            {
                $this->waitRefresh();
                $crawler->click();
            }
            $crawler = $this->driver->waitFor(".section-hero-header-title-title");
            $source = $crawler->html();
            $this->store("https://www.google.com/maps/place/".$place, $source);
        }
        else
        {
            $source = $r->getContent();
        }

        $d = hQuery::fromHTML($source);

        unset($source);

        $this->memory[] = $place;
        unset($place);

        $name = $this->text($d->find("h1.section-hero-header-title-title"));
        $category = $this->text($d->find("button[jsaction='pane.rating.category']"));
        if($category == "") $category = $this->keyword;
        $address = str_replace("  ", "", $this->text($d->find("button[data-item-id='address']")));
        if(!empty($number = $d->find("img[src='//www.gstatic.com/images/icons/material/system_gm/1x/phone_gm_blue_24dp.png']")))
        {
        $number = str_replace(" ", "", $this->text($number->parent()->parent()->nextElementSibling()));
        }
        $score = $this->text($d->find("span.section-star-display"));
        $ratings = explode(" ", $this->attr($d->find("button[jsaction='pane.rating.moreReviews']"), "aria-label"))[0];
        $website = str_replace(" ", "", $this->text($d->find("button[data-item-id='authority']")));

        if($r->getStatusCode() == 666)
        {
            $crawler->filter(".section-back-to-list-button")->click();
        }

        return([$name, $category, $address, $number, $score, $ratings, $website]);
    }

    protected function getEntries()
    {
 
        $this->driver->request("GET", $this->url);
        
        while (True)
        {
            $subCount = $this->driver->waitFor(".section-result")->filter(".section-result")->count();

            for($i = 0; $i < $subCount; $i++)
            {
                $sub = $this->driver->waitFor(".section-result")->filter(".section-result");
                $data = $this->loadData($sub->eq($i));
                if($data != false)
                {
                    yield $data;
                }

            }
            if(!$this->nextPage())
            {
                break;
            }
        }
        $this->driver->close();

    }


}

?>