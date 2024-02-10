<?php

namespace PixelYourSite;
defined('ABSPATH') || exit;

class TikTok_logger extends PYS_Logger {

	protected $log_path = null;

	public function __construct( ) {
		$this->log_path = trailingslashit( PYS_PATH ).'logs/';
	}

	public function init() {
		$this->isEnabled = Tiktok()->getOption('logs_enable');
	}

	public static function get_log_file_name( ) {
		return 'tiktok_debug.log';
	}

	public static function get_log_file_path( ) {
		return trailingslashit( PYS_PATH ).'logs/' . self::get_log_file_name( );
	}

	public static function get_log_file_url( ) {
		return trailingslashit( PYS_URL ) .'logs/'. static::get_log_file_name( );
	}

}