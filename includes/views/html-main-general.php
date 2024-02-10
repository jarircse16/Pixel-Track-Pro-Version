<?php

namespace PixelYourSite;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

?>

    <!-- Pixel IDs -->
    <div class="card card-static">
        <div class="card-header">
            Pixel IDs
        </div>
        <div class="card-body">

            <?php if (Facebook()->enabled()) : ?>

                <div class="row align-items-center mb-3 py-2">
                    <div class="col-2">
                        <img class="tag-logo" src="<?php echo PYS_URL; ?>/dist/images/facebook-small-square.png">
                    </div>
                    <div class="col-6">
                        Your Meta Pixel (formerly Facebook Pixel)
                    </div>
                    <div class="col-4">
                        <label for="fb_settings_switch" class="btn btn-block btn-sm btn-primary btn-settings">Click for
                            settings</label>
                    </div>

                </div>
                <input type="checkbox" id="fb_settings_switch" style="display: none">
                <div class="settings_content">
                    <div class="row  mb-2">
                        <div class="col-12">
                            <?php Facebook()->render_switcher_input("use_server_api"); ?>
                            <h4 class="switcher-label">Enable Conversion API (add the token below)</h4>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <?php Facebook()->render_switcher_input('advanced_matching_enabled'); ?>
                            <h4 class="switcher-label">Enable Advanced Matching</h4>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <p>
                                Learn about Conversion API and Advanced Matching privacy and consent:
                                <a href="https://www.youtube.com/watch?v=PsKdCkKNeLU" target="_blank">watch video</a>
                            </p>
                            <p>
                                Install multiple Facebook Pixels with CAPI support:
                                <a href="https://www.youtube.com/watch?v=HM98mGZshvc" target="_blank">watch video</a>
                            </p>
                            <p>
                                What is Events Matching and EMQ and how you can improve it:
                                <a href=" https://www.youtube.com/watch?v=3soI_Fl0JQw" target="_blank">watch video</a>
                            </p>
                        </div>
                    </div>

                    <div class="plate pixel_info">
						<?php if( isSuperPackActive() && SuperPack()->getOption( 'additional_ids_enabled' ) ): ?>
							<?php Facebook()->render_text_input_array_item( 'main_pixel', "", 0, true ); ?>
                            <div class="row align-items-center pt-3">
                                <div class="col-12">
                                    <?php
									    Facebook()->render_switcher_input( "main_pixel_enabled" );
                                    ?>
                                    <h4 class="switcher-label">Enable Pixel</h4>
                                </div>
                            </div>
						<?php endif; ?>
                        <div class="row align-items-center mb-3 pt-3">
                            <div class="col-12">
                                <h4 class="label">Meta Pixel (formerly Facebook Pixel) ID:</h4>
                                <?php Facebook()->render_pixel_id('pixel_id', 'Meta Pixel (formerly Facebook Pixel) ID'); ?>
                                <small class="form-text">
                                    <a href="https://www.webmxt.com/pixelyoursite-free-version/add-your-facebook-pixel"
                                       target="_blank">How to get it?</a>
                                </small>
                            </div>
                        </div>
                        <div class="row align-items-center mb-3">
                            <div class="col-12">
                                <h4 class="label">Conversion API:</h4>
                                <?php Facebook()->render_text_area_array_item("server_access_api_token", "Api token") ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                Send events directly from your web server to Facebook through the Conversion API. This
                                can help you capture more events. An access token is required to use the server-side
                                API.
                                <a href='https://www.webmxt.com/facebook-conversion-api-capi' target='_blank'>Learn
                                    how to generate the token and how to test Conversion API</a>
                            </div>
                        </div>

                        <div class="row align-items-center pb-3">
                            <div class="col-12">
                                <h4 class="label">test_event_code :</h4>
                                <?php Facebook()->render_text_input_array_item("test_api_event_code", "Code"); ?>
                                <?php Facebook()->render_text_input_array_item("test_api_event_code_expiration_at", "", 0, true); ?>
                                <small class="form-text">
                                    Use this if you need to test the server-side event. <strong>Remove it after
                                        testing.</strong> The code will auto-delete itself after 24 hours.
                                </small>
                            </div>
                        </div>
						<?php if ( isSuperPackActive() ) {
							if ( SuperPack()->getOption( 'additional_ids_enabled' ) ) : ?>
                                <p>
                                    <?php Facebook()->render_checkbox_input( 'is_fire_signal', 'Fire the active automated events for this pixel' ); ?>
                                </p>
                                <?php if ( isWooCommerceActive() ) : ?>
                                    <p>
                                        <?php Facebook()->render_checkbox_input( 'is_fire_woo', 'Fire the WooCommerce events for this pixel' ); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ( isEddActive() ) : ?>
                                    <p>
                                        <?php Facebook()->render_checkbox_input( 'is_fire_edd', 'Fire the Easy Digital Downloads events for this pixel' ); ?>
                                    </p>
                                <?php endif; ?>
                                <p>
                                    <strong>Display conditions:</strong>
                                    <?php
                                    $main_pixel = Facebook()->getOption( 'main_pixel' );
                                    if ( !empty( $main_pixel ) && isset( $main_pixel[ 0 ] ) ) {
                                        $conditions = SuperPack\SPPixelId::fromArray( json_decode( $main_pixel[ 0 ], true ) );
                                        SuperPack\SpPixelCondition()->renderHtml( $conditions->displayConditions );
                                    } else {
                                        SuperPack\SpPixelCondition()->renderHtml();
                                    } ?>
                                </p>
							<?php endif; ?>
                            <?php
							if ( SuperPack()->getOption( 'enable_hide_this_tag_by_tags' ) || SuperPack()->getOption( 'enable_hide_this_tag_by_url' ) ) {
								Facebook()->render_hide_pixel_block();
							}
						}
                        ?>
                    </div>
                    <?php if (isSuperPackActive()) : ?>
                        <?php SuperPack\renderFacebookPixelIDs(); ?>
                    <?php endif; ?>
                    <hr>
                    <?php addMetaTagFields(Facebook(), "https://www.webmxt.com/verify-domain-facebook"); ?>
                </div>

                <hr>
            <?php endif; ?>

            <?php if (GA()->enabled()) : ?>

                <div class="row align-items-center mb-3 py-2">
                    <div class="col-2">
                        <img class="tag-logo" src="<?php echo PYS_URL; ?>/dist/images/analytics-square-small.png">
                    </div>
                    <div class="col-6">
                        Your Google Analytics
                    </div>
                    <div class="col-4">
                        <label for="gan_settings_switch" class="btn btn-block btn-sm btn-primary btn-settings">Click for
                            settings</label>
                    </div>
                </div>

				<?php
				$noticeRenderNotSupportUA = false;
				$noticeOnlyUA = true;
				if ( GA()->enabled() && !empty( GA()->getOption( 'tracking_id' ) ) ) {
					$trackingId = GA()->getOption( 'tracking_id' );
					if ( !isGaV4( $trackingId ) ) {
						$noticeRenderNotSupportUA = true;
					} else {
						$noticeOnlyUA = false;
					}
				}
				if ( isSuperPackActive( '3.1.1' ) && SuperPack()->getOption( 'enabled' ) && SuperPack()->getOption( 'additional_ids_enabled' ) ) {
					$additionalPixels = SuperPack()->getGaAdditionalPixel();
					foreach ( $additionalPixels as $additionalPixel ) {
						if ( $additionalPixel->isEnable ) {
							if ( !isGaV4( $additionalPixel->pixel ) ) {
								$noticeRenderNotSupportUA = true;
							} else {
								$noticeOnlyUA = false;
							}
						}
					}
				}
				if ( $noticeRenderNotSupportUA ) {
					?>
                    <div class="row align-items-center mb-3 py-2 not-supported">
                        <div class="col-12">
							<?php
							if ( $noticeOnlyUA ) {
								?>
                                <p>The old Universal Analytics properties are not supported by Google Analytics anymore.
                                    You must
                                    use the new GA4 properties instead. <a
                                            href="https://www.youtube.com/watch?v=KkiGbfl1q48"
                                            target="_blank">Watch this video to find how to get your
                                        GA4 tag</a>.</p>
								<?php
							} else {
								?>
                                <p>Your old Universal Analytics property does't send data anymore, consider removing it.
                                    Google
                                    Analytics supports only GA4 properties. <a
                                            href="https://www.youtube.com/watch?v=KkiGbfl1q48"
                                            target="_blank">Watch this video to find how to get
                                        your GA4 tag</a>.</p>
								<?php
							} ?>
                        </div>
                    </div>
					<?php
				} ?>

                <input type="checkbox" id="gan_settings_switch" style="display: none">
                <div class="settings_content">

                    <div class="plate pixel_info">
						<?php if( isSuperPackActive() && SuperPack()->getOption( 'additional_ids_enabled' ) ): ?>
							<?php GA()->render_text_input_array_item( 'main_pixel', "", 0, true ); ?>
                            <div class="row align-items-center pt-3 pb-2">
                                <div class="col-12">
									<?php
									GA()->render_switcher_input( "main_pixel_enabled" );
									?>
                                    <h4 class="switcher-label">Enable Pixel</h4>
                                </div>
                            </div>
						<?php endif; ?>

                        <div class="row  mb-2">
                            <div class="col-12">
                                <?php GA()->render_switcher_input("use_server_api"); ?>
                                <h4 class="switcher-label">Enable Measurement Protocol (add the api_secret)</h4>
                            </div>
                        </div>
                        <div class="row pt-3 pb-3">
                            <div class="col-12">
                                <h4 class="label mb-3 ">Google Analytics tracking ID:</h4>
                                <?php GA()->render_pixel_id('tracking_id', 'Google Analytics tracking ID'); ?>
                                <p class="ga_pixel_info small">
                                    <?php
                                    $pixels = GA()->getPixelIDs();
                                    if (count($pixels)) {
                                        if (strpos($pixels[0], 'G') === 0) {
                                            echo 'We identified this tag as a GA4 property.';
                                        } else {
                                            echo '<span class="not-support-tag">We identified this tag as a Google Analytics Universal property.</span>';
                                        }
                                    }

                                    ?>
                                </p>
                                <small class="form-text mb-2">
                                    <a href="https://www.webmxt.com/documentation/add-your-google-analytics-code"
                                       target="_blank">How to get it?</a>
                                </small>
                                <div class="row align-items-center mb-3">
                                    <div class="col-12">
                                        <h4 class="label">Measurement Protocol API secret: </h4>
                                        <?php GA()->render_text_area_array_item("server_access_api_token", "API secret", 0, GA()->getOption('use_server_api')) ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        Generate the API secret inside your Google Analytics account: navigate to <b>Admin > Data Streams > choose your stream > Measurement Protocol API secrets</b>. The Measurement Protocol is used for WooCommerce and Easy Digital Downloads "Google Analytics Advanced Purchase Tracking" and refund tracking. Required for GA4 properties only.
                                    </div>
                                </div>
                                <input type="checkbox" class="custom-control-input"
                                       name="pys[ga][is_enable_debug_mode][-1]" value="0" checked/>
                                <?php GA()->render_checkbox_input_array("is_enable_debug_mode", "Enable Analytics Debug mode for this property"); ?>
                                <p>
                                    Learn how to get the Google Analytics 4 tag ID and how to test it:
                                    <a href="https://www.youtube.com/watch?v=KkiGbfl1q48" target="_blank">watch video</a>
                                </p>
                                <p>
                                    Install the old Google Analytics UA property and the new GA4 at the same time:
                                    <a href="https://www.youtube.com/watch?v=JUuss5sewxg" target="_blank">watch
                                        video</a>
                                </p>
                                <p>
                                    Learn how to get your Measurement Protocol API secret:
                                    <a href="https://www.youtube.com/watch?v=cURMzxY3JSg" target="_blank">watch
                                        video</a>
                                </p>
                                <?php if ( isSuperPackActive() ) {
                                    if ( SuperPack()->getOption( 'additional_ids_enabled' ) ) : ?>
                                        <p>
                                            <?php GA()->render_checkbox_input( 'is_fire_signal', 'Fire the active automated events for this pixel' ); ?>
                                        </p>
                                        <?php if ( isWooCommerceActive() ) : ?>
                                            <p>
                                                <?php GA()->render_checkbox_input( 'is_fire_woo', 'Fire the WooCommerce events for this pixel' ); ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ( isEddActive() ) : ?>
                                            <p>
                                                <?php GA()->render_checkbox_input( 'is_fire_edd', 'Fire the Easy Digital Downloads events for this pixel' ); ?>
                                            </p>
                                        <?php endif; ?>
                                        <p>
                                            <strong>Display conditions:</strong>
                                            <?php
                                            $main_pixel = GA()->getOption( 'main_pixel' );
                                            if ( !empty( $main_pixel ) && isset( $main_pixel[ 0 ] ) ) {
                                                $conditions = SuperPack\SPPixelId::fromArray( json_decode( $main_pixel[ 0 ], true ) );
                                                SuperPack\SpPixelCondition()->renderHtml( $conditions->displayConditions );
                                            } else {
                                                SuperPack\SpPixelCondition()->renderHtml();
                                            } ?>
                                        </p>
								    <?php endif; ?>
                                    <?php
                                    if ( SuperPack()->getOption( 'enable_hide_this_tag_by_tags' ) || SuperPack()->getOption( 'enable_hide_this_tag_by_url' ) ) {
                                        GA()->render_hide_pixel_block();
                                    }
							    } ?>
                            </div>
                        </div>
                    </div>
                    <?php if (isSuperPackActive()) : ?>
                        <?php SuperPack\renderGoogleAnalyticsPixelIDs(); ?>
                    <?php endif; ?>
                </div>
                <hr>

            <?php endif; ?>

            <?php if (Ads()->enabled()) : ?>

                <div class="row align-items-center mb-3 py-2">
                    <div class="col-2">
                        <img class="tag-logo" src="<?php echo PYS_URL; ?>/dist/images/google-ads-square-small.png">
                    </div>
                    <div class="col-6">
                        Your Google Ads Tag
                    </div>
                    <div class="col-4">
                        <label for="gads_settings_switch" class="btn btn-block btn-sm btn-primary btn-settings">Click
                            for settings</label>
                    </div>
                </div>
                <input type="checkbox" id="gads_settings_switch" style="display: none">
                <div class="settings_content">

                    <div class="plate pixel_info">
						<?php if( isSuperPackActive() && SuperPack()->getOption( 'additional_ids_enabled' ) ): ?>
							<?php Ads()->render_text_input_array_item( 'main_pixel', "", 0, true ); ?>
                            <div class="row align-items-center pt-3 pb-2">
                                <div class="col-12">
									<?php
									Ads()->render_switcher_input( "main_pixel_enabled" );
									?>
                                    <h4 class="switcher-label">Enable Pixel</h4>
                                </div>
                            </div>
						<?php endif; ?>
                        <div class="row pt-3 pb-3">
                            <div class="col-12">
                                <h4 class="label">Google Ads Tag:</h4>
                                <?php Ads()->render_pixel_id('ads_ids', 'AW-123456789'); ?>
                                <small class="form-text mb-2">
                                    <a href="https://www.webmxt.com/documentation/google-ads-tag"
                                       target="_blank">How to get
                                        it?</a>
                                </small>
                                <input type="checkbox" checked hidden value=""
                                       name="pys[google_ads][enhanced_conversions_manual_enabled][-1]"/>
                                <?php Ads()->render_switcher_input_array("enhanced_conversions_manual_enabled", 0); ?>
                                <div class="switcher-label">Enable enhanced conversions</div>
                                <p class="small">Enhanced conversion data is sent when you add a conversion label to your events.
                                    You need to select Manual setup > Edit code" when creating the conversion inside your Google Ads account.
                                    The enhanced conversion data is sent for all WooCommerce and Easy Digital Downloads purchase-related conversions.
                                    For the other events, we send it for logged-in users only, or when we can detect if from forms using Advanced user-data detection.
                                </p>


                                <div class="mt-3 mb-3">
                                    How to install the Google the Google Ads Tag:
                                    <a href="https://www.youtube.com/watch?v=plkv_v4nz8I" target="_blank">watch
                                        video</a>
                                </div>
                                <div class="mb-3">
                                    How to configure Google Ads Conversions:
                                    <a href="https://www.youtube.com/watch?v=x1VvVDa5L7c" target="_blank">watch
                                        video</a>
                                </div>
                                <div class="mb-3">
                                    Lear how to use Enhanced Conversions:
                                    <a href="https://www.youtube.com/watch?v=0uuTiOnVw80" target="_blank">watch
                                        video</a>
                                </div>

								<?php if ( isSuperPackActive() ) {
									if ( SuperPack()->getOption( 'additional_ids_enabled' ) ) : ?>
                                        <p>
											<?php Ads()->render_checkbox_input( 'is_fire_signal', 'Fire the active automated events for this pixel' ); ?>
                                        </p>
										<?php if ( isWooCommerceActive() ) : ?>
                                            <p>
												<?php Ads()->render_checkbox_input( 'is_fire_woo', 'Fire the WooCommerce events for this pixel' ); ?>
                                            </p>
										<?php endif; ?>
										<?php if ( isEddActive() ) : ?>
                                            <p>
												<?php Ads()->render_checkbox_input( 'is_fire_edd', 'Fire the Easy Digital Downloads events for this pixel' ); ?>
                                            </p>
										<?php endif; ?>

                                        <p>
                                            <strong>Display conditions:</strong>
                                            <?php
                                            $main_pixel = Ads()->getOption( 'main_pixel' );
                                            if ( !empty( $main_pixel ) && isset( $main_pixel[ 0 ] ) ) {
                                                $conditions = SuperPack\SPPixelId::fromArray( json_decode( $main_pixel[ 0 ], true ) );
                                                SuperPack\SpPixelCondition()->renderHtml( $conditions->displayConditions );
                                            } else {
                                                SuperPack\SpPixelCondition()->renderHtml();
                                            } ?>
                                        </p>
									<?php endif; ?>

									<?php
									if ( SuperPack()->getOption( 'enable_hide_this_tag_by_tags' ) || SuperPack()->getOption( 'enable_hide_this_tag_by_url' ) ) {
										Ads()->render_hide_pixel_block();
									}
								} ?>
                            </div>
                        </div>
                    </div>
                    <?php if (isSuperPackActive()) : ?>
                        <?php SuperPack\renderGoogleAdsIDs(); ?>
                    <?php endif; ?>
                    <hr>
                    <?php addMetaTagFields(Ads(), ""); ?>
                </div>
                <hr>

            <?php endif; ?>

            <?php if (Tiktok()->enabled()) : ?>

                <div class="row align-items-center mb-3 py-2">
                    <div class="col-2">
                        <img class="tag-logo" src="<?php echo PYS_URL; ?>/dist/images/tiktok-logo.png">
                    </div>
                    <div class="col-6">
                        Your TikTok
                    </div>
                    <div class="col-4">
                        <label for="tiktok_settings_switch" class="btn btn-block btn-sm btn-primary btn-settings">Click
                            for settings</label>
                    </div>
                </div>
                <input type="checkbox" id="tiktok_settings_switch" style="display: none">
                <div class="settings_content">

                    <div class="plate pb-3 pt-3 pixel_info">
						<?php if( isSuperPackActive() && SuperPack()->getOption( 'additional_ids_enabled' ) ): ?>
							<?php Tiktok()->render_text_input_array_item( 'main_pixel', "", 0, true ); ?>
                            <div class="row align-items-center pt-3 pb-2">
                                <div class="col-12">
									<?php
									Tiktok()->render_switcher_input( "main_pixel_enabled" );
									?>
                                    <h4 class="switcher-label">Enable Pixel</h4>
                                </div>
                            </div>
						<?php endif; ?>

                        <div class="row">
                            <div class="col-12">
                                <h4 class="label mb-2">TikTok pixel:</h4>
                                <?php Tiktok()->render_pixel_id('pixel_id', 'TikTok pixel'); ?>
                                <p class="small">Beta: the TikTok Tag integration is still in beta.</p>
                            </div>
                        </div>
                        <div class="row align-items-center pb-3">
                            <div class="col-12">
                                <h4 class="label">Access Token:</h4>
								<?php Tiktok()->render_text_area_array_item( "server_access_api_token", "Access token" ) ?>
                            </div>
                        </div>
                        <div class="row align-items-center pb-3">
                            <div class="col-12">
                                <h4 class="label">test_event_code :</h4>
								<?php Tiktok()->render_text_input_array_item("test_api_event_code", "Code"); ?>
								<?php Tiktok()->render_text_input_array_item("test_api_event_code_expiration_at", "", 0, true); ?>
                                <small class="form-text">
                                    Use this if you need to test the server-side event. <strong>Remove it after
                                        testing.</strong> The code will auto-delete itself after 24 hours.
                                </small>
                            </div>
                        </div>
                        <div class="row pt-3 pb-3">
                            <div class="col-12">
								<?php Tiktok()->render_switcher_input( "use_server_api" ); ?>
                                <h4 class="switcher-label">Enable TikTok Conversion API</h4>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <?php Tiktok()->render_switcher_input('advanced_matching_enabled'); ?>
                                <h4 class="switcher-label">Enable Advanced Matching</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <p>How to install the TikTok tag and how to enable TikTok API:: <a
                                            href="https://www.youtube.com/watch?v=OCSR6zacnFM" target="_blank">watch
                                        video</a></p>
                            </div>
                        </div>

						<?php if ( isSuperPackActive() ) {
							if ( SuperPack()->getOption( 'additional_ids_enabled' ) ) : ?>
                                <p>
									<?php Tiktok()->render_checkbox_input( 'is_fire_signal', 'Fire the active automated events for this pixel' ); ?>
                                </p>
								<?php if ( isWooCommerceActive() ) : ?>
                                    <p>
										<?php Tiktok()->render_checkbox_input( 'is_fire_woo', 'Fire the WooCommerce events for this pixel' ); ?>
                                    </p>
								<?php endif; ?>
								<?php if ( isEddActive() ) : ?>
                                    <p>
										<?php Tiktok()->render_checkbox_input( 'is_fire_edd', 'Fire the Easy Digital Downloads events for this pixel' ); ?>
                                    </p>
								<?php endif; ?>

                                <p>
                                    <strong>Display conditions:</strong>
                                    <?php
                                    $main_pixel = Tiktok()->getOption( 'main_pixel' );
                                    if ( !empty( $main_pixel ) && isset( $main_pixel[ 0 ] ) ) {
                                        $conditions = SuperPack\SPPixelId::fromArray( json_decode( $main_pixel[ 0 ], true ) );
                                        SuperPack\SpPixelCondition()->renderHtml( $conditions->displayConditions );
                                    } else {
                                        SuperPack\SpPixelCondition()->renderHtml();
                                    } ?>
                                </p>
							<?php endif; ?>

							<?php
							if ( SuperPack()->getOption( 'enable_hide_this_tag_by_tags' ) || SuperPack()->getOption( 'enable_hide_this_tag_by_url' ) ) {
								Tiktok()->render_hide_pixel_block();
							}
						} ?>
                    </div>
                </div>
                <hr>
            <?php endif; ?>
            <?php do_action('pys_admin_pixel_ids'); ?>
        </div>
    </div>
    <!-- <div class="panel panel-primary link_youtube">
        <div class="row">
            <div class="col">
                <p class="text-center">Subscribe to our YouTube Channel to learn how to use the plugin and improve tracking</p>
                <p class="text-center mb-0">
                    <a href="https://www.youtube.com/channel/UCnie2zvwAjTLz9B4rqvAlFQ" class="btn btn-sm btn-save" target="_blank">Go to YouTube</a>
                </p>
            </div>
        </div>
    </div> -->


    <!-- Global Events -->
    <div class="card">
        <div class="card-header has_switch">
            <?php PYS()->render_switcher_input('automatic_events_enabled'); ?>Track key actions with the automatic
            events
            <?php
            if(!PYS()->getOption('automatic_events_enabled')) {
                cardCollapseBtn('style="display:none"');
            } else {
                cardCollapseBtn();
            } ?>
        </div>
        <div class="card-body">
            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_internal_link_enabled'); ?>Track internal
                    links <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php
                    enableEventForEachPixel('automatic_event_internal_link_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>
                    <p>Fires when the website visitor clicks on internal links.</p>
                    <p><strong>Event name: </strong>InternalClick</p>
                    <p><strong>Event name on TikTok: </strong>ClickButton</p>
                    <p><strong>Specific parameters: </strong><i>text, target_url</i></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_outbound_link_enabled'); ?>Track outbound
                    links <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php
                    enableEventForEachPixel('automatic_event_outbound_link_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>
                    <p>Fire this event when the visitor clicks on links to other domains.</p>
                    <p><strong>Event name: </strong>OutboundClick</p>
                    <p><strong>Event name on TikTok: </strong>ClickButton</p>
                    <p><strong>Specific parameters: </strong><i>text, target_url</i></p>
                    <p class="small">*Google Analytics 4 automatically tracks clicks on links to external domains with
                        an event called "click". If you want, you can disable this event for Google Analytics</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_video_enabled'); ?>Track embedded YouTube or
                    Vimeo video views <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php if (Facebook()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
                                <?php Facebook()->render_switcher_input("automatic_event_video_enabled"); ?>
                                <h4 class="switcher-label">Enable on Facebook</h4>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (GA()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
                                <?php GA()->render_switcher_input("automatic_event_video_enabled"); ?>
                                <h4 class="switcher-label">Enable on Google Analytics</h4>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col col-offset-left">
                                <?php GA()->render_checkbox_input("automatic_event_video_youtube_disabled", "disable YouTube videos for Google Analytics"); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (Ads()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
                                <?php Ads()->render_switcher_input("automatic_event_video_enabled"); ?>
                                <h4 class="switcher-label">Enable on Google Ads</h4>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (Bing()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
                                <?php Bing()->render_switcher_input("automatic_event_video_enabled"); ?>
                                <h4 class="switcher-label">Enable on Bing</h4>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (Pinterest()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
                                <?php Pinterest()->render_switcher_input("automatic_event_video_enabled"); ?>
                                <h4 class="switcher-label">Enable on Pinterest</h4>
                            </div>
                        </div>
                    <?php endif; ?>
					<?php if (Tiktok()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
								<?php Tiktok()->render_switcher_input("automatic_event_video_enabled"); ?>
                                <h4 class="switcher-label">Enable on TikTok</h4>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col col-offset-left">
                                <label>Select trigger:</label><?php
								$options = array(
									'0%' => 'Play',
									'10%' => '10%',
									'50%' => '50%',
									'90%' => '90%',
									'100%' => '100%',
								);
								Tiktok()->render_select_input('automatic_event_video_trigger', $options); ?>

                            </div>
                        </div>
					<?php endif; ?>
                    <div class="mt-4">
                        <?php PYS()->render_checkbox_input("automatic_event_video_youtube_enabled", "Track YouTube embedded video"); ?>
                    </div>
                    <div class="mt-2">
                        <?php PYS()->render_checkbox_input("automatic_event_video_vimeo_enabled", "Track Vimeo embedded video"); ?>
                    </div>


                    <br/>
                    <p>Fires when the website visitor watches embedded YouTube or Vimeo videos.</p>
                    <p><strong>Event name: </strong>WatchVideo</p>
                    <p><strong>Specific parameters: </strong><i>progress, video_type, video_title, video_id</i></p>
                    <p class="small">
                        *Google Analytics 4 automatically tracks YouTube embedded videos with two events called "video"
                        and "video_progress". You can disable this event for Google Analytics YouTube videos.
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_tel_link_enabled'); ?>Track tel links <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php
                    enableEventForEachPixel('automatic_event_tel_link_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>
                    <p>Fires when the website visitor clicks on HTML links marked with "tel".</p>
                    <p><strong>Event name: </strong>TelClick</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_email_link_enabled'); ?>Track email links <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php
                    enableEventForEachPixel('automatic_event_email_link_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>
                    <p>Fires when the website visitor clicks on HTML links marked with "email".</p>
                    <p><strong>Event name: </strong>EmailClick</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_form_enabled'); ?>Track Forms <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <p>
                        The Form event will fire when a form is successfully submitted for the following plugins: Contact Form 7, Forminator, WP Forms, Formidable Pro, Ninja Forms, and Fluent Forms. For forms added by different means, we will fire the event when the submit button is clicked. Watch <a href="https://www.youtube.com/watch?v=c4Hrb8WK5bw" target="_blank">this video</a> to learn more.
                    </p>
                    <?php
                    enableEventForEachPixel('automatic_event_form_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>

                    <p>Fires when the website visitor clicks form submit buttons.</p>
                    <br>
                    <?php
                        $eventsFormFactory = apply_filters("pys_form_event_factory",[]);
                        foreach ($eventsFormFactory as $activeFormPlugin) : ?>
                            <p><strong><?php echo $activeFormPlugin->getName(); ?> detected</strong> - we will fire the Form event for each successfully submited form.</p>

                    <?php
                        endforeach;
                        if($eventsFormFactory) :
                    ?>
                    <div class="col">
                        <?php PYS()->render_checkbox_input( 'enable_success_send_form',
                            'Fire the event only for the supported plugins, when the form is succesfully submited.' ); ?>
                    </div>
                    <br>
                            <p>Configure Lead or other events using our <a href="<?php echo buildAdminUrl( 'pixeltrackpro', 'events' ); ?>">events triggers</a>. Learn how from <a href="https://www.youtube.com/watch?v=c4Hrb8WK5bw" target="_blank">this video</a></p>
                    <br>
                        <?php endif; ?>
                    <p><strong>Event name: </strong>Form</p>
                    <p><strong>Event name on TikTok: </strong>FormSubmit</p>
                    <p><strong>Specific parameters: </strong><i>text, from_class, form_id</i></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_signup_enabled'); ?>Track user signup <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php if ( Facebook()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
                                <?php if(isWooCommerceActive()
                                        &&  Facebook()->getOption("woo_complete_registration_fire_every_time")
                                    ) :
                                        Facebook()->render_switcher_input('automatic_event_signup_enabled_disable',false,true);
                                     ?>
                                        <h4 class="switcher-label">Enable on Facebook</h4>
                                        <div class="small ml-2">
                                            Facebook CompleteReservation is fired every time a WooCommerce takes place.<br/>
                                            You can change this from the WooCommerce events
                                            <a href="<?=buildAdminUrl( 'pixeltrackpro', 'woo' )?>" target="_blank">
                                                settings
                                            </a>
                                        </div>
                                <?php else: ?>
                                    <?php Facebook()->render_switcher_input('automatic_event_signup_enabled'); ?>
                                    <h4 class="switcher-label">Enable on Facebook</h4>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ( GA()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
                                <?php GA()->render_switcher_input('automatic_event_signup_enabled'); ?>
                                <h4 class="switcher-label">Enable on Google Analytics</h4>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( Ads()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
                                <?php Ads()->render_switcher_input('automatic_event_signup_enabled'); ?>
                                <h4 class="switcher-label">Enable on Google Ads</h4>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( Bing()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
                                <?php Bing()->render_switcher_input('automatic_event_signup_enabled'); ?>
                                <h4 class="switcher-label">Enable on Bing</h4>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ( Pinterest()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
                                <?php Pinterest()->render_switcher_input('automatic_event_signup_enabled'); ?>
                                <h4 class="switcher-label">Enable on Pinterest</h4>
                            </div>
                        </div>
                    <?php endif; ?>
					<?php if ( Tiktok()->enabled()) : ?>
                        <div class="row">
                            <div class="col">
								<?php Tiktok()->render_switcher_input('automatic_event_signup_enabled'); ?>
                                <h4 class="switcher-label">Enable on TikTok</h4>
                            </div>
                        </div>
					<?php endif; ?>
                    <br/>
                    <p>Fires when the website visitor signup for a WordPress account.</p>
                    <p><strong>Event name: </strong></p>
                    <p>
                        On Google Analytics the event is called sign_up (standard event).<br/>
                        On Google Ads the event is called sign_up (custom event)<br/>
                        On Facebook the event is called CompleteRegistration (standard event).<br/>
                        On Pinterest the event is called Signup (standard event).<br/>
                        On Bing the event is called sign_up (custom event)<br/>
                        On TikTok the event is called SignUp (custom event)
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_login_enabled'); ?>Track user login <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php
                    enableEventForEachPixel('automatic_event_login_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>
                    <p>Fires when the website visitor logins a WordPress account.</p>
                    <p><strong>Event name: </strong></p>
                    <p>On Google Analytics the event is called login (standard event).<br/>
                        On Google Ads the event is called login (custom event)<br/>
                        On Facebook, Pinterest and Bing, the event is called Login (custom event)<br/>
                        On TikTok the event is called Login (custom event).</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_download_enabled'); ?>Track Downloads <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php
                    enableEventForEachPixel('automatic_event_download_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>
                    <div>Extension of files to track as downloads:</div>
                    <?php PYS()->render_tags_select_input('automatic_event_download_extensions'); ?>

                    <p class="mt-2">Fires when the website visitor open files with the designated format.</p>
                    <p><strong>Event name: </strong>Download</p>
                    <p><strong>Specific parameters: </strong><i>download_type, download_name, download_url</i></p>
                    <p class="small">
                        *Google Analytics 4 automatically tracks this action with an event called "file_download". If you want,
                        you can disable this event for Google Analytics
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_comment_enabled'); ?>Track comments <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php
                    enableEventForEachPixel('automatic_event_comment_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>
                    <p>Fires when the website visitor ads a comment.</p>
                    <p><strong>Event name: </strong>Comment</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_adsense_enabled'); ?>Track AdSense <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php
                    enableEventForEachPixel('automatic_event_adsense_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>
                    <p>Fires when the website visitor clicks on an AdSense ad.</p>
                    <p><strong>Event name: </strong>AdSense</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_scroll_enabled'); ?>Track page scroll <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php
                    enableEventForEachPixel('automatic_event_scroll_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>
                    <div class="mb-2 form-inline">
                        <label>Trigger for scroll value</label>
                        <?php PYS()->render_number_input('automatic_event_scroll_value', '', false, 100); ?>
                        <div>%</div>
                    </div>

                    <p>Fires when the website visitor scrolls the page.</p>
                    <p><strong>Event name: </strong>PageScroll</p>
                    <p class="small">*Google Analytics 4 automatically tracks 90% page scroll with an event called "scroll".
                        If you want, you can disable this event for Google Analytics</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_time_on_page_enabled'); ?>Track time on page <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php
                    enableEventForEachPixel('automatic_event_time_on_page_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>
                    <div class="mb-2 form-inline">
                        <label>Trigger for time</label>
                        <?php PYS()->render_number_input('automatic_event_time_on_page_value', '', false, 100); ?>
                        <div>seconds</div>
                    </div>
                    <p><strong>Event name: </strong>TimeOnPage</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header has_switch">
                    <?php PYS()->render_switcher_input('automatic_event_search_enabled'); ?>Track searches <?php cardCollapseBtn(); ?>
                </div>
                <div class="card-body">
                    <?php
                    enableEventForEachPixel('automatic_event_search_enabled', true, true, true, true, true, true);
                    ?>
                    <br/>
                    <p><strong>Event name: </strong></p>
                    <p>
                        On Google Analytics the event is called search (standard event).<br/>
                        On Google Ads the event is called search (custom event)<br/>
                        On Facebook, Pinterest called Search (standard event).<br/>
                        On Bing the event is called search (custom event).<br/>
                        On TikTok the event is called Search (standard event).
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- Dynamic Ads for Blog Setup -->
    <div class="card">
        <div class="card-header has_switch">
            <?php PYS()->render_switcher_input('fdp_enabled'); ?> Dynamic Ads for Blog Setup <?php cardCollapseBtn(); ?>
        </div>
        <div class="card-body">
            <div class="row mt-3">
                <div class="col-11">
                    This setup will help you to run Facebook Dynamic Product Ads for your blog content.
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <a href="https://www.webmxt.com/facebook-dynamic-product-ads-for-wordpress" target="_blank">Click
                        here to learn how to do it</a>
                </div>
            </div>
            <?php if (Facebook()->enabled()) : ?>
                <hr/>
                <div class="row mt-3">
                    <div class="col">
                        <?php Facebook()->render_switcher_input('fdp_use_own_pixel_id'); ?>
                        <h4 class="switcher-label">Fire this events just for this Pixel ID. Remember to connect this
                            Pixel ID to your <a
                                    href="https://www.webmxt.com/wordpress-feed-for-facebook-dynamic-ads"
                                    target="_blank">blog related Product Catalog</a></h4>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">
                        <label>Meta Pixel (formerly Facebook Pixel) ID:</label>
                        <?php Facebook()->render_text_input('fdp_pixel_id'); ?>
                    </div>
                </div>


                <hr/>

                <div class="row mt-5">
                    <div class="col">
                        <label>Content type:</label><?php
                        $options = array(
                            'product' => 'Product',
                            '' => 'Empty'
                        );
                        Facebook()->render_select_input('fdp_content_type', $options); ?>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col">
                        <label>Currency:</label><?php
                        $options = array();
                        $cur = getPysCurrencySymbols();
                        foreach ($cur as $key => $val) {
                            $options[$key] = $key;
                        }
                        Facebook()->render_select_input('fdp_currency', $options); ?>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col">
                        <?php Facebook()->render_switcher_input('fdp_view_content_enabled'); ?>
                        <h4 class="switcher-label">Enable the ViewContent on every blog page</h4>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <?php Facebook()->render_switcher_input('fdp_view_category_enabled'); ?>
                        <h4 class="switcher-label">Enable the ViewCategory on every blog categories page</h4>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-11">
                        <?php Facebook()->render_switcher_input('fdp_add_to_cart_enabled'); ?>
                        <h4 class="switcher-label">Enable the AddToCart event on every blog page</h4>
                    </div>

                    <div class="col-11 form-inline col-offset-left">
                        <label>Value:</label>
                        <?php Facebook()->render_number_input('fdp_add_to_cart_value', "Value"); ?>
                    </div>

                    <div class="col-11 form-inline col-offset-left">
                        <label>Fire the AddToCart event</label>

                        <?php
                        $options = array(
                            'scroll_pos' => 'Page Scroll',
                            'comment' => 'User commented',
                            'css_click' => 'Click on CSS selector',
                            'ad_sense_click' => 'AdSense Clicks',
                            'video_play' => 'YouTube or Vimeo Play',
                            //Default event fires
                        );
                        Facebook()->render_select_input('fdp_add_to_cart_event_fire', $options); ?>
                        <span id="fdp_add_to_cart_event_fire_scroll_block">
                        <?php Facebook()->render_number_input('fdp_add_to_cart_event_fire_scroll', 50); ?> <span>%</span>
                    </span>

                        <?php Facebook()->render_text_input('fdp_add_to_cart_event_fire_css', "CSS selector"); ?>

                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-11">
                        <?php Facebook()->render_switcher_input('fdp_purchase_enabled'); ?>
                        <h4 class="switcher-label">Enable the Purchase event on every blog page</h4>
                    </div>
                    <div class="col-11 form-inline col-offset-left">
                        <label>Value:</label>
                        <?php Facebook()->render_number_input('fdp_purchase_value', "Value"); ?>
                    </div>
                    <div class="col-11 form-inline col-offset-left">
                        <label>Fire the Purchase event</label>

                        <?php
                        $options = array(
                            'scroll_pos' => 'Page Scroll',
                            'comment' => 'User commented',
                            'css_click' => 'Click on CSS selector',
                            'ad_sense_click' => 'AdSense Clicks',
                            'video_play' => 'YouTube or Vimeo Play',
                            //Default event fires
                        );
                        Facebook()->render_select_input('fdp_purchase_event_fire', $options); ?>
                        <span id="fdp_purchase_event_fire_scroll_block">
                        <?php Facebook()->render_number_input('fdp_purchase_event_fire_scroll', 50); ?> <span>%</span>
                    </span>

                        <?php Facebook()->render_text_input('fdp_purchase_event_fire_css', "CSS selector"); ?>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col">
                        <strong>You need to upload your blog posts into a Facebook Product Catalog.</strong> You can do
                        this with our dedicated plugin:
                        <a href="https://www.webmxt.com/wordpress-feed-facebook-dpa" target="_blank">Click
                            Here</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Actions info -->
    <div class="card">
        <div class="card-header">
            Active Events:
        </div>
        <div class="card-body show" style="display: block;">
            <?php
            $customCount = EventsCustom()->getCount();
            //$customFdp = EventsFdp()->getCount();
            $autoEvents = EventsAutomatic()->getCount();
            $wooEvents = EventsWoo()->getCount();
            $eddEvents = EventsEdd()->getCount();


            $total = $customCount + $autoEvents + $wooEvents + $eddEvents;
            ?>
            <p><strong>You have <?= $total ?> active events in total.</strong></p>
            <p>You have <?= $autoEvents ?> automated active events. You can control them on this page.</p>
            <p>You have <?= $customCount ?> manually added active events. You can control them on the <a
                        href="<?= buildAdminUrl('pixeltrackpro', 'events') ?>">Events page</a>.</p>
            <?php if (isWooCommerceActive()) : ?>
                <p>You have <?= $wooEvents ?> WooCommerce active events. You can control them on the <a
                            href="<?= buildAdminUrl('pixeltrackpro', 'woo') ?>">WooCommerce page</a>.</p>
            <?php endif; ?>
            <?php if (isEddActive()) : ?>
                <p>You have <?= $eddEvents ?> EDD active events. You can control them on the <a
                            href="<?= buildAdminUrl('pixeltrackpro', 'edd') ?>">EDD page</a>.</p>
            <?php endif; ?>
            <p class="mt-5 small">We count each manually added event, regardless of its name or targeted tag.</p>
            <p class="small">We don't count the Dynamic Ads for Blog events.</p>
        </div>
    </div>

    <h2 class="section-title mt-3">Global Parameters</h2>

    <!-- About params -->
    <div class="card">
        <div class="card-header">
            About Parameters:
        </div>
        <div class="card-body show" style="display: block;">
            <p>Parameters add extra information to events.

            <p>They help you create Custom Audiences or Custom Conversions on Facebook, Goals, and Audiences on Google,
                Audiences on Pinterest, Conversions on Bing.</p>

            <p>The plugin tracks the following parameters by default for all the events and for all installed
                tags: <i>page_title, post_type, post_id, landing_page, event_url, user_role, plugin, event_time (pro),
                    event_day (pro), event_month (pro), traffic_source (pro), UTMs (pro).</i></p>

            <p>Facebook, Pinterest, and Google Ads Page View event also tracks the following parameters: <i>tags,
                    category</i>.</p>

            <p>You can add extra parameters to events configured on the Events tab. WooCommerce or Easy Digital
                Downloads events will have the e-commerce parameters specific to each tag.</p>

            <p>The Search event has the specific search parameter.</p>

            <p>The automatic events have various specific parameters, depending on the action that fires the event.</p>

        </div>
    </div>

    <!-- Control global param -->
    <div class="card">
        <div class="card-header">
            Control the Global Parameters <?php cardCollapseBtn(); ?>
        </div>
        <div class="card-body">
            <div class="row mt-3 mb-3">
                <div class="col-12">
                    You will have these parameters for all events, and for all installed tags. We recommend to
                    keep these parameters active, but if you start to get privacy warnings about some of them,
                    you can turn those parameters OFF.
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <hr>
                    <?php PYS()->render_switcher_input("enable_page_title_param"); ?>
                    <h4 class="switcher-label">page_title</h4>
                    <hr>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <?php PYS()->render_switcher_input("enable_post_type_param"); ?>
                    <h4 class="switcher-label">post_type</h4>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('enable_post_category_param'); ?>
                    <h4 class="switcher-label">post_category</h4>
                    <hr>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <?php PYS()->render_switcher_input("enable_post_id_param"); ?>
                    <h4 class="switcher-label">post_id</h4>
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('enable_content_name_param'); ?>
                    <h4 class="switcher-label">content_name</h4>
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('enable_event_url_param'); ?>
                    <h4 class="switcher-label">event_url</h4>
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('enable_user_role_param'); ?>
                    <h4 class="switcher-label">user_role</h4>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-inline">
                        <?php PYS()->render_switcher_input( 'send_external_id' ); ?>
                        <h4 class="switcher-label">external_id</h4>
                    </div>

                    <small class="mt-1">We will store it in cookie called pbid</small>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-inline">
                        <label>external_id expire cookie:</label>
                        <?php PYS()->render_number_input( 'external_id_expire', '', false, 365, 1); ?>
                    </div>

                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('enable_lading_page_param'); ?>
                    <h4 class="switcher-label">landing_page (PRO)</h4>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('enable_event_time_param'); ?>
                    <h4 class="switcher-label">event_time (PRO)</h4>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('enable_event_day_param'); ?>
                    <h4 class="switcher-label">event_day (PRO)</h4>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('enable_event_month_param'); ?>
                    <h4 class="switcher-label">event_month (PRO)</h4>
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('track_traffic_source'); ?>
                    <h4 class="switcher-label">traffic_source (PRO)</h4>
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('track_utms'); ?>
                    <h4 class="switcher-label">UTMs (PRO)</h4>
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('enable_tags_param'); ?>
                    <h4 class="switcher-label">tags (PRO)</h4>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php PYS()->render_switcher_input('enable_categories_param'); ?>
                    <h4 class="switcher-label">categories (PRO)</h4>
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?php PYS()->renderDummySwitcher(true); ?>
                    <h4 class="switcher-label">search (mandatory)</h4>
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?php PYS()->renderDummySwitcher(true); ?>
                    <h4 class="switcher-label">plugin (mandatory)</h4>
                    <hr>
                </div>
            </div>

        </div>
    </div>

    <h2 class="section-title mt-3 ">Global Settings</h2>

    <div class="panel">
        <div class="row mb-3">
            <div class="col-12">
                <?php PYS()->render_switcher_input("server_event_use_ajax" ); ?>
                <h4 class="switcher-label">Use Ajax when API is enabled, or when external_id's are used. Keep this option active if you use a cache.</h4>
                <div><small class="mt-1">Use Ajax when Meta conversion API, or Pinterest API are enabled, or when external_id's are used. This helps serving unique event_id values for each pair of browser/server events, ensuring deduplication works. It also ensures uniques external_id's are used for each user. Keep this option active if you use a cache solution that can serve the same event_id or the same external_id multiple times.</small></div>
                <hr>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <?php PYS()->render_switcher_input('debug_enabled'); ?>
                <h4 class="switcher-label">Debugging Mode. You will be able to see details about the events inside
                    your browser console (developer tools).</h4>
            </div>
        </div>


        <div class="row  mb-3">
            <div class="col">
                <?php PYS()->render_switcher_input('enable_remove_source_url_params'); ?>
                <h4 class="switcher-label">
                    Remove URL parameters from <i>event_source_url</i>. Event_source_url is required
                    for Facebook CAPI events. In order to avoid sending parameters that might contain private
                    information,
                    we recommend to keep this option ON.
                </h4>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <?php PYS()->render_switcher_input('enable_remove_target_url_param'); ?>
                <h4 class="switcher-label">Remove target_url parameters.</h4>

            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <?php PYS()->render_switcher_input('enable_remove_download_url_param'); ?>
                <h4 class="switcher-label">Remove download_url parameters.</h4>

            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <div class="form-inline">
                    <?php PYS()->render_switcher_input('compress_front_js'); ?>
                    <h4 class="switcher-label">Compress frontend js</h4>
                </div>

                <small class="mt-1">Compress JS files (please test all your events if you enable this option because it can create conflicts with various caches).</small>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <div class="form-inline">
                    <?php PYS()->render_switcher_input('hide_version_plugin_in_console'); ?>
                    <h4 class="switcher-label">Remove the name of the plugin from the console</h4>
                </div>

                <small class="mt-1">Once ON, we remove all mentions about the plugin or add-ons from the console.</small>
            </div>
        </div>
        <hr>
        <div class="row mb-3">
            <div class="col">
                <?php PYS()->render_switcher_input('enable_auto_save_advance_matching'); ?>
                <h4 class="switcher-label">Advanced user-data detection <a href="https://www.youtube.com/watch?v=snUKcsTbvCk" target="_blank">Watch video</a></h4>

                <small class="mt-1 d-block">
                    The plugin will try to detect user-related data like email, phone, first name, or last name and use it for subsequent Meta CAPI events personal parameters, and Meta browser events Advanced Matching. This data is also used for Google Ads enhanced converions, Pinterest and TikTok events.</small>



            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <h4 class="switcher-label">Fn:</h4>
                <?php
                $default_name_input = ["first_name","first-name","first name","name"];
                $eventsFormFactory = apply_filters("pys_form_event_factory",[]);
                foreach ($eventsFormFactory as $activeFormPlugin) :
                    if(isset($activeFormPlugin->getDefaultMatchingInput()['first_name']))
                    {
                        $default_name_input = array_unique( array_merge( $default_name_input , $activeFormPlugin->getDefaultMatchingInput()['first_name'] ) );
                    }
                endforeach;
                PYS()->render_tags_select_input('advance_matching_fn_names',false, $default_name_input);

                ?>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <h4 class="switcher-label">Ln:</h4>
                <?php
                $default_last_name_input = ["last_name","last-name","last name"];
                foreach ($eventsFormFactory as $activeFormPlugin) :
                    if(isset($activeFormPlugin->getDefaultMatchingInput()['last_name']))
                    {
                        $default_last_name_input = array_unique( array_merge( $default_last_name_input , $activeFormPlugin->getDefaultMatchingInput()['last_name'] ) );
                    }
                endforeach;
                PYS()->render_tags_select_input('advance_matching_ln_names',false, $default_last_name_input);
                ?>
            </div>
        </div>
        <div class="row mb-3 advance_matching_bottom_margin">
            <div class="col">
                <h4 class="switcher-label">Tel:</h4>
                <?php
                $default_tel_input = ["phone","tel"];
                foreach ($eventsFormFactory as $activeFormPlugin) :
                    if(isset($activeFormPlugin->getDefaultMatchingInput()['tel']))
                    {
                        $default_tel_input = array_unique( array_merge( $default_tel_input , $activeFormPlugin->getDefaultMatchingInput()['tel'] ) );
                    }
                endforeach;
                PYS()->render_tags_select_input('advance_matching_tel_names',false,$default_tel_input);
                ?>
                <hr>
            </div>
        </div>



        <div class="row mt-3">
            <div class="col">
                <div class="form-inline">
                    <label>First Visit Options:</label>
                    <?php PYS()->render_number_input('cookie_duration', '', false, null, 1); ?>
                    <label>day(s)</label>
                </div>
                <small class="mt-1">Define for how long we will store cookies for the "First Visit" attribution model.
                    Used for events parameters (<i>landing page, traffic source, UTMs</i>) and WooCommerce or EDD Reports.
                </small>

            </div>
        </div>
        <div class="row mt-2">
            <div class="col">
                <div class="form-inline">
                    <label>Last Visit Options:</label>
                    <?php PYS()->render_number_input('last_visit_duration', '', false, null, 1); ?>
                    <label>min</label>
                </div>

                <small class="mt-1">Define for how long we will store the cookies for the "Last Visit" attribution model.
                    Used for events parameters (<i>landing page, traffic source, UTMs</i>) and WooCommerce or EDD Reports.</small>

            </div>
        </div>
        <div class="row">
            <div class="col collapse-inner">
                    <label>Attribution model for events parameters:</label>
                    <div class="custom-controls-stacked">
                        <?php PYS()->render_radio_input( 'visit_data_model', 'first_visit',
                            'First Visit' ); ?>
                        <?php PYS()->render_radio_input( 'visit_data_model', 'last_visit',
                            'Last Visit' ); ?>
                    </div>

            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <hr/>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <?php PYS()->render_switcher_input('block_robot_enabled'); ?>
                <h4 class="switcher-label">Disable the plugin for known web crawlers</h4>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <?php PYS()->render_switcher_input('block_ip_enabled'); ?>
                <h4 class="switcher-label">Disable the plugin for these IP addresses:</h4>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <?php PYS()->render_tags_select_input('blocked_ips',false); ?>
            </div>
        </div>

        <hr>
        <div class="row form-group">
            <div class="col">
                <h4 class="label">Ignore these user roles from tracking:</h4>
                <?php PYS()->render_multi_select_input('do_not_track_user_roles', getAvailableUserRoles()); ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h4 class="label">Permissions:</h4>
                <?php PYS()->render_multi_select_input('admin_permissions', getAvailableUserRoles()); ?>
            </div>
        </div>

    </div>

    <hr>
    <div class="row justify-content-center">
        <div class="col-4">
            <button class="btn btn-block btn-sm btn-save">Save Settings</button>
        </div>
    </div>

    <script>
        jQuery(document).ready(function ($) {
            $(document).on('click', '.remove-meta-row', function () {
                var $row = $(this).closest('.row');
                $row.next().remove();
                $row.remove();
            });
        });
    </script>

    <?php function enableEventForEachPixel($event, $fb = true, $ga = true, $ads = true, $bi = true, $tic = true, $pin = true)
{ ?>
    <?php if ($fb && Facebook()->enabled()) : ?>
    <div class="row">
        <div class="col">
            <?php Facebook()->render_switcher_input($event); ?>
            <h4 class="switcher-label">Enable on Facebook</h4>
        </div>
    </div>
<?php endif; ?>
    <?php if ($ga && GA()->enabled()) : ?>
    <div class="row">
        <div class="col">
            <?php GA()->render_switcher_input($event); ?>
            <h4 class="switcher-label">Enable on Google Analytics</h4>
        </div>
    </div>
<?php endif; ?>

    <?php if ($ads && Ads()->enabled()) : ?>
    <div class="row">
        <div class="col">
            <?php Ads()->render_switcher_input($event); ?>
            <h4 class="switcher-label">Enable on Google Ads</h4>
        </div>
    </div>
<?php endif; ?>

    <?php if ($bi && Bing()->enabled()) : ?>
    <div class="row">
        <div class="col">
            <?php Bing()->render_switcher_input($event); ?>
            <h4 class="switcher-label">Enable on Bing</h4>
        </div>
    </div>
<?php endif; ?>
    <?php if ($pin && Pinterest()->enabled()) : ?>
    <div class="row">
        <div class="col">
            <?php Pinterest()->render_switcher_input($event); ?>
            <h4 class="switcher-label">Enable on Pinterest</h4>
        </div>
    </div>
<?php endif; ?>
    <?php if ($tic && Tiktok()->enabled()) : ?>
    <div class="row">
        <div class="col">
            <?php Tiktok()->render_switcher_input($event); ?>
            <h4 class="switcher-label">Enable on TikTok</h4>
        </div>
    </div>
<?php endif; ?>
    <?php
}
