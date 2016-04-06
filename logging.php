class Logging {
  
  private $logEnabled;
  private $sender;
  
  function __construct() 
    { 
      $logEnabled = false;  
    } 
    
    function __construct1($EnableLogging) 
    { 
      this->logEnabled = $EnableLogging;
    } 
    
  public function EnableLogging() {
    this->logEnabled = true;
  }
  
  public function DisableLogging() {
    this->logEnabled = false;
  }
  
  public function LogMessage($Sender, $Message) {
    if($logEnabled)
      IPS_LogMessage($Sender, $Message);
  }

}
