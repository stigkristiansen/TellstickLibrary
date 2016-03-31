<?

function GetParameter($Parameter, $Message) {
	$arr = explode (";", $Message);
	$max = sizeof($arr);

	for($i=0;$i<$max;$i++) {
	   if(stripos($arr[$i], $Parameter.":")!==false) {
			break;
	   }
	}

	if($i<$max){
		$startPos = stripos($arr[$i], ":")+1;
		$value = substr($arr[$i], $startPos);
		return $value;
	} else {
	   return "";
	}

}

function DecodeNexa($Message) {

	$model = GetParameter("model", $Message);
  	$data = GetParameter("data", $Message) + 0;

	if(stripos($model, "selflearning")!==false)
	   return DecodeNexaSelflearning($data);
	else
	   return DecodeNexaCodeSwitch($data);

}


function DecodeNexaSelflearning($Data) {
//  	IPS_LogMessage("Tellstick 2","Decoding Nexa Selflearning");

  	//$Data = GetParameter("data", $Message) + 0;

  	$house = $Data & 0xFFFFFFC0;
	$house >>= 6;

	$group = $Data & 0x20;
	$group >>= 5;

	$method = $Data & 0x10;
	$method >>= 4;

	$unit = $Data & 0xF;
	$unit++;

	$stringMethod ="";
	if($method==1)
	   $stringMethod="turnon";
	else if ($method==0)
	   $stringMethod="turnoff";


   $decoded = "class:command;protocol:arctech;model:selflearning;house:".$house.";unit:".$unit.";group:".$group.";method:".$stringMethod;

//  	IPS_LogMessage("Tellstick 2", "Decoded Nexa message: ".$decoded);

  	return $decoded;
}

function DecodeNexaCodeSwitch($Message) {
//	IPS_LogMessage("Tellstick 2","Decoding Nexa Code Switch");
}

function DecodeOregon($Message) {

	$model = GetParameter("model", $Message);
	$data = GetParameter("data", $Message);

	if(stripos($model, "0xF824")!==false)
		return DecodeOregonF824($data);

}

function DecodeOregonF824($data) {

	//IPS_LogMessage("Tellstick 2","Decoding Oregon model F824 with data: ".$data);

	$arr=str_split($data);

	$crcCheck = $arr[13];
	$messageChecksum1 = "0x".$arr[12];
	$messageChecksum2 = "0x".$arr[11];
	$unknown = "0x".$arr[10];
	$hum1 = "0x".$arr[9];
	$hum2 = "0x".$arr[8];
	$neg = "0x".$arr[7];
//	$neg = $arr[7];
	$temp1 = "0x".$arr[6];
	$temp2 = "0x".$arr[5];
	$temp3 = "0x".$arr[4];
	$battery = "0x".$arr[3]; //PROBABLY battery
	$checksum = $arr[1] + $arr[2];
	$rollingcode = $checksum;
	$channel = "0x".$arr[0];

	//IPS_LogMessage("Tellstick 2","Neg: ".$neg);

	$checksum += $unknown + $hum1 + $hum2 + $neg + $temp1 + $temp2 + $temp3 + $battery + $channel + 0xF + 0x8 + 0x2 + 0x4;

	if ((($checksum >> 4) & 0xF) != $messageChecksum1 || ($checksum & 0xF) != $messageChecksum2){
		//checksum error
		return "";
	}

	$temperature = (($temp1 * 100) + ($temp2 * 10) + $temp3)/10.0;
	if ($neg>0) {
		$temperature *= -1;
	}
	$humidity = ($hum1 * 10.0) + $hum2;

	$decoded = "class:sensor;protocol:oregon;model:F824;id:".$rollingcode.";temp:".$temperature.";humidity:".$humidity.";";

//  	IPS_LogMessage("Tellstick 2", "Decoded Oregon message: ".$decoded);

	return $decoded;

}


?>
