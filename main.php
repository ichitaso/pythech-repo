<?php
$protocol = $_SERVER["SERVER_PROTOCOL"];
$udid = $_SERVER["HTTP_X_UNIQUE_ID"];
$ip = $_SERVER['REMOTE_ADDR'];

if (!$udid) {
	error("403 You Are Not An iPhone");
	return;
}

$request = $_GET["request"];
$extension = pathinfo($request, PATHINFO_EXTENSION);

if (!file_exists($request) && $extension =! "deb") {
	error("404 Not Found");
	return;
}
if (!file_exists($request) && $extension == "deb") {
	$dirname = pathinfo($request, PATHINFO_DIRNAME);
	$filename = pathinfo($request, PATHINFO_FILENAME);
	if(!preg_grep("/^$filename/", scandir($dirname))){
		error("404 Not Found");
		return;
	}
}

if (file_exists("auth/$ip")) {
	if ($extension) {
		header("Content-Type: application/vnd.debian.binary-package]");
		header("Content-Disposition: attachment; filename=\"$request\"");
		header("Content-Length: ".filesize($request));
	}
	switch($uniquepackages){
		case TRUE:
			echo readfile("$request.".file_get_contents("auth/$ip"));
			break;
		default:
		case FALSE:
			echo readfile($request);
			break;
	}
	unlink("auth/$ip");
}
else{
	error("403 Not Authentified");
}

function error($exit) {
	global $protocol;
	header(sprintf("$protocol $exit"));
}
?>
