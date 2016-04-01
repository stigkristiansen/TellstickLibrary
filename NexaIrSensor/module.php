<?

require_once(__DIR__ . "/../TellstickUtil.php");  

class NexaIrSensor extends IPSModule {

    
    public function Create() {
        parent::Create();
        
		$this->ConnectParent("{655884D6-7969-4DAF-8992-637BEE9FD70D}");
		
		$this->RegisterPropertyInteger ("house", 0 );
		$this->RegisterPropertyInteger ("unit", 0 );

    }

    public function ApplyChanges() {
        parent::ApplyChanges();
		
		$this->RegisterVariableBoolean( "Status", "Status", "", false);
    }
	
    public function ReceiveData($JSONString) {
    	$data = json_decode($JSONString);
        $message = utf8_decode($data->Buffer);
        
        IPS_LogMessage("NexaIRSensor", "Received ".$message);

	}
}

?>
