<?php

/**
 * Informations of the add/edit form will be checked if it's a valid input.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package Agent_AddPages
 */

namespace AgentAddPages\Model\Service;

define("REQUIRED", "1");    # array( 1 => array( "error" => 1, "options" => "" ));
define("MAXLENGTH", "2");   # array( 2 => array( "error" => 1, "options" => array( "maxlength" => $maxlength, "actuallength" => $strlen ) ));
define("YEAR", "3");        # array( 3 => array( "error" => 1, "options" => array( "enteredyear" => $year ) ));
define("DATE", "4");        # array( 4 => array( "error" => 1, "options" => array( "entereddate" => $date ) ));  [$date = JJJJMMDD]
define("EMAIL", "5");       # array( 5 => array( "error" => 1, "options" => "" ));
define("DIGITFIELD", "6");  # array( 6 => array( "error" => 1, "options" => "" ));
define("ZIP", "7");         # array( 7 => array( "error" => 1, "options" => "" ));
define("PAYMENT", "8");     # array( 8 => array( "error" => 1, "options" => "" ));
define("BOOL", "9");        # array( 9 => array( "error" => 1, "options" => "" ));
define("MAINTEXTANDPAGEID", "10");# array( 10 => array( "error" => 1, "options" => "" ));
define("TODATELOWERFROMDATE", "11");# array( 11 => array( "error" => 1, "options" => "" ));
define("URL", "12");        # array( 12 => array( "error" => 1, "options" => "" ));
define("NOTALLOWEDFILEEXTENSION", "13");        # array( 13 => array( "error" => 1, "options" => "" ));
define("NOSELECTIONDONE", "14");        # array( 14 => array( "error" => 1, "options" => "" ));

class isValid
{

    public function __construct()
    {
        $this->allowedKeys = array(
            "pageID",
            "templateID",
            "pageCode",
            "uploadfield");

        $this->errors = array();
    }

    public function setValues($array)
    {
        $this->array = $array;
    }

    public function validate()
    {
        $valid = true;
        foreach ($this->allowedKeys as $key) {
            $function = $key . "Validate";
            $result = $this->$function($this->array[$key]);
            if ($result == false) {
                $valid = false;
            }
        }

        return $valid;
    }

    private function addError($key, $number, $array = false)
    {
        $this->errors[$key][$number]['error'] = 1;
        $this->errors[$key][$number]['options'] = $array;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getErrorsByKey($key)
    {
        return $this->errors[$key];
    }

    private function pageIDValidate($value)
    {
        $bool = true;
        
        if(!$this->requiredValidation("pageID", $value)){
            $bool = false;
        }
        
        if(!is_int($value)){
            $bool = false;
            $this->addError("pageID", 6);
        }
        
        return $bool;
    }
    
    private function templateIDValidate($value)
    {
        if($value == 0){
            $this->addError("templateID", 14);
            return false;
        }
        return true;
    }
    
    private function pageCodeValidate($value)
    {
        
        if(!$this->requiredValidation("pageCode", $value)){
            return false;
        }
        return true;
    }
    
    private function uploadfieldValidate($fileDataArray)
    {
        if(empty($fileDataArray["name"])) {
            return true;
        }
        $uploadExtension = \lw_io::getFileExtension($fileDataArray["name"]);
        
        if($uploadExtension == "txt" || $uploadExtension == "cps") {
            return true;
        }
        
        $this->addError("uploadfield", 13);
        return false;
    }
    

    function defaultValidation($key, $value, $length)
    {
        $bool = true;

        if (strlen($value) > $length) {
            $this->addError($key, 2, array("maxlength" => $length, "actuallength" => strlen($value)));
            $bool = false;
        }

        if ($bool == false) {
            return false;
        }
        return true;
    }

    public function requiredValidation($key, $value)
    {
        if ($value == "") {
            $this->addError($key, 1);
            return false;
        }
        return true;
    }

}