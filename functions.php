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

//function DrawDepartments($locale, $shopId)
//{
//  $xml=CallAPI(GetApiBaseUrl().$shopId."/productTypeDepartments",false);

//  echo '<div class="departments">';
//  foreach($xml->children() as $department)
//  {
//    echo '<div class="department">';
//    $attributes = $department->attributes('http://www.w3.org/1999/xlink');
//    $href= $attributes['href'].$locale;
//    DrawDepartment($href,4);
//  }
//  echo '</div>';
//}

function DrawDepartmentId($locale, $shopId, $id, $categoryCount,$departmentUrl, $baseCategoryUrl)
{
  $href=GetApiBaseUrl().$shopId."/productTypeDepartments/".$id.$locale;
  DrawDepartment($href,$categoryCount,$departmentUrl,$baseCategoryUrl);
}

function DrawDepartment($href,$categoryCount,$departmentUrl, $baseCategoryUrl)
{
    $departmentXml = CallAPI($href);
    $id = $departmentXml->attributes()->id;
    echo '<div class"departmentName"><a href="',$departmentUrl,'"><h2>',$departmentXml->name,"</h2></a></div>";
    DrawCategories($categoryCount, $departmentXml, $baseCategoryUrl);
    echo '</div>';
}

function DrawCategories($maxCount, $departmentXml,$baseCategoryUrl)
{
    $i=0;
    $departmentId = $departmentXml->attributes()->id;
    foreach($departmentXml->categories->category as $cat)
    {
      $productId = $cat->productTypes->productType->attributes()->id;
     //$productXml = CallAPI(
      echo '<fieldset class="category">';
      $refurl=$baseCategoryUrl.'?departmentid='.$departmentId.'&categoryid='.$cat->attributes()->id;
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

function DrawCategory($locale,$shopid, $departmentId, $categoryId, $baseCategoryUrl,$baseproducturl)
{
$departmentXml=GetDepartmentXml($locale,$departmentId);
$categoryXml=QueryAttribute($departmentXml->categories->category,'id',$categoryId);
//echo $categoryXml->name;
echo '<div class"departmentName"><h2>',$categoryXml->name,' - ',$departmentXml->name,"</h2></div>";
foreach($categoryXml->productTypes->productType as $productType)
{
  $productId = $productType->attributes()->id;
  DrawProduct($locale,$shopid,$productId,$baseproducturl);
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
function DrawProductImageAppearance($productId,$apperanceId, $width)
{
      $view=1;
      if($productId==925){
        $view=3;
      }
      $imgHref=GetImageBaseUrl().'productTypes/'.$productId.'/views/'.$view.',width='.$width.',height='.$width.',appearanceId='.$apperanceId;
      echo '<img src="',$imgHref,'"/>'; 
}

function DrawProduct($locale,$shopid,$productId,$baseproducturl){
  $productXml = GetProductXml($locale,$shopid,$productId);
  echo '<fieldset class="category">';
  $refurl=$baseproducturl.'?productid='.$productId;
  echo '<a href="',$refurl,'">',$productXml->name,'</a>';
  echo '<a href="',$refurl,'">';
  DrawProductImage($productId);
  echo '</a>';
  echo '</fieldset>';
 
}

function DrawProductDetail($locale,$shopid,$departmentId,$categoryId,$productId,$basecategoryurl,$baseproducturl,$basedesignerurl){
  $productXml = GetProductXml($locale,$shopid,$productId);

  foreach($productXml->appearances->appearance as $appearance)
  {
    $appearanceId = $appearance->attributes()->id;
    echo '<fieldset class="smallproduct">';
    $refurl=$basedesignerurl.'?productid='.$productId.'&productcolor='.$appearanceId;
    echo '<a href="',$refurl,'">',$appearance->name,'</a>';
    echo '<a href="',$refurl,'">';
    DrawProductImageAppearance($productId,$appearanceId,75);
    echo '</a>';
    echo '</fieldset>';
  }
}


function GetProductXml($locale,$shopid, $productId)
{
  $productHref=GetApiBaseUrl().$shopid.'/productTypes/'.$productId.$locale;
  echo '<a href="',$productHref,'">',$productHref,'</a>';
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

