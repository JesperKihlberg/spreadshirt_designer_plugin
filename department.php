<?php
function department_func( $atts ) {
    $a = shortcode_atts( array(
        'shopid' => '',
        'locale' => '',
        'departmentid' => '1',
        'categorycount' => '10',
        'departmenturl' => '/index.php/maend',
        'basecategoryurl' => '/index.php/kategori',
    ), $atts );
  
  $shopId = $a['shopid'];// 'http://monshirtdk.spreadshirt.dk';
  $locale = '?locale='.$a['locale'];//'/dk/DK';
  $departmentId = $a['departmentid'];
  $categorycount = $a['categorycount'];
  $departmenturl = $a['departmenturl'];
  $basecategoryurl = $a['basecategoryurl'];
  DrawDepartmentId($locale,$shopId, $departmentId,$categorycount,$departmenturl, $basecategoryurl);
}
?>
