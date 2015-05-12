<?php
function GetApiBaseUrl(){
  $apiBaseUrl='http://api.spreadshirt.net/api/v1/shops/';
  return $apiBaseUrl;
}
function GetImageBaseUrl(){
  $imageBaseUrl='http://image.spreadshirtmedia.net/image-server/v1/';
  return $imageBaseUrl;
}

function GetShopId(){
  $shopId = '1034542';
  return $shopId;
}

function GetDepartmentXml($locale,$departmentId)
{
  $href = GetApiBaseUrl().GetShopId()."/productTypeDepartments/".$departmentId.$locale;
  $departmentXml=CallAPI($href);
  return $departmentXml;
}

function DrawDepartments($locale)
{
  $xml=CallAPI(GetApiBaseUrl().GetShopId()."/productTypeDepartments",false);

  echo '<div class="departments">';
  foreach($xml->children() as $department)
  {
    echo '<div class="department">';
    $attributes = $department->attributes('http://www.w3.org/1999/xlink');
    $href= $attributes['href'].$locale;
    DrawDepartment($href,4);
  }
  echo '</div>';
}

function DrawDepartmentId($locale, $id, $categoryCount)
{
  $href=GetApiBaseUrl().GetShopId()."/productTypeDepartments/".$id.$locale;
  DrawDepartment($href,$categoryCount);
}

function DrawDepartment($href,$categoryCount)
{
    $departmentXml = CallAPI($href);
    $id = $departmentXml->attributes()->id;
    echo '<div class"departmentName"><a href="departments.php?departmentid=',$id,'"><h2>',$departmentXml->name,"</h2></a></div>";
    DrawCategories($categoryCount, $departmentXml);
    echo '</div>';
}

function DrawCategories($maxCount, $departmentXml)
{
    $i=0;
    $departmentId = $departmentXml->attributes()->id;
    foreach($departmentXml->categories->category as $cat)
    {
      $productId = $cat->productTypes->productType->attributes()->id;
     //$productXml = CallAPI(
      echo '<fieldset class="category">';
      $refurl='categories.php?departmentid='.$departmentId.'&categoryid='.$cat->attributes()->id;
      echo '<a href="',$refurl,'">',$cat->name,'</a>';
      echo '<a href="',$refurl,'">';
      DrawProductImage($productId);
      echo '</a>';
      echo '</fieldset>';
      $i=$i+1;
      if($i>=$maxCount)
        break;
    }
}

function DrawCategory($locale, $departmentId, $categoryId)
{
$departmentXml=GetDepartmentXml($locale,$departmentId);
$categoryXml=QueryAttribute($departmentXml->categories->category,'id',$categoryId);
//echo $categoryXml->name;
echo '<div class"departmentName"><h2>',$categoryXml->name,' - <a href="',$href,'">',$departmentXml->name,"</a></h2></div>";
foreach($categoryXml->productTypes->productType as $productType)
{
  $productId = $productType->attributes()->id;
  DrawProduct($productId,$locale);
}

}


function DrawProductImage($productId)
{
      $view=1;
      if($productId==925){
        $view=3;
      }
      $imgHref=GetImageBaseUrl().'productTypes/'.$productId.'/views/'.$view.',width=130,height=130';
      echo '<img src="',$imgHref,'"/>'; 
}

function DrawProduct($productId,$locale){
  $productXml = GetProductXml($productId,$locale);
  echo '<fieldset class="category">';
  $refurl='product.php?productid='.$productId;
  echo '<a href="',$refurl,'">',$productXml->name,'</a>';
  echo '<a href="',$refurl,'">';
  DrawProductImage($productId);
  echo '</a>';
  echo '</fieldset>';
 
}



function GetProductXml($productId, $locale)
{
  $productHref=GetApiBaseUrl().'1034542/productTypes/'.$productId.$locale;
//  echo '<a href="',$productHref,'">',$productHref,'</a>';
  $productXml = CallAPI($productHref);
  return $productXml;
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

