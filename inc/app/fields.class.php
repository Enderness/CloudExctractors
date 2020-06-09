<?php 

class fields
{
    private static function data($page)
    {
        switch($page)
        {
            case "companyleads":
                return array(
                    "fields" => array("CIN","Company name","Email","Address","ROC","Category","Subcategory","Class","Status","Authorized Capital","Paid Up Capital","Date of Incorporation"),
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
                    "inputs" => array(array("name"=>"Keyword","type"=>"text","required"=>True),array("name"=>"location","type"=>"text","required"=>False),array("name"=>"Maxpage","type"=>"number","required"=>False),array("name"=>"Emails","type"=>"checkbox","required"=>False))
                );
            case "linkedin":
                return array(
                    "fields" => array("Email"),
                    "inputs" => array(array("name"=>"Query","type"=>"tags","required"=>True),array("name"=>"Country","type"=>"text","required"=>True),array("name"=>"Limit","type"=>"number","required"=>False),array("name"=>"Delay","type"=>"checkbox","required"=>False))
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

        if(isset($_SESSION["serial_key"]))
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
    public static function isScraper($scraper)
    {
        $scrapers = array("companyleads","facebook","googlemaps","indiamart","justdial","linkedin","tradeindia");
        return in_array($scraper, $scrapers);
    }
}
