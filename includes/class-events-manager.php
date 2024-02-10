<?php

namespace PixelYourSite;

use WC_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class EventsManager {

    private static $_instance;
	public $doingAMP = false;
	
	private $staticEvents = array();
    private $dynamicEvents = array();
    private $triggerEvents = array();
    private $triggerEventTypes = array();

    /**
     * @var SingleEvent array
     */
	private $facebookServerEvents = array();
    private $standardParams = array();

    private $eddCustomerTotals = array();

    private $hide_pixels= array();



	public function __construct() {

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueueScripts' ),10 );
        add_action( 'wp_enqueue_scripts', array( $this, 'setupEventsParams' ),14 );
        add_action( 'wp_enqueue_scripts', array( $this, 'outputData' ),15 );
        add_filter( 'pys_getTypePage', array( $this, 'getPageType' ), 10 );
		add_action( 'wp_footer', array( $this, 'outputNoScriptData' ), 10 );
        if ( function_exists( 'WC' ) ) {
            add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'pys_list_name_woocommerce_after_single_product_item'));
            add_action( 'woocommerce_after_shop_loop_item', array( $this,'pys_list_name_woocommerce_after_shop_loop_item' ));
            add_filter('woocommerce_related_products_args', array( $this, 'pys_list_name_woocommerce_add_related_to_loop'));
            add_filter('woocommerce_related_products_columns', array( $this, 'pys_list_name_woocommerce_add_related_to_loop'));
            add_filter( 'woocommerce_cross_sells_columns', array( $this,'pys_list_name_woocommerce_add_cross_sell_to_loop' ));
            add_filter( 'woocommerce_upsells_columns', array( $this,'pys_list_name_woocommerce_add_upsells_to_loop' ));

            add_action( 'woocommerce_shortcode_before_products_loop', array( $this,'pys_list_name_before_products_loop'));

            add_action( 'woocommerce_shortcode_before_recent_products_loop', array( $this,'pys_list_name_before_recent_products_loop'));
            add_action( 'woocommerce_shortcode_before_sale_products_loop', array( $this,'pys_list_name_before_sale_products_loop' ));
            add_action( 'woocommerce_shortcode_before_best_selling_products_loop', array( $this,'pys_list_name_before_best_selling_products_loop' ));
            add_action( 'woocommerce_shortcode_before_top_rated_products_loop', array( $this,'pys_list_name_before_top_rated_products_loop' ));
            add_action( 'woocommerce_shortcode_before_featured_products_loop', array( $this,'pys_list_name_before_featured_products_loop' ));
            add_action( 'woocommerce_shortcode_before_related_products_loop', array( $this,'pys_list_name_before_related_products_loop' ));


            add_filter( 'loop_end', array( $this, 'pys_woocommerce_reset_loop') );
        }
        add_action('wp_head', array( $this, 'delete_cookie_before_load_content'), 1);

	}

    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;

    }
    function delete_cookie_before_load_content() {
        if (isset($_COOKIE['select_prod_list'])) {
            $productlist = json_decode(stripslashes($_COOKIE['select_prod_list']), true);
            $current_url = 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            if (isset($productlist['url']) && $productlist['url'] != $current_url) {
                setcookie('select_prod_list', '', time() - 3600, '/');
            }
        }
    }

    /**
     * @param $arg
     * @return mixed
     */
    function pys_list_name_woocommerce_add_related_to_loop($arg ) {
        global $wp_query, $woocommerce_loop;
        $prod_obj = $wp_query->get_queried_object();
        $woocommerce_loop['listtype'] = GA()->getOption('woo_view_item_list_track_name') ? 'Related Products - '.$prod_obj->post_title : 'Related Products';
        $woocommerce_loop['listtypeid'] = GA()->getOption('woo_view_item_list_track_name') ? 'related_products_'.$prod_obj->post_name : 'related_products';
        return $arg;
    }

    /**
     * @return void
     */
    function pys_list_name_before_products_loop($arg) {
        global $woocommerce_loop;
        $woocommerce_loop['listtype'] = 'Shortcode';
        $woocommerce_loop['listtypeid'] = 'shortcode';
    }
    /**
     * @return void
     */
    function pys_list_name_before_recent_products_loop() {
        global $woocommerce_loop;

        $woocommerce_loop['listtype'] = 'Recent Products';
        $woocommerce_loop['listtypeid'] = 'recent_products';
    }
    /**
     * @param $arg
     * @return mixed
     */
    function pys_list_name_woocommerce_add_cross_sell_to_loop($arg ) {
        global $woocommerce_loop;

        $woocommerce_loop['listtype'] = 'Cross-Sell Products';
        $woocommerce_loop['listtypeid'] = 'cross_sale_products';

        return $arg;
    }

    /**
     * @param $arg
     * @return mixed
     */
    function pys_list_name_woocommerce_add_upsells_to_loop($arg ) {
        global $woocommerce_loop;

        $woocommerce_loop['listtype'] = 'Upsell Products';
        $woocommerce_loop['listtypeid'] = 'upsell_products';

        return $arg;
    }


    /**
     * @return void
     */
    function pys_list_name_before_sale_products_loop() {
        global $woocommerce_loop;

        $woocommerce_loop['listtype'] = 'Shortcode - Sale Products';
        $woocommerce_loop['listtypeid'] = 'shortcode_sale_products';
    }


    /**
     * @return void
     */
    function pys_list_name_before_best_selling_products_loop() {
        global $woocommerce_loop;

        $woocommerce_loop['listtype'] = 'Shortcode - Best Selling Products';
        $woocommerce_loop['listtypeid'] = 'shortcode_best_selling_products';
    }

    /**
     * @return void
     */
    function pys_list_name_before_top_rated_products_loop() {
        global $woocommerce_loop;

        $woocommerce_loop['listtype'] = 'Shortcode - Top Rated Products';
        $woocommerce_loop['listtypeid'] = 'shortcode_top_related_products';
    }

    /**
     * @return void
     */
    function pys_list_name_before_featured_products_loop() {
        global $woocommerce_loop;

        $woocommerce_loop['listtype'] = 'Shortcode - Featured Products';
        $woocommerce_loop['listtypeid'] = 'shortcode_featured_products';
    }

    /**
     * @return void
     */
    function pys_list_name_before_related_products_loop() {
        global $wp_query, $woocommerce_loop;
        $prod_obj = $wp_query->get_queried_object();
        $woocommerce_loop['listtype'] = GA()->getOption('woo_view_item_list_track_name') ? 'Related Products - '.$prod_obj->post_title : 'Related Products';
        $woocommerce_loop['listtypeid'] = GA()->getOption('woo_view_item_list_track_name') ? 'related_products_'.$prod_obj->post_name : 'related_products';
    }

    function pys_list_name_woocommerce_after_single_product_item() {

        if(!GA()->getOption('woo_view_item_list_enabled') && !GA()->getOption('woo_select_content_enabled') ) return;

        global $product, $woocommerce_loop;
        if (isset($_COOKIE['select_prod_list'])) {

            $productlist = json_decode(stripslashes($_COOKIE['select_prod_list']), true);

            $listtype = isset($productlist['list_name']) ? sanitize_text_field($productlist['list_name']) : '';
            $listtypeid = isset($productlist['list_id']) ? sanitize_text_field($productlist['list_id']) : '';
            echo $this->pys_list_name_woocommerce_get_product_list_item_extra_tag( //phpcs:ignore
                $product,
                $listtype,
                $listtypeid,
                apply_filters(
                    'the_permalink',
                    get_permalink(),
                    0
                )
            );
        }

        // no need to escape here as everthing is handled within the function call with esc_attr() and esc_url().

    }
    /**
     * @return void
     */
    function pys_list_name_woocommerce_after_shop_loop_item() {
        if(!GA()->getOption('woo_view_item_list_enabled')) return;
        global $product, $woocommerce_loop;
        $listtype = '';
        $listtypeid = '';
        if ( isset( $woocommerce_loop['listtype'] ) && ( '' !== $woocommerce_loop['listtype'] ) ) {
            $listtype = $woocommerce_loop['listtype'];
        }
        if ( isset( $woocommerce_loop['listtypeid'] ) && ( '' !== $woocommerce_loop['listtypeid'] ) ) {
            $listtypeid = $woocommerce_loop['listtypeid'];
        }
        if ( is_product_category() && empty($listtype)) {
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $listtype = GA()->getOption('woo_view_item_list_track_name') ? 'Category - '.$cat_obj->name : 'Category';
            $listtypeid = GA()->getOption('woo_view_item_list_track_name') ? 'category_'.$cat_obj->slug : 'category';
        }
        if ( is_shop()  && empty($listtype)) {
            $listtype = 'Shop Page';
            $listtypeid = 'shop_page';
        }
        if ( is_tag()  && empty($listtype)) {
            global $wp_query;
            $tag_obj = $wp_query->get_queried_object();
            $listtype = GA()->getOption('woo_view_item_list_track_name') ? 'Tag - ' . $tag_obj->name : 'Tag';
            $listtypeid = GA()->getOption('woo_view_item_list_track_name') ? 'tag_'. $tag_obj->slug : 'tag';
        }
        if ( is_search()  && empty($listtype)) {
            $listtype = 'Search Results';
            $listtypeid = 'search_results';
        }


        $itemix = '';
        if ( isset( $woocommerce_loop['loop'] ) && ( '' !== $woocommerce_loop['loop'] ) ) {
            $itemix = $woocommerce_loop['loop'];
        }
        // no need to escape here as everthing is handled within the function call with esc_attr() and esc_url().
        echo $this->pys_list_name_woocommerce_get_product_list_item_extra_tag( //phpcs:ignore
            $product,
            $listtype,
            $listtypeid,
            $itemix,
            apply_filters(
                'the_permalink',
                get_permalink(),
                0
            )
        );
    }

    /**
     * @return void
     */
    function pys_woocommerce_reset_loop() {
        global $woocommerce_loop;

        $woocommerce_loop['listtype'] = '';
        $woocommerce_loop['listtypeid'] = '';
        $woocommerce_loop['loop'] = '';
    }

    /**
     * @param $product
     * @param $listtype
     * @param $listtypeid
     * @param $itemix
     * @param $permalink
     * @return false|string|void
     */
    function pys_list_name_woocommerce_get_product_list_item_extra_tag($product, $listtype = '', $listtypeid = '', $itemix = 0, $permalink = '' ) {
        if ( ! isset( $product ) ) {
            return;
        }

        if ( ! ( $product instanceof WC_Product )) {
            return false;
        }
        if ( '' !== $listtype ) {
            $list_name = $listtype;
        }
        if ( '' !== $listtypeid ) {
            $list_id = $listtypeid;
        }

        if(!empty($list_name) || !empty($list_id))
        {
            return sprintf(
                '<span class="pys_list_name_productdata" style="display:none; visibility:hidden;" data-pys_list_name_productlist_name="%s" data-pys_list_name_productlist_id="%s"></span>',

                GA()->getOption('woo_track_item_list_name') ? esc_attr( $list_name ) : '',
                GA()->getOption('woo_track_item_list_id') ? esc_attr( $list_id ): ''
            );
        }



    }

    /**
     * @param $product
     * @param $additional_product_attributes
     * @param $attributes_used_for
     * @return array|false
     */
    function pys_list_name_process_product($product, $additional_product_attributes, $attributes_used_for ) {
        global $pys_list_name_options;

        if ( ! $product ) {
            return false;
        }

        if ( ! ( $product instanceof WC_Product ) ) {
            return false;
        }

        $product_id     = $product->get_id();
        $product_type   = $product->get_type();
        $remarketing_id = $product_id;
        $product_sku    = $product->get_sku();



        $_temp_productdata = array(
            'id'         => $remarketing_id,
            'name'       => $product->get_title(),
            'sku'        => $product_sku ? $product_sku : $product_id,
            'price'      => round( (float) wc_get_price_to_display( $product ), 2 ),
            'stocklevel' => $product->get_stock_quantity(),
        );


        if ( 'variation' === $product_type ) {
            $_temp_productdata['variant'] = implode( ',', $product->get_variation_attributes() );
        }

        $_temp_productdata = array_merge( $_temp_productdata, $additional_product_attributes );

        return $_temp_productdata;
    }
	public function enqueueScripts() {

        wp_register_script( 'vimeo', PYS_URL . '/dist/scripts/vimeo.min.js' );
		wp_register_script( 'jquery-bind-first', PYS_URL . '/dist/scripts/jquery.bind-first-0.2.3.min.js', array( 'jquery' ) );
		wp_register_script( 'js-cookie-pys', PYS_URL . '/dist/scripts/js.cookie-2.1.3.min.js', array(), '2.1.3' );
		
		wp_enqueue_script( 'js-cookie-pys' );
		wp_enqueue_script( 'jquery-bind-first' );

		if ( PYS()->getOption( 'automatic_events_enabled' )
            && PYS()->getOption( 'automatic_event_video_enabled' )
            && PYS()->getOption( 'automatic_event_video_vimeo_enabled' )
        ) {

			wp_enqueue_script( 'vimeo' );
		}
        if ( PYS()->getOption( 'compress_front_js' )){
            wp_enqueue_script( 'pys', PYS_URL . '/dist/scripts/public.bundle.js',
            array( 'jquery', 'js-cookie-pys', 'jquery-bind-first' ), PYS_VERSION );
        }
        else
        {
            wp_enqueue_script( 'pys', PYS_URL . '/dist/scripts/public.js',
                array( 'jquery', 'js-cookie-pys', 'jquery-bind-first' ), PYS_VERSION );
        }


	}
    function getPageType() {
        if ( is_home() ) {
            return 'home';
        } elseif ( is_front_page() ) {
            return 'front_page';
        } elseif ( is_single() ) {
            return 'single';
        } elseif ( is_page() ) {
            return 'page';
        } elseif ( is_archive() ) {
            return 'archive';
        } elseif ( is_category() ) {
            return 'category';
        } elseif ( is_tag() ) {
            return 'tag';
        } elseif ( is_search() ) {
            return 'search';
        } elseif ( is_404() ) {
            return '404';
        } else {
            return 'unknown';
        }
    }
	public function outputData() {

		$data = array(
			'staticEvents'          => $this->staticEvents,
            'dynamicEvents'          => $this->dynamicEvents,
			'triggerEvents'         => $this->triggerEvents,
			'triggerEventTypes'     => $this->triggerEventTypes,
		);

		// collect options for configured pixel
		foreach ( PYS()->getRegisteredPixels() as $pixel ) {
			/** @var Pixel|Settings $pixel */
			
			if ( $pixel->configured() ) {
				$data[ $pixel->getSlug() ] = $pixel->getPixelOptions();
			}

		}

		$options = array(
			'debug'                             => PYS()->getOption( 'debug_enabled' ),
			'siteUrl'                           => site_url(),
			'ajaxUrl'                           => admin_url( 'admin-ajax.php' ),
            'ajax_event'                        => wp_create_nonce('ajax-event-nonce'),
			'trackUTMs'                         => PYS()->getOption( 'track_utms' ),
			'trackTrafficSource'                => PYS()->getOption( 'track_traffic_source' ),
            'user_id'                           => GA()->enabled() && GA()->getOption("track_user_id") ? get_current_user_id() : 0,
            'enable_lading_page_param'          => PYS()->getOption( 'enable_lading_page_param' ),
            'cookie_duration'                   => PYS()->getOption( 'cookie_duration' ),
            'enable_event_day_param'            => PYS()->getOption( 'enable_event_day_param' ),
            'enable_event_month_param'          => PYS()->getOption( 'enable_event_month_param' ),
            'enable_event_time_param'           => PYS()->getOption( 'enable_event_time_param' ),
            'enable_remove_target_url_param'    => PYS()->getOption( 'enable_remove_target_url_param' ),
            'enable_remove_download_url_param'  => PYS()->getOption( 'enable_remove_download_url_param' ),
            'visit_data_model'                  => PYS()->getOption('visit_data_model'),
            'last_visit_duration'               => PYS()->getOption('last_visit_duration'),
            'enable_auto_save_advance_matching' => PYS()->getOption( 'enable_auto_save_advance_matching' ),
            'advance_matching_fn_names'         => PYS()->getOption( 'advance_matching_fn_names' ),
            'advance_matching_ln_names'         => PYS()->getOption( 'advance_matching_ln_names' ),
            'advance_matching_tel_names'         => PYS()->getOption( 'advance_matching_tel_names' ),
            'enable_success_send_form'         => PYS()->getOption( 'enable_success_send_form' ),
            'enable_automatic_events' => PYS()->getOption( 'automatic_events_enabled' ),
            'enable_event_video'                => PYS()->getOption( 'automatic_event_video_enabled' ),
			"ajaxForServerEvent"                => PYS()->getOption( 'server_event_use_ajax'),
            "send_external_id" => PYS()->getOption( 'send_external_id'),
            "external_id_expire"=> PYS()->getOption( 'external_id_expire')
		);
		
		$options['gdpr'] = array(
			'ajax_enabled'              => PYS()->getOption( 'gdpr_ajax_enabled' ),
			'all_disabled_by_api'       => apply_filters( 'ptp_disable_by_gdpr', false ),
			'facebook_disabled_by_api'  => apply_filters( 'ptp_disable_facebook_by_gdpr', false ),
            'tiktok_disabled_by_api'  => apply_filters( 'ptp_disable_tiktok_by_gdpr', false ),
			'analytics_disabled_by_api' => apply_filters( 'ptp_disable_analytics_by_gdpr', false ),
			'google_ads_disabled_by_api' => apply_filters( 'ptp_disable_google_ads_by_gdpr', false ),
			'pinterest_disabled_by_api' => apply_filters( 'ptp_disable_pinterest_by_gdpr', false ),
			'bing_disabled_by_api' => apply_filters( 'ptp_disable_bing_by_gdpr', false ),

            'externalID_disabled_by_api' => apply_filters( 'ptp_disable_externalID_by_gdpr', false ),
			
			'facebook_prior_consent_enabled'   => PYS()->getOption( 'gdpr_facebook_prior_consent_enabled' ),
            'tiktok_prior_consent_enabled'   => PYS()->getOption( 'gdpr_tiktok_prior_consent_enabled' ),
			'analytics_prior_consent_enabled'  => PYS()->getOption( 'gdpr_analytics_prior_consent_enabled' ),
			'google_ads_prior_consent_enabled' => PYS()->getOption( 'gdpr_google_ads_prior_consent_enabled' ),
			'pinterest_prior_consent_enabled'  => PYS()->getOption( 'gdpr_pinterest_prior_consent_enabled' ),
			'bing_prior_consent_enabled' => PYS()->getOption( 'gdpr_bing_prior_consent_enabled' ),

			'cookiebot_integration_enabled'         => isCookiebotPluginActivated() && PYS()->getOption( 'gdpr_cookiebot_integration_enabled' ),
			'cookiebot_facebook_consent_category'   => PYS()->getOption( 'gdpr_cookiebot_facebook_consent_category' ),
            'cookiebot_tiktok_consent_category'   => PYS()->getOption( 'gdpr_cookiebot_tiktok_consent_category' ),
			'cookiebot_analytics_consent_category'  => PYS()->getOption( 'gdpr_cookiebot_analytics_consent_category' ),
			'cookiebot_google_ads_consent_category' => PYS()->getOption( 'gdpr_cookiebot_google_ads_consent_category' ),
			'cookiebot_pinterest_consent_category'  => PYS()->getOption( 'gdpr_cookiebot_pinterest_consent_category' ),
			'cookiebot_bing_consent_category' => PYS()->getOption( 'gdpr_cookiebot_bing_consent_category' ),
			'cookie_notice_integration_enabled' => isCookieNoticePluginActivated() && PYS()->getOption( 'gdpr_cookie_notice_integration_enabled' ),
			'cookie_law_info_integration_enabled' => isCookieLawInfoPluginActivated() && PYS()->getOption( 'gdpr_cookie_law_info_integration_enabled' ),
            'real_cookie_banner_integration_enabled' => isRealCookieBannerPluginActivated() && PYS()->getOption( 'gdpr_real_cookie_banner_integration_enabled' ),
            'consent_magic_integration_enabled' => isConsentMagicPluginActivated() && PYS()->getOption( 'consent_magic_integration_enabled' ),
		);
        $options['cookie'] = array(
            'disabled_all_cookie'       => apply_filters( 'ptp_disable_all_cookie', false ),
            'disabled_start_session_cookie' => apply_filters( 'ptp_disabled_start_session_cookie', false ),
            'disabled_advanced_form_data_cookie' => apply_filters( 'ptp_disable_advanced_form_data_cookie', false ),
            'disabled_landing_page_cookie'  => apply_filters( 'ptp_disable_landing_page_cookie', false ),
            'disabled_first_visit_cookie'  => apply_filters( 'ptp_disable_first_visit_cookie', false ),
            'disabled_trafficsource_cookie' => apply_filters( 'ptp_disable_trafficsource_cookie', false ),
            'disabled_utmTerms_cookie' => apply_filters( 'ptp_disable_utmTerms_cookie', false ),
            'disabled_utmId_cookie' => apply_filters( 'ptp_disable_utmId_cookie', false ),
        );

        $options['tracking_analytics'] = array(
            "TrafficSource"=> $_COOKIE['pysTrafficSource'] ?? $_SESSION['TrafficSource'] ?? 'undefined' ,
            "TrafficLanding"=> $_COOKIE['pys_landing_page'] ?? $_SESSION['LandingPage']  ?? 'undefined',
            "TrafficUtms"=> getUtms(),
            "TrafficUtmsId"=> getUtmsId(),
        );

        /**
         * @var EventsFactory[] $eventsFactory
         */
        $eventsFactory = apply_filters("pys_event_factory",[]);
        foreach ($eventsFactory as $factory) {
            $opt =  $factory->getOptions();
            if(!empty($opt)) {
                $options[$factory::getSlug()] = $factory->getOptions();
            }
        }

		$data = array_merge( $data, $options );

		wp_localize_script( 'pys', 'pysOptions', $data );

	}
	
	public function outputNoScriptData() {
        if(!apply_filters( 'ptp_disable_by_gdpr', false)) {
            foreach (PYS()->getRegisteredPixels() as $pixel) {
                /** @var Pixel|Settings $pixel */
                if (!apply_filters('pys_disable_' . $pixel->getSlug() . '_by_gdpr', false)) {
                    $pixel->outputNoScriptEvents();
                }
            }
        }
	}

    public function getEddCustomerTotals() {

        // setup and cache params
        if ( empty( $this->eddCustomerTotals ) ) {
            $this->eddCustomerTotals = getEddCustomerTotals();
        }

        return $this->eddCustomerTotals;

    }

	public function setupEventsParams() {
        $this->standardParams = getStandardParams();

        $this->facebookServerEvents = array();

        if(EventsEdd()->isEnabled()) {
            // AddToCart on button
            if ( isEventEnabled( 'edd_add_to_cart_enabled') && PYS()->getOption( 'edd_add_to_cart_on_button_click' ) ) {
                add_action( 'edd_purchase_link_end', array( $this, 'setupEddSingleDownloadData' ) );
            }
        }

        if(EventsWoo()->isEnabled()){
            // AddToCart on button and Affiliate
            if(PYS()->getOption('woo_add_to_cart_catch_method') == "add_cart_js") {
                if ( isEventEnabled( 'woo_add_to_cart_enabled') && PYS()->getOption( 'woo_add_to_cart_on_button_click' )
                    || isEventEnabled( 'woo_affiliate_enabled') ) {

                    add_action( 'woocommerce_after_shop_loop_item', array( $this, 'setupWooLoopProductData' ) );
                    add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'setupWooBlocksProductData' ), 10, 3 );
                    add_filter('jet-woo-builder/elementor-views/frontend/archive-item-content', array( $this, 'setupWooBlocksProductData' ),10, 3);

                    if(is_product()) {
                        if(PYS()->getOption('woo_add_to_cart_on_single_product') == 'add_cart_hook') {
                            add_action( 'woocommerce_after_add_to_cart_button', 'PixelYourSite\EventsManager::setupWooSingleProductData' );
                        } else {
                            EventsManager::setupWooSingleProductData();
                        }
                    } else {
                        add_action( 'woocommerce_after_add_to_cart_button', 'PixelYourSite\EventsManager::setupWooSingleProductData' );
                    }
                }
            }

            add_filter("ptp_validate_pixel_event",array($this,'validatePixelEvent'),10,3);
        }

        /**
        * @var EventsFactory[] $eventsFactory
         **/
        $eventsFactory = apply_filters("pys_event_factory",[]);

        foreach ($eventsFactory as $factory) {
            if(!$factory->isEnabled())  continue;
            $events = $factory->generateEvents();
            $this->addEvents($events,$factory->getSlug());
        }

		// initial event
		foreach ( PYS()->getRegisteredPixels() as $pixel ) {
			if(method_exists($pixel,'generateEvents')) {
				$pixelEvents =  $pixel->generateEvents( new SingleEvent('init_event',EventTypes::$STATIC,'') );
				if ( count($pixelEvents) == 0 ) {
					continue; // event is disabled or not supported for the pixel
				}
				$event = $pixelEvents[0];
			} else {
				$event = new SingleEvent('init_event',EventTypes::$STATIC,'');
				$isSuccess = $pixel->addParamsToEvent( $event );
				if ( !$isSuccess ) {
					continue; // event is disabled or not supported for the pixel
				}
			}


			if($pixel->getSlug() != Tiktok()->getSlug()) {
				$params = array();
				if(get_post_type() == "post") {
					global $post;
					$catIds = wp_get_object_terms( $post->ID, 'category', array( 'fields' => 'names' ) );
					$params['post_category'] = implode(", ",$catIds) ;
				}
				$event->addParams($params);
				$event->addParams($this->standardParams);
			}

			$this->addStaticEvent( $event,$pixel,"" );
		}

        // add Facebook Server events for async sending
        if (count($this->facebookServerEvents) > 0 &&  Facebook()->enabled()) {
            FacebookServer()->sendEventsAsync($this->facebookServerEvents);
            $this->facebookServerEvents = array();
        }

        // remove new user mark
        if($user_id = get_current_user_id()) {
            if ( get_user_meta( $user_id, 'pys_complete_registration', true ) ) {
                delete_user_meta( $user_id, 'pys_complete_registration' );
            }
        }
	}



	function addEvents($pixelEvents,$slug) {


	    foreach ($pixelEvents as $pixelSlug => $events) {
            $pixel = PYS()->getRegisteredPixels()[$pixelSlug];
	        foreach ($events as $event) {
                // add standard params

				if($pixelSlug != Tiktok()->getSlug())
					$event->addParams($this->standardParams);

                if($event->getType() == EventTypes::$STATIC) {
                    $this->addStaticEvent( $event,$pixel,$slug );
                } elseif($event->getType() == EventTypes::$TRIGGER) {
                    $this->addTriggerEvent($event,$pixel,$slug);
                } else {
                    $this->addDynamicEvent($event,$pixel,$slug);
                }
            }

        }
    }

    /**
     * @param SingleEvent $event
     * @param $pixel
     * @param $slug
     */
    function addDynamicEvent($event,$pixel,$slug) {

        if($event->getId() == 'woo_select_content_search' ||
            $event->getId() == 'woo_select_content_shop' ||
            $event->getId() == 'woo_select_content_tag'||
            $event->getId() == 'woo_select_content_single' ||
            $event->getId() == 'woo_select_content_category')
        {
            $eventData = $event->getData();
            $eventData = $this::filterEventParams($eventData,$slug,['event_id'=>$event->getId(),'pixel'=>$pixel->getSlug()]);
            $this->dynamicEvents[ $event->getId() ][ $event->args ][ $pixel->getSlug() ] = $eventData;
        }
        else if($event->getId() == 'edd_remove_from_cart' || $event->getId() == 'woo_remove_from_cart')
        {
            $eventData = $event->getData();
            $eventData = $this::filterEventParams($eventData,$slug,['event_id'=>$event->getId(),'pixel'=>$pixel->getSlug()]);
            $this->dynamicEvents[ $event->getId() ][ $event->args['key'] ][ $pixel->getSlug() ] = $eventData;
        } else {
            $eventData = $event->getData();
            $eventData = $this::filterEventParams($eventData,$slug,['event_id'=>$event->getId(),'pixel'=>$pixel->getSlug()]);
            //save static event data
            $this->dynamicEvents[ $event->getId() ][ $pixel->getSlug() ] = $eventData;
        }
    }



    function addTriggerEvent($event,$pixel,$slug) {
        $eventData = $event->getData();
        $eventData = $this::filterEventParams($eventData,$slug,['event_id'=>$event->getId(),'pixel'=>$pixel->getSlug()]);
        //save static event data
        if($event->getId() == "custom_event") {
            $eventId = $event->args->getPostId();
        } else {
            $eventId = $event->getId();
        }
        $this->triggerEvents[ $eventId ][ $pixel->getSlug() ] = $eventData;
        if(!empty($event->args) && $event->args->isFormTriggerType())
        {
            $this->triggerEventTypes[ $eventData['trigger_type'] ][ $eventId ]['forms'] = $eventData['trigger_value'];
            if(isset($eventData['disabled_form_action']))
            {
                $this->triggerEventTypes[ $eventData['trigger_type'] ][ $eventId ]['disabled_form_action'] = $eventData['disabled_form_action'];
            }
        }
        else
        {
            $this->triggerEventTypes[ $eventData['trigger_type'] ][ $eventId ] = $eventData['trigger_value'];
        }
    }
    /**
     * Create stack event, they fire when page loaded
     * @param PYSEvent $event
     */
    function addStaticEvent($event, $pixel,$slug) {

            $eventData = $event->getData();
            $eventData = $this::filterEventParams($eventData,$slug,['event_id'=>$event->getId(),'pixel'=>$pixel->getSlug()]);
            // send only for FB Server events
            if($pixel->getSlug() == "facebook" &&
                ($event->getId() == "woo_complete_registration") &&
                Facebook()->isServerApiEnabled() &&
                Facebook()->getOption("woo_complete_registration_send_from_server") &&
                !$this->isGdprPluginEnabled() )
            {
                if($eventData['delay'] == 0) {
                    $this->facebookServerEvents[] = $event;
                }
                return;
            }

            //save static event data
            $this->staticEvents[ $pixel->getSlug() ][ $event->getId() ][] = $eventData;
            // fire fb server api event
            if($pixel->getSlug() == "facebook") {
                if( $eventData['delay'] == 0 && !PYS()->getOption( "server_event_use_ajax" )) {
                    $this->facebookServerEvents[] = $event;
                }
            }

    }

    static function  filterEventParams($data,$slug,$context = null) {

        if(!PYS()->getOption('enable_content_name_param')) {
            unset($data['params']['content_name']);
        }

        if(!PYS()->getOption('enable_page_title_param')) {
            unset($data['params']['page_title']);
        }

        if(!PYS()->getOption('enable_tags_param')) {
            unset($data['params']['tags']);
        }

        if(!PYS()->getOption('enable_categories_param')) {
            unset($data['params']['categories']);
        }
        if(!PYS()->getOption('enable_post_category_param')) {
            unset($data['params']['post_category']);
        }

        if($slug == EventsWoo::getSlug()) {
            if(!PYS()->getOption("enable_woo_category_name_param")) {
                unset($data['params']['category_name']);
            }
            if(!PYS()->getOption("enable_woo_num_items_param")) {
                unset($data['params']['num_items']);
            }
            if(!PYS()->getOption("enable_woo_tags_param")) {
                unset($data['params']['tags']);
            }
            if(!PYS()->getOption("enable_woo_total_param")) {
                unset($data['params']['total']);
            }

            if(!PYS()->getOption("enable_woo_tax_param")) {
                unset($data['params']['tax']);
            }
            if(!PYS()->getOption("enable_woo_fees_param")) {
                unset($data['params']['fees']);
            }
            if(!PYS()->getOption("enable_woo_transactions_count_param")) {
                unset($data['params']['transactions_count']);
            }
            if(!PYS()->getOption("enable_woo_predicted_ltv_param")) {
                unset($data['params']['predicted_ltv']);
            }
            if(!PYS()->getOption("enable_woo_average_order_param")) {
                unset($data['params']['average_order']);
            }
            if(!PYS()->getOption("enable_woo_coupon_used_param")) {
                unset($data['params']['coupon_used']);
            }
            if(!PYS()->getOption("enable_woo_coupon_name_param")) {
                unset($data['params']['coupon_name']);
            }
            if(!PYS()->getOption("enable_woo_shipping_param")) {
                unset($data['params']['shipping']);
            }
            if(!PYS()->getOption("enable_woo_shipping_cost_param")) {
                unset($data['params']['shipping_cost']);
            }


        }

        if($slug == EventsEdd::getSlug()) {
            if(!PYS()->getOption("enable_edd_category_name_param")) {
                unset($data['params']['category_name']);
            }
            if(!PYS()->getOption("enable_edd_num_items_param")) {
                unset($data['params']['num_items']);
            }
            if(!PYS()->getOption("enable_edd_total_param")) {
                unset($data['params']['total']);
            }

            if(!PYS()->getOption("enable_edd_tags_param")) {
                unset($data['params']['tags']);
            }
            if(!PYS()->getOption("enable_edd_tax_param")) {
                unset($data['params']['tax']);
            }
            if(!PYS()->getOption("enable_edd_coupon_param")) {
                unset($data['params']['coupon']);
            }
        }

        return apply_filters('ptp_event_data',$data,$slug,$context);
    }

	function validatePixelEvent($isValid,$event,$pixel) {
        // skip woo purchase by zero value settings
        if( ($event->getId() == "woo_purchase" || $event->getId() ==  "woo_purchase_category")
            && PYS()->getOption("woo_purchase_not_fire_for_zero")
        ) {
            if($pixel->getSlug() == "bing") {
                if($event->getParamValue('event_value') == 0) {
                    return false;
                }
            } else {
                if($event->getParamValue('value') == 0) {
                    return false;
                }
            }

        }

        if( ($event->getId() == "woo_purchase" || $event->getId() ==  "woo_purchase_category")
            && PYS()->getOption("woo_purchase_not_fire_for_zero_items")
        ) {
            if(empty($event->args['products']))
            {
                return false;
            }
        }

        // skip edd purchase by zero value settings
        if( ($event->getId() == "edd_purchase" || $event->getId() ==  "edd_purchase_category") &&
            PYS()->getOption("edd_purchase_not_fire_for_zero")
        ) {

            if($pixel->getSlug() == "bing") {
                if($event->getParamValue('event_value') == 0) {
                    return false;
                }
            } else {
                if($event->getParamValue('value') == 0) {
                    return false;
                }
            }

        }

        if( ($event->getId() == "edd_purchase" || $event->getId() ==  "edd_purchase_category") &&
            PYS()->getOption("edd_purchase_not_fire_for_zero_items")
        ) {
            if(empty($event->args['products']))
            {
                return false;
            }
        }

        return $isValid;
    }

	public function getStaticEvents( $context ) {
		return isset( $this->staticEvents[ $context ] ) ? $this->staticEvents[ $context ] : array();
	}






	public function setupEddSingleDownloadData($purchase_link) {
        $download = $purchase_link;

		$download_ids = array();

		if ( edd_has_variable_prices( $download ) ) {

			$prices = edd_get_variable_prices( $download );

			foreach ( $prices as $price_index => $price_data ) {
				$download_ids[] = $download . '_' . $price_index;
			}

		} else {
			$download_ids[] = $download;
		}

		$params = array();

		foreach ( $download_ids as $download_id ) {
            $event = EventsEdd()->getEvent('edd_add_to_cart_on_button_click');
            $event->args = $download_id;
			foreach ( PYS()->getRegisteredPixels() as $pixel ) {
				/** @var Pixel|Settings $pixel */
                $pixelEvents =  $pixel->generateEvents( $event );

				foreach ($pixelEvents as $singleEvent) {
                    $eventData = EventsManager::filterEventParams($singleEvent->getData(),"edd");
                    /**
                     * Format is pysEddProductData[ id ][ id ] or pysEddProductData[ id ] [ id_1, id_2, ... ]
                     */
                    $params[ $download_id ][ $pixel->getSlug() ] = [ // replace data there use only one event
                        'params' => $eventData['params']
                    ];
                }
			}
		}

		if ( empty( $params ) ) {
			return;
		}

		?>

		<script type="application/javascript" style="display:none">
			/* <![CDATA[ */
			window.pysEddProductData = window.pysEddProductData || [];
			window.pysEddProductData[<?php echo $download; ?>] = <?php echo json_encode( $params ); ?>;
			/* ]]> */
		</script>

		<?php

	}


	function isGdprPluginEnabled() {
        return apply_filters( 'ptp_disable_by_gdpr', false ) ||
            apply_filters( 'ptp_disable_facebook_by_gdpr', false ) ||
            isCookiebotPluginActivated() && PYS()->getOption( 'gdpr_cookiebot_integration_enabled' ) ||
            isCookieNoticePluginActivated() && PYS()->getOption( 'gdpr_cookie_notice_integration_enabled' ) ||
            isRealCookieBannerPluginActivated() && PYS()->getOption( 'gdpr_real_cookie_banner_integration_enabled' ) ||
            isConsentMagicPluginActivated() && PYS()->getOption( 'consent_magic_integration_enabled' ) ||
            isCookieLawInfoPluginActivated() && PYS()->getOption( 'gdpr_cookie_law_info_integration_enabled' );
    }

    public function setupWooLoopProductData()
    {
        global $product;
        $this->setupWooProductData($product);
    }

    public function setupWooBlocksProductData($html, $data, $product)
    {
        $this->setupWooProductData($product);
        return $html;
    }

    public function setupWooProductData($product) {
        if ( !is_a($product,"WC_Product")
            || wooProductIsType( $product, 'variable' )
            || wooProductIsType( $product, 'grouped' )
        ) {
            return; // skip variable products
        }

        if ( wooProductIsType( $product, 'external' ) ) {
            $eventType = 'woo_affiliate';
        } else {
            $eventType = 'woo_add_to_cart_on_button_click';
        }

        $product_id = $product->get_id();

        $params = array();


        foreach ( PYS()->getRegisteredPixels() as $pixel ) {
            /** @var Pixel|Settings $pixel */
            $events = [];
            $initEvent = new SingleEvent($eventType,EventTypes::$STATIC,"woo");
            $initEvent->args = ['productId' => $product_id,'quantity' => 1];
            if(method_exists($pixel,'generateEvents')) {
                add_filter('pys_conditional_post_id', function($id) use ($product_id) { return $product_id; });
                $events =  $pixel->generateEvents( $initEvent );
                remove_all_filters('pys_conditional_post_id',10);
            } else {
                $isSuccess = $pixel->addParamsToEvent( $initEvent );
                if ( $isSuccess ) {
                    $events[] = $initEvent;
                }
            }

            if(count($events) == 0) continue;

            $event = $events[0];

            // prepare event data
            $eventData = EventsManager::filterEventParams($event->getData(),"woo",[
                'event_id'=>$event->getId(),
                'pixel'=>$pixel->getSlug(),
                'product_id'=>$product_id
            ]);

            $params[$pixel->getSlug()] = $eventData;


        }

        if ( empty( $params ) ) {
            return;
        }

        $params = json_encode( $params );

        ?>

        <script type="application/javascript" style="display:none">
            /* <![CDATA[ */
            window.pysWooProductData = window.pysWooProductData || [];
            window.pysWooProductData[ <?php echo $product_id; ?> ] = <?php echo $params; ?>;
            /* ]]> */
        </script>

        <?php

    }

    public static function setupWooSingleProductData() {
        global $product;

        if ( ! is_object( $product)) $product = wc_get_product( get_the_ID() );

        if(!$product || !is_a($product,"WC_Product") ) return;

        if ( wooProductIsType( $product, 'external' ) ) {
            $eventType = 'woo_affiliate';
        } else {
            $eventType = 'woo_add_to_cart_on_button_click';
        }
        $product_id = $product->get_id();

        // main product id
        $product_ids[] = $product_id;

        // variations ids
        if ( wooProductIsType( $product, 'variable' ) ) {
            $product_ids = array_merge($product_ids, $product->get_children());
        }

        $params = array();

        foreach ( $product_ids as $product_id ) {

            foreach ( PYS()->getRegisteredPixels() as $pixel ) {
                /** @var Pixel|Settings $pixel */
                $initEvent = new SingleEvent($eventType,EventTypes::$STATIC,"woo");
                $initEvent->args = ['productId' => $product_id,'quantity' => 1];
                $events = [];
                if(method_exists($pixel,'generateEvents')) {
                    add_filter('pys_conditional_post_id', function($id) use ($product_id) { return $product_id; });
                    $events =  $pixel->generateEvents( $initEvent );
                    remove_all_filters('pys_conditional_post_id',10);
                } else {
                    if( $pixel->addParamsToEvent( $initEvent )) {
                        $events[] = $initEvent;
                    }
                }

                if(count($events) == 0) continue;
                $event = $events[0];

                // prepare event data
                $eventData = $event->getData();
                $eventData = EventsManager::filterEventParams($eventData,"woo",[
                                                                        'event_id'=>$event->getId(),
                                                                        'pixel'=>$pixel->getSlug(),
                                                                        'product_id'=>$product_id
                                                                    ]);

                $params[ $product_id ][ $pixel->getSlug() ] = $eventData;

            }

        }

        if ( empty( $params ) ) {
            return;
        }

        ?>

        <script type="application/javascript" style="display:none">
            /* <![CDATA[ */
            window.pysWooProductData = window.pysWooProductData || [];
            <?php foreach ( $params as $product_id => $product_data ) : ?>
            window.pysWooProductData[<?php echo $product_id; ?>] = <?php echo json_encode( $product_data ); ?>;
            <?php endforeach; ?>
            /* ]]> */
        </script>

        <?php

    }

