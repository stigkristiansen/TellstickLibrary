class Logging {
  
  private $logEnabled;
  private $sender;
  
  function __construct() { 
    this->logEnabled = false;
    this->sender = "":
  } 
    
  function __construct1($EnableLogging) { 
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
  
  public functio LogMessage($Message) {
    IPS_LogMessage(this->Sender, $Message);
  }

}
