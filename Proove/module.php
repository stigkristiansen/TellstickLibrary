<?

require_once(__DIR__ . "/../TellstickUtil.php");  
require_once(__DIR__ . "/../Logging.php");

class ProovePoolThermometer extends IPSModule
{

    
    public function Create()
    {
        parent::Create();
        $this->ConnectParent("{655884D6-7969-4DAF-8992-637BEE9FD70D}");
		
		//$this->RegisterPropertyInteger ("model", 0 );
		$this->RegisterPropertyInteger ("id", 0 );
		$this->RegisterPropertyInteger ("timeout", 2 );
		$this->RegisterPropertyBoolean ("log", false );
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();

        $this->RegisterVariableFloat( "Temperature", "Temperature", "~Temperature", 0 );
		$this->RegisterVariableInteger( "Last", "Last", "", 2 );
		IPS_SetHidden($this->GetIDForIdent('Last'), true);
    }
	
    public function ReceiveData($JSONString) {
		$data = json_decode($JSONString);
        $message = utf8_decode($data->Buffer);
        
        $log = new Logging($this->ReadPropertyBoolean("log"), IPS_Getname($this->InstanceID));
        
        $log->LogMessage("Received ".$message);
        
        if($data->DataID!="{F746048C-AAB6-479D-AC48-B4C08875E5CF}") {
        	$log->LogMessage("This is not for me! (unsupported GUID in DataID)");
        	return;
        }

        $protocol = GetParameter("protocol", $message);

		if(stripos($protocol, "fineoffset")!==false) {
			$decodedMessage = DecodeFineOffset($message);
			$log->LogMessage("Decoded message: ".$decodedMessage);
		} else {
			$log->LogMessage("This is not for me! (unsupported protocol: ".$protocol.")");
			return;
		}
	
		if(strlen($decodedMessage)>0) {
			$model = GetParameter("model", $decodedMessage);
			
			$id = intval(GetParameter("id", $decodedMessage));
			
			$log->LogMessage("Received command from: ".$model.":".$id);
			
			$myId = $this->ReadPropertyInteger("id");
			
			if($myModel=="temperature" && $myId==$id) {
				$interval = $this->ReadPropertyInteger("timeout");
				$now = time();
		
				$lastId = $this->GetIDForIdent("Last");
				$lastProcessed = GetValueInteger($lastId);

				if($lastProcessed+$interval<$now) {
					$temperature = GetParameter("temp", $decodedMessage);
					SetValueFloat($this->GetIDForIdent("Temperature"), $temperature);
					SetValueInteger($lastId, $now);
				}
			}  else 
				$log->LogMessage("This is not me!"); 
	
		} else {
			$log->LogMessage("Unsupported model");
		}
    }
}

?>
