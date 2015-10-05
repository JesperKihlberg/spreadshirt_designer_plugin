<?php
function article_func( $atts ) {
    $a = shortcode_atts( array(
        'shopid' => '',
        'locale' => '',
        'productid' => '',
        'producttypeid' => '',
        'apperanceid' => '',
        'width' => '130',
        'basedesignerurl' => '/designer',
    ), $atts );

    $shopId = $a['shopid'];// 'http://monshirtdk.spreadshirt.dk';
    $locale = '?locale='.$a['locale'];//'/dk/DK';
    $productId = $a['productid'];
    $productTypeId = $a['producttypeid'];
    $apperanceId = $a['apperanceid'];
    $width = $a['width'];
    $designerUrl = $a['basedesignerurl'];

    ob_start();
    DrawArticle($locale,$shopId,$productId,$productTypeId,$apperanceId,$width,$designerUrl);
    $output_string=ob_get_contents();;
    ob_end_clean();

    return $output_string;
}
?>