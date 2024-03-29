<?php
namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


class AjaxHookEventManager {

    public static $DIV_ID_FOR_AJAX_EVENTS = "pys_ajax_events";
    private static $_instance;


    static function addPendingEvent($name,$event) {
        $events = WC()->session->get( 'pys_events', array() );
        $events[$name] = $event;
        WC()->session->set( 'pys_events', $events );
    }

    /**
     * @param $name
     * @param $slug
     * @return mixed|null
     */
    static function getPendingEvent($name,$unset) {
        if ( function_exists( 'WC' ) ) {
            if(!WC()->session) return null;
            $session_data = WC()->session->get_session_data();
            $events = isset( $session_data['pys_events'] ) ? WC()->session->get( 'pys_events', array() ) : array();
            PYS()->getLog()->debug('events hook called', $events);
            if (isset($events[$name])) {
                $event = $events[$name];
                if ($unset) {
                    unset($events[$name]);
                    WC()->session->set('pys_events', $events);
                }
                return $event;
            }
            return null;
        }
        return null;
    }

    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;

    }

    public function __construct() {

    }

    public function addHooks() {

        $user = wp_get_current_user();
        if(isDisabledForUserRole($user)) {
            return;
        }

        if(EventsWoo()->isEnabled()) {
            if ( PYS()->getOption('woo_add_to_cart_on_button_click')
                && isEventEnabled('woo_add_to_cart_enabled')
            )
            {
                add_filter('woocommerce_add_cart_item_data', array( __CLASS__, 'add_item_list_name_to_cart_item_data'), 10, 4);

                if(PYS()->getOption('woo_add_to_cart_catch_method') == "add_cart_hook"){
                    add_action( 'wp_footer', array( __CLASS__, 'addDivForAjaxPixelEvent')  );
                    add_action( 'woocommerce_add_to_cart',array(__CLASS__, 'trackWooAddToCartEvent'),40, 6);

                    if (wp_doing_ajax()) {
                        add_filter('woocommerce_add_to_cart_fragments', array(__CLASS__, 'addPixelCodeToAddToCartFragment'));
                    } else {
                        add_action("wp_footer",array(__CLASS__, 'printEvent'));
                    }
                } else {
                    add_action( 'woocommerce_after_add_to_cart_button', 'PixelYourSite\EventsManager::setupWooSingleProductData' );
                }
            }
        }
        // if(isWcfActive()) {
        add_action( 'cartflows_offer_product_processed',array( __CLASS__, 'wcf_save_last_offer_step' ), 10,3);
        // }


    }

    /**
     * @param \WC_Order $order
     * @param $product_data
     * @param $child_order
     */
    public static function wcf_save_last_offer_step($order, $product_data, $child_order) {
        $order->update_meta_data('pys_wcf_last_offer_step',$product_data['step_id']);
        $order->save();
    }
    static function add_item_list_name_to_cart_item_data($cart_item_data, $product_id, $variation_id, $quantity) {
        if (isset($_COOKIE['productlist'])) {
            $productlist = json_decode(stripslashes($_COOKIE['productlist']), true);
            if (isset($productlist['pys_list_name_productlist_name'])) {
                $cart_item_data['item_list_name'] = sanitize_text_field($productlist['pys_list_name_productlist_name']);
            }

            if (isset($productlist['pys_list_name_productlist_id'])) {
                $cart_item_data['item_list_id'] = sanitize_text_field($productlist['pys_list_name_productlist_id']);
            }
            if(PYS()->getOption('woo_add_to_cart_catch_method') == "add_cart_js")
            {
                setcookie('productlist', '', time() - 3600);
            }
        }
        return $cart_item_data;
    }
    static function trackWooAddToCartEvent($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
        global $post;
        $post = get_post( $product_id, OBJECT );
        setup_postdata( $post );
        if(isWcfStep()) return; // this event will fire from js for Wcf

        if(isset($cart_item_data['woosb_parent_id'])) return; // fix for WPC Product Bundles for WooCommerce (Premium) product

        $is_ajax_request = wp_doing_ajax();
        if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yith_wacp_add_item_cart') {
            $is_ajax_request = true;
        }
        $standardParams = getStandardParams();

        PYS()->getLog()->debug('trackWooAddToCartEvent is_hook_request '.$is_ajax_request);
        $dataList = [];
        foreach ( PYS()->getRegisteredPixels() as $pixel ) {

            if( !empty($variation_id)
                && $variation_id > 0
                && ( !$pixel->getOption( 'woo_variable_as_simple' )
                    || ( $pixel->getSlug() == "facebook"
                        && !Facebook\Helpers\isDefaultWooContentIdLogic()
                    )
                )
            ) {
                $_product_id = $variation_id;
            } else {
                $_product_id = $product_id;
            }


            $event = new SingleEvent('woo_add_to_cart_on_button_click',EventTypes::$STATIC,"woo");
            $event->args = ['productId' => $_product_id,'quantity' => $quantity];

            $pixelEvents = [];
            if(method_exists($pixel,'generateEvents')) {
                add_filter('pys_conditional_post_id', function($id) use ($product_id) { return $product_id; });
                $pixelEvents =  $pixel->generateEvents( $event );
                remove_all_filters('pys_conditional_post_id');
            } else {
                $isSuccess = $pixel->addParamsToEvent( $event );
                if ( $isSuccess ) {
                    $pixelEvents[] = $event;
                }
            }

            if(count($pixelEvents) == 0) continue;
            $event = $pixelEvents[0];


            // add standard params
            if($pixel->getSlug() != "tiktok") {
                $event->addParams($standardParams);
            }

            // prepare event data
            $eventData = $event->getData();
            $eventData = EventsManager::filterEventParams($eventData,"woo",[
                'event_id'=>$event->getId(),
                'pixel'=>$pixel->getSlug(),
                'product_id'=>$product_id
            ]);

            $dataList[$pixel->getSlug()] = $eventData;

			if($pixel->getSlug() == "facebook" && Facebook()->isServerApiEnabled()) {

				if($is_ajax_request) {
					FacebookServer()->sendEventsNow(array($event));
				} else {
					FacebookServer()->sendEventsAsync(array($event));
				}
			}

			if($pixel->getSlug() == "tiktok" && Tiktok()->isServerApiEnabled()) {

				if($is_ajax_request) {
					TikTokServer()->sendEventsNow(array($event));
				} else {
					TikTokServer()->sendEventsAsync(array($event));
				}
			}

			if($pixel->getSlug() == "pinterest" && method_exists(Pinterest(), 'isServerApiEnabled') && Pinterest()->isServerApiEnabled()) {

				if ( $is_ajax_request ) {
					PinterestServer()->sendEventsNow( array( $event ) );
				} else {
					PinterestServer()->sendEventsAsync( array( $event ) );
				}
			}
        }
        wp_reset_postdata();
        AjaxHookEventManager::addPendingEvent("woo_add_to_cart_on_button_click",$dataList);

    }

    public static function printEvent() {

        $pixelsEventData = self::getPendingEvent("woo_add_to_cart_on_button_click",true);

        if( !is_null($pixelsEventData) ) {
            PYS()->getLog()->debug('trackWooAddToCartEvent printEvent is footer');
            echo "<div  id='pys_late_event' style='display:none' dir='".json_encode($pixelsEventData,JSON_HEX_APOS)."'></div>";
        }
    }

    public  static function addDivForAjaxPixelEvent(){
        if(isWcfStep()) return; // this event will fire from js for Wcf

        echo self::getDivForAjaxPixelEvent();
        ?>
        <script>
            var node = document.getElementsByClassName('woocommerce-message')[0];
            if(node && document.getElementById('pys_late_event')) {
                var messageText = node.textContent.trim();
                if(!messageText) {
                    node.style.display = 'none';
                }
            }
        </script>
        <?php
    }

    public  static function getDivForAjaxPixelEvent($content = ''){
        return "<div id='".self::$DIV_ID_FOR_AJAX_EVENTS."'>" . $content . "</div>";
    }

    public static function addPixelCodeToAddToCarMessage($message, $products, $show_qty) {
        $pixelsEventData = self::getPendingEvent("woo_add_to_cart_on_button_click",true);
        if( !is_null($pixelsEventData) ){
            $message .= "<div id='pys_late_event' dir='".json_encode($pixelsEventData,JSON_HEX_APOS)."'></div>";
        }
        return $message;
    }

    public static function addPixelCodeToAddToCartFragment($fragments) {

        $pixelsEventData = self::getPendingEvent("woo_add_to_cart_on_button_click",true);
        if( !is_null($pixelsEventData) ){
            PYS()->getLog()->debug('addPixelCodeToAddToCartFragment send data with fragment');
            $pixel_code = self::generatePixelCode($pixelsEventData);
            $fragments['#'.self::$DIV_ID_FOR_AJAX_EVENTS] = self::getDivForAjaxPixelEvent($pixel_code);
        }

        return $fragments;
    }

    public static function generatePixelCode($pixelsEventData){

        ob_start();
        //$cartHashKey = apply_filters( 'woocommerce_cart_hash_key', 'wc_cart_hash_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() ) );
        ?>
        <script>
            function pys_getCookie(name) {
                var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
                return v ? v[2] : null;
            }
            function pys_setCookie(name, value, days) {
                var d = new Date;
                d.setTime(d.getTime() + 24*60*60*1000*days);
                document.cookie = name + "=" + value + ";path=/;expires=" + d.toGMTString();
            }
            var name = 'pysAddToCartFragmentId';
            var cartHash = "<?=WC()->cart->get_cart_hash()?>";

            if(pys_getCookie(name) != cartHash) { // prevent re send event if user update page
                <?php foreach ($pixelsEventData as $slug => $eventData) : ?>

                var pixel = getPixelBySlag('<?=$slug?>');
                var event = <?=json_encode($eventData)?>;
                pixel.fireEvent(event.name, event);

                <?php  endforeach; ?>
                pys_setCookie(name,cartHash,90)
            }
        </script>
        <?php

        $code = ob_get_clean();
        return $code;
    }



}

/**
 * @return AjaxHookEventManager
 */
function AjaxHookEventManager() {
    return AjaxHookEventManager::instance();
}

AjaxHookEventManager();
