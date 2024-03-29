<?php

namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @property int    post_id
 * @property string title
 * @property bool   enabled
 *
 * @property int    delay
 * @property array  triggers
 * @property array  url_filters
 * @property string trigger_type
 * @property int    time_window
 * @property bool   enable_time_window
 *
 * @property array forms
 * @property bool disabled_form_action
 *
 * @property bool   facebook_enabled
 * @property string   facebook_pixel_id
 * @property string facebook_event_type
 * @property string facebook_custom_event_type
 * @property bool   facebook_params_enabled
 * @property array  facebook_params
 * @property array  facebook_custom_params
 *
 * @property bool   pinterest_enabled
 * @property string pinterest_event_type
 * @property string pinterest_custom_event_type
 * @property bool   pinterest_params_enabled
 * @property array  pinterest_custom_params
 *
 * @property bool   ga_enabled
 * @property string ga_pixel_id
 * @property string ga_event_action
 * @property string ga_custom_event_action
 * @property array  ga_custom_params
 * @property array  ga_params
 * @property string ga_version
 * @property string ga_conversion_label
 *
 * @property bool   ga_ads_enabled
 * @property string ga_ads_pixel_id
 * @property string ga_ads_event_action
 * @property string ga_ads_custom_event_action
 * @property array  ga_ads_custom_params
 * @property array  ga_ads_params
 * @property string ga_ads_version
 * @property string ga_ads_conversion_label
 * @property string ga_ads_event_category
 * @property string ga_ads_event_label
 *
 * @property bool   google_ads_enabled
 * @property string google_ads_conversion_id
 * @property string google_ads_conversion_label
 * @property string google_ads_event_action
 * @property string google_ads_custom_event_action
 * @property string google_ads_event_category
 * @property string google_ads_event_label
 * @property string google_ads_event_value
 * @property array  google_ads_custom_params
 *
 * @property bool   bing_enabled
 * @property string bing_event_action
 * @property string bing_event_category
 * @property string bing_event_label
 * @property string bing_event_value
 * @property string bing_pixel_id
 *
 * @property bool   tiktok_enabled
 * @property string tiktok_pixel_id
 * @property string tiktok_event_type
 * @property string tiktok_custom_event_type
 * @property bool   tiktok_params_enabled
 * @property array  tiktok_params
 * @property array  tiktok_custom_params
 */
class CustomEvent {

	private $post_id;

	private $title = 'Untitled';

	private $enabled = true;

