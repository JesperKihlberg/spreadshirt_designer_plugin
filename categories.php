
<html>
 <head>
  <title>Categories</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link media="all" type="text/css" href="main.css" rel="stylesheet"></link>

 </head>
 <body>
 <?php
include("functions.php");
$locale='?locale=dk_DK';

$departmentId=$_GET['departmentid'];
$categoryId=$_GET['categoryid'];
//echo $departmentId;
//echo $categoryId;
$href = "http://api.spreadshirt.net/api/v1/shops/1034542/productTypeDepartments/".$departmentId.$locale;
//echo $href;
$departmentXml=CallAPI($href);
$categoryXml=QueryAttribute($departmentXml->categories->category,'id',$categoryId);
//echo $categoryXml->name;
echo '<div class"departmentName"><h2>',$categoryXml->name,' - <a href="',$href,'">',$departmentXml->name,"</a></h2></div>";
foreach($categoryXml->productTypes->productType as $productType)
{
  $productId = $productType->attributes()->id;
  DrawProduct($productId,$locale);
}

?> 
 </body>
</html>


