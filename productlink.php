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

    DrawProduct($locale,$shopId,$productTypeId,$baseproducturl,$drawHeader)
}
?>