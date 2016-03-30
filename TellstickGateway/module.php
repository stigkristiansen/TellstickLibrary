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
        
    }
	
    public function ReceiveData($JSONString) {
 
        // Empfangene Daten vom I/O
        $data = json_decode($JSONString);
        IPS_LogMessage("ReceiveData", utf8_decode($data->Buffer));
 
        // Hier werden die Daten verarbeitet
 
        // Weiterleitung zu allen Gerät-/Device-Instanzen
        //$this->SendDataToChildren(json_encode(Array("DataID" => "{66164EB8-3439-4599-B937-A365D7A68567}", "Buffer" => $data->Buffer)));
    }


}

?>
