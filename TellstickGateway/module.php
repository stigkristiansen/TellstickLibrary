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
	
    public function ReceiveData($JSONString)
    {
    }

}

?>
