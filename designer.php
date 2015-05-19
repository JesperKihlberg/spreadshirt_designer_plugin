 <?php

function designer_func( $atts ) {
    $a = shortcode_atts( array(
        'shopurl' => '',
        'language' => '',
    ), $atts );
$baseShopUrl = $a['shopurl'];// 'http://monshirtdk.spreadshirt.dk';
$language = $a['language'];//'/dk/DK';

$urlExtension='';
if (isset($_GET['productid']))
{
  $urlExtension = $urlExtension.'/productType/'.$_GET['productid'];
}
if (isset($_GET['product']))
{
  $urlExtension = $urlExtension.'/product/'.$_GET['product'];
}
if (isset($_GET['designid']))
{
  $urlExtension = $urlExtension.'/design/'.$_GET['designid'];
}
if (isset($_GET['productcolor']))
{
  $urlExtension = $urlExtension.'/productColor/'.$_GET['productcolor'];
}
if (isset($_GET['designcolor1']))
{
  $urlExtension = $urlExtension.'/designColor1/'.$_GET['designcolor1'];
}
if (isset($_GET['designcolor2']))
{
  $urlExtension = $urlExtension.'/designColor2/'.$_GET['designcolor2'];
}
if (isset($_GET['department']))
{
  $urlExtension = $urlExtension.'/department/'.$_GET['department'];
}

echo '<iframe height="1800" width="670" src="',$baseShopUrl,$language,'/Shop/Index/Index',$urlExtension,'/" name="Spreadshop" id="Spreadshop" frameborder="0" onload="window.scrollTo(0, 0);"></iframe>';

//    return RenderDesigner();
}