    public static $currencies = array(
        'AUD' => 'Australian Dollar',
        'BRL' => 'Brazilian Real',
        'CAD' => 'Canadian Dollar',
        'CZK' => 'Czech Koruna',
        'DKK' => 'Danish Krone',
        'EUR' => 'Euro',
        'HKD' => 'Hong Kong Dollar',
        'HUF' => 'Hungarian Forint',
        'IDR' => 'Indonesian Rupiah',
        'ILS' => 'Israeli New Sheqel',
        'JPY' => 'Japanese Yen',
        'KRW' => 'Korean Won',
        'MYR' => 'Malaysian Ringgit',
        'MXN' => 'Mexican Peso',
        'NOK' => 'Norwegian Krone',
        'NZD' => 'New Zealand Dollar',
        'PHP' => 'Philippine Peso',
        'PLN' => 'Polish Zloty',
        'RON' => 'Romanian Leu',
        'GBP' => 'Pound Sterling',
        'SGD' => 'Singapore Dollar',
        'SEK' => 'Swedish Krona',
        'CHF' => 'Swiss Franc',
        'TWD' => 'Taiwan New Dollar',
        'THB' => 'Thai Baht',
        'TRY' => 'Turkish Lira',
        'USD' => 'U.S. Dollar',
        'ZAR' => 'South African Rands'
    );
    public static $tikTokEvents = [
        'CustomEvent'   => [],
        'Search'                => [],
        'ViewContent'           => [
            ['type'=>'input','label'=>'content_type','name'=>'pys[event][tiktok_params][content_type]'],
            ['type'=>'input','label'=>'quantity','name'=>'pys[event][tiktok_params][quantity]'],
            ['type'=>'input','label'=>'content_id','name'=>'pys[event][tiktok_params][content_id]'],
            ['type'=>'input','label'=>'value','name'=>'pys[event][tiktok_params][value]'],
            ['type'=>'currency','label'=>'currency','name'=>'pys[event][tiktok_params][currency]'],
        ],
        'ClickButton'           => [],
        'AddToWishlist'         => [],
        'AddToCart'             => [
            ['type'=>'input','label'=>'content_type','name'=>'pys[event][tiktok_params][content_type]'],
            ['type'=>'input','label'=>'quantity','name'=>'pys[event][tiktok_params][quantity]'],
            ['type'=>'input','label'=>'content_id','name'=>'pys[event][tiktok_params][content_id]'],
            ['type'=>'input','label'=>'value','name'=>'pys[event][tiktok_params][value]'],
            ['type'=>'currency','label'=>'currency','name'=>'pys[event][tiktok_params][currency]'],
        ],
        'InitiateCheckout'      => [],
        'AddPaymentInfo'        => [],
        'CompletePayment'       => [
            ['type'=>'input','label'=>'content_type','name'=>'pys[event][tiktok_params][content_type]'],
            ['type'=>'input','label'=>'quantity','name'=>'pys[event][tiktok_params][quantity]'],
            ['type'=>'input','label'=>'content_id','name'=>'pys[event][tiktok_params][content_id]'],
            ['type'=>'input','label'=>'value','name'=>'pys[event][tiktok_params][value]'],
            ['type'=>'currency','label'=>'currency','name'=>'pys[event][tiktok_params][currency]'],
        ],
        'PlaceAnOrder'          => [
            ['type'=>'input','label'=>'content_type','name'=>'pys[event][tiktok_params][content_type]'],
            ['type'=>'input','label'=>'quantity','name'=>'pys[event][tiktok_params][quantity]'],
            ['type'=>'input','label'=>'content_id','name'=>'pys[event][tiktok_params][content_id]'],
            ['type'=>'input','label'=>'value','name'=>'pys[event][tiktok_params][value]'],
            ['type'=>'currency','label'=>'currency','name'=>'pys[event][tiktok_params][currency]'],
        ],
        'Contact'               => [],
        'Download'              => [],
        'SubmitForm'            => [],
        'CompleteRegistration'  => [],
        'Subscribe'             => []
    ];
	public $GAEvents = array(
	    "" => array("CustomEvent"=>array()),
	    "All Properties"    => array(
            "earn_virtual_currency" => array("virtual_currency_name","value"),
            "join_group" => array("group_id"),
            "login" => array("method"),
            "purchase" => array("transaction_id",'value','currency','tax','shipping','items','coupon', 'google_business_vertical'),
            "refund" => array("transaction_id",'value','currency','tax','shipping','items'),
            "search" => array("search_term"),
            "select_content" => array("content_type",'item_id'),
            "share" => array("content_type",'item_id'),
            "sign_up" => array("method"),
            "spend_virtual_currency" => array("item_name",'virtual_currency_name','value'),
            "tutorial_begin" => array(),
            "tutorial_complete" => array(),
            "conversion" => array(),
        ),
        "Retail/Ecommerce"  => array(
            'add_payment_info'  => array('coupon','currency','items','payment_type','value'),
            'add_shipping_info' => array('coupon','currency','items','shipping_tier','value'),
            'add_to_cart'  => array('currency', 'items', 'value', 'google_business_vertical'),
            'add_to_wishlist'  => array('currency', 'items', 'value'),
            'begin_checkout'  => array('coupon','currency', 'items', 'value'),
            'generate_lead'  => array('value', 'currency', 'google_business_vertical'),
            'purchase'  => array('affiliation', 'coupon', 'currency', 'items', 'transaction_id', 'shipping', 'tax', 'value', 'google_business_vertical'),
            'refund'  => array('affiliation', 'coupon', 'currency', 'items', 'transaction_id', 'shipping', 'tax', 'value'),
            'remove_from_cart'  => array('currency', 'items', 'value'),
            'select_item'  => array('items', 'item_list_name', 'item_list_id'),
            'select_promotion'  => array('items', 'promotion_id', 'promotion_name', 'creative_name', 'creative_slot', 'location_id'),
            'view_cart'  => array('currency', 'items', 'value'),
            'view_item'  => array('currency', 'items', 'value', 'google_business_vertical'),
            'view_item_list'  => array('items', 'item_list_name', 'item_list_id', 'google_business_vertical'),
            'view_promotion'  => array('items', 'promotion_id', 'promotion_name', 'creative_name', 'creative_slot', 'location_id')
        ),
        "Jobs, Education, Local Deals, Real Estate"  => array(
            'add_payment_info'  =>  array("coupon", 'currency', 'items', 'payment_type', 'value'),
            'add_shipping_info'  =>  array('coupon', 'currency', 'items', 'shipping_tier', 'value'),
            'add_to_cart'  =>  array('currency', 'items', 'value', 'google_business_vertical'),
            'add_to_wishlist'  =>  array('currency', 'items', 'value'),
            'begin_checkout'  =>  array('coupon','currency', 'items', 'value'),
            'purchase'  =>  array('affiliation', 'coupon', 'currency', 'items', 'transaction_id', 'shipping', 'tax', 'value', 'google_business_vertical'),
            'refund'  =>  array('affiliation', 'coupon', 'currency', 'items', 'transaction_id', 'shipping', 'tax', 'value'),
            'remove_from_cart'  =>  array('currency', 'items', 'value'),
            'select_item'  =>  array('items', 'item_list_name', 'item_list_id'),
            'select_promotion'  =>  array('items', 'promotion_id', 'promotion_name', 'creative_name', 'creative_slot', 'location_id'),
            'view_cart'  =>  array('currency', 'items', 'value'),
            'view_item'  =>  array('currency', 'items', 'value', 'google_business_vertical'),
            'view_item_list'  =>  array('items', 'item_list_name', 'item_list_id', 'google_business_vertical'),
            'view_promotion'  =>  array('items', 'promotion_id', 'promotion_name', 'creative_name', 'creative_slot', 'location_id')
        ),
        "Travel (Hotel/Air)"  => array(
            'add_payment_info'  =>  array("coupon", 'currency', 'items', 'payment_type', 'value'),
            'add_shipping_info'  =>  array('coupon', 'currency', 'items', 'shipping_tier', 'value'),
            'add_to_cart'  =>  array('currency', 'items', 'value', 'google_business_vertical'),
            'add_to_wishlist'  =>  array('currency', 'items', 'value'),
            'begin_checkout'  =>  array('coupon','currency', 'items', 'value'),
            'generate_lead' => array('value', 'currency', 'google_business_vertical'),
            'purchase' => array('affiliation', 'coupon', 'currency', 'items', 'transaction_id', 'shipping', 'tax', 'value', 'google_business_vertical'),
            'refund' => array('affiliation', 'coupon', 'currency', 'items', 'transaction_id', 'shipping', 'tax', 'value'),
            'remove_from_cart'  =>  array('currency', 'items', 'value'),
            'select_item'  =>  array('items', 'item_list_name', 'item_list_id'),
            'select_promotion'  =>  array('items', 'promotion_id', 'promotion_name', 'creative_name', 'creative_slot', 'location_id'),
            'view_cart'  =>  array('currency', 'items', 'value'),
            'view_item'  =>  array('currency', 'items', 'value', 'google_business_vertical'),
            'view_item_list'  =>  array('items', 'item_list_name', 'item_list_id', 'google_business_vertical'),
            'view_promotion'  =>  array('items', 'promotion_id', 'promotion_name', 'creative_name', 'creative_slot', 'location_id')
        ),
        "Games" => array(
            'earn_virtual_currency'  => array('virtual_currency_name', 'value'),
            'join_group'  => array('group_id'),
            'level_end'  => array('level_name', 'success'),
            'level_start'  => array('level_name'),
            'level_up'  => array('character', 'level'),
            'post_score'  => array('level', 'character', 'score'),
            'select_content'  => array('content_type', 'item_id'),
            'spend_virtual_currency'  => array('item_name', 'virtual_currency_name', 'value'),
            'tutorial_begin'  => array(),
            'tutorial_complete'  => array(),
            'unlock_achievement'  => array('achievement_id'),
        )
    );

