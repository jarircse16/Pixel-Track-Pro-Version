<?php
namespace PixelYourSite;


class EnrichOrder {
    private static $_instance;

    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function init() {
        //woo

        if(PYS()->getOption("woo_enabled_save_data_to_orders")) {
            add_action( 'woocommerce_new_order',array($this,'woo_save_checkout_fields'),10, 1);

            add_action( 'woocommerce_analytics_update_order_stats',array($this,'woo_update_analytics'));
            add_action( 'add_meta_boxes', array($this,'woo_add_order_meta_boxes') );

            if(PYS()->getOption("woo_add_enrich_to_admin_email")) {
                add_action( 'woocommerce_email_customer_details', array($this,'woo_add_enrich_to_admin_email'),80,4 );
            }
        }

        // edd
        if(PYS()->getOption("edd_enabled_save_data_to_orders")) {
            add_filter('edd_payment_meta', array($this, 'edd_save_checkout_fields'),10,2);
            add_action('edd_view_order_details_main_after', array($this, 'add_edd_order_details'));
        }
    }

    function woo_update_analytics($orderId, $update = false) {
        $order = wc_get_order( $orderId );
        if(!$order->meta_exists( 'pys_enrich_data_analytics' )) {
            $totals = getWooUserStat($orderId);
            if ($totals['orders_count'] == 0) {
                $totals = array(
                    'orders_count' => 'Guest order',
                    'avg_order_value' => 'Guest order',
                    'ltv' => 'Guest order',
                );
            }
            if (isWooCommerceVersionGte('3.0.0')) {
                // WooCommerce >= 3.0
                if ($order) {
                    $order->update_meta_data("pys_enrich_data_analytics", $totals);
                    $order->save();
                }

            } else {
                // WooCommerce < 3.0
                update_post_meta($orderId, 'pys_enrich_data_analytics', $totals);
            }
        }
    }

    function woo_save_checkout_fields($order_id) {
        $pysData = [];
        $pysData = $this->getPysData();
        $order = wc_get_order( $order_id );
        if ( isWooCommerceVersionGte('3.0.0') ) {
            // WooCommerce >= 3.0
            if($order) {
                $order->update_meta_data("pys_enrich_data",$pysData);
                $order->save();
            }

        } else {
            // WooCommerce < 3.0
            update_post_meta( $order_id, 'pys_enrich_data', $pysData );
        }

    }

    function woo_add_order_meta_boxes () {
        $screen = isWooUseHPStorage()
            ? wc_get_page_screen_id( 'shop-order' )
            : 'shop_order';

        add_meta_box( 'pys_enrich_fields_woo', __('PixelTrackPro','pixelyoursite'),
            array($this,"woo_render_order_fields"), $screen);
    }

    /**
     * @param \WC_Order$order
     * @param $sent_to_admin
     * @param $plain_text
     * @param $email
     */

    function woo_add_enrich_to_admin_email($order, $sent_to_admin) {
        if($sent_to_admin) {
            $orderId = $order->get_id();
            $render_tracking = false;
            echo "<h2 style='text-align: center'>". __('PixelTrack Professional','pixelyoursite')."</h2>";
            echo "Your clients don't see this information! We send it to you in this \"New Order\" email. If you want to remove this data from the \"New Order\" email, open <a href='".admin_url("admin.php?page=pixelyoursite&tab=woo")."' target='_blank'>PixelYourSite's WooCommerce page</a>, disable \"Send reports data to the New Order email\" and save.
            <br>You can see more data inside the plugin on this <a href='".admin_url("admin.php?page=pixelyoursite_woo_reports")."' target='_blank'>WooCommerce Reports page</a>.
            <br>Find out more about how WooCommerce reports work by watching this <a href='https://www.youtube.com/watch?v=4VpVf9llfkU' target='_blank'>video</a>.<br>";
            include 'views/html-order-meta-box.php';
        }

    }

