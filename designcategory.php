<?php
function designcategory_func( $atts ) {
    $a = shortcode_atts( array(
        'shopid' => '',
        'locale' => '',
        'designcategoryid' => '',
        'producttypeid' => '',
        'width' => '130',
    ), $atts );

    $shopId = $a['shopid'];// 'http://monshirtdk.spreadshirt.dk';
    $locale = '?locale='.$a['locale'];//'/dk/DK';
    $designCategoryId = $a['designcategoryid'];
    $productTypeId = $a['producttypeid'];
    $width = $a['width'];
 
    ob_start();
    DrawDesignCategory($locale,$shopId,$designCategoryId,$productTypeId,$width);
    $output_string=ob_get_contents();;
    ob_end_clean();

    return $output_string;
}
?>