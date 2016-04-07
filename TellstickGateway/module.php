<?

require_once(__DIR__ . "/../Logging.php");

class TellstickGateway extends IPSModule
{
    
    
    public function Create()
    {
        parent::Create();
        $this->RequireParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
        
        $this->RegisterPropertyBoolean ("log", false );
		
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
    
		$this->RegisterVariableString("Buffer", "Buffer");	
		$this->RegisterVariableString("LastCommand", "LastCommand");

		IPS_SetHidden($this->GetIDForIdent('Buffer'), true);
        IPS_SetHidden($this->GetIDForIdent('LastCommand'), true);    
    }
	
    public function ReceiveData($JSONString) {
	$messages = array();
	
        $incomingData = json_decode($JSONString);
	$incomingBuffer = utf8_decode($incomingData->Buffer);
		
	$log = new Logging($this->ReadPropertyBoolean("log"), IPS_Getname($this->InstanceID));
	$log->LogMessage("Incoming from serial: ".$incomingBuffer);
		
	$bufferId = $this->GetIDForIdent("Buffer");
	
        if (!$this->Lock("ReceiveLock")) {
            $log->LogMessage("Buffer is already locked");
            return false; 
        } else
	$log->LogMessage("Buffer is locked");

	$data = GetValueString($bufferId);
	$data = substr($data, strpos($data, "+W"));
        $data .= $incomingBuffer;
		
	do {
		$foundMessage = false;
		$arr = str_split($data);
		$max = sizeof($arr);
		for($i=0;$i<$max-1;$i++) {
			if(ord($arr[$i])==0x0D && ord($arr[$i+1])==0x0A) {
				$message = substr($data, 2, $i-1);
				$log->LogMessage("Found message: ".$message);
				if(CheckMessage($messages, $message)) {
					$this->SendDataToChildren(json_encode(Array("DataID" => "{F746048C-AAB6-479D-AC48-B4C08875E5CF}", "Buffer" => $message)));
					SetValueString($this->GetIDForIdent("LastCommand"), $message);	
				
					$messages[]=$message;
				}
				$foundMessage = true;
									
				if($i!=$max-2)
					$data = substr($data, $i+2);
				else
					$data = "";
				   
				break;
			}
		}
	} while ($foundMessage && strlen($data)>0);
	
	SetValueString($bufferId, $data);

	$this->Unlock("ReceiveLock");
      
	return true;
    }
    
    private function CheckMessage($messages, $message) {
    	$iMax = sizeof($messages);
    	for($i=0;$i<$iMax;$i++) {
    		if($message==$messages[$i])
    			return false;
    	}
    	
    	return true;
    }
    	
    	
   
    private function Lock($ident)   {
        for ($i = 0; $i < 100; $i++)
        {
            if (IPS_SemaphoreEnter("TSG_" . (string) $this->InstanceID . (string) $ident, 1))
            {
                return true;
            }
            else
            {
                $log = new Logging($this->ReadPropertyBoolean("log"), IPS_Getname($this->InstanceID));
		$log->LogMessage("Waiting for lock");
				IPS_Sleep(mt_rand(1, 5));
            }
        }
        return false;
    }

    private function Unlock($ident)
    {
        IPS_SemaphoreLeave("TSG_" . (string) $this->InstanceID . (string) $ident);
		$log = new Logging($this->ReadPropertyBoolean("log"), IPS_Getname($this->InstanceID));
		$log->LogMessage("Buffer is unlocked");
    }
}

?>
