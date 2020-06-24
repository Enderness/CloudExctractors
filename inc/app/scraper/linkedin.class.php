<?php

use TRegx\CleanRegex\Pattern;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;


class linkedin extends scraper
{

    public $keywords;
    public $country;
    public $limit;

    public $error;

    protected $reponses;

    private $driver;
    private $headers;
    private $query;
    private $emails;

    function __construct()
    {
        register_shutdown_function([$this, "die"]);
        $this->responses = array();
        $this->emails = array();
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
        if(empty($_GET["Query"]) || empty($_GET["Country"]))
        {
            $this->error = "Input is empty!";
            return;
        }

        $this->keywords = explode(",",$_GET["Query"]);
        foreach($this->keywords as $po => $pe)
        {
            $this->keywords[$po] = '"'.$pe.'"';
        }

        $this->country = $_GET["Country"];
        $this->limit = ((!empty($_GET["Limit"]) && @is_numeric($_GET["Limit"])) ? (int)$_GET["Limit"]:10);

        $this->query = 'intitle:profiles inurl:dir/ site:'.$this->country.'.linkedin.com '.join(" AND ", $this->keywords).' @gmail.com OR @hotmail.com OR @yahoo.com OR @outlook.com';

        $this->driver = \Symfony\Component\Panther\Client::createChromeClient();
    }

    public function die()
    {
            $error = error_get_last();
            if ($error['type'] === E_ERROR) 
            {
                $this->driver->quit();
                echo $error;
                exit(0);
            }
    }

    private function getEmails()
    {
        $cr = $this->driver->waitFor("div#rso");
        $html = $cr->text();
        $pattern = '[\w\-\.]+@[\w\-]+\.[a-zA-Z-]{2,4}';
        return pattern($pattern)->match($html)->all();
    }

    private function nextPage()
    {

    }

    protected function getEntries()
    {

        $this->driver->request("GET", "https://google.com/?hl=en");
        $this->driver->waitFor("input.gNO89b");
        $this->driver->findElement(WebDriverBy::cssSelector("input[name='q']"))->sendKeys($this->query);
        $this->driver->findElement(WebDriverBy::cssSelector("input[name='q']"))->sendKeys(WebDriverKeys::RETURN_KEY);

        foreach($this->getEmails() as $email)
        {
            if(!in_array($email, $this->emails))
            {
                $this->emails[] = $email;
                yield [$email];
            }
        }

        //nextPage

    }

}

?>