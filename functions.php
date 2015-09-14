<?php
function GetApiBaseUrl(){
    $apiBaseUrl='http://api.spreadshirt.net/api/v1/shops/';
    return $apiBaseUrl;
}
function GetImageBaseUrl(){
    $imageBaseUrl='http://image.spreadshirtmedia.net/image-server/v1/';
    return $imageBaseUrl;
}

function GetDepartmentXml($locale,$shopId,$departmentId)
{
    $href = GetApiBaseUrl().$shopId."/productTypeDepartments/".$departmentId.$locale;
    $departmentXml=CallAPI($href);
    return $departmentXml;
}

function DrawDepartmentId($locale, $shopId, $id, $categoryCount,$departmentUrl, $baseCategoryUrl)
{
    $href=GetApiBaseUrl().$shopId."/productTypeDepartments/".$id.$locale;
    DrawDepartment($locale,$shopId,$href,$categoryCount,$departmentUrl,$baseCategoryUrl);
}

function DrawDepartment($locale,$shopId,$href,$categoryCount,$departmentUrl, $baseCategoryUrl)
{
    $departmentXml = CallAPI($href);
    $id = $departmentXml->attributes()->id;
    echo '<div class"departmentName"><a href="',$departmentUrl,'"><h2>',$departmentXml->name,"</h2></a></div>";
    DrawCategories($locale,$shopId,$categoryCount, $departmentXml, $baseCategoryUrl);
    echo '</div>';
}

function DrawCategories($locale,$shopId,$maxCount, $departmentXml,$baseCategoryUrl)
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
        DrawProductImage($locale,$shopId,$productId);
        echo '</a>';
        echo '</fieldset>';
        $i=$i+1;
        if($i>=$maxCount)
            break;
    }
}

function DrawCategory($locale,$shopId, $departmentId, $categoryId, $baseCategoryUrl,$baseproducturl)
{
    $departmentXml=GetDepartmentXml($locale,$shopId,$departmentId);
    $categoryXml=QueryAttribute($departmentXml->categories->category,'id',$categoryId);
    //echo $categoryXml->name;
    echo '<div class"departmentName"><h2>',$categoryXml->name,' - ',$departmentXml->name,"</h2></div>";
    foreach($categoryXml->productTypes->productType as $productType)
    {
        $productId = $productType->attributes()->id;
        DrawProduct($locale,$shopId,$productId,$baseproducturl);
    }

}

function DrawProductImage($locale,$shopId,$productId)
{
    DrawProductImageAppearance($locale,$shopId,$productId,'', 130);
}

function DrawProductImageAppearance($locale,$shopId,$productId,$apperanceId, $width)
{
    $articleHref = GetApiBaseUrl().$shopId.'/articles?query=+productTypeIds:('.$productId.')&limit=1';
    $articleXml = CallAPI($articleHref);
    $imgHref = '';
    if($articleXml->count()>0)
    {
        $imgHref=$articleXml->article->resources->resource->attributes('http://www.w3.org/1999/xlink')->href;
    }
    else
    {
        $view=1;
        if($productId==925){
            $view=3;
        }
        $imgHref=GetImageBaseUrl().'productTypes/'.$productId.'/views/'.$view;
    }
    $imgHref.=',width='.$width.',height='.$width;
    if($apperanceId!='')
    {
        $imgHref.=  ',appearanceId='.$apperanceId;
    }
    echo '<img src="',$imgHref,'"/>'; 
}

function DrawRelatedArticles($locale,$shopId,$productTypeId,$apperanceId,$width,$designerUrl){
    $articleHref = GetApiBaseUrl().$shopId.'/articles?query=+productTypeIds:('.$productTypeId.')&limit=20';
    $articleXml = CallAPI($articleHref);

    if($articleXml->count()>0)
    {
        foreach($articleXml->article->resources->resource as $resource){
            $imgHref=$resource->attributes('http://www.w3.org/1999/xlink')->href;
            $productId = explode("/views/",explode("/products/",$imgHref)[1])[0];
 
            $designerLinkProperties ='product='.$productId;
            $imgUrlProperties= '" productId="'.$productId;
            
            DrawArticle($locale,$shopId,$productTypeId, $apperanceId, $width, $designerUrl, $imgHref, $designerLinkProperties, $imgUrlProperties);
        }
    }
    else
    {
        $view=1;
        if($productId==925){
            $view=3;
        }

        $imgHref=GetImageBaseUrl().'productTypes/'.$productTypeId.'/views/'.$view;

        $designerLinkProperties ='productid='.$productTypeId;
        $imgUrlProperties= '" productTypeId="'.$productTypeId;
        
        DrawArticle($locale,$shopId,$productTypeId, $apperanceId, $width, $designerUrl, $imgHref, $designerLinkProperties, $imgUrlProperties);
    }
}

