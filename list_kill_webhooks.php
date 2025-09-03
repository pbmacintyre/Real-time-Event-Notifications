<?php

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

show_errors();

echo_spaces("webhooks listing, if there is nothing listed below then there are no active webhooks", "", 1);

$controller = ringcentral_sdk();

// list subscriptions then optionally delete the one we don't want.

try {
	$response = $controller['platform']->get("/subscription");
	$subscriptions = $response->json()->records;
} catch (Exception $e) {
	echo_spaces("catch error",  $e->getMessage());
}

foreach ($subscriptions as $subscription) {
//	echo_spaces("Subscription Info", $subscription, 2);

	echo_spaces("Subscription ID", $subscription->id);
	echo_spaces("Creation Time", $subscription->creationTime);
	echo_spaces("Expires", $subscription->expirationTime);
	echo_spaces("Webhook URI", $subscription->deliveryMode->address);
	echo_spaces("Webhook transport type", $subscription->deliveryMode->transportType);

    foreach ($subscription->eventFilters as $event_url) {

        $query = parse_url($event_url, PHP_URL_QUERY);
        // $query = "type=Fax&direction=Inbound"

        parse_str($query, $params);
        // $params = ['type' => 'Fax', 'direction' => 'Inbound'];

        echo_spaces("Event Type", $params['type']);
    }
    echo_spaces("Event Filter(s) URI", $subscription->eventFilters);



	if ($subscription->id == "f7a15b17-3339-44fe-947f-b2e8e96d28a3") {
		$response = $controller['platform']->delete("/restapi/v1.0/subscription/$subscription->id");
		echo_spaces("Subscription ID Deleted", $subscription->id, 1);
	}
}
?>
<br/>
<a href="index.php"> Return to home page </a>


