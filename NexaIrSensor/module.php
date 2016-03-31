<?

require_once(__DIR__ . "/../TellstickUtil.php");  

class NexaIrSensor extends IPSModule
{

    
    public function Create()
    {
        parent::Create();
        $this->ConnectParent("{655884D6-7969-4DAF-8992-637BEE9FD70D}");

    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
    }
	
    public function ReceiveData($JSONString) {
         
        $data = json_decode($JSONString);
        $message = utf8_decode($data->Buffer);
        
        IPS_LogMessage("NexaIRSensor", "Received ".$message);
        
        if($data->DataID!="{F746048C-AAB6-479D-AC48-B4C08875E5CF}") {
        	IPS_LogMessage("NexaIRSensor", "This is not for me!");
        	return;
        }

        $protocol = GetParameter("protocol", $message);

	if(stripos($protocol, "arctech")==false) {
		return;
	}
	
	$decodedMessage = DecodeNexa($message);
	IPS_LogMessage("NexaIRSensor", "Decoded message: ".$decodedMessage);
 
    }

}

?>
