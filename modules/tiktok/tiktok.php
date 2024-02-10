<?php

namespace PixelYourSite;

use PixelYourSite;
use PixelYourSite\TikTok\Helpers;

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
require_once PYS_PATH . '/modules/tiktok/function_helpers.php';
require_once PYS_PATH . '/modules/tiktok/tiktok-logger.php';

class Tiktok extends Settings implements Pixel {
	private static $_instance;
	private        $configured;
	private        $logger;


	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	public function __construct() {
		parent::__construct( 'tiktok' );
		$this->locateOptions( PYS_PATH . '/modules/tiktok/options_fields.json', PYS_PATH . '/modules/tiktok/options_defaults.json' );
		add_action( 'pys_register_pixels', function ( $core ) {
			/** @var PYS $core */
			$core->registerPixel( $this );
		} );

		$this->logger = new TikTok_logger();
		add_action( 'init', array(
			$this,
			'init'
		), 9 );
	}

	public function enabled() {
		return $this->getOption( 'enabled' );
	}

	public function init() {
		$this->logger->init();
	}

	public function configured() {

		$license_status = PYS()->getOption( 'license_status' );
		$pixel_id = $this->getAllPixels();
		// $disabledPixel =  apply_filters( 'ptp_pixel_disabled', '', $this->getSlug() );

		$this->configured = $this->enabled() && !empty( $license_status ) // license was activated before
			&& count( $pixel_id ) > 0 && !empty( $pixel_id[ 0 ] );
		// && $disabledPixel != 'all';
		return $this->configured;
	}

	public function getPixelIDs() {

		if( isSuperPackActive()
			&& SuperPack()->getOption( 'enabled' )
			&& SuperPack()->getOption( 'additional_ids_enabled' ) )
		{
			if ( !$this->getOption( 'main_pixel_enabled' ) ) {
				return apply_filters( "pys_tiktok_ids", [] );
			}
		}
		$pixels = (array) $this->getOption( 'pixel_id' );

		if ( count( $pixels ) == 0 || empty( $pixels[ 0 ] ) ) {
			return apply_filters( "pys_tiktok_ids", [] );
		} else {
			$id = array_shift( $pixels );
			return apply_filters( "pys_tiktok_ids", array( $id ) ); // return first id only
		}
	}

	public function getAllPixels( $checkLang = true ) {
		return $this->getPixelIDs();
	}

	/**
	 * @param SingleEvent $event
	 * @return array
	 */
	public function getAllPixelsForEvent( $event ) {

		$pixels = array();
		$main_pixel = $this->getPixelIDs();

		if(isSuperPackActive('3.0.0')
			&& SuperPack()->getOption( 'enabled' )
			&& SuperPack()->getOption( 'additional_ids_enabled' ))
		{
			if ( !empty( $main_pixel ) ) {
				$main_pixel_options = $this->getOption( 'main_pixel' );
				if ( !empty( $main_pixel_options ) && isset( $main_pixel_options[ 0 ] ) ) {
					$main_pixel_options = $this->normalizeSPOptions( $main_pixel[ 0 ], $main_pixel_options[ 0 ] );
				} else {
					$main_pixel_options = $this->normalizeSPOptions( $main_pixel[ 0 ], '' );
				}
				$pixel_options = SuperPack\SPPixelId::fromArray( $main_pixel_options );
				if ( $pixel_options->isValidForEvent( $event ) && $pixel_options->isConditionalValidForEvent( $event ) ) {
					$pixels = array_merge( $pixels, $main_pixel );
				}
			}
		} else {
			$pixels = array_merge( $pixels, $main_pixel );
		}

		return $pixels;
	}

	/**
	 * @return array
	 */
	public function getPixelOptions() {
		$options = [
			'pixelIds'         => $this->getAllPixels(),
			'serverApiEnabled' => $this->isServerApiEnabled(),
		];

		if ( $this->getOption( 'advanced_matching_enabled' ) ) {
			$options[ 'advanced_matching' ] = $this->getAdvancedMatchingParams();
		}
		if ( isSuperPackActive( '3.3.1' ) && SuperPack()->getOption( 'enabled' ) && SuperPack()->getOption( 'enable_hide_this_tag_by_tags' ) ) {
			$options[ 'hide_pixels' ] = $this->getHideInfoPixels();
		}
		return $options;
	}

