<?php
namespace PixelYourSite;
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
require_once PYS_PATH.'/modules/google_analytics/server/server_event_helper.php';
use PYS_PRO_GLOBAL\Br33f\Ga4\MeasurementProtocol\Service;
use PYS_PRO_GLOBAL\Br33f\Ga4\MeasurementProtocol\Dto\Request\BaseRequest;

/**
 * The Measurement Protocol API wrapper class.
 *
 * A basic wrapper around the GA Measurement Protocol HTTP API used for making
 * server-side API calls to track events.
 *
 */
class GaMeasurementProtocolAPI
{
    static $uaMap = [
        'traffic_source',
        'event_category',
    ];
    /** @var string endpoint for GA API */
    public $ga_url = 'https://www.google-analytics.com/collect';
    private $access_token = '';
    //public $ga_url = 'https://www.google-analytics.com/debug/collect'; //debug


    /**
     * Send event in shutdown hook (not work in ajax)
     * @param SingleEvent[] $events
     */
    public function sendEventsAsync($events)
    {
        // not use
    }

    /**
     * Send Event Now
     *
     * @param SingleEvent[] $events
     */
    public function sendEventsNow($events)
    {
        foreach ($events as $event) {
            $ids = $event->payload['trackingIds'];
            $this->sendEvent($ids, $event);
        }
    }

    private function sendEvent($tags, $event)
    {
        if (!$this->access_token) {
            $this->access_token = GA()->getApiTokens();
        }
        foreach ($tags as $tag) {
            if($this->isGaV4($tag))
            {
                $data = $event->getData();
                $params = $data['params'];
                if(!empty($data['woo_order']))
                {
                    $orderId = $data['woo_order'];
                    $type = 'woo';
                }
                elseif (!empty($data['edd_order']))
                {
                    $orderId = $data['edd_order'];
                    $type = 'edd';
                }
                else
                {
                    continue;
                }


                if (empty($this->access_token[$tag]) || empty($orderId)) {
                    continue;
                }

                $clientId = GaServerEventHelper::getGAStatFromOrder('clientId', $orderId, $type);
                if (empty($clientId)) {
                    continue;
                }

                $ga4Service = new Service($this->access_token[$tag]);
                $ga4Service->setMeasurementId($tag);

                $baseRequest = new BaseRequest();
                $baseRequest->setClientId($clientId);
                $eventData = GaServerEventHelper::mapSingleEventToServerDataGA4($event);
                PYS()->getLog()->debug('Send for GA4', $tag);
                PYS()->getLog()->debug('Send GA4 server event request', $eventData);
                // Add event to base request (you can add up to 25 events to single request)
                $baseRequest->addEvent($eventData);

// We have all the data we need. Just send the request.
                $response = $ga4Service->send($baseRequest);
                PYS()->getLog()->debug('Send GA4 server event response', $response);
            }
            else
            {
                $eventData = GaServerEventHelper::mapSingleEventToServerData($event);
                $eventData['v'] = '1';// API version
                $eventData['tid'] = $tag; // tracking ID
                $eventData['z'] = time();

                $response = wp_safe_remote_request($this->ga_url, $this->prepareRequestArgs($eventData));
                if (is_wp_error($response)) {
                    PYS()->getLog()->debug('Send GA server event error', $response);
                    return;
                }
                PYS()->getLog()->debug('Send GA server event response', $response);
            }

//            $response_code     = wp_remote_retrieve_response_code( $response );
//            $response_message  = wp_remote_retrieve_response_message( $response );
//            $raw_response_body = wp_remote_retrieve_body( $response );


        }
    }

    private function prepareRequestArgs($params)
    {
        $args = array(
            'method' => 'POST',
            'timeout' => MINUTE_IN_SECONDS,
            'redirection' => 0,
            // 'httpversion' => '1.0',
            'sslverify' => true,
            'blocking' => true,
            // 'user-agent'  => $this->get_request_user_agent(),
            'headers' => [],
            'body' => $this->paramsToString($params),
            'cookies' => array(),
        );

        return $args;
    }

    public function paramsToString($params)
    {

        return http_build_query($params, '', '&');
    }

    public function isGaV4($tag) {
        return strpos($tag, 'G') === 0;
    }

}