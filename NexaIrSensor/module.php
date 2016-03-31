<?

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
         // Empfangene Daten vom Gateway/Splitter
        $data = json_decode($JSONString);
        IPS_LogMessage("NexaIRSensor", "Received ".utf8_decode($data->Buffer));
        
        if($data->DataID!="{F746048C-AAB6-479D-AC48-B4C08875E5CF}") {
        	IPS_LogMessage("NexaIRSensor", "This is not for me!")
        	return;
        }
 
    }

}

?>
