<?php
function article_func( $atts ) {
    $a = shortcode_atts( array(
        'shopid' => '',
        'locale' => '',
        'articleid' => '',
        'producttypeid' => '',
        'apperanceid' => '',
        'width' => '130',
        'basedesignerurl' => '/designer',
    ), $atts );

    $shopId = $a['shopid'];// 'http://monshirtdk.spreadshirt.dk';
    $locale = '?locale='.$a['locale'];//'/dk/DK';
    $articleId = $a['articleid'];
    $productTypeId = $a['producttypeid'];
    $apperanceId = $a['apperanceid'];
    $width = $a['width'];
    $designerUrl = $a['basedesignerurl'];

    DrawArticle($locale,$shopId,$articleId,$productTypeId,$apperanceId,$width,$designerUrl);
}
?>