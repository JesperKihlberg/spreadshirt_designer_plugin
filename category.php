<?php
function category_func( $atts ) {
    $a = shortcode_atts( array(
        'shopid' => '',
        'locale' => '',
        'baseproducturl' => '/index.php/produkt',
        'basecategoryurl' => '/index.php/kategori',
        'departmentid' => '',
        'categoryid' => '',
    ), $atts );

    $shopid = $a['shopid'];// 'http://monshirtdk.spreadshirt.dk';
    $locale = '?locale='.$a['locale'];//'/dk/DK';
    $basecategoryurl = $a['basecategoryurl'];
    $baseproducturl = $a['baseproducturl'];
    $categoryid=$a['categoryid'];
    $departmentid=$a['departmentid'];

    if($departmentid==''){
        $departmentid=$_GET['departmentid'];
    }
    if($categoryid==''){
        $categoryid=$_GET['categoryid'];
    }
    echo $departmentId;
    echo $categoryId;
    DrawCategory($locale,$shopid, $departmentid, $categoryid,$basecategoryurl,$baseproducturl);
}
?>
