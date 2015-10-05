<?php
function productlink_func( $atts ) {
    $a = shortcode_atts( array(
        'shopid' => '',
        'locale' => '',
        'producttypeid' => '',
        'drawheader' => 'true',
        'baseproducturl' => '/product',
    ), $atts );

    $shopId = $a['shopid'];// 'http://monshirtdk.spreadshirt.dk';
    $locale = '?locale='.$a['locale'];//'/dk/DK';
    $productTypeId = $a['producttypeid'];
    $drawHeader = $a['drawheader'];
    $baseproducturl = $a['baseproducturl'];

    ob_start();
    DrawProduct($locale,$shopId,$productTypeId,$baseproducturl,$drawHeader);
    $output_string=ob_get_contents();;
    ob_end_clean();

    return $output_string;
}
?>