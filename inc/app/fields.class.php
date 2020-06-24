<?php 

class fields
{
    private static function data($page)
    {
        switch($page)
        {
            case "companyleads":
                return array(
                    "fields" => array("CIN/LLP","Company name","Email","Address","ROC","Category","Subcategory","Class","Status","Authorized Capital","Paid Up Capital","Date of Incorporation"),
                    "inputs" => array(array("name"=>"Keyword","type"=>"text","required"=>True),array("name"=>"Limit","type"=>"number","required"=>False))
                );
            case "facebook":
                return array(
                    "fields" => array("Id","Category","Name","Address","Latitude","Longitude","Phone","Checkins","Website","Is verified","Link"),
                    "inputs" => array(array("name"=>"Token","type"=>"Password","required"=>True),array("name"=>"Keyword","type"=>"text","required"=>True),array("name"=>"Location","type"=>"text","required"=>True),array("name"=>"Radius","type"=>"number","required"=>False),array("name"=>"Limit","type"=>"number","required"=>False))
                );
            case "googlemaps":
                return array(
                    "fields" => array("Name","Category","Address","Phone","Score","Ratings","Website"),
                    "inputs" => array(array("name"=>"Keyword","type"=>"text","required"=>True),array("name"=>"Location","type"=>"text","required"=>True),array("name"=>"Limit","type"=>"number","required"=>False))
                );
            case "indiamart":
                return array(
                    "fields" => array("Product","Company","Website","Address","Contact person","Phone","GST","Year of Establishment"),
                    "inputs" => array(array("name"=>"Keyword","type"=>"text","required"=>True),array("name"=>"Location","type"=>"text","required"=>False),array("name"=>"Limit","type"=>"number","required"=>False))
                );
            case "justdial":
                return array(
                    "fields" => array("Category","Company","Address","Email","Numbers","Latitude","Longitude","Rating","Votes","Verified","Trusted","Website"),
                    "inputs" => array(array("name"=>"Keyword","type"=>"text","required"=>True),array("name"=>"Location","type"=>"text","required"=>False),array("name"=>"Limit","type"=>"number","required"=>False),array("name"=>"Emails","type"=>"checkbox","required"=>False))
                );
            case "linkedin":
                return array(
                    "fields" => array("Email"),
                    "inputs" => array(array("name"=>"Query","type"=>"tags","required"=>True),array("name"=>"Country","type"=>"countries","required"=>True),array("name"=>"Limit","type"=>"number","required"=>False))
                );
            case "tradeindia":
                return array(
                    "fields" => array("Product","Company","Contact Name","Phone", "Address", "Year of Establishment"),
                    "inputs" => array(array("name"=>"Keyword","type"=>"text","required"=>True),array("name"=>"Location","type"=>"text","required"=>False),array("name"=>"Limit","type"=>"number","required"=>False))
                );
        }
    }

    public static function format($page, $type)
    {

        if(user::isPermitted($page))
        {
            switch($type)
            {
                case "form":
                    $forms = "";
                    foreach(self::data($page)["inputs"] as $input)
                    {
                        if($input["required"])
                        {
                            $required = 'required=""';
                        }
                        else
                        {
                            $required = '';
                        }

                        if($input["type"]=="checkbox")
                        {
                            $forms .= '<div class="form-group"><div class="form-check">
							            <label class="form-check-label" for="input_'.$input["name"].'">
                                        <input type="'.$input["type"].'" name="'.$input["name"].'" class="form-check-input" id="input_'.$input["name"].'" '.$required.'">
                                        '.$input["name"].'
						            </div></div>';
                        }
                        elseif($input["type"]=="tags")
                        {
                            $forms .= '
                            <div class="form-group">
                            <label for="input_'.$input["name"].'">'.$input["name"].'</label>
                            <input name="'.$input["name"].'" id="input_'.$input["name"].'" style="display: none;">
                            </div>';
                        }
                        elseif($input["type"]=="countries")
                        {
                            $forms .= '
                            <div class="form-group">
                            <label for="input_'.$input["name"].'">'.$input["name"].'</label>
                            <select class="chosen-select" name="'.$input["name"].'" id="input_'.$input["name"].'">';

                            $countries = json_decode(file_get_contents(DOCR."vendor/countries.json"), true);
                            foreach($countries as $po => $country)
                            {
                                $forms .= '<option value="'.$po.'">'.$country.'</option>';
                            }

                            $forms .='</select>
                            </div>';
                        }
                        else
                        {
                            $forms .= '<div class="form-group">
                                            <label for="input_'.$input["name"].'">'.$input["name"].'</label>
                                            <input min="1" name="'.$input["name"].'" type="'.$input["type"].'" class="form-control" id="input_'.$input["name"].'" '.$required.' autocomplete="off" placeholder="'.$input["name"].'">
                                        </div>';
                        }
                    }
                    return $forms;
                
                case "field":
                    $fields = "";
                    foreach(self::data($page)["fields"] as $field)
                    {
                        $fields .= "<th>".$field."</th>";
                    }
                    return $fields;
            }
        }
    }
    public static function printSidebar()
    {
        global $db;

        if(!isset($_SESSION["user"]))
        {
            echo "";
            return null;
        }
        $q = $db->query("SELECT `permissions` FROM users WHERE id=?", $_SESSION["user"]);
        $f = $q->fetch();
        $perms = explode(",",$f["permissions"]);
        unset($f);
        unset($q);

        $scrapers = array("1" => ["justdial","Justdial"], "2" => ["indiamart", "Indiamart"], "3" => ["tradeindia", "Tradeindia"], "4" => ["linkedin", "LinkedIn"], "5" => ["googlemaps", "Google Maps"], "6" => ["companyleads","Companyleads"], "7" => ["facebook", "Facebook"]);
        foreach($scrapers as $po => $scraper)
        {
            if(in_array($po, $perms))
            {
                echo '		
                <li class="nav-item">
                    <a href="'.$scraper[0].'" class="nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-box link-icon"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        <span class="link-title">'.$scraper[1].'</span>
                    </a>
                </li>';
            }
        }
    }
    public static function isScraper($scraper)
    {
        $scrapers = array("companyleads","facebook","googlemaps","indiamart","justdial","linkedin","tradeindia");
        return in_array($scraper, $scrapers);
    }
}