    function woo_render_order_fields($post) {
	    if ($post instanceof \WP_Post) {
		    $orderId = $post->ID;
	    } elseif (method_exists($post, 'get_id')) {
		    $orderId = $post->get_id();
	    } else {
		    // Обработка ситуации, когда $post не является ни объектом \WP_Post, ни объектом с методом get_id().
		    $orderId = null; // Или другое значение по умолчанию.
	    }
        $render_tracking = true;
        echo "<div style='margin:20px 10px'><p>You can see more data on the <a href='".admin_url("admin.php?page=pixelyoursite_woo_reports")."' target='_blank'>WooCommerce Reports page</a>. 
                    </p><p>You can turn OFF WooCommerce Reports from the plugin's <a href='".admin_url("admin.php?page=pixelyoursite&tab=woo")."' target='_blank'>WooCommerce page</a>.
                    </p>Find out more about how WooCommerce reports work by watching this <a href='https://www.youtube.com/watch?v=4VpVf9llfkU' target='_blank'>video</a>.</div>";
        include 'views/html-order-meta-box.php';
    }

    function edd_save_checkout_fields( $payment_meta ,$init_payment_data) {

        if ( 0 !== did_action('edd_pre_process_purchase') ) {
            $pysData = [];
            $pys_landing = '';
            $pys_source = '';
            $utms = getUtms(true);
            $utms_id = getUtmsId(true);

            $pys_utm = implode("|", array_map(function ($key, $value) {
                return "$key:$value";
            }, array_keys($utms), $utms));
            $pys_utm_id = implode("|", array_map(function ($key, $value) {
                return "$key:$value";
            }, array_keys($utms_id), $utms_id));
            $pys_browser_time = getBrowserTime();
            if (isset($_COOKIE['pys_landing_page']) || isset($_SESSION['LandingPage'])) {
                $pys_landing = $_COOKIE['pys_landing_page'] ?? $_SESSION['LandingPage'];
            }
            if (isset($_COOKIE['pysTrafficSource']) || isset($_SESSION['TrafficSource'])) {
                $pys_source = $_COOKIE['pysTrafficSource'] ?? $_SESSION['TrafficSource'];
            }
            $pysData['pys_landing'] = isset($_REQUEST['pys_landing']) ? sanitize_text_field($_REQUEST['pys_landing']) : $pys_landing ?? 'undefined';
            $pysData['pys_source'] = isset($_REQUEST['pys_source']) ? sanitize_text_field($_REQUEST['pys_source']) : $pys_source ?? 'undefined';
            $pysData['pys_utm'] = isset($_REQUEST['pys_utm']) ? sanitize_text_field($_REQUEST['pys_utm']) : $pys_utm;
            $pysData['pys_browser_time'] = isset($_REQUEST['pys_browser_time']) ? sanitize_text_field($_REQUEST['pys_browser_time']) : $pys_browser_time;

            if(PYS()->getOption("edd_enabled_save_data_to_orders")) {
                if(get_current_user_id()) {
                    $totals = getEddCustomerTotals();
                } else {
                    $totals = getEddCustomerTotalsByEmail($payment_meta['email']);
                    if($totals['orders_count'] == 0) {
                        $totals = array(
                            'orders_count' => 'Guest order',
                            'avg_order_value' => 'Guest order',
                            'ltv' => 'Guest order',
                        );
                    }
                }
            }
            $pysData['last_pys_landing'] = isset($_REQUEST['last_pys_landing']) ? sanitize_text_field($_REQUEST['last_pys_landing']) : $pys_landing ?? 'undefined';
            $pysData['last_pys_source'] = isset($_REQUEST['last_pys_source']) ? sanitize_text_field($_REQUEST['last_pys_source']) : $pys_source ?? 'undefined';
            $pysData['last_pys_utm'] = isset($_REQUEST['last_pys_utm']) ? sanitize_text_field($_REQUEST['last_pys_utm']) : $pys_utm;

            $pysData['pys_utm_id'] = isset($_REQUEST['pys_utm_id']) ? sanitize_text_field($_REQUEST['pys_utm_id']) : $pys_utm_id;
            $pysData['last_pys_utm_id'] = isset($_REQUEST['last_pys_utm_id']) ? sanitize_text_field($_REQUEST['last_pys_utm_id']) : $pys_utm_id;

            $pysData = array_merge($pysData,$totals);
            $payment_meta['pys_enrich_data'] = $pysData;
        }
        return $payment_meta;
    }


