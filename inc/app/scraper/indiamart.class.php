<?php

use Symfony\Component\HttpClient\CachingHttpClient;
use \WSSC\WebSocketClient;
use \WSSC\Components\ClientConfig;

class indiamart extends scraper
{
    public $keyword;
    public $location;
    public $limit;

    public $error;

    private $im_token;
    private $url;
    private $headers;

    private $glid;
    private $geo;
    private $code;

    private $ws;
    private $email;

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
        $this->location = (!empty($_GET["Location"])) ? $_GET["Location"]: "";
        $this->limit = (!empty($_GET["Limit"]) && @is_numeric($_GET["Limit"])) ? (int)$_GET["Limit"]:10;

        $this->im_token = "imobile@15061981";
		$this->headers = [
			"User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36",
			"Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
			"Upgrade-Insecure-Requests" => "1"
        ];
        $_SERVER["SERVER_ADDR"] = "62.248.252.36";
        $this->checkForToken();

		$this->url = "https://dir.indiamart.com/search.mp/next?glid=".$this->glid."&ss=".$this->keyword."&cq=".$this->location."&pg=";

    }

    private function startEmail()
    {
        $config = new ClientConfig();
        $config->setTimeout(1000);
        $this->ws = new WebSocketClient("wss://dropmail.me/websocket", $config);

        $this->email = substr(explode(":", $this->ws->receive())[0], 1);
        $this->ws->receive();
    }

    private function waitForCode()
    {
        while(empty($data = $this->ws->receive()) || @count($data = explode("-", json_decode(substr($data,1), true)["subject"])) < 2)
        {
            
        }   
        return $data[1];
    }

    private function getLocation()
    {
        $body = [
            "modid" => "DIR",
            "token" => $this->im_token
        ];

        $r = $this->client->request("POST", "http://geoip.imimg.com/api/location.php", [
            "headers" => $this->headers,
            "body" => $body
        ]);
        $this->geo = json_decode($r->getContent(false), true)["Response"]["Data"];
    }

    private function getIdentity()
    {
        $body = [
            "username" => $this->email,
			"iso" => $this->geo["geoip_countryiso"],
			"modid" => "DIR",
			"format" => "JSON",
			"create_user" => "1",
			"originalreferer" => "test",
			"GEOIP_COUNTRY_ISO" => $this->geo["geoip_countryiso"],
			"ip" => $_SERVER["SERVER_ADDR"],
			"screen_name" => "Login+with+Full+Login+Form",
			"country" => $this->geo["geoip_countryname"]
        ];

        $r = $this->client->request("POST", "https://login.indiamart.com/user/identify/", [
            "headers" => $this->headers,
            "body" => $body
        ]);
        $this->glid = json_decode($r->getContent(false), true)["DataCookie"]["glid"];
    }

    private function getOTP()
    {
        $body = [
			"token" => $this->im_token,
			"email" => $this->email,
			"glusrid" => $this->glid,
			"modid" => "DIR",
			"user_mobile_country_code" => "123",
			"flag" => "OTPGen",
			"user_ip" => $_SERVER["SERVER_ADDR"],
			"user_country" => $this->geo["geoip_countryiso"],
			"process" => "OTP_Login_Desktop",
			"user_updatedusing" => "Login+With+OTP+Form+Desktop"
        ];
        $r = $this->client->request("POST", "https://login.indiamart.com/users/OTPVerification/", [
            "headers" => $this->headers,
            "body" => $body
        ]);
        if($r->getContent(false))
        {
            unset($r);
        }
        $this->code = $this->waitForCode();
    }
    
    private function verifyOTP()
    {
        $body = [
            "token" => $this->im_token,
			"email" => $this->email,
			"modid" => "DIR",
			"user_mobile_country_code" => "123",
			"flag" => "OTPVer",
			"user_ip" => $_SERVER["SERVER_ADDR"],
			"country_name" => $this->geo["geoip_countryname"],
			"auth_key" => $this->code,
			"glusrid" => $this->glid,
			"verify_process" => "ONLINE",
			"verify_screen" => "VERIFICATION+FROM+LOGIN+WITH+OTP+DESKTOP+FORM",
			"process" => "OTP_Login_Desktop",
			"user_country" => $this->geo["geoip_countryiso"]
        ];

        $r = $this->client->request("POST", "https://login.indiamart.com/users/OTPVerification/", [
            "headers" => $this->headers,
            "body" => $body
        ]);
        return json_decode($r->getContent(), true)["Response"]["LOGIN_DATA"]["im_iss"]["t"];
    }

    private function login()
    {
        $this->startEmail();
        $this->getLocation();
        $this->getIdentity();
        $this->getOTP();
        $akKey = $this->verifyOTP();

        apcu_store("indiamartToken", $akKey.";".$this->glid);
        unset($this->geo);
        unset($this->code);
        unset($this->ws);
        unset($this->email);

        return $akKey;
    }

    private function checkForToken()
    {
        if(!empty($key = apcu_fetch("indiamartToken")))
        {
            $key = explode(";",$key);
            $this->headers["cookie"] = "im_iss=".urlencode("t=".$key[0]);
            $this->glid = $key[1];
        }
        else
        {
            $this->headers["cookie"] = "im_iss=".urlencode("t=".$this->login());
        }
    }

    private function getUrls($page)
    {
        $r = $this->client->request("GET", $this->url.$page, [
            "headers" => $this->headers
        ]);
        $d = hQuery::fromHTML(json_decode($r->getContent(false), true)["content"]);
        unset($r);

        $links = array();
        if(($hrefs = $d->find("a.ptitle"))==null) return [];

        foreach($hrefs as $po => $link)
        {
            if($link->attr("href") != null && strpos($link->attr("href"), "indiamart")!==false)
            {
                $links[$po] = $link->attr("href");
            }
        }

        return $links;

    }

    private function getData($link)
    {


        $r = $this->client->request("GET", $link, [
            "headers" => $this->headers
        ]);
        $d = hQuery::fromHTML($r->getContent(false));
        unset($r);

        $product = str_replace('"', '', $this->text($d->find("h1.bo")));

        $contact_form = $d->find("div#pdp_sdtl");

        if($contact_form != null)
        {
            $company = $this->text($contact_form->find("a.pcmN"));
            $website = $this->attr($contact_form->find("a.pcmN"), "href");
            $address = $this->attr($contact_form->find("input#gmap_address"), "value");
            $contact_person = $this->text($contact_form->find("div#supp_nm"));
            $phone = $this->text($contact_form->find("span.duet"));
        }
        else
        {
            $company = "";
            $website = "";
            $address = "";
            $contact_person = "";
            $phone = "";
        }
        unset($contact_form);

        $company_about = $d->find("div.f16");

        $gst = "";
        $added = "";
        if($company_about != null)
        {
            foreach($company_about->find("span") as $span)
            {
                switch($span->text())
                {
                    case "GST":
                        $gst = $span->text();
                        break;

                    case "Year of Establishment":
                        $added = $span->text();
                        break;
                }
            }
        }

        return [$product, $company, $website, $address, $contact_person, $phone, $gst, $added];
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
                yield $this->getData($link);
            }
            $page++;
        }
    }
}

?>