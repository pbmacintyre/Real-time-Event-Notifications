<?php

/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 *
 */

// runs when a RingCentral event is triggered...

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

//show_errors();

$hvt = isset($_SERVER['HTTP_VALIDATION_TOKEN']) ? $_SERVER['HTTP_VALIDATION_TOKEN'] : '';
if (strlen($hvt) > 0) {
	header("Validation-Token: {$hvt}");
}

$incoming = file_get_contents("php://input");

// use following to send incoming event data to a file for visual review
file_put_contents("received_EVENT_payload.log", $incoming);

if (empty($incoming)) {
	http_response_code(200);
	echo json_encode(array('responseType' => 'error', 'responseDescription' => 'No data provided Check SMS payload.'));
	exit();
}

$incoming_data = json_decode($incoming);

if (!$incoming_data) {
	http_response_code(200);
	echo json_encode(array('responseType' => 'error', 'responseDescription' => 'Media type not supported.  Please use JSON.'));
	exit();
}

// get the type of event
$incoming_event_type = htmlentities($incoming_data->body->changes[0]->type);

// handle the type of the event with notifications or SMS or whatever you want.

echo_spaces("Event Type", $incoming_event_type, 1);
echo_spaces("Event Info", $incoming_data->body->changes, 1);




