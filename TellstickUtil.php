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