	public function updateOptions( $values = null ) {

		if ( isset( $_POST[ 'pys' ][ $this->getSlug() ][ 'test_api_event_code' ] ) ) {
			$api_event_code_expiration_at = array();
			foreach ( $_POST[ 'pys' ][ $this->getSlug() ][ 'test_api_event_code' ] as $key => $test_api ) {
				if ( !empty( $test_api ) && empty( $this->getOption( 'test_api_event_code_expiration_at' )[ $key ] ) ) {
					$api_event_code_expiration_at[] = time() + $this->convertTimeToSeconds();
				} elseif ( !empty( $this->getOption( 'test_api_event_code_expiration_at' )[ $key ] ) ) {
					$api_event_code_expiration_at[] = $this->getOption( 'test_api_event_code_expiration_at' )[ $key ];
				}

			}
			$_POST[ 'pys' ][ $this->getSlug() ][ 'test_api_event_code_expiration_at' ] = $api_event_code_expiration_at;
		}

		parent::updateOptions( $values );
	}

	/**
	 * Create pixel event and fill it
	 * @param SingleEvent $event
	 * @return array
	 */
	public function generateEvents( $event ) {
		$pixelEvents = [];
		if ( !$this->configured() ) {
			return [];
		}

		$pixelIds = $this->getAllPixelsForEvent( $event );

		if ( count( $pixelIds ) > 0 ) {
			$pixelEvent = clone $event;

			if ( $this->addParamsToEvent( $pixelEvent ) ) {
				$pixelEvent->addPayload( [ 'pixelIds' => $pixelIds ] );
				$pixelEvents[] = $pixelEvent;
			}

			if ( $event->getId() == "woo_purchase" ) { // dublicate event
				$pixelEvent = clone $event;
				$pixelEvent->setId( "woo_complete_payment" );
				if ( $this->addParamsToEvent( $pixelEvent ) ) {
					$pixelEvent->addPayload( [ 'pixelIds' => $pixelIds ] );
					$pixelEvents[] = $pixelEvent;
				}
			}
			if ( $event->getId() == "edd_purchase" ) { // dublicate event
				$pixelEvent = clone $event;
				$pixelEvent->setId( "edd_complete_payment" );
				if ( $this->addParamsToEvent( $pixelEvent ) ) {
					$pixelEvent->addPayload( [ 'pixelIds' => $pixelIds ] );
					$pixelEvents[] = $pixelEvent;
				}
			}

		}

		return $pixelEvents;
	}

	public function outputNoScriptEvents() {

	}

