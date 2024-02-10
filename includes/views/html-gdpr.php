<?php

namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="card card-static">
    <div class="card-header">
        Note
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <p>These solutions are not perfect or easy to implement especially for a non-technical person. Contact
                    THEIR support if you need any help. The free plugins might not cover every aspect of the GDPR
                    legislation.</p>
                <p class="mb-0">We are aware of the shortcomings and we try to offer more easy to use integrations in
                    the feature.</p>
            </div>
        </div>
    </div>
</div>

<!-- API -->
<div class="card card-static">
    <div class="card-header">
        For Developers
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col">
	            <?php PYS()->render_switcher_input( 'gdpr_ajax_enabled' ); ?>
                <h4 class="switcher-label">Enable AJAX filter values update</h4>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p>Use <code>ptp_gdpr_ajax_enabled</code>filter to by-pass option above.</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p>Use following filters to control each pixel:
                    <code>ptp_disable_by_gdpr</code>, <code>ptp_disable_facebook_by_gdpr</code>,
                    <code>ptp_disable_analytics_by_gdpr</code>, <code>ptp_disable_tiktok_by_gdpr</code>, <code>ptp_disable_google_ads_by_gdpr</code>,
                    <code>ptp_disable_pinterest_by_gdpr</code> or <code>ptp_disable_bing_by_gdpr</code>.
                </p>
                <p class="mb-0">First filter will disable all pixels, other can be used to disable particular pixel.
                    Simply pass <code>TRUE</code> value to disable a pixel.
                </p>
            </div>
        </div>
    </div>
    <hr>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <h2>Use following filters to control each cookie:</h2>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p>
                    <p><code>ptp_disable_all_cookie</code> - disable all PYS cookies</p>
                    <p><code>ptp_disabled_start_session_cookie</code> - disable start_session & session_limit cookie</p>
                    <p><code>ptp_disable_first_visit_cookie</code> - disable pys_first_visit cookie</p>
                    <p><code>ptp_disable_landing_page_cookie</code> - disable pys_landing_page & last_pys_landing_page cookies</p>
                    <p><code>ptp_disable_trafficsource_cookie</code> - disable pysTrafficSource & last_pysTrafficSource cookies</p>
                    <p><code>ptp_disable_utmTerms_cookie</code> - disable ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content' ,'utm_term'] with prefix <code>pys_</code> and <code>last_pys_</code> cookies</p>
                    <p><code>ptp_disable_utmId_cookie</code> - disable ['fbadid', 'gadid', 'padid', 'bingid'] with prefix <code>pys_</code> and <code>last_pys_</code> cookies</p>
                    <p><code>ptp_disable_advanced_form_data_cookie</code> - disable pys_advanced_form_data cookies</p>
                    <p><code>ptp_disable_externalID_by_gdpr</code> - disable pbid(external_id) cookie</p>
                </p>
                <p class="mb-0">
                    To disable cookies, use filters where necessary.<br>
                    First filter will disable all cookies, other can be used to disable particular cookie.
                    Simply pass <code>__return_true</code> value to disable a cookie.
                </p>
                <p class="mb-0">
                    Example:<br>
                    <code>add_filter( 'ptp_disable_advanced_form_data_cookie', '__return_true', 10, 2 );</code>
                </p>
            </div>
        </div>
    </div>
</div>

<hr>
<div class="row justify-content-center">
	<div class="col-4">
		<button class="btn btn-block btn-sm btn-save">Save Settings</button>
	</div>
</div>
