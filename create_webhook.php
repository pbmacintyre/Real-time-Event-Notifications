<?php
// create watching webhook subscription for opt Out messages
session_start();
require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');
show_errors();

page_header();

// echo_spaces("Session", $_SESSION);
//exit();
?>

<table class="CustomTable">
    <tr class="CustomTable">
        <td colspan="2" >
            <img src="images/rc-logo.png"/>
			<?php

			$subscription_url = "https://" . $_SERVER['HTTP_HOST'];
			require(__DIR__ . '/includes/vendor/autoload.php');
			$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/includes")->load();

			$url_suffix = $_ENV['RC_WEBHOOK_URL_SUFFIX'];
			$subscription_url .= $url_suffix ;

			$controller = ringcentral_sdk();

//			$response = $controller['platform']->get("/subscription");
//			$subscriptions = $response->json()->records;
//			if (count($subscriptions) > 0) {
//				echo_spaces("<br/>One or more webhooks already exist for this app, they are listed below", "", 2);
//				foreach ($subscriptions as $subscription) {
//					echo_spaces("Webhook ID", $subscription->id);
//                    echo_spaces("Creation Time", $subscription->creationTime);
//                    echo_spaces("Webhook URI", $subscription->deliveryMode->address);
//                    echo_spaces("Webhook transport type", $subscription->deliveryMode->transportType);
//                    echo_spaces("Event Filter(s) URI", $subscription->eventFilters);
//				}
//			} else {
				try {
                    if ($_SESSION['event_types']['voice'] == 1) {
                        $event_filters[] = "/restapi/v1.0/account/~/extension/~/message-store?type=Voicemail&direction=Inbound" ;
                    }
                    if ($_SESSION['event_types']['sms'] == 1) {
                        $event_filters[] = "/restapi/v1.0/account/~/extension/~/message-store?type=SMS&direction=Inbound" ;
                    }
                    if ($_SESSION['event_types']['fax'] == 1) {
                        $event_filters[] = "/restapi/v1.0/account/~/extension/~/message-store?type=Fax&direction=Inbound" ;
                    }
                    if ($_SESSION['event_types']['pager'] == 1) {
                        $event_filters[] = "/restapi/v1.0/account/~/extension/~/message-store?type=Pager&direction=Inbound" ;
                    }

                    // create the webhook for the provided event type(s)
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
//			}
			?>
            <hr>
            <a href="index.php"> Return to home page </a>
        </td>
    </tr>
</table>