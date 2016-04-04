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
		
		IPS_LogMessage("Tellstick Library", "Incoming from serial: ".$incomingBuffer);
		
		$bufferId = $this->GetIDForIdent("Buffer");
	
        if (!$this->lock("ReceiveLock")) {
            trigger_error("ReceiveBuffer is locked",E_USER_NOTICE);
            return false;
        } else
			IPS_LogMessage("Tellstick Library","Buffer is locked");

		$data = GetValueString($bufferId);
        $data .= $incomingBuffer;
		
		do {
			$foundMessage = false;
			
			$arr = str_split($data);
			$max = sizeof($arr);
			for($i=0;$i<$max-1;$i++) {
				if(ord($arr[$i])==0x0D && ord($arr[$i+1])==0x0A) {
					$message = substr($data, 2, $i-1);
					IPS_LogMessage("Tellstick Library", "Received message: ".$message);
					$this->SendDataToChildren(json_encode(Array("DataID" => "{F746048C-AAB6-479D-AC48-B4C08875E5CF}", "Buffer" => $message)));
					SetValueString($this->GetIDForIdent("LastCommand"), $message);	
					
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
		IPS_LogMessage("Tellstick Library","Buffer is unlocked");
    }
}

?>