	private $data = array(
		'delay'        => null,
		'trigger_type' => 'page_visit',
		'triggers'     => array(),
		'url_filters'  => array(),
        'enable_time_window' => false,
        'time_window' => 24,

        'forms' => null,
        'disabled_form_action' => false,
		
		'facebook_enabled'           => false,
		'facebook_event_type'        => 'ViewContent',
		'facebook_custom_event_type' => null,
		'facebook_params_enabled'    => false,
		'facebook_params'            => array(),
		'facebook_custom_params'     => array(),
		'facebook_pixel_id'          => array('all'),


        'tiktok_enabled'           => false,
        'tiktok_event_type'        => 'Search',
        'tiktok_custom_event_type' => null,
        'tiktok_params_enabled'    => false,
        'tiktok_params'            => array(),
        'tiktok_custom_params'     => array(),
        'tiktok_pixel_id'          => 'all',

        'bing_pixel_id'              => 'all',
		'pinterest_enabled'           => false,
		'pinterest_event_type'        => 'ViewContent',
		'pinterest_custom_event_type' => null,
		'pinterest_params_enabled'    => false,
		'pinterest_custom_params'     => array(),
		
		'ga_enabled'             => false,
        'ga_pixel_id'            => array('all'),
		'ga_event_action'        => '_custom',
		'ga_custom_event_action' => null,
		//ver 4
        'ga_params'             => array(),
        'ga_custom_params'      => array(),
        'ga_custom_params_enabled'    => false,
        'ga_conversion_label'    => null,

        'ga_ads_enabled'             => false,
        'ga_ads_pixel_id'            => array('all'),
        'ga_ads_event_action'        => '_custom',
        'ga_ads_custom_event_action' => null,
        //ver 4
        'ga_ads_params'             => array(),
        'ga_ads_custom_params'      => array(),
        'ga_ads_custom_params_enabled'    => false,
        'ga_ads_conversion_label'    => null,

		
		'google_ads_enabled'             => false,
		'google_ads_conversion_id'       => array('all'),
		'google_ads_conversion_label'    => null,
		'google_ads_event_action'        => 'conversion',
		'google_ads_custom_event_action' => null,
		'google_ads_event_category'      => null,
		'google_ads_event_label'         => null,
		'google_ads_event_value'         => null,
		'google_ads_custom_params'       => array(),

        'bing_enabled' => false,
        'bing_event_action' => null,
        'bing_event_category' => null,
        'bing_event_label' => null,
        'bing_event_value' => null,
	);

	public function __construct( $post_id = null ) {
		$this->initialize( $post_id );
	}

	function getAllData() {
        return $this->data;
    }

	public function __get( $key ) {

		if ( $key == 'post_id' ) {
			return $this->post_id;
		}

		if ( $key == 'title' ) {
			return $this->title;
		}

		if ( $key == 'enabled' ) {
			return $this->enabled;
		}

		if ( isset( $this->data[ $key ] ) ) {
			return $this->data[ $key ];
		} else {
			return null;
		}

	}

	private function initialize( $post_id ) {

		if ( $post_id ) {

			$this->post_id = $post_id;
			$this->title   = get_the_title( $post_id );
			
			$data = get_post_meta( $post_id, '_ptp_event_data', true );

            // add loaded data to default or use default
			$this->data = is_array( $data ) ? $data+$this->data : $this->data;
//            if(empty($this->data['ga_pixel_id'])) {
//                $all = GA()->getAllPixels();
//                if(count($all) > 0) {
//                    $this->data['ga_pixel_id'] = $all[0];
//                } else {
//                    $this->data['ga_pixel_id'] = '';
//                    $this->data['ga_enabled'] = false;
//                    $this->clearGa();
//                }
//            }

			$state = get_post_meta( $post_id, '_pys_event_state', true );
			$this->enabled = $state == 'active' ? true : false;

        }

	}

	public function setData($newData) {

	    //set title
        wp_update_post( array(
            'ID'         => $this->post_id,
            'post_title' => $newData['title']
        ) );

        // set state
        $state =  $newData['enabled'] ? 'active' : 'paused';
        $this->enabled = $newData['enabled'];
        update_post_meta( $this->post_id, '_pys_event_state', $state );


        // set other
        $this->data = $newData;

        //save
        update_post_meta( $this->post_id, '_ptp_event_data', $this->data );
    }