function DrawArticle($locale,$shopId,$productTypeId, $apperanceId, $width, $designerUrl, $imgHref, $designerLinkProperties, $imgUrlProperties){

    $imgHref.=',width='.$width.',height='.$width;

    $baseHref = $imgHref;
    if($apperanceId=='')
    {
        $productXml = GetProductXml($locale,$shopId,$productTypeId);
        $apperanceId = $productXml->appearances->appearance->attributes()->id;
    }
    $imgHref.=  ',appearanceId='.$apperanceId;

    echo '<a class="designerLink" href="',$designerUrl,'?',$designerLinkProperties,'&productcolor=',$apperanceId,'" baseUrl="',$designerUrl,'?',$designerLinkProperties,'&productcolor=">';
    echo '<div data-content="TILPAS OG BESTIL" class="designerLinkDiv">';
    echo '<img class="articleThumb" baseUrl="',$baseHref,',appearanceId="',$imgUrlProperties,'" src="',$imgHref,'"/>'; 
    echo '</div>';
    echo '</a>';
}

function DrawAppearanceIcons($locale,$shopId,$productId){
    $productXml = GetProductXml($locale,$shopId,$productId);
    echo '<div class="appearanceIcons">';
    foreach($productXml->appearances->appearance as $appearance)
    {
        $imgHref = $appearance->resources->resource->attributes('http://www.w3.org/1999/xlink')->href;
        $name = $appearance->name;
        $appearanceId = $appearance->attributes()->id;
        echo '<img class="appearanceIcon" onClick="onAppereanceClick(',$appearanceId,')" appearanceId="',$appearanceId,'" src="',$imgHref,'" alt="',$name,'" title="',$name,'" />'; 
    }
    echo '</div>';
}


function DrawProduct($locale,$shopId,$productId,$baseproducturl){
    $productXml = GetProductXml($locale,$shopId,$productId);
    echo '<fieldset class="category">';
    $refurl=$baseproducturl.'?productid='.$productId;
    echo '<a href="',$refurl,'">',$productXml->name,'</a>';
    echo '<a href="',$refurl,'">';
    DrawProductImage($locale,$shopId,$productId);
    echo '</a>';
    echo '</fieldset>';
    
}

function DrawProductDetail($locale,$shopId,$departmentId,$categoryId,$productId,$basecategoryurl,$baseproducturl,$basedesignerurl){
    $productXml = GetProductXml($locale,$shopId,$productId);
    echo '<div class="productName">',$productXml->name,'</div>';
    echo '<div class="productShortDesc">',$productXml->shortDescription,'</div>';
    echo '<div class="productDesc">',$productXml->description,'</div>';
    DrawAppearanceIcons($locale,$shopId,$productId);
    DrawRelatedArticles($locale,$shopId,$productId,'',130,$basedesignerurl);
}

function DrawDesigns($count,$locale,$shopId, $departmentid, $categoryid,$productid,$basecategoryurl,$baseproducturl,$basedesignerurl){
    echo 'designs';
    $designsHref = GetApiBaseUrl().$shopId.'/articleCategories/'.$locale;
    echo '<a href="',$designsHref,'">',$designsHref,'</a>';
}

function GetProductXml($locale,$shopId, $productId)
{
    $productHref=GetApiBaseUrl().$shopId.'/productTypes/'.$productId.$locale;
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
    //  $xml->registerXPathNamespace("xlink", 'http://www.w3.org/1999/xlink');
    return $xml;
}
?>

