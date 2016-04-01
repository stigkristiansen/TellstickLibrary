<?

require_once(__DIR__ . "/../TellstickUtil.php");  

class NexaIrSensor extends IPSModule
{

    
    public function Create()
    {
        parent::Create();
        $this->ConnectParent("{655884D6-7969-4DAF-8992-637BEE9FD70D}");
		
		$this->RegisterPropertyInteger ("house", 0 );
		$this->RegisterPropertyInteger ("unit", 0 );

    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
		
		$this->RegisterVariableBoolean( "Status", "Status", "", false);
    }
	
    public function ReceiveData($JSONString) {
    	
    	$data = json_decode($JSONString);
        $message = utf8_decode($data->Buffer);
        
        IPS_LogMessage("NexaIRSensor", "Received ".$message);
        
        if($data->DataID!="{F746048C-AAB6-479D-AC48-B4C08875E5CF}") {
        	IPS_LogMessage("NexaIRSensor", "This is not for me! (unsupported GUID in DataID)");
        	return;
        }
        
        

	return;

        $protocol = GetParameter("protocol", $message);

		if(stripos($protocol, "arctech")!==false) {
			$decodedMessage = DecodeNexa($message);
			IPS_LogMessage("NexaIRSensor", "Decoded message: ".$decodedMessage);
		} else {
			IPS_LogMessage("NexaIRSensor", "This is not for me! (unsupported protocol: ".$protocol.")");
			return;
		}
		
		
		
		//3/31/2016 21:27:52 | NexaIRSensor | Decoded message: class:command;protocol:arctech;model:selflearning;house:11843482;unit:10;group:0;method:turnoff
	
		if(strlen($decodedMessage)>0) {
			$unit = intval(GetParameter("unit", $decodedMessage));
			$house = intval(GetParameter("house", $decodedMessage));
			
			IPS_LogMessage("NexaIRSensor", $house.":".$unit);
						
			$myUnit = $this->ReadPropertyInteger("unit");
			$myHouse = $this->ReadPropertyInteger("house");
			
			IPS_LogMessage("NexaIRSensor", $myHouse.":".$myUnit);
			
			if($myUnit==$unit && $myHouse==$house) {
				$method = GetParameter("method", $decodedMessage);
								
				SetValueBoolean($this->GetIDForIdent("Status"), ($method=='turnon'?true:false)); 
				
			}	
		} else {
			IPS_LogMessage("NexaIRSensor", "Unsupported model");
		}
 
    }

}

?>
