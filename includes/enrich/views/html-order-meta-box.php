<?php

use function PixelYourSite\getWooUserStat;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
use function PixelYourSite\isWooUseHPStorage;
use function PixelYourSite\PYS;


include_once "function-helper.php";
if(!isset($orderId)) return;
$order = wc_get_order($orderId);
$data = array();
$dataAnalytics = array();
$traking_type = 'Not tracked';
if(isWooUseHPStorage()) {
    // WooCommerce >= 3.0
    if($order) {
        $data = $order->get_meta( 'pys_enrich_data', true );
        if($order->get_meta('_pys_advance_purchase_event_fired', true)){
            $traking_type = 'Advanced Purchase Tracking (APT)';
        }elseif($order->get_meta('_pys_purchase_event_fired', true)){
            $traking_type = 'Tag or API';
        }
    }

} else {
    // WooCommerce < 3.0
    if(get_post_meta($orderId, "pys_enrich_data", true))
    {
        $data = get_post_meta($orderId, "pys_enrich_data",true);
    }
    if(get_post_meta($orderId,'_pys_advance_purchase_event_fired', true)){
        $traking_type = 'Advanced Purchase Tracking (APT)';
    }elseif(get_post_meta($orderId,'_pys_purchase_event_fired', true)){
        $traking_type = 'Tag or API';
    }
}

$dataAnalytics = getWooUserStat($orderId);
if($dataAnalytics['orders_count'] == 0) {
    $dataAnalytics = array(
        'orders_count' => 'Guest order',
        'avg_order_value' => 'Guest order',
        'ltv' => 'Guest order',
    );
}


if(PYS()->getOption('woo_enabled_show_tracking_type') && (isset($render_tracking) && $render_tracking)){
    ?>
    <table class="type_tracking" style="margin:20px 10px">
        <tr>
            <td class="type_tracking_title" style="font-size: 13px; font-weight: bold;"><?php _e('Tracking type:', 'pys'); ?> </td>
            <td class="type_tracking_value" style="font-size: 13px; font-weight: normal; text-decoration: underline"><?php echo $traking_type; ?></td>
        </tr>
    </table>
    <?php
}

if($dataAnalytics && is_array($dataAnalytics) && is_array($data)) {
    $data = array_merge($data,$dataAnalytics);
}

if($data && is_array($data) ) :
    ?>
    <style>
        table.pys_order_meta {
            width: 100%;text-align:left
        }
        table.pys_order_meta td.border span {
            border-top: 1px solid #f1f1f1;
            display: block;
        }
        table.pys_order_meta th,
        table.pys_order_meta td {
            padding:10px
        }
    </style>
    <table class="pys_order_meta">
            <tr>
                <td colspan="2" ><strong>FIRST VISIT</strong></td>
            </tr>
            <tr>
                <td colspan="2" class="border"><span></span></td>
            </tr>
            <tr >
                <th>Landing Page:</th>
                <td><a href="<?=!empty($data['pys_landing']) ? $data['pys_landing'] : ""; ?>" target="_blank" ><?=!empty($data['pys_landing']) ? $data['pys_landing'] : ""; ?></a></td>
            </tr>
            <tr>
                <th>Traffic source:</th>
                <td><?=!empty($data['pys_source']) ? $data['pys_source'] : ""?></td>
            </tr>
            <?php

            if(!empty($data['pys_utm'])) {
                $utms = explode("|",$data['pys_utm']);
                \PixelYourSite\Enrich\printUtm($utms);
            }

            ?>
            <tr>
                <td colspan="2" class="border"><span></span></td>
            </tr>
            <tr>
                <td colspan="2" ><strong>LAST VISIT</strong></td>
            </tr>
            <tr>
                <td colspan="2" class="border"><span></span></td>
            </tr>
            <tr >
                <?php
                $lastLanding = isset($data['last_pys_landing']) ? $data['last_pys_landing'] : ""; ?>
                <th>Landing Page:</th>
                <td><a href="<?=$lastLanding?>" target="_blank" ><?=$lastLanding?></a></td>
            </tr>
            <tr>
                <th>Traffic source:</th>
                <td><?= isset($data['last_pys_source']) ? $data['last_pys_source'] : ""?></td>
            </tr>
            <?php
            if(!empty($data['last_pys_utm'])) {
                $utms = explode("|",$data['last_pys_utm']);
                \PixelYourSite\Enrich\printUtm($utms);
            }

            ?>
            <tr>
                <td colspan="2" class="border"><span></span></td>
            </tr>
            <?php
            if(!empty($data['pys_browser_time'])) :
                $userTime = explode("|",$data['pys_browser_time']);
                ?>
                <tr >
                    <th>Client's browser time</th>
                    <td></td>
                </tr>
                <tr >
                    <th>Hour:</th>
                    <td><?=$userTime[0] ?></td>
                </tr>
                <tr >
                    <th>Day:</th>
                    <td><?=$userTime[1] ?></td>
                </tr>
                <tr >
                    <th>Month:</th>
                    <td><?=$userTime[2] ?></td>
                </tr>
            <?php endif; ?>

            <tr>
                <td colspan="2" class="border"<td><span></span></td>
            </tr>


        <?php if( !isset($sent_to_admin)) : ?>
            <tr >
                <th>Number of orders:</th>
                <td><?=!empty($data['orders_count']) ? $data['orders_count'] : ""?></td>
            </tr>
            <tr >
                <th>Lifetime value:</th>
                <td><?=!empty($data['ltv']) ? $data['ltv'] : ""?></td>
            </tr>
            <tr >
                <th>Average order value:</th>
                <td><?=!empty($data['avg_order_value']) ? $data['avg_order_value'] : ""?></td>
            </tr>
        <?php endif; ?>
    </table>

<?php else: ?>
    <h2>No data</h2>
<?php endif; ?>
