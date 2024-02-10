<?php

namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

?>

<div class="card card-static">
    <div class="card-header ">
        <?php PYS()->render_switcher_input('logs_enable'); ?> Meta API logs
        <div style="float: right;margin-top: 10px;">
            <a style="margin-right: 30px"
               href="<?php echo esc_url( buildAdminUrl( 'pixeltrackpro', 'logs' ) ); ?>&clear_plugin_logs=true">Clear
                Meta API logs</a>
            <a href="<?= PYS_Logger::get_log_file_url() ?>" target="_blank" download>Download Meta API logs</a>
        </div>
    </div>
    <div class="card-body">
        <textarea style="white-space: nowrap;width: 100%;height: 500px;"><?php
            echo PYS()->getLog()->getLogs();
            ?></textarea>
    </div>
</div>

<?php if ( Tiktok()->enabled() ) : ?>
    <div class="card card-static">
        <div class="card-header ">
			<?php Tiktok()->render_switcher_input( 'logs_enable' ); ?> TikTok API Logs
            <div style="float: right;margin-top: 10px;">
                <a style="margin-right: 30px"
                   href="<?php echo esc_url( buildAdminUrl( 'pixeltrackpro', 'logs' ) ); ?>&clear_tiktok_logs=true">Clear
                    TikTok API Logs</a>
                <a href="<?php echo TikTok_logger::get_log_file_url() ?>" target="_blank" download>Download TikTok API Logs</a>
            </div>
        </div>
        <div class="card-body">
            <textarea style="white-space: nowrap;width: 100%;height: 500px;"><?php
				echo Tiktok()->getLog()->getLogs();
				?></textarea>
        </div>
    </div>
<?php endif; ?>

<?php if ( Pinterest()->enabled() && method_exists(Pinterest(), 'getLog') ) : ?>
    <div class="card card-static">
        <div class="card-header ">
			<?php Pinterest()->render_switcher_input( 'logs_enable' ); ?> Pinterest API Logs
            <div style="float: right;margin-top: 10px;">
                <a style="margin-right: 30px"
                   href="<?php echo esc_url( buildAdminUrl( 'pixeltrackpro', 'logs' ) ); ?>&clear_pinterest_logs=true">Clear
                    Pinterest API Logs</a>
                <a href="<?= Pinterest_logger::get_log_file_url() ?>" target="_blank" download>Download Pinterest API Logs</a>
            </div>
        </div>
        <div class="card-body">
            <textarea style="white-space: nowrap;width: 100%;height: 500px;"><?php
				echo Pinterest()->getLog()->getLogs();
				?></textarea>
        </div>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-4">
        <button class="btn btn-block btn-sm btn-save">Save Settings</button>
    </div>
</div>