	/**
	 * @param SingleEvent $event
	 * @return false
	 */
	private function addParamsToEvent( &$event ) {
		$isActive = false;

		switch ( $event->getId() ) {

			//Automatic events
			case 'automatic_event_form' :

				$event->addPayload( [ "name" => "SubmitForm" ] );
				$isActive = $this->getOption( $event->getId() . '_enabled' );
				break;

			case 'automatic_event_signup' :
				$event->addPayload( [ "name" => "SignUp" ] );
				$isActive = $this->getOption( $event->getId() . '_enabled' );
				break;

			case 'automatic_event_download' :
				$event->addPayload( [ "name" => "Download" ] );
				$isActive = $this->getOption( $event->getId() . '_enabled' );
				break;

			case 'automatic_event_search' :
				$event->addPayload( [ "name" => "Search" ] );
				if ( !empty( $_GET[ 's' ] ) ) {
					$event->addParams( [ "query" => $_GET[ 's' ] ] );
				}

				$isActive = $this->getOption( $event->getId() . '_enabled' );
				break;

			case "automatic_event_outbound_link":
			case "automatic_event_internal_link":
				$isActive = $this->add_click_button_params( $event );
				break;

			case 'automatic_event_login' :
				$event->addPayload( [ "name" => "Login" ] );
				$isActive = $this->getOption( $event->getId() . '_enabled' );
				break;

			case 'automatic_event_scroll' :
			case 'automatic_event_tel_link' :
			case 'automatic_event_email_link' :
			case 'automatic_event_comment' :
			case 'automatic_event_adsense' :
			case 'automatic_event_time_on_page' :
				$isActive = $this->getOption( $event->getId() . '_enabled' );
				break;

			case 'automatic_event_video' :
				$isActive = $this->getOption( $event->getId() . '_enabled' );
				if ( $isActive ) {
					$event->addPayload( array( 'automatic_event_video_trigger' => $this->getOption( "automatic_event_video_trigger" ) ) );
				}
				break;

			//Woo
			case 'woo_add_to_cart_on_button_click':
				$isActive = $this->add_woo_add_to_cart_params( $event );
				break;

			case 'woo_view_content':
				$isActive = $this->add_woo_view_content_params( $event );
				break;

			case 'woo_initiate_checkout':
				$isActive = $this->add_woo_initiate_checkout_params( $event );
				break;

			case 'woo_purchase':
				$isActive = $this->add_woo_purchase_params( $event );
				break;

			case 'woo_complete_payment':
				$isActive = $this->add_woo_compete_payment_params( $event );
				break;

			case 'woo_frequent_shopper':
			case 'woo_vip_client':
			case 'woo_big_whale':
			case 'woo_FirstTimeBuyer':
			case 'woo_ReturningCustomer':
				$isActive = $this->getWooAdvancedMarketingEventParams( $event );
				break;

			case 'edd_view_content':
				$isActive = $this->add_edd_view_content_params( $event );
				break;

			case 'edd_add_to_cart_on_checkout_page':
				$isActive = $this->add_edd_add_to_cart_on_check_params( $event );
				break;

			case 'edd_initiate_checkout':
				$isActive = $this->add_edd_init_checkout_params( $event );
				break;

			case 'edd_purchase':
				$isActive = $this->add_edd_purchase_params( $event );
				break;

			case 'edd_complete_payment':
				$isActive = $this->add_edd_complete_payment_params( $event );
				break;

			case 'edd_add_to_cart_on_button_click':
				$isActive = $this->add_edd_add_to_cart_params( $event );
				break;

			case 'edd_frequent_shopper':
			case 'edd_vip_client':
			case 'edd_big_whale':
				$isActive = $this->setEddCartEventParams( $event );
				break;

			case 'custom_event':
				$isActive = $this->add_custom_event_params( $event );
				break;

			case 'wcf_add_to_cart_on_bump_click':
			case 'wcf_add_to_cart_on_next_step_click':
				$isActive = $this->add_wcf_add_to_cart_params( $event );
				break;

			case 'wcf_view_content':
				$isActive = $this->addwcf_view_content_params( $event );
				break;
		}

		if ( $isActive ) {
			if ( $this->isServerApiEnabled() ) {
				$event->payload[ 'event_id' ] = PixelYourSite\pys_generate_token();
			}
		}

		return $isActive;
	}

	public function getEventData( $eventType, $args = null ) {

		return false;
	}