	public function update( $args = null ) {

		if ( ! is_array( $args ) ) {
			$args = $this->data;
		}


		/**
		 * GENERAL
		 */

		// title
		wp_update_post( array(
			'ID'         => $this->post_id,
			'post_title' => empty( $args['title'] ) ? $this->title : sanitize_text_field( $args['title'] )
		) );

		// state
		$state = isset( $args['enabled'] ) && $args['enabled'] ? 'active' : 'paused';
		$this->enabled = $state == 'active' ? true : false;
		update_post_meta( $this->post_id, '_pys_event_state', $state );

		$trigger_types = array( 'page_visit', 'url_click', 'css_click', 'css_mouseover', 'scroll_pos','post_type' );
        $eventsFormFactory = apply_filters("pys_form_event_factory",[]);
        foreach ($eventsFormFactory as $activeFormPlugin) :
            $trigger_types[] = $activeFormPlugin->getSlug();
        endforeach;
		// trigger type
		$this->data['trigger_type'] = isset( $args['trigger_type'] ) && in_array( $args['trigger_type'], $trigger_types )
			? $args['trigger_type']
			: 'page_visit';

		// delay
		$this->data['delay'] = ($this->trigger_type == 'page_visit' || $this->trigger_type == 'post_type') && isset( $args['delay'] ) && $args['delay']
			? (int) $args['delay']
			: null;
        // post_type_value
        $this->data['post_type_value'] = $this->trigger_type == 'post_type' && isset( $args['post_type_value'] ) && $args['post_type_value']
            ?  $args['post_type_value']
            : null;

        $this->data['enable_time_window'] = isset( $args['enable_time_window']) ? (bool)$args['enable_time_window'] : false;
        $this->data['time_window'] = isset( $args['time_window']) ? (int)$args['time_window'] : 24;


		/**
		 * TRIGGERS
		 */

		// reset old triggers
		$this->data['triggers'] = array();

		// page visit triggers
		if ( $this->trigger_type == 'page_visit' && isset( $args['page_visit_triggers'] )
		     && is_array( $args['page_visit_triggers'] ) ) {

			foreach ( $args['page_visit_triggers'] as $trigger ) {

				if ( ! empty( $trigger['value'] ) ) {

					$this->data['triggers'][] = array(
						'rule'  => $trigger['rule'],
						'value' => $trigger['value'],
					);

				}

			}

		}

		// url click triggers
		if ( $this->trigger_type == 'url_click' && isset( $args['url_click_triggers'] )
		     && is_array( $args['url_click_triggers'] ) ) {

			foreach ( $args['url_click_triggers'] as $trigger ) {

				if ( ! empty( $trigger['value'] ) ) {
					
					$this->data['triggers'][] = array(
                        'rule'  => $trigger['rule'],
						'value' => $trigger['value'],
					);

				}

			}

		}

		// css click triggers
		if ( $this->trigger_type == 'css_click' && isset( $args['css_click_triggers'] )
		     && is_array( $args['css_click_triggers'] ) ) {

			foreach ( $args['css_click_triggers'] as $trigger ) {

				if ( ! empty( $trigger['value'] ) ) {

					$this->data['triggers'][] = array(
						'rule'  => null,
						'value' => sanitize_text_field( $trigger['value'] ),
					);

				}

			}

		}

		// css mouseover triggers
		if ( $this->trigger_type == 'css_mouseover' && isset( $args['css_mouseover_triggers'] )
		     && is_array( $args['css_mouseover_triggers'] ) ) {

			foreach ( $args['css_mouseover_triggers'] as $trigger ) {

				if ( ! empty( $trigger['value'] ) ) {

					$this->data['triggers'][] = array(
						'rule'  => null,
						'value' => sanitize_text_field( $trigger['value'] ),
					);

				}

			}

		}

		// scroll pos triggers
		if ( $this->trigger_type == 'scroll_pos' && isset( $args['scroll_pos_triggers'] )
		     && is_array( $args['scroll_pos_triggers'] ) ) {

			foreach ( $args['scroll_pos_triggers'] as $trigger ) {

				if ( ! empty( $trigger['value'] ) ) {
					
					$this->data['triggers'][] = array(
						'rule'  => null,
						'value' => (int) $trigger['value'],
					);

				}

			}

		}

		// reset old url filters
		$this->data['url_filters'] = array();

		if ( in_array( $this->trigger_type, array( 'url_click', 'css_click', 'css_mouseover', 'scroll_pos' ) ) &&
		     isset( $args['url_filter_triggers'] ) && is_array( $args['url_filter_triggers'] ) ) {

			foreach ( $args['url_filter_triggers'] as $trigger ) {

				if ( ! empty( $trigger['value'] ) ) {
					
					$this->data['url_filters'][] = array(
						'rule'  => null,
						'value' => $trigger['value'],
					);

				}

			}

		}

        /**
         * TIKTOK
         */
        $this->updateTikTok($args);
		/**
		 * FACEBOOK
		 */
        if($this->isFormTriggerType() && isset( $args[$this->trigger_type]['forms'] ))
        {
            $this->data['forms'] = array();
            foreach ( $args[$this->trigger_type]['forms'] as $form ) {

                if ( ! empty( $form ) ) {

                    $this->data['forms'][] = $form;

                }
            }
        }
        if($this->isFormTriggerType() && isset( $args[$this->trigger_type]['disabled_form_action'] )) {
            $this->data['disabled_form_action'] = $this->disabled_form_action = $args[$this->trigger_type]['disabled_form_action'] ? true : false;
        }
		$facebook_event_types = array(
			'ViewContent',
			'AddToCart',
			'AddToWishlist',
			'InitiateCheckout',
			'AddPaymentInfo',
			'Purchase',
			'Lead',
			'CompleteRegistration',
			
			'Subscribe',
			'CustomizeProduct',
			'FindLocation',
			'StartTrial',
			'SubmitApplication',
			'Schedule',
			'Contact',
			'Donate',
			
			'CustomEvent'
		);

		// enabled
		$this->data['facebook_enabled'] = isset( $args['facebook_enabled'] ) && $args['facebook_enabled'] ? true : false;
        $allFBpixels = Facebook()->getAllPixels(false);
        if(!empty( $args['facebook_pixel_id'] )) {
            $this->data['facebook_pixel_id'] = array_map(function($pixelId) use ($allFBpixels) {
                if (in_array( $pixelId,$allFBpixels) || $pixelId == 'all') {
                    return $pixelId;
                }
            }, $args['facebook_pixel_id']);
        } elseif (count($allFBpixels) > 0) {
            $this->data['facebook_pixel_id'] = $allFBpixels[0];
        } else {
            $this->data['facebook_pixel_id'] = [];
        }

		// event type
		$this->data['facebook_event_type'] = isset( $args['facebook_event_type'] ) && in_array( $args['facebook_event_type'], $facebook_event_types )
			? sanitize_text_field( $args['facebook_event_type'] )
			: 'ViewContent';

		// custom event type
		$this->data['facebook_custom_event_type'] = $this->facebook_event_type == 'CustomEvent' && ! empty( $args['facebook_custom_event_type'] )
			? sanitizeKey( $args['facebook_custom_event_type'] )
			: null;

		// params enabled
		$this->data['facebook_params_enabled'] = isset( $args['facebook_params_enabled'] ) && $args['facebook_params_enabled'] ? true : false;

		// params
		if ( $this->facebook_params_enabled && isset( $args['facebook_params'] ) && $this->facebook_event_type !== 'CustomEvent' ) {

			$this->data['facebook_params'] = array(
				'value'            => ! empty( $args['facebook_params']['value'] ) ? sanitize_text_field( $args['facebook_params']['value'] ) : null,
				'currency'         => ! empty( $args['facebook_params']['currency'] ) ? sanitize_text_field( $args['facebook_params']['currency'] ) : null,
				'content_name'     => ! empty( $args['facebook_params']['content_name'] ) ? sanitize_text_field( $args['facebook_params']['content_name'] ) : null,
				'content_ids'      => ! empty( $args['facebook_params']['content_ids'] ) ? sanitize_text_field( $args['facebook_params']['content_ids'] ) : null,
				'content_type'     => ! empty( $args['facebook_params']['content_type'] ) ? sanitize_text_field( $args['facebook_params']['content_type'] ) : null,
				'content_category' => ! empty( $args['facebook_params']['content_category'] ) ? sanitize_text_field( $args['facebook_params']['content_category'] ) : null,
				'num_items'        => ! empty( $args['facebook_params']['num_items'] ) ? (int) $args['facebook_params']['num_items'] : null,
				'order_id'         => ! empty( $args['facebook_params']['order_id'] ) ? sanitize_text_field( $args['facebook_params']['order_id'] ) : null,
				'search_string'    => ! empty( $args['facebook_params']['search_string'] ) ? sanitize_text_field( $args['facebook_params']['search_string'] ) : null,
				'status'           => ! empty( $args['facebook_params']['status'] ) ? sanitize_text_field( $args['facebook_params']['status'] ) : null,
				'predicted_ltv'    => ! empty( $args['facebook_params']['predicted_ltv'] ) ? sanitize_text_field( $args['facebook_params']['predicted_ltv'] ) : null,
			);

			// custom currency
			if ( $this->data['facebook_params']['currency'] == 'custom' && ! empty( $args['facebook_params']['custom_currency'] )) {
				$this->data['facebook_params']['custom_currency'] = sanitize_text_field( $args['facebook_params']['custom_currency'] );
			} else {
				$this->data['facebook_params']['custom_currency'] = null;
			}

		} else {
			
			$this->data['facebook_params'] = array(
				'value'            => null,
				'currency'         => null,
				'custom_currency'  => null,
				'content_name'     => null,
				'content_ids'      => null,
				'content_type'     => null,
				'content_category' => null,
				'num_items'        => null,
				'order_id'         => null,
				'search_string'    => null,
				'status'           => null,
				'predicted_ltv'    => null,
			);

		}

		// reset old custom params
		$this->data['facebook_custom_params'] = array();

		// custom params
		if ( $this->facebook_params_enabled && isset( $args['facebook_custom_params'] ) ) {

			foreach ( $args['facebook_custom_params'] as $custom_param ) {

				if ( ! empty( $custom_param['name'] ) && ! empty( $custom_param['value'] ) ) {

					$this->data['facebook_custom_params'][] = array(
						'name'  => sanitize_text_field( $custom_param['name'] ),
						'value' => sanitize_text_field( $custom_param['value'] ),
					);

				}

			}

		}

		/**
		 * PINTEREST
		 */
		
		$pinterest_event_types = array(
			'pagevisit',
			'viewcategory',
			'search',
			'addtocart',
			'checkout',
			'watchvideo',
			'signup',
			'lead',
			'custom',
			'CustomEvent',
		);
		
		// enabled
		$this->data['pinterest_enabled'] = isset( $args['pinterest_enabled'] ) && $args['pinterest_enabled'] ? true
			: false;
		
		// event type
		$this->data['pinterest_event_type'] = isset( $args['pinterest_event_type'] ) && in_array( $args['pinterest_event_type'],
			$pinterest_event_types )
			? sanitize_text_field( $args['pinterest_event_type'] )
			: 'pagevisit';
		
		// custom event type
		$this->data['pinterest_custom_event_type'] = $this->pinterest_event_type == 'CustomEvent' && ! empty( $args['pinterest_custom_event_type'] )
			? sanitizeKey( $args['pinterest_custom_event_type'] )
			: null;
		
		// params enabled
		$this->data['pinterest_params_enabled'] = isset( $args['pinterest_params_enabled'] ) && $args['pinterest_params_enabled']
			? true : false;
		
		// reset old custom params
		$this->data['pinterest_custom_params'] = array();
		
		// custom params
		if ( $this->pinterest_params_enabled && isset( $args['pinterest_custom_params'] ) ) {
			
			foreach ( $args['pinterest_custom_params'] as $custom_param ) {
				
				if ( ! empty( $custom_param['name'] ) && ! empty( $custom_param['value'] ) ) {
					
					$this->data['pinterest_custom_params'][] = array(
						'name'  => sanitize_text_field( $custom_param['name'] ),
						'value' => sanitize_text_field( $custom_param['value'] ),
					);
					
				}
				
			}
			
		}

        $this->updateUnifyGA($args);



        /**
         * BING
         */

        $this->data['bing_enabled'] = isset($args['bing_enabled']) && $args['bing_enabled'] ? true : false;
        $this->data['bing_event_action'] = !empty($args['bing_event_action']) ? sanitize_text_field($args['bing_event_action']) : null;
        $this->data['bing_event_category'] = !empty($args['bing_event_category']) ? sanitize_text_field($args['bing_event_category']) : null;
        $this->data['bing_event_label'] = !empty($args['bing_event_label']) ? sanitize_text_field($args['bing_event_label']) : null;
        $this->data['bing_event_value'] = !empty($args['bing_event_value']) ? sanitize_text_field($args['bing_event_value']) : null;
        $this->data['bing_pixel_id'] = !empty( $args['bing_pixel_id'] ) && in_array( $args['bing_pixel_id'],
            Bing()->getAllPixels() ) ? $args['bing_pixel_id'] : 'all';


        update_post_meta( $this->post_id, '_ptp_event_data', $this->data );

	}

