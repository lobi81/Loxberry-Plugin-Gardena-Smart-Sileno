<?php

/**
* Ref. http://www.roboter-forum.com/showthread.php?16777-Gardena-Smart-System-Analyse
*/
class gardena
{
    var $user_id, $token, $locations;
    var $devices = array();
    
    const LOGINURL = "https://sg-api.dss.husqvarnagroup.net/sg-1/sessions";
    const LOCATIONSURL = "https://sg-api.dss.husqvarnagroup.net/sg-1/locations/?user_id=";
    const DEVICESURL = "https://sg-api.dss.husqvarnagroup.net/sg-1/devices?locationId=";
    const CMDURL = "https://sg-api.dss.husqvarnagroup.net/sg-1/devices/|DEVICEID|/abilities/mower/command?locationId=";
        
    var $CMD_MOWER_PARK_UNTIL_NEXT_TIMER = array("name" => "park_until_next_timer");
    var $CMD_MOWER_PARK_UNTIL_FURTHER_NOTICE = array("name" => "park_until_further_notice");
    var $CMD_MOWER_START_RESUME_SCHEDULE = array("name" => "start_resume_schedule");
    var $CMD_MOWER_START_06HOURS = array("name" => "start_override_timer", "parameters" => array("duration" => 360));
    var $CMD_MOWER_START_24HOURS = array("name" => "start_override_timer", "parameters" => array("duration" => 1440));
    var $CMD_MOWER_START_3DAYS = array("name" => "start_override_timer", "parameters" => array("duration" => 4320));
    
    const CATEGORY_MOWER = "mower";
    const CATEGORY_GATEWAY = "gateway";
    
    const PROPERTY_STATUS = "status";
    
    const ABILITY_CONNECTIONSTATE = "radio";
    
    public function __construct($user, $pw)
    {
     $data = array(
            "sessions" => array(
                "email" => "$user", "password" => "$pw")
                );                     
                                                               
        $data_string = json_encode($data);                                                                                   
                                                                                                                             
        $ch = curl_init(self::LOGINURL);                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type:application/json',                                                                                
            'Content-Length: ' . strlen($data_string))                                                                       
        );   
            
        $result = curl_exec($ch);
        $data = json_decode($result);

        $this -> token = $data -> sessions -> token;
        $this -> user_id = $data -> sessions -> user_id;
        
        $this -> loadLocations();
        $this -> loadDevices(); 
    }
    
    function gardena($user, $pw)
    {
    	self::__construct($user, $pw);
               
    }
    
    
    function loadLocations()
    {                                       
        $url = self::LOCATIONSURL . $this -> user_id;
                                                                                                                             
        $ch = curl_init($url);                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                                                                                     
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type:application/json',                                                                                
            'X-Session:' . $this -> token)                                                                       
        );   
            
        $this -> locations = json_decode(curl_exec($ch)) -> locations;  
                                                                       
    }
    
    function loadDevices()
    {         
        foreach($this->locations as $location)
        {
            $url = self::DEVICESURL . $location -> id;
                                                                                                                                 
            $ch = curl_init($url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                                                                                     
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type:application/json',                                                                                
                'X-Session:' . $this -> token)                                                                       
            );   
                
            $this -> devices[$location -> id] = json_decode(curl_exec($ch)) -> devices;
        }
    }
         
           
    /**
    * Finds the first occurrence of a certain category type.
    * Example: You want to find your only mower, having one or more gardens. 
    * 
    * @param constant $category
    */
    function getFirstDeviceOfCategory($category)
    {
        foreach($this -> devices as $locationId => $devices)
        {        
            foreach($devices as $device)
                if ($device -> category == $category)
                    return $device;
        }
    }
    
    function getDeviceLocation($device)
    {
        foreach($this -> locations as $location)
            foreach($location -> devices as $d)
                if ($d == $device -> id)
                    return $location;
    }
    
      
    function sendCommand($device, $command)
    {
        $location = $this -> getDeviceLocation($device);
        
        $url = str_replace("|DEVICEID|", $device -> id, self::CMDURL) . $location -> id;
                             
        $data_string = json_encode($command);       
       
        $ch = curl_init($url);                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type:application/json',                                                                                
            'X-Session:' . $this -> token,
            'Content-Length: ' . strlen($data_string)
            ));  

        $result =  curl_exec($ch);        
        
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == "204") //success
            return true;
            
        return json_encode($result);
    }       
    
    function getMowerState($device)
    {
        return $this->getPropertyData($device, $this::CATEGORY_MOWER, $this::PROPERTY_STATUS) -> value;
    }
    
    function getDeviceStatusReportFriendly($device)                                        
    {
        $result = "";
        foreach ($device -> status_report_history as $entry)
        {               
             $result .= $entry -> timestamp . " | " . $entry -> source . " | " . $entry -> message . "<br>";
        }                                                           
        
        return $result;
    }
    
    function getAbilityData($device, $abilityName)
    {
        foreach($device -> abilities as $ability)
            if ($ability -> name == $abilityName)
                return $ability;
    }
    
    function getPropertyData($device, $abilityName, $propertyName)
    {
        $ability = $this->getAbilityData($device, $abilityName);
        
        foreach($ability -> properties as $property)
            if ($property -> name == $propertyName)
                return $property;
    }
    
    function getInfoDetail($device, $category_name, $proberty_name)
    {
    	$test = "";
        foreach ($device -> abilities as $ability)
        {
            if ($ability -> name == $category_name)
            {
                foreach($ability -> properties as $property)
                {
                    if ($property -> name == $proberty_name)
                    {
                    	//if($property -> value
                   		$test = var_dump($property -> value);
                    	if (sizeof($property -> supported_values) > 0){
                    	$test = $test . "---Key:";
                    	$test = $test . array_search ($property -> value, $property -> supported_values);
      					$test = $test . "<br>Possible Values for $proberty_name: ";
                		$test = $test . var_export($property -> supported_values, true);
                    	}
                    	
                    }
                }
            }
        }
        return $test;
    }
    
    /**
    * Note "quality 80" seems to be quite the highest possible value (measured with external antenna and 2-3 meters distance)
    * 
    * @param mixed $device
    */
    function getConnectionDataFriendly($device)
    {
        $ability = $this->getAbilityData($device, $this::ABILITY_CONNECTIONSTATE);
        
        $properties = array('quality', 'connection_status', 'state');
        
        foreach ($properties as $property)
        {
            $p = $this->getPropertyData($device, $ability -> name, $property);
            
            echo $property . ": " . $p -> value . " | " . $p -> timestamp . "<br>";
        }
    }
}


?>