	private function get_edd_add_to_cart_on_button_click_params( $download_id ) {
		global $post;

		// maybe extract download price id
		if ( strpos( $download_id, '_' ) !== false ) {
			list( $download_id, $price_index ) = explode( '_', $download_id );
		} else {
			$price_index = null;
		}

		$params = array(
			'content_type' => 'product',
		);

		// content_name, category_name
		$params[ 'content_name' ] = get_the_title( $download_id );
		$content_category = implode( ', ', getObjectTerms( 'download_category', $download_id ) );

		$contents = array(
			'content_name'     => get_the_title( $download_id ),
			'content_category' => $content_category,
			'content_id'       => (string) $download_id,
			'quantity'         => 1,
			'content_type'     => 'product',
		);

		// currency, value
		if ( PYS()->getOption( 'edd_add_to_cart_value_enabled' ) ) {

			if ( PYS()->getOption( 'edd_event_value' ) == 'custom' ) {
				$amount = getEddDownloadPrice( $download_id, $price_index );
			} else {
				$amount = getEddDownloadPriceToDisplay( $download_id, $price_index );
			}

			$params[ 'currency' ] = edd_get_currency();
			$params[ 'value' ] = $amount;
			$contents[ 'price' ] = $amount;
		}

		// contents
		$params[ 'contents' ][] = $contents;

		return $params;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	function add_page_view_params( &$event ) {
		global $post;

		$cpt = get_post_type();
		$params = array(
			'content_name' => $post->post_title,
			'content_id'   => $post->ID,
		);

		if ( isWooCommerceActive() && $cpt == 'product' ) {
			$params[ 'content_category' ] = implode( ', ', getObjectTerms( 'product_cat', $post->ID ) );
		} elseif ( isEddActive() && $cpt == 'download' ) {
			$params[ 'content_category' ] = implode( ', ', getObjectTerms( 'download_category', $post->ID ) );
		} elseif ( $post instanceof \WP_Post ) {
			$catIds = wp_get_object_terms( $post->ID, 'category', array( 'fields' => 'names' ) );
			$params[ 'content_category' ] = implode( ", ", $catIds );
		}

		$data = array(
			'name' => 'ViewContent',
		);
		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 * content_type, quantity, description, content_id, currency, value
	 */
	private function add_woo_add_to_cart_params( &$event ) {

		if ( !$this->getOption( 'woo_add_to_cart_enabled' ) ) {
			return false;
		}

		if ( isset( $event->args[ 'productId' ] ) ) {
			$quantity = $event->args[ 'quantity' ];
			$product = wc_get_product( $event->args[ 'productId' ] );

			if ( !$product ) {
				return false;
			}

			$product_id = Helpers\getTikTokWooVariableToSimpleProductId( $product );

			$params = PixelYourSite\TikTok\Helpers\getWooSingleAddToCartParams( $product_id, $quantity );
			$event->addParams( $params );
			$content_id = Helpers\getTikTokWooProductContentId( $product_id );
			$params = [
				'content_category' => implode( ', ', getObjectTerms( 'product_cat', $product_id ) ),
				'quantity'         => $quantity,
				'currency'         => get_woocommerce_currency(),
				'content_name'     => $product->get_name(),
				'content_id'       => $content_id,
				'content_type'     => 'product'
			];

			$customProductPrice = getWfcProductSalePrice( $product, $event->args );
			$isGrouped = $product->get_type() == "grouped";
			if ( $isGrouped ) {
				$product_ids = $product->get_children();
			} else {
				$product_ids[] = $product_id;
			}
			$price = 0;
			foreach ( $product_ids as $child_id ) {
				$childProduct = wc_get_product( $child_id );
				if ( $childProduct->get_type() == "variable" && $isGrouped ) {
					continue;
				}
				$price += getWooProductPriceToDisplay( $child_id, $quantity, $customProductPrice );
			}

			$params[ 'value' ] = $price;
			$event->addParams( $params );
		}

		$data = [
			'name' => 'AddToCart'
		];

		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 * content_type, quantity, description, content_id, currency, value
	 */
	private function add_woo_view_content_params( &$event ) {
		if ( !$this->getOption( 'woo_view_content_enabled' ) ) {
			return false;
		}
		$product = wc_get_product( $event->args[ 'id' ] );
		$quantity = $event->args[ 'quantity' ];
		$customProductPrice = getWfcProductSalePrice( $product, $event->args );

		if ( !$product ) return false;


		if ( $this->getOption( 'woo_variable_data_select_product' ) && !$this->getOption( 'woo_variable_as_simple' ) ) {
			$product_id = getVariableIdByAttributes( $product );
		} else {
			$product_id = Helpers\getTikTokWooVariableToSimpleProductId( $product );
		}
		$content_id = Helpers\getTikTokWooProductContentId( $product_id ?? $product->get_id() );
		$price = getWooProductPriceToDisplay( $product_id ?? $product->get_id(), $quantity, $customProductPrice );
		$params = [
			'quantity'         => $quantity,
			'currency'         => get_woocommerce_currency(),
			'content_name'     => $product->get_name(),
			'content_category' => implode( ', ', getObjectTerms( 'product_cat', $product_id ?? $product->get_id() ) ),
			'content_id'       => $content_id,
		];
		if ( wooProductIsType( $product, 'variable' ) && !$this->getOption( 'woo_variable_as_simple' ) ) {
			$params[ 'content_type' ] = 'product_group';
		} else {
			$params[ 'content_type' ] = 'product';
		}
		$data = [
			'name' => 'ViewContent'
		];

		if ( PYS()->getOption( 'woo_view_content_value_enabled' ) ) {
			$params[ 'value' ] = $price;
		}

		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 * content_type, quantity, description, content_id, currency, value
	 */
	function addwcf_view_content_params( &$event ) {
		if ( !$this->getOption( 'woo_view_content_enabled' ) || empty( $event->args[ 'products' ] ) ) {
			return false;
		}
		$contents = [];
		$total = 0;

		foreach ( $event->args[ 'products' ] as $product_data ) {

			$product = wc_get_product( $product_data[ 'id' ] );
			if ( $this->getOption( 'woo_variable_data_select_product' ) && !$this->getOption( 'woo_variable_as_simple' ) ) {
				$product_id = getVariableIdByAttributes( $product );
			} else {
				$product_id = Helpers\getTikTokWooVariableToSimpleProductId( $product );
			}
			$content_id = Helpers\getTikTokWooProductContentId( $product_id ?? $product->get_id() );

			$contents[] = [
				'price'            => $product_data[ 'price' ],
				'content_name'     => $product_data[ 'name' ],
				'content_category' => implode( ', ', array_column( $product_data[ 'categories' ], "name" ) ),
				'content_id'       => $content_id,
				'quantity'         => $product_data[ 'quantity' ],
				'content_type'     => 'product',
			];
			$total += $product_data[ 'price' ] * $product_data[ 'quantity' ];
		}
		$params = [
			'content_type' => 'product',
			'currency'     => get_woocommerce_currency(),
			'contents'     => $contents,
		];

		$data = [
			'name' => 'ViewContent'
		];

		if ( PYS()->getOption( 'woo_view_content_value_enabled' ) ) {
			$params[ 'value' ] = $total;
		}

		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	private function add_woo_initiate_checkout_params( &$event ) {

		if ( !$this->getOption( 'woo_initiate_checkout_enabled' ) ) {
			return false;
		}

		$contents = [];
		foreach ( $event->args[ 'products' ] as $product ) {
			$product_id = Helpers\getTikTokWooCartProductId( $product );
			$content_id = Helpers\getTikTokWooProductContentId( $product_id );

			$contents[] = array(
				'price'            => $product[ 'price' ],
				'content_name'     => $product[ 'name' ],
				'content_category' => implode( ', ', array_column( $product[ 'categories' ], "name" ) ),
				'content_id'       => $content_id,
				'quantity'         => $product[ 'quantity' ],
				'content_type'     => 'product',
			);
		}

		$params = array(
			'content_type' => 'product',
			'contents'     => $contents,
			'currency'     => get_woocommerce_currency(),
			'value'        => getWooEventCartSubtotal( $event ),
		);
		$data = [
			'name' => 'InitiateCheckout'
		];

		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	private function add_woo_compete_payment_params( &$event ) {

		if ( !$this->getOption( 'woo_compete_payment_enabled' ) ) {
			return false;
		}

		$contents = [];

		foreach ( $event->args[ 'products' ] as $product_data ) {
			$product_id = Helpers\getTikTokWooProductDataId( $product_data );
			$content_id = Helpers\getTikTokWooProductContentId( $product_id );

			$contents[] = array(
				'price'            => $product_data[ 'price' ],
				'content_name'     => $product_data[ 'name' ],
				'content_category' => implode( ', ', array_column( $product_data[ 'categories' ], "name" ) ),
				'content_id'       => $content_id,
				'quantity'         => $product_data[ 'quantity' ],
				'content_type'     => 'product',
			);
		}

		$params = array(
			'content_type' => 'product',
			'contents'     => $contents,
			'currency'     => get_woocommerce_currency(),
			'value'        => getWooEventOrderTotal( $event ),
		);

		$data = [
			'name'  => 'CompletePayment',
			'delay' => 0.2,
		];

		$event->addParams( $params );
		$event->addPayload( $data );

		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	private function add_woo_purchase_params( &$event ) {
		if ( !$this->getOption( 'woo_purchase_enabled' ) ) {
			return false;
		}

		$contents = [];

		foreach ( $event->args[ 'products' ] as $product_data ) {
			$product_id = Helpers\getTikTokWooProductDataId( $product_data );
			$content_id = Helpers\getTikTokWooProductContentId( $product_id );

			$contents[] = array(
				'price'            => $product_data[ 'price' ],
				'content_name'     => $product_data[ 'name' ],
				'content_category' => implode( ', ', array_column( $product_data[ 'categories' ], "name" ) ),
				'content_id'       => $content_id,
				'quantity'         => $product_data[ 'quantity' ],
				'content_type'     => 'product',
			);
		}

		$params = array(
			'content_type' => 'product',
			'contents'     => $contents,
			'currency'     => get_woocommerce_currency(),
			'value'        => getWooEventOrderTotal( $event ),
		);
		$data = [
			'name' => 'PlaceAnOrder'
		];

		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	private function add_edd_add_to_cart_on_check_params( &$event ) {
		if ( !$this->getOption( 'edd_add_to_cart_enabled' ) ) return false;

		$data = [
			'name' => 'AddToCart'
		];
		$params = $this->getEddProductParams( $event );
		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	private function add_edd_view_content_params( &$event ) {
		if ( !$this->getOption( 'edd_view_content_enabled' ) ) return false;

		$data = [
			'name' => 'ViewContent'
		];
		$params = $this->getEddProductParams( $event );
		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	private function add_edd_init_checkout_params( &$event ) {
		if ( !$this->getOption( 'edd_initiate_checkout_enabled' ) ) return false;

		$params = $this->getEddProductParams( $event );

		$data = [
			'name' => 'InitiateCheckout'
		];

		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	private function add_edd_purchase_params( &$event ) {
		if ( !$this->getOption( 'edd_purchase_enabled' ) ) return false;
		$data = [
			'name' => 'PlaceAnOrder'
		];
		$params = $this->getEddProductParams( $event );

		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	private function add_edd_complete_payment_params( &$event ) {
		if ( !$this->getOption( 'edd_complete_payment_enabled' ) ) return false;
		$data = [
			'name' => 'CompletePayment'
		];
		$params = $this->getEddProductParams( $event );

		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	private function add_edd_add_to_cart_params( &$event ) {
		if ( !$this->getOption( 'edd_add_to_cart_enabled' ) ) return false;
		$params = [];
		if ( $event->args != null ) {
			$params = $this->get_edd_add_to_cart_on_button_click_params( $event->args );
		}
		$data = [
			'name' => 'AddToCart'
		];
		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}


	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	private function add_custom_event_params( &$event ) {
		/**
		 * @var CustomEvent $customEvent
		 */
		$customEvent = $event->args;
		if ( !$customEvent->isTikTokEnabled() ) return false;

		$params = [];

		if ( $customEvent->tiktok_params_enabled ) {
			$params = $customEvent->tiktok_params;
			$customParams = $customEvent->tiktok_custom_params;
			foreach ( $customParams as $custom_param ) {
				$params[ $custom_param[ 'name' ] ] = $custom_param[ 'value' ];
			}
			// SuperPack Dynamic Params feature
			$params = apply_filters( 'pys_superpack_dynamic_params', $params, 'tiktok' );
		}

		$data = [
			'name'  => $customEvent->getTikTokEventType(),
			'delay' => $customEvent->getDelay(),
		];
		$event->addPayload( $data );
		$event->addParams( $params );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return bool
	 */
	private function add_wcf_add_to_cart_params( &$event ) {
		if ( !$this->getOption( 'woo_add_to_cart_enabled' ) || empty( $event->args[ 'products' ] ) ) {
			return false; // return if args is empty
		}
		$contents = [];
		$total = 0;
		foreach ( $event->args[ 'products' ] as $product_data ) {

			$product = wc_get_product( $product_data[ 'id' ] );
			$product_id = Helpers\getTikTokWooVariableToSimpleProductId( $product );
			$content_id = Helpers\getTikTokWooProductContentId( $product_id );

			$contents[] = array(
				'price'            => $product_data[ 'price' ],
				'content_name'     => $product_data[ 'name' ],
				'content_category' => implode( ', ', array_column( $product_data[ 'categories' ], "name" ) ),
				'content_id'       => $content_id,
				'quantity'         => $product_data[ 'quantity' ],
				'content_type'     => 'product',
			);

			$total += $product_data[ 'price' ] * $product_data[ 'quantity' ];
		}
		$params = [
			'content_type' => 'product',
			'currency'     => get_woocommerce_currency(),
			'contents'     => $contents,
			'value'        => $total
		];
		$data = [
			'name' => 'AddToCart'
		];

		$event->addParams( $params );
		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @return array
	 */
	private function getEddProductParams( $event ) {

		switch ( $event->getId() ) {

			default:
			{
				$value_enabled = true;
			}
		}

		$total = 0;
		$total_as_is = 0;
		$isPurchase = $event->getId() == 'edd_purchase' || $event->getId() == 'edd_complete_payment';

		foreach ( $event->args[ 'products' ] as $product ) {
			$download_id = (int) $product[ 'product_id' ];
			$edd_download_price = getEddDownloadPrice( $download_id, $product[ 'price_index' ] );

			$contents[] = array(
				'price'            => $edd_download_price,
				'content_name'     => $product[ 'name' ],
				'content_category' => implode( ', ', array_column( $product[ 'categories' ], 'name' ) ),
				'content_id'       => $download_id,
				'quantity'         => $product[ 'quantity' ],
				'content_type'     => 'product',
			);

			if ( $isPurchase ) {
				if ( PYS()->getOption( 'edd_tax_option' ) == 'included' ) {
					$total += $product[ 'subtotal' ] + $product[ 'tax' ] - $product[ 'discount' ];
				} else {
					$total += $product[ 'subtotal' ] - $product[ 'discount' ];
				}
				$total_as_is += $product[ 'price' ];

			} else {

				$total += $edd_download_price * $product[ 'quantity' ];
				if ( isset( $product[ 'cart_item_key' ] ) ) {
					$total_as_is += edd_get_cart_item_final_price( $product[ 'cart_item_key' ] );
				} else {
					$total_as_is += floatval( edd_get_download_final_price( $download_id, [] ) );
				}
			}
		}

		//add fee
		$fee = $event->args[ 'fee' ] ?? 0;
		$feeTax = $event->args[ 'fee_tax' ] ?? 0;

		if ( PYS()->getOption( 'edd_event_value' ) == 'custom' ) {
			if ( PYS()->getOption( 'edd_tax_option' ) == 'included' ) {
				$total += $fee + $feeTax;
			} else {
				$total += $fee;
			}
		} else {
			if ( edd_prices_include_tax() ) {
				$total_as_is += $fee + $feeTax;
			} else {
				$total_as_is += $fee;
			}
		}

		$params = [
			'content_type' => 'product',
		];
		if(!empty($contents)){
			$params['contents'] = $contents;
		}
		if ( $value_enabled ) {
			if ( PYS()->getOption( 'edd_event_value' ) == 'custom' ) {
				$params[ 'value' ] = $total;
			} else {
				$params[ 'value' ] = $total_as_is;
			}
			$params[ 'currency' ] = edd_get_currency();
		}

		return $params;
	}

	function getAdvancedMatchingParams() {

		$params = array();

		if ( isset( $_COOKIE[ 'pys_advanced_form_data' ] ) ) {
			$jsonStr = stripslashes( $_COOKIE[ 'pys_advanced_form_data' ] );
			$advancedForm = json_decode( $jsonStr, true );

			if ( isset( $advancedForm[ "email" ] ) && $advancedForm[ "email" ] != "" ) {
				$params[ 'sha256_email' ] = hash( 'sha256', $advancedForm[ "email" ] );
			}
			if ( isset( $advancedForm[ "phone" ] ) && $advancedForm[ "phone" ] != "" ) {
				$params[ 'sha256_phone_number' ] = hash( 'sha256', $advancedForm[ "phone" ] );
			}
		}

		$user = wp_get_current_user();

		if ( $user && $user->ID ) {
			// get user regular data
			$userEmail = $user->get( 'user_email' );
			$userPhone = get_user_meta( $user->ID, 'user_phone', true );
			if ( !empty( $userEmail ) ) {
				$params[ 'sha256_email' ] = hash( 'sha256', $userEmail );
			}
			if ( !empty( $userPhone ) ) {
				$params[ 'sha256_phone_number' ] = hash( 'sha256', $userPhone );
			}

		} else {
			if ( isEddActive() ) {
				$payment_key = getEddPaymentKey();
				$order_id = (int) edd_get_purchase_id_by_key( $payment_key );
				if ( $order_id ) {
					$userEdd = edd_get_payment_meta_user_info( $order_id );
					if ( !empty( $userEdd[ 'email' ] ) ) {
						$params[ 'sha256_email' ] = hash( 'sha256', $userEdd[ 'email' ] );
					}
				}
			}
		}

		if ( isWooCommerceActive() ) {

			if ( $user && $user->ID ) {
				$billing_phone = $user->get( 'billing_phone' );
				if ( !empty( $billing_phone ) ) {
					$params[ 'sha256_phone_number' ] = hash( 'sha256', $billing_phone );
				}
			} else {
				$orderId = wooGetOrderIdFromRequest();
				if ( $orderId > 0 ) {
					$order = wc_get_order( $orderId );
					if ( $order ) {
						$email = $order->get_billing_email();
						$phone = $order->get_billing_phone();
						if ( !empty( $email ) ) {
							$params[ 'sha256_email' ] = hash( 'sha256', $email );
						}
						if ( !empty( $phone ) ) {
							$params[ 'sha256_phone_number' ] = hash( 'sha256', $phone );
						}
					}
				}
			}
		}

		if ( EventsManager::isTrackExternalId() ) {
			if ( PYS()->get_pbid() ) {
				$params[ 'external_id' ] = PYS()->get_pbid();
			}
		}

		return apply_filters( "pys_tt_advanced_matching", $params );
	}

	private function add_click_button_params( &$event ) {

		$isActive = $this->getOption( $event->getId() . '_enabled' );
		$params = array();
		$event->addParams( $params );
		$data = array(
			'name' => 'ClickButton'
		);

		$event->addPayload( $data );

		return $isActive;
	}

	private function getWooAdvancedMarketingEventParams( $event ) {

		if ( !$this->getOption( $event->getId() . '_enabled' ) ) {
			return false;
		}

		$data = array();

		switch ( $event->getId() ) {
			case 'woo_frequent_shopper':
				$data[ 'name' ] = 'FrequentShopper';
				break;
			case 'woo_vip_client':
				$data[ 'name' ]  = 'VipClient';
				break;
			case 'woo_FirstTimeBuyer':
				$data[ 'name' ]  = 'FirstTimeBuyer';
				break;
			case 'woo_ReturningCustomer':
				$data[ 'name' ]  = 'ReturningCustomer';
				break;
			case 'woo_big_whale':
				$data[ 'name' ]  = 'BigWhale';
				break;
			default:
				return false;
		}

		$event->addPayload( $data );
		return true;
	}

	/**
	 * @param SingleEvent $event
	 * @param array $args
	 * @return boolean
	 */
	private function setEddCartEventParams( $event ) {

		if ( !$this->getOption( $event->getId() . '_enabled' ) ) {
			return false;
		}

		$data = array();

		switch ( $event->getId() ) {
			case 'edd_frequent_shopper':
				$data[ 'name' ] = 'FrequentShopper';
				break;
			case 'edd_vip_client':
				$data[ 'name' ] = 'VipClient';
				break;
			case 'edd_big_whale':
				$data[ 'name' ] = 'BigWhale';
				break;
		}

		$event->addPayload( $data );
		return true;
	}

	private function addDataToEvent( $eventData, &$event ) {
		$params = $eventData[ "data" ];
		unset( $eventData[ "data" ] );
		$event->addParams( $params );
		$event->addPayload( $eventData );
	}

	/**
	 * @return bool
	 */
	public function isServerApiEnabled() {
		return $this->getOption( "use_server_api" );
	}

	public function getApiTokens() {

		$tokens = array();
		$pixel_ids = (array) $this->getOption( 'pixel_id' );
		if ( count( $pixel_ids ) > 0 ) {
			$tokens[ $pixel_ids[ 0 ] ] = (array) $this->getOption( 'server_access_api_token' );
		}

		return $tokens;
	}

	public function getApiTestCode() {

		$testCode = array();
		$pixelids = (array) $this->getOption( 'pixel_id' );
		if ( count( $pixelids ) > 0 ) {
			$serverTestCode = (array) $this->getOption( 'test_api_event_code' );
			$testCode[ $pixelids[ 0 ] ] = reset( $serverTestCode );
		}

		return $testCode;
	}

	public function getLog() {
		return $this->logger;
	}
}


/**
 * @return Tiktok
 */
function Tiktok() {
	return Tiktok::instance();
}

Tiktok();