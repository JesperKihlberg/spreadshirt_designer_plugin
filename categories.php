<?php
function category_func( $atts ) {
    $a = shortcode_atts( array(
        'shopid' => '',
        'locale' => '',
        'departmenturl' => '/index.php/maend',
        'baseproducturl' => '/index.php/produkter',
        'basecategoryurl' => '/index.php/kategori',
    ), $atts );

  $shopid = $a['shopid'];// 'http://monshirtdk.spreadshirt.dk';
  $locale = '?locale='.$a['locale'];//'/dk/DK';
  $departmenturl = $a['departmenturl'];
  $basecategoryurl = $a['basecategoryurl'];
  $baseproducturl = $a['baseproducturl'];

  $departmentid=$_GET['departmentid'];
  $categoryid=$_GET['categoryid'];
  //echo $departmentId;
  //echo $categoryId;
  DrawCategory($locale,$shopid, $departmentid, $categoryid,$departmenturl,$basecategoryurl,$baseproducturl);
}