	public function enable() {

		$this->enabled = true;
		update_post_meta( $this->post_id, '_pys_event_state', 'active' );

	}

	public function disable() {

		$this->enabled = false;
		update_post_meta( $this->post_id, '_pys_event_state', 'paused' );

	}

	/**
	 * @return int
	 */
	public function getPostId() {
	    return $this->post_id;
    }

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	public function isEnabled() {
		return $this->enabled;
	}

	public function getTriggerType() {
		return $this->trigger_type;
	}

    public function isFormTriggerType() {
        $form_trigger_type = array();
        $eventsFormFactory = apply_filters("pys_form_event_factory",[]);
        foreach ($eventsFormFactory as $activeFormPlugin) :
            $form_trigger_type[] = $activeFormPlugin->getSlug();
        endforeach;

        if(in_array($this->trigger_type, $form_trigger_type))
        {
            return true;
        }
        return false;
    }

	public function getDelay() {
		return $this->delay;
	}

    public function getPostTypeValue() {
        return $this->post_type_value;
    }

	public function hasTimeWindow() {
	    return $this->enable_time_window;
    }

    public function getTimeWindow() {
        return $this->time_window;
    }

	/**
	 * @return array
	 */
	public function getPageVisitTriggers() {
		return $this->trigger_type == 'page_visit' ? $this->triggers : array();
	}

