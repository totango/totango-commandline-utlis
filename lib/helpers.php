<?php 

$DATE_OUT = "m/d/y H:i";
function getMandatoryParam($param, $error) {
	global $argv;
	$idx = array_search($param, $argv);
	
	if ((!$idx) || ($idx >= count($argv))) {
		echo "$error\n----------------------------------------------\n";
		if (function_exists("printUsage"))
			printUsage();
		die(1);
	}
	
	return $argv[$idx+1];
}

function echoLog($msg) {
	file_put_contents('php://stderr', $msg);
}


function getArrayParams($param) {
	global $argv;
		
	foreach ($argv as $i => $v) {
		if ($v == $param) {
			$ret[] = $argv[$i+1];
		}
	}
	return $ret;
}


function getOptionalParam($param, $default) {
	global $argv;
	
	$idx = array_search($param, $argv);
	
	if ((!$idx) || ($idx >= count($argv))) {
		return $default;
	}
	
	return $argv[$idx + 1];
}
?>