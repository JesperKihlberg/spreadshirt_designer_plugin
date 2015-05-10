
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
//echo $departmentId;
//echo $categoryId;
DrawDepartmentId($locale, $departmentId, 50);

?> 
 </body>
</html>