	/**
	 * @return array
	 */
	public function getURLClickTriggers() {
		return $this->trigger_type == 'url_click' ? $this->triggers : array();
	}

	/**
	 * @return array
	 */
	public function getCSSClickTriggers() {
		return $this->trigger_type == 'css_click' ? $this->triggers : array();
	}

	/**
	 * @return array
	 */
	public function getCSSMouseOverTriggers() {
		return $this->trigger_type == 'css_mouseover' ? $this->triggers : array();
	}

	/**
	 * @return array
	 */
	public function getScrollPosTriggers() {
		return $this->trigger_type == 'scroll_pos' ? $this->triggers : array();
	}

    public function getFormEventTriggerForms() {
        return $this->forms;
    }
	/**
	 * @return array
	 */
	public function getURLFilters() {
		return in_array( $this->trigger_type, array( 'url_click', 'css_click', 'css_mouseover', 'scroll_pos' ) )
			? $this->url_filters
			: array();
	}
	
	public function isFacebookEnabled() {
		return (bool) $this->facebook_enabled;
	}
	
	public function getFacebookEventType() {
		return $this->facebook_event_type == 'CustomEvent' ? $this->facebook_custom_event_type : $this->facebook_event_type;
	}
	
	public function isFacebookParamsEnabled() {
		return (bool) $this->facebook_params_enabled;
	}
	
	public function getFacebookParam( $key ) {
		return isset( $this->facebook_params[ $key ] ) ? $this->facebook_params[ $key ] : null;
	}
	
	public function getFacebookParams() {
		return $this->facebook_params_enabled ? $this->facebook_params : array();
	}
	
	public function getFacebookCustomParams() {
		return $this->facebook_params_enabled ? $this->facebook_custom_params : array();
	}
	
	public function isPinterestEnabled() {
		return (bool) $this->pinterest_enabled;
	}
	
	public function getPinterestEventType() {
		return $this->pinterest_event_type == 'CustomEvent'
			? $this->pinterest_custom_event_type
			: $this->pinterest_event_type;
	}
	
	public function isPinterestParamsEnabled() {
		return (bool) $this->pinterest_params_enabled;
	}
	
	public function getPinterestCustomParams() {
		return $this->pinterest_params_enabled ? $this->pinterest_custom_params : array();
	}

	public function isGoogleAnalyticsEnabled() {
		return (bool) $this->ga_enabled;
	}

    public function isGoogleAnalyticsPresent(){
        $allValues = array_merge(GA()->getAllPixels(), Ads()->getAllPixels());
        $selectedValues = $this->ga_ads_pixel_id;

        $hasAWElement = !empty($selectedValues) && (
		        ( in_array( 'all', $selectedValues ) &&
		          (bool) array_filter( $allValues, function ( $value ) {
			          return strpos( $value, 'G' ) === 0;
		          } ) ) ||
		        (bool) array_filter($selectedValues, function($value) {
                    return strpos($value, 'G') === 0;
                })
            );

        return $hasAWElement;
    }