//$formData = array('formType' => 'gravity', 'formId' => $form['id'])
    public function trackFormAfterReload($formData = null)
    {
        $this->standardParams = getStandardParams();
        $disabled_form_action = false;
        if($formData)
        {
            $triggerEvent = EventsCustom()->getEvents();
            foreach ($triggerEvent as $trigger)
            {
                $event = EventsCustom()->getEvent($trigger);
                $triggerValue = $trigger->getFormEventTriggerForms();
                if(array_key_exists('formType', $formData) && array_key_exists('formId', $formData) && $trigger->trigger_type == $formData['formType'] && in_array($formData['formId'], $triggerValue))
                {
                    $disabled_form_action = $trigger->disabled_form_action;
                    foreach ( PYS()->getRegisteredPixels() as $pixel ) {
                        if(method_exists($pixel,'generateEvents')) {
                            $events =  $pixel->generateEvents( $event );
                            if(!empty($events))
                            {
                                foreach ($events as $ev)
                                {
                                    $ev->addParams($this->standardParams);
                                    $this->addStaticEvent($ev, $pixel, 'pys');
                                }
                            }
                        }


                    }
                }
            }
        }

    }


    static function isTrackExternalId(){
        return PYS()->getOption("send_external_id") && !apply_filters( 'ptp_disable_externalID_by_gdpr', false ) && !apply_filters( 'ptp_disable_all_cookie', false );
    }

}