<?php

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

	// if (strpos($_SERVER['HTTP_ACCEPT'], "application/json") !== false) {
	// 	header('Content-Type: application/json; charset=utf-8');
	// 	echo json_encode($_SERVER);
	// } else {
	// 	var_dump($_SERVER);
	// }	

	var_dump($_SERVER);