    function add_edd_order_details($payment_id) {
        echo '<div id="edd-payment-notes" class="postbox">
    <h3 class="hndle"><span>PixelTrackPro</span></h3>';
        echo "<div style='margin:20px'><p>You can see more data on the <a href='".admin_url("admin.php?page=pixelyoursite_edd_reports")."' target='_blank'>Easy Digital Downloads Reports</a> page.
        </p>You can turn OFF EDD Reports from the plugin's <a href='".admin_url("admin.php?page=pixelyoursite&tab=edd")."' target='_blank'>Easy Digital Downloads page</a>.</div>";
        include 'views/html-edd-order-box.php';
        echo '</div>';
    }

    function getPysData(){
        $pysData = array();
        $pys_landing = '';
        $pys_source = '';
        $utms = getUtms(true);
        $utms_id = getUtmsId(true);

        $pys_utm = implode("|", array_map(function ($key, $value) {
            return "$key:$value";
        }, array_keys($utms), $utms));
        $pys_utm_id = implode("|", array_map(function ($key, $value) {
            return "$key:$value";
        }, array_keys($utms_id), $utms_id));
        $pys_browser_time = getBrowserTime();
            if (isset($_COOKIE['pys_landing_page']) || isset($_SESSION['LandingPage'])) {
                $pys_landing = $_COOKIE['pys_landing_page'] ?? $_SESSION['LandingPage'];
            }
            if (isset($_COOKIE['pysTrafficSource']) || isset($_SESSION['TrafficSource'])) {
                $pys_source = $_COOKIE['pysTrafficSource'] ?? $_SESSION['TrafficSource'];
            }
        $pysData['pys_landing'] = isset($_REQUEST['pys_landing']) ? sanitize_text_field($_REQUEST['pys_landing']) : $pys_landing ?? 'undefined';
        $pysData['pys_source'] = isset($_REQUEST['pys_source']) ? sanitize_text_field($_REQUEST['pys_source']) : $pys_source ?? 'undefined';
        $pysData['pys_utm'] = isset($_REQUEST['pys_utm']) ? sanitize_text_field($_REQUEST['pys_utm']) : $pys_utm;
        $pysData['pys_browser_time'] = isset($_REQUEST['pys_browser_time']) ? sanitize_text_field($_REQUEST['pys_browser_time']) : $pys_browser_time;

        $pysData['last_pys_landing'] = isset($_REQUEST['last_pys_landing']) ? sanitize_text_field($_REQUEST['last_pys_landing']) : $pys_landing ?? 'undefined';
        $pysData['last_pys_source'] = isset($_REQUEST['last_pys_source']) ? sanitize_text_field($_REQUEST['last_pys_source']) : $pys_source ?? 'undefined';
        $pysData['last_pys_utm'] = isset($_REQUEST['last_pys_utm']) ? sanitize_text_field($_REQUEST['last_pys_utm']) : $pys_utm;

        $pysData['pys_utm_id'] = isset($_REQUEST['pys_utm_id']) ? sanitize_text_field($_REQUEST['pys_utm_id']) : $pys_utm_id;
        $pysData['last_pys_utm_id'] = isset($_REQUEST['last_pys_utm_id']) ? sanitize_text_field($_REQUEST['last_pys_utm_id']) : $pys_utm_id;

        return $pysData;
    }
}

/**
 * @return EnrichOrder
 */
function EnrichOrder() {
    return EnrichOrder::instance();
}

EnrichOrder();

