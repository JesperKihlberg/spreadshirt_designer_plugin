<?php
function product_func( $atts ) {
    $a = shortcode_atts( array(
        'shopid' => '',
        'locale' => '',
        'baseproducturl' => '/index.php/produkt',
        'basecategoryurl' => '/index.php/kategori',
        'basedesignerurl' => '/index.php/designer',
    ), $atts );

  $shopId = $a['shopid'];// 'http://monshirtdk.spreadshirt.dk';
  $locale = '?locale='.$a['locale'];//'/dk/DK';
  $basecategoryurl = $a['basecategoryurl'];
  $baseproducturl = $a['baseproducturl'];
  $basedesignerurl = $a['basedesignerurl'];

  $departmentid=$_GET['departmentid'];
  $categoryid=$_GET['categoryid'];
  $productid=$_GET['productid'];
  //echo $departmentId;
  //echo $categoryId;
  ob_start();
  DrawProductDetail($locale,$shopId, $departmentid, $categoryid,$productid,$basecategoryurl,$baseproducturl,$basedesignerurl);
  $output_string=ob_get_contents();;
  ob_end_clean();

  return $output_string;
}
?>