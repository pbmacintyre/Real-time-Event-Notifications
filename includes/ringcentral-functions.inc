<?php
/** Copyright (C) 2019-2025 Paladin Business Solutions */

/* ================= */
/* Generic functions */
/* ================= */

function app_name() {
	return "Real Time Event Notifications Demo App";
}

/* ================== */
/* Get RingCental SDK */
/* ================== */
function ringcentral_sdk() {
	// Include Libraries
	require('includes/vendor/autoload.php');

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$jwt_key = $_ENV['RC_JWT_KEY'];

	$sdk = new RingCentral\SDK\SDK(
		$_ENV['RC_APP_CLIENT_ID'],
		$_ENV['RC_APP_CLIENT_SECRET'],
		$_ENV['RC_SERVER_URL']);

	$platform = $sdk->platform();

	// Login via API
	if (!$sdk->platform()->loggedIn()) {
		try {
			$platform->login(["jwt" => $jwt_key]);
		} catch (\RingCentral\SDK\Http\ApiException $e) {
			echo_spaces("SDK Failure", $e->getMessage());
		}
	}
	$controller = array('SDK' => $sdk, 'platform' => $platform);
	return $controller;
}


function send_mms ($to_sms_number, $sms_message_body, $file_with_path) {

	// Include Libraries
	require('includes/vendor/autoload.php');

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$fromNumber = $_ENV['RC_MOBILE_SENDING_NUMBER'];

	$controller = ringcentral_sdk();
	$platform = $controller['platform'];
	$sdk = $controller['SDK'];

	$bodyParams = array(
		'from' => array( 'phoneNumber' => $fromNumber ),
		'to'   => array( array('phoneNumber' => $to_sms_number ) ),
		'text' => $sms_message_body,
	);
	$endpoint = "/restapi/v1.0/account/~/extension/~/mms";
	$message_id = "" ;

	try {
		$request = $sdk->createMultipartBuilder()
			->setBody($bodyParams)
			->add(fopen($file_with_path, 'r'))
			->request($endpoint);

		$resp = $platform->sendRequest($request);
		$message_id = $resp->json()->id;

	}
	catch (\RingCentral\SDK\Http\ApiException $e) {
		// Getting error messages using PHP native interface
		print 'Expected HTTP Error: ' . $e;
		print '  Message: ' . $e->apiResponse->response()->error() . PHP_EOL;
	}
	return $message_id;
}
