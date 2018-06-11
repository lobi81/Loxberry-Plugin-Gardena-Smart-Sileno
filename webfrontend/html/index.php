<?php

    error_reporting(-1);
	ini_set('display_errors','On');
    include("data.inc.php");
    include("gardena.class.inc.php");
    
    $gardena = new gardena($pw_user_maeher, $pw_pawo_maeher);
    $mower = $gardena -> getFirstDeviceOfCategory($gardena::CATEGORY_MOWER);
    $gateway = $gardena -> getFirstDeviceOfCategory($gardena::CATEGORY_GATEWAY);
    
    //echo var_dump($gardena);
  	//echo "<br>";

	if(!empty($_GET["action"])) //action hat einen Wert
	{
		if ($_GET["action"] === "INFO")
		{
		    echo "<b>CATEGORY_MOWER</b><br>";
		   
		    $category_name = "device_info";
		    $proberty_name = "last_time_online";
		    echo "<b>$category_name</b><br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name); 
		    $proberty_name = "version";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    $proberty_name = "serial_number";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    
		    $category_name = "internal_temperature";
		    $proberty_name = "temperature";
		    echo "<br><br><b>$category_name</b><br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    
		    $category_name = "battery";
		    $proberty_name = "level";
		    echo "<br><br><b>$category_name</b><br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    $proberty_name = "rechargeable_battery_status";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    $proberty_name = "charging";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name) ;
		    
		    $category_name = "mower";
		    $proberty_name = "manual_operation";
		    echo "<br><br><b>$category_name</b><br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    $proberty_name = "status";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		   	$proberty_name = "error";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    $proberty_name = "source_for_next_start";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    $proberty_name = "timestamp_next_start";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    $proberty_name = "override_end_time";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    
		    $category_name = "radio";
		    $proberty_name = "quality";
		    echo "<br><br><b>$category_name</b><br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    $proberty_name = "connection_status";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		    $proberty_name = "state";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($mower, $category_name, $proberty_name);
		 
		 
			echo "<br><br><b>CATEGORY_GATEWAY</b><br>";
		  
		    $category_name = "device_info";
		    $proberty_name = "last_time_online";
		    echo "<b>$category_name</b><br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($gateway, $category_name, $proberty_name); 
		    $proberty_name = "version";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($gateway, $category_name, $proberty_name);
		    $proberty_name = "serial_number";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($gateway, $category_name, $proberty_name);
		    
		    $category_name = "gateway";
		    $proberty_name = "ip_address";
		    echo "<br><br><b>$category_name</b><br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($gateway, $category_name, $proberty_name); 
		    $proberty_name = "time_zone";
		    echo "<br><u>**$proberty_name**:</u> ";
		    echo $gardena -> getInfoDetail($gateway, $category_name, $proberty_name);
		//    echo "<br>";
		//    var_dump($gateway);
		//    echo "<br>";
		//    var_dump($mower);
		//    echo "<br><br>getDeviceStatusReportFriendly - Maeher<br>";
		//    echo $gardena -> getDeviceStatusReportFriendly($mower);
		//    echo "<br><br>getDeviceStatusReportFriendly - Gateway<br>";
		//    echo $gardena -> getDeviceStatusReportFriendly($gateway);
		//    echo "<br><br>getConnectionDataFriendly - Gateway<br>";
		//    echo $gardena -> getConnectionDataFriendly($gateway);
		//    echo "<br><br>getConnectionDataFriendly - Maeher<br>";
		//	echo $gardena -> getConnectionDataFriendly($mower);
		//	echo $gardena -> getMowerState($mower);
		//  echo $gardena -> getMowerState($gateway);
		}
		else if ($_GET["action"] === "PARK_UNTIL_FURTHER_NOTICE")
		{
			$gardena -> sendCommand($mower, $gardena -> CMD_MOWER_PARK_UNTIL_FURTHER_NOTICE);
		}
		else if ($_GET["action"] === "PARK_UNTIL_NEXT_TIMER")
		{
			$gardena -> sendCommand($mower, $gardena -> CMD_MOWER_PARK_UNTIL_NEXT_TIMER);
		}
		else if ($_GET["action"] === "RESUME_SCHEDUL")
		{
			$gardena -> sendCommand($mower, $gardena -> CMD_MOWER_RESUME_SCHEDUL);
		}
		else if ($_GET["action"] === "START")
		{		
			if(!empty($_GET["duration"]))
			{
				if(ctype_digit($_GET["duration"]))
				{
					$CMD_MOWER_START_XXHOURS = array("name" => "start_override_timer", "parameters" => array("duration" => $_GET["duration"]));
					echo "START for:";
					echo var_dump($CMD_MOWER_START_XXHOURS);
					echo "<br>";
					$gardena -> sendCommand($mower, $CMD_MOWER_START_XXHOURS);
				}
				else
				{
					echo "<br>ERROR: Parameter duration is not a Number";
				}			
			}
			else
			{
				echo "START for 6h<br>";
				$gardena -> sendCommand($mower, $gardena -> CMD_MOWER_START_06HOURS);		
			}	
		}
		else
		{
			echo "<br>ERROR: Parameter action has not a valid value";
		}
	}
	else
	{
		echo "Possible Param: <br><b>action</b><br>Values: INFO, PARK_UNTIL_FURTHER_NOTICE, PARK_UNTIL_NEXT_TIMER, RESUME_SCHEDUL, START<br>";
		echo "<br><b>[duration]</b><br>Value:Duration in minutes, only for Param action START. Without the Param duration the mower will start for 6h";
	}
?>