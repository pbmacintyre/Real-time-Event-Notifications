<?php
// create watching webhook subscription for incoming events
session_start();

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

//show_errors();
page_header();

?>

<table class="CustomTable">
    <tr class="CustomTable">
        <td colspan="2">
            <img alt="RingCentral Logo" src="images/rc-logo.png"/>
            <?php

            require(__DIR__ . '/includes/vendor/autoload.php');
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/includes")->load();

            $subscription_url = "https://" . $_SERVER['HTTP_HOST'] . $_ENV['RC_WEBHOOK_URL_SUFFIX'];

            $controller = ringcentral_sdk();

            //  code for limiting subscription creation if some event types already exist

            $existing_events = array();

            $response = $controller['platform']->get("/subscription");
            $subscriptions = $response->json()->records;
            if (count($subscriptions) > 0) {
                echo_spaces("<br/>One or more webhooks already exist for this app, their details are listed below", "", 1);
                foreach ($subscriptions as $subscription) {
                    echo_spaces("Webhook ID", $subscription->id);
                    echo_spaces("Creation Time", $subscription->creationTime);
                    echo_spaces("Expires", $subscription->expirationTime);
//                    echo_spaces("Webhook URI", $subscription->deliveryMode->address);
//                    echo_spaces("Webhook transport type", $subscription->deliveryMode->transportType);
//                    echo_spaces("Event Filter(s) URI", $subscription->eventFilters);

                    foreach ($subscription->eventFilters as $event_url) {
                        $query = parse_url($event_url, PHP_URL_QUERY);
                        // $query = "type=Fax&direction=Inbound"

                        parse_str($query, $params);
                        // $params = ['type' => 'event type', 'direction' => 'Inbound'];
                        $existing_events[] = $params['type'] ;
                        echo_spaces("Existing Event Type", $params['type'] . " " . $params['direction']);
                    }
                    echo_spaces();
                }
            }
            if ($_SESSION['event_types']['voice']) {
                if (in_array("Voicemail", $existing_events)) {
                    echo_spaces("Voice Mail Subscription already exists, so it won't be created");
                } else {
                    $event_filters[] = "/restapi/v1.0/account/~/extension/~/message-store?type=Voicemail&direction=Inbound";
                }
            }
            if ($_SESSION['event_types']['pager']) {
                if (in_array("Pager", $existing_events)) {
                    echo_spaces("Pager Subscription already exists, so it won't be created");
                } else {
                    $event_filters[] = "/restapi/v1.0/account/~/extension/~/message-store?type=Pager&direction=Inbound";
                }
            }
            if ($_SESSION['event_types']['fax']) {
                if (in_array("Fax", $existing_events)) {
                    echo_spaces("Fax Subscription already exists, so it won't be created");
                } else {
                    $event_filters[] = "/restapi/v1.0/account/~/extension/~/message-store?type=Fax&direction=Inbound";
                }
            }
            if ($_SESSION['event_types']['sms']) {
                if (in_array("SMS", $existing_events)) {
                    echo_spaces("SMS Subscription already exists, so it won't be created");
                } else {
                    $event_filters[] = "/restapi/v1.0/account/~/extension/~/message-store?type=SMS&direction=Inbound";
                }
            }
            echo_spaces();

            // create the webhook for the provided event type(s)
            if ($event_filters) {
                try {
                    $api_call = $controller['platform']->post('/subscription',
                            array(
                                    "eventFilters" => $event_filters,
                                    "expiresIn" => "315360000",
                                    "deliveryMode" => array(
                                            "transportType" => "WebHook",
                                        // need full URL for this to work as well
                                            "address" => $subscription_url,
                                    )
                            )
                    );
                    $webhook_id = $api_call->json()->id;
                    echo_spaces("<br/>Webhook successfully created ! Webhook ID", $webhook_id, 2);

                } catch (\RingCentral\SDK\Http\ApiException $e) {
                    echo_spaces("Create webhook API Exception", $e->getMessage(), 1);
                }
            }

            ?>
            <hr>
            <a href="index.php"> Return to home page </a>
        </td>
    </tr>
</table>