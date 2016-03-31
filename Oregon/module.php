<?

require_once(__DIR__ . "/../TellstickUtil.php");  

class OregonWeatherStation extends IPSModule
{

    
    public function Create()
    {
        parent::Create();
        $this->ConnectParent("{655884D6-7969-4DAF-8992-637BEE9FD70D}");

    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
        
        $this->RegisterVariableInteger( "Humidity", "Humidity", "~Humidity", 1 );
        $this->RegisterVariableFloat( "Temperature", "Temperature", "~Temperature", 0 );
    }
	
    public function ReceiveData($JSONString) {
         
        $data = json_decode($JSONString);
        $message = utf8_decode($data->Buffer);
        
        IPS_LogMessage("OregonSensor", "Received ".$message);
        
        if($data->DataID!="{F746048C-AAB6-479D-AC48-B4C08875E5CF}") {
        	IPS_LogMessage("OregonSensor", "This is not for me! (unsupported GUID in DataID)");
        	return;
        }

        $protocol = GetParameter("protocol", $message);

	if(stripos($protocol, "oregon")!==false) {
		$decodedMessage = DecodeOregon($message);
		IPS_LogMessage("OregonSensor", "Decoded message: ".$decodedMessage);
	} else {
		IPS_LogMessage("OregonSensor", "This is not for me! (unsupported protocol: ".$protocol.")");
		return;
	}
	
	if(len($decodedMessage)>0) {
		$temperature = GetParameter("temp", $DecodedMessage);
		$humidity = GetParameter("humidity", $DecodedMessage);
	
		SetValueInteger($this->GetIDForIdent("Humidity"), $humidity); 
		SetValueFloat($this->GetIDForIdent("Temperature"), $temperature);
	}
 
    }

}

?>
