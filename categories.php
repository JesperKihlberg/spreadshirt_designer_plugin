
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
DrawCategory($locale, $departmentId, $categoryId);

?> 
 </body>
</html>


