<?php
function products_func( $atts ) {
    $a = shortcode_atts( array(
        'shopid' => '',
        'locale' => '',
    ), $atts );
  
  $shopId = $a['shopid'];// 'http://monshirtdk.spreadshirt.dk';
  $locale = '?locale='.$a['locale'];//'/dk/DK';
//  $locale = '?locale=dk_DK';
  DrawDepartments($locale,$shopId);
}