	public function getGAMergedCustomParams() {
        if(is_array($this->ga_ads_custom_params)) {
            return $this->ga_ads_custom_params;
        }
        return [];
    }

    public function getGACustomParams() {
        if(is_array($this->ga_custom_params)) {
            return $this->ga_custom_params;
        }
        return [];
    }


    public function getGaParams() {
        if($this->isGaV4()) {
            if(is_array($this->ga_params)) {
                return $this->ga_params;
            } else {
                return [];
            }

        }

        $list = array();
        foreach ($this->GAEvents as $group) {
            foreach ($group as $name => $fields) {
                if($name == $this->data['ga_event_action']) {
                    foreach ($fields as $field) {
                        $list[$field] = "";
                    }
                }
            }
        }

        return $list;
    }

    public function getMergedGaParams() {
        if(is_array($this->ga_ads_params)) {
            return $this->ga_ads_params;
        } else {
            return [];
        }
    }


	public function getGoogleAnalyticsAction() {
		return $this->ga_event_action == '_custom' || $this->ga_event_action ==  'CustomEvent' ? $this->ga_custom_event_action : $this->ga_event_action;
	}

    public function getMergedAction(){
        return $this->ga_ads_event_action == '_custom' || $this->ga_ads_event_action ==  'CustomEvent' ? $this->ga_ads_custom_event_action : $this->ga_ads_event_action;
    }
	
	public function isGoogleAdsEnabled() {
        return (bool) $this->google_ads_enabled;
	}
    public function isGoogleAdsPresent(){
        $allValues = array_merge(GA()->getAllPixels(), Ads()->getAllPixels());
        $selectedValues = $this->ga_ads_pixel_id;

        $hasAWElement = !empty($selectedValues) && (
		        ( in_array( 'all', $selectedValues ) &&
		          (bool) array_filter( $allValues, function ( $value ) {
			          return strpos( $value, 'AW' ) === 0;
		          } ) ) ||
		        (bool) array_filter($selectedValues, function($value) {
                    return strpos($value, 'AW') === 0;
                })
            );

        return $hasAWElement;
    }
	public function getGoogleAdsAction() {
		return $this->google_ads_event_action == '_custom' ? $this->google_ads_custom_event_action : $this->google_ads_event_action;
	}
    public function getGoogleAdsEventCategory() {
        return $this->google_ads_event_category;
    }
    public function getGoogleAdsEventLabel() {
        return $this->google_ads_event_label;
    }
	public function getGoogleAdsCustomParams() {
		return  (array)$this->google_ads_custom_params;
	}

    public function isUnifyAnalyticsEnabled(){
        return (bool) $this->ga_ads_enabled;
    }

    public function isBingEnabled() {
        return (bool) $this->bing_enabled;
    }

    public function isGaV4() {
        return true;
        /*$tag = $this->data['ga_pixel_id'];
        if (is_array($tag)) {
            foreach ($tag as $t) {
                if (!is_string($t)) {
                    return false;
                }
                if (strpos($t, 'G') === 0 || $t == 'all') {
                    return true;
                }
            }
            return false;
        } else {
            return strpos($tag, 'G') === 0 || $tag == 'all';
        }*/
    }

    private function clearGa() {
        $this->data['ga_params'] = array();
        $this->data['ga_custom_params'] = array();
        $this->data['ga_event_action'] = 'CustomEvent';
        $this->data['ga_custom_event_action']=null;
    }

    function migrateUnifyGA() {
        $all = array_merge(GA()->getAllPixels(false), Ads()->getAllPixels(false));
        $this->data['ga_ads_enabled'] = $this->isGoogleAnalyticsEnabled() || $this->isGoogleAdsEnabled() ? true : false;
        if($this->isGoogleAnalyticsEnabled() && $this->isGoogleAdsEnabled()){
            $pixel_ids = array_unique(array_merge((array)$this->data['ga_pixel_id'], (array)$this->data['google_ads_conversion_id']));
        }
        elseif ($this->isGoogleAnalyticsEnabled()){
            $pixel_ids = (array)$this->data['ga_pixel_id'];
        }
        elseif ($this->isGoogleAdsEnabled()){
            $pixel_ids = (array)$this->data['google_ads_conversion_id'];
        }
        else{
            return;
        }

        $this->data['ga_ads_pixel_id']  = array_map(function($pixelId) use ($all) {
            if (in_array($pixelId, $all) || $pixelId == 'all') {
                return $pixelId;
            } else {
                return '';
            }
        }, $pixel_ids);

        $this->data['ga_ads_pixel_id'] = array_filter($this->data['ga_ads_pixel_id']);
        if($this->isGoogleAdsEnabled() && $this->data['google_ads_conversion_label']) {
            $this->data['ga_ads_conversion_label'] = $this->data['google_ads_conversion_label'];
        }

        if($this->isGoogleAnalyticsEnabled()){
            $this->data['ga_ads_event_action'] = $this->ga_event_action;
            $this->data['ga_ads_custom_event_action'] = $this->ga_event_action == '_custom' || $this->ga_event_action ==  'CustomEvent' ? $this->ga_custom_event_action : '';
            $this->data['ga_ads_params'] = $this->getGaParams();
            $this->data['ga_ads_custom_params'] = $this->getGACustomParams();
        }elseif ($this->isGoogleAdsEnabled()){
            $this->data['ga_ads_event_action'] = $this->google_ads_event_action;
            $this->data['ga_ads_custom_event_action'] = $this->google_ads_event_action == '_custom' || $this->google_ads_event_action ==  'CustomEvent' ? $this->google_ads_custom_event_action : '';
            $this->data['ga_ads_params'] = array();
            $this->data['ga_ads_custom_params'] = $this->getGoogleAdsCustomParams();
        }
        if($this->isGoogleAdsEnabled()){
            $this->data['ga_ads_custom_params'][] = array('name'=> 'event_category', 'value' => $this->getGoogleAdsEventCategory());
            $this->data['ga_ads_custom_params'][] = array('name'=> 'event_label', 'value' => $this->getGoogleAdsEventLabel());
        }
        $outputArray = [];

        foreach ($this->data['ga_ads_custom_params'] as $item) {
            $key = $item["name"];
            if (!isset($outputArray[$key])) {
                $outputArray[$key] = $item;
            }
        }
        $this->data['ga_ads_custom_params'] = array_values($outputArray);
        update_post_meta( $this->post_id, '_ptp_event_data', $this->data );
    }

    private function updateUnifyGA($args){
        $all = array_merge(GA()->getAllPixels(false), Ads()->getAllPixels(false));;

        if(!empty( $args['ga_ads_pixel_id'] )) {
            $this->data['ga_ads_pixel_id'] = array_map(function($pixelId) use ($all) {
                if (in_array( $pixelId,$all) || $pixelId == 'all') {
                    return $pixelId;
                }
            }, $args['ga_ads_pixel_id']);
        } elseif (count($all) > 0) {
            $this->data['ga_ads_pixel_id'] = $all[0];
        } else {
            $this->data['ga_ads_pixel_id'] = [];
        }

        $this->data['ga_ads_enabled'] = isset( $args['ga_ads_enabled']  )
            && $args['ga_ads_enabled'];

        $this->data['ga_ads_event_action'] = isset( $args['ga_ads_event_action'] )
            ? sanitize_text_field( $args['ga_ads_event_action'] )
            : 'view_item';

        $this->data['ga_ads_params'] = array();

        foreach ($this->GAEvents as $group) {
            foreach ($group as $name => $fields) {
                if($name == $this->data['ga_ads_event_action']) {
                    foreach ($fields as $field) {
                        $this->data['ga_ads_params'][$field] = isset($args['ga_ads_params'][$field]) ? $args['ga_ads_params'][$field] : "";
                    }
                    break;
                }
            }
        }

        if ( isset( $args['ga_ads_params'] ) ) {
            foreach ($args['ga_ads_params'] as $key => $val) {
                $this->data['ga_ads_params'][$key] = sanitize_text_field( $val );
            }
        }

        // reset old custom params
        $this->data['ga_ads_custom_params'] = array();

        // custom params
        if ( isset( $args['ga_ads_custom_params'] ) ) {

            foreach ( $args['ga_ads_custom_params'] as $custom_param ) {

                if ( ! empty( $custom_param['name'] ) && ! empty( $custom_param['value'] ) ) {

                    $this->data['ga_ads_custom_params'][] = array(
                        'name'  => sanitize_text_field( $custom_param['name'] ),
                        'value' => sanitize_text_field( $custom_param['value'] ),
                    );

                }

            }

        }
        $sanitizeGoogleAdsConversionLabel = function ($label) {
            return wp_kses_post( trim( stripslashes( $label ) ) );
        };
        $this->data['ga_ads_conversion_label'] = ! empty( $args['ga_ads_conversion_label'] )
            ? $sanitizeGoogleAdsConversionLabel( $args['ga_ads_conversion_label'] )
            : null;
    }

    private function updateTikTok($args) {
        $tiktok_event_types = ['CustomEvent','ViewContent','ClickButton','Search',
            'AddToWishlist','AddToCart','InitiateCheckout','AddPaymentInfo',
            'CompletePayment','PlaceAnOrder','Contact','Download','SubmitForm',
            'CompleteRegistration','Subscribe'];
        $standard_params = [
            'content_id',
            'content_type',
            'content_category',
            'content_name',
            'currency',
            'value',
            'quantity',
            'price',
            'query',
        ];
        // enabled
        $this->data['tiktok_enabled'] = isset( $args['tiktok_enabled'] ) && $args['tiktok_enabled'] ? true : false;

        //pixel id
        $this->data['tiktok_pixel_id'] = !empty( $args['tiktok_pixel_id'] )
        && in_array( $args['tiktok_pixel_id'], Tiktok()->getAllPixels() )
            ? $args['tiktok_pixel_id'] : 'all';

        // event type
        $this->data['tiktok_event_type'] = isset( $args['tiktok_event_type'] ) && in_array( $args['tiktok_event_type'], $tiktok_event_types )
            ? sanitize_text_field( $args['tiktok_event_type'] )
            : 'ViewContent';

        // custom event type
        $this->data['tiktok_custom_event_type'] = $this->tiktok_event_type == 'CustomEvent' && ! empty( $args['tiktok_custom_event_type'] )
            ? sanitizeKey( $args['tiktok_custom_event_type'] )
            : null;

        // params enabled
        $this->data['tiktok_params_enabled'] = isset( $args['tiktok_params_enabled'] ) && $args['tiktok_params_enabled'] ? true : false;

        // params
        if ( $this->tiktok_params_enabled && isset( $args['tiktok_params'] ) && $this->tiktok_event_type !== 'CustomEvent' ) {

            $params = [];
            foreach ($standard_params as $standard) {
                $params[$standard] = ! empty( $args['tiktok_params'][$standard] ) ? sanitize_text_field( $args['tiktok_params'][$standard] ) : null;
            }
        } else {
            // clear all
            $params = [];
            foreach ($standard_params as $standard) {
                $params[$standard] =  null;
            }
        }
        $this->data['tiktok_params'] = $params;

    }

    /**
     * @return bool
     */
    public function isTikTokEnabled() {
        return (bool) $this->tiktok_enabled;
    }

    public function getTikTokEventType() {
        return $this->tiktok_event_type == 'CustomEvent' ? $this->tiktok_custom_event_type : $this->tiktok_event_type;
    }
}