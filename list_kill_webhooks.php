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
	echo_spaces("Webhook URI", $subscription->deliveryMode->address);
	echo_spaces("Webhook transport type", $subscription->deliveryMode->transportType);
	echo_spaces("Event Filter(s) URI", $subscription->eventFilters);

	if ($subscription->id == "e94bae96-2556-4971-8a5f-23fc86410828") {
		$response = $controller['platform']->delete("/restapi/v1.0/subscription/{$subscription->id}");
		echo_spaces("Subscription ID Deleted", $subscription->id, 1);
	}
}
?>
<a href="index.php"> Return to home page </a>