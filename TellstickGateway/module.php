<?

class TellstickGateway extends IPSModule
{

    public function Create()
    {
        parent::Create();
        $this->RequireParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
		
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
				
        $incomingData = json_decode($JSONString);
		$incomingBuffer = utf8_decode($incomingData->Buffer);
		
		IPS_LogMessage("Tellstick Library", $incomingBuffer);
		
		$bufferId = $this->GetIDForIdent("Buffer");
	
        if (!$this->lock("ReceiveLock")) {
            trigger_error("ReceiveBuffer is locked",E_USER_NOTICE);
            return false;
        }

		$data = GetValueString($bufferId);
        $data .= $incomingBuffer;
		
		$foundMessage = false;
		$arr = str_split($data);
		$max = sizeof($arr);
		for($i=0;$i<$max-1;$i++) {
         if(ord($arr[$i])==0x0D && ord($arr[$i+1])==0x0A) {
				$message = substr($data, 2, $i-1);
				$foundMessage = true;
				
				IPS_LogMessage("Tellstick Library", "Received message: ".$message);
				
				if($i!=$max-2){
					$newData = substr($data, $i+2);
					SetValueString($bufferId, $newData);
				} else
					SetValueString($bufferId, "");
				   
				break;
			}
		}
		
		if(!$foundMessage) {
			SetValueString($bufferId, $data);
		} else {
			SetValueString($this->GetIDForIdent("LastCommand"), $message);
		} 
		
		
		
		if($foundMessage) {
			$this->SendDataToChildren(json_encode(Array("DataID" => "{F746048C-AAB6-479D-AC48-B4C08875E5CF}", "Buffer" => $message)));
		}

		$this->unlock("ReceiveLock");
        
    }


	private function lock($ident)
    {
        for ($i = 0; $i < 100; $i++)
        {
            if (IPS_SemaphoreEnter("TSG_" . (string) $this->InstanceID . (string) $ident, 1))
            {
                return true;
            }
            else
            {
                IPS_LogMessage("Tellstick Library","Waiting for lock");
				IPS_Sleep(mt_rand(1, 5));
            }
        }
        return false;
    }

    private function unlock($ident)
    {
        IPS_SemaphoreLeave("TSG_" . (string) $this->InstanceID . (string) $ident);
    }
}

?>
