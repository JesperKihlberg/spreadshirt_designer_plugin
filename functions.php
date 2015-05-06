<?php

function DrawDepartments($locale)
{
  $xml=CallAPI("http://api.spreadshirt.net/api/v1/shops/1034542/productTypeDepartments",false);

  echo '<div class="departments">';
  foreach($xml->children() as $department)
  {
    echo '<div class="department">';
    $attributes = $department->attributes('http://www.w3.org/1999/xlink');
    $href= $attributes['href'].$locale;
    DrawDepartment($href);
  }
  echo '</div>';
}
function DrawDepartment($href)
{
    $departmentXml = CallAPI($href);
    echo '<div class"departmentName"><a href="',$href,'"><h2>',$departmentXml->name,"</h2></a></div>";
    DrawCategories(4, $departmentXml);
    echo '</div>';
}
function DrawCategories($maxCount, $departmentXml)
{
    $i=0;
    $departmentId = $departmentXml->attributes()->id;
    foreach($departmentXml->categories->category as $cat)
    {
      $view=1;
      $productId = $cat->productTypes->productType->attributes()->id;
      if($productId==925){
        $view=3;
      }
      $imgHref='http://image.spreadshirtmedia.net/image-server/v1/productTypes/'.$productId.'/views/'.$view.',width=130,height=130';
      //$productXml = CallAPI(
      echo '<fieldset class="category">';
      $refurl='categories.php?departmentid='.$departmentId.'&categoryid='.$cat->attributes()->id;
      echo '<a href="',$refurl,'">',$cat->name,'</a>';
      echo '<a href="',$refurl,'"><img src="',$imgHref,'"/></a>';
      echo '</fieldset>';
      $i=$i+1;
      if($i>=$maxCount)
        break;
    }
   
}
function DrawProduct($productId){
   $imgHref='http://image.spreadshirtmedia.net/image-server/v1/productTypes/'.$productId.'/views/'.$view.',width=130,height=130';
  //$productXml = CallAPI(
  echo '<fieldset class="category">';
  $refurl='product.php?productid='.$productId;
  echo '<a href="',$refurl,'">',$cat->name,'</a>';
  echo '<a href="',$refurl,'"><img src="',$imgHref,'"/></a>';
echo '</fieldset>';
 
}

function QueryAttribute($xmlNode, $attr_name, $attr_value) {
  foreach($xmlNode as $node) { 
    if($node[$attr_name] == $attr_value){
      return $node;
    }
  }
}

function CallAPI($url, $data = false)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  
  curl_close($ch);

  $xml = simplexml_load_string($result);
  return $xml;
}
?> 

