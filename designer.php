
<html>
 <head>
  <title>Designer</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link media="all" type="text/css" href="main.css" rel="stylesheet"></link>

 </head>
 <body>
 <?php
include("functions.php");
$locale='?locale=dk_DK';

$productId=$_GET['productid'];
$designId=$_GET['designid'];
echo $productId;
echo $designId;

$urlExtension = '';
if (isset($_GET['productid']))
{
  $urlExtension = $urlExtension.'/productType/'.$productId;
}
if (isset($_GET['designid']))
{
  $urlExtension = $urlExtension.'/design/'.$designId;
}

echo '<iframe height="1800" width="670" src="http://monshirtdk.spreadshirt.dk/dk/DK/Shop/Index/Index',$urlExtension,'/" name="Spreadshop" id="Spreadshop" frameborder="0" onload="window.scrollTo(0, 0);"></iframe>';
?> 
 </body>
</html>


