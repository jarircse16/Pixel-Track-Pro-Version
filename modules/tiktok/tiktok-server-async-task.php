<?php
namespace PixelYourSite;



defined('ABSPATH') or die('Direct access not allowed');

class TikTokAsyncTask extends \WP_Async_Task {
	protected $action = 'pys_send_tiktok_server_event';

	protected function prepare_data( $data ) {
		try {
            if (!empty($data)) {
                if (empty($this->_body_data)) {
                    return ['data' => base64_encode(serialize($data))];
                } else {
                    $oldData = unserialize(base64_decode($this->_body_data['data']));

                    // Check if $oldData[0] and $data[0] are arrays before merging
                    if (is_array($oldData[0]) && is_array($data[0])) {
                        $data = ['data' => base64_encode(serialize(array_merge($oldData[0], $data[0])))];
                    } else {
                        $data = ['data' => base64_encode(serialize($data))];
                    }
                    return $data;
                }
            }
		} catch ( \Exception $ex ) {
			error_log( $ex );
		}

		return array();
	}

	protected function run_action() {
		try {
			$data = unserialize( base64_decode( $_POST[ 'data' ] ) );
			$events = is_array( $data[ 0 ] ) ? $data[ 0 ] : $data;

			if ( empty( $events ) ) {
				return;
			}

			TikTokServer()->sendEventsAsync( $events );

		} catch ( \Exception $ex ) {
			error_log( $ex );
		}
	}
}