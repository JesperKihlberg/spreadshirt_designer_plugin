<?php
function GetApiBaseUrl() {
	$apiBaseUrl = 'http://api.spreadshirt.net/api/v1/shops/';
	return $apiBaseUrl;
}
function GetImageBaseUrl() {
	$imageBaseUrl = 'http://image.spreadshirtmedia.net/image-server/v1/';
	return $imageBaseUrl;
}
function GetDepartmentXml($locale, $shopId, $departmentId) {
	$href = GetApiBaseUrl () . $shopId . "/productTypeDepartments/" . $departmentId . $locale;
	$departmentXml = CallAPI ( $href );
	return $departmentXml;
}
function DrawDepartmentId($locale, $shopId, $id, $categoryCount, $departmentUrl, $baseCategoryUrl) {
	$href = GetApiBaseUrl () . $shopId . "/productTypeDepartments/" . $id . $locale;
	DrawDepartment ( $locale, $shopId, $href, $categoryCount, $departmentUrl, $baseCategoryUrl );
}
function DrawDepartment($locale, $shopId, $href, $categoryCount, $departmentUrl, $baseCategoryUrl) {
	$departmentXml = CallAPI ( $href );
	$id = $departmentXml->attributes ()->id;
	echo '<div class="departmentName"><a href="', $departmentUrl, '"><h2>', $departmentXml->name, "</h2></a></div>";
	DrawCategories ( $locale, $shopId, $categoryCount, $departmentXml, $baseCategoryUrl );
	echo '</div>';
}
function DrawCategories($locale, $shopId, $maxCount, $departmentXml, $baseCategoryUrl) {
	$i = 0;
	$departmentId = $departmentXml->attributes ()->id;
	foreach ( $departmentXml->categories->category as $cat ) {
		$productId = $cat->productTypes->productType->attributes ()->id;
		// $productXml = CallAPI(
		echo '<fieldset class="category">';
		$refurl = $baseCategoryUrl . '?departmentid=' . $departmentId . '&categoryid=' . $cat->attributes ()->id;
		echo '<a href="', $refurl, '">';
		DrawProductImage ( $locale, $shopId, $productId );
		echo '</a>';
		echo '<a href="', $refurl, '">', $cat->name, '</a>';
		echo '</fieldset>';
		$i = $i + 1;
		if ($i >= $maxCount)
			break;
	}
}
function DrawCategory($locale, $shopId, $departmentId, $categoryId, $baseCategoryUrl, $baseproducturl) {
	$departmentXml = GetDepartmentXml ( $locale, $shopId, $departmentId );
	$categoryXml = QueryAttribute ( $departmentXml->categories->category, 'id', $categoryId );
	// echo $categoryXml->name;
	echo '<div class="departmentName"><h2>', $categoryXml->name, ' - ', $departmentXml->name, "</h2></div>";
	foreach ( $categoryXml->productTypes->productType as $productType ) {
		$productId = $productType->attributes ()->id;
		DrawProduct ( $locale, $shopId, $productId, $baseproducturl, true );
	}
}
function DrawProductImage($locale, $shopId, $productId) {
	DrawProductImageAppearance ( $locale, $shopId, $productId, '', 130 );
}
function DrawProductImageAppearance($locale, $shopId, $productId, $apperanceId, $width) {
	$articleHref = GetApiBaseUrl () . $shopId . '/articles?query=+productTypeIds:(' . $productId . ')&limit=10';
	$articleXml = CallAPI ( $articleHref );
	$imgHref = '';
	if ($articleXml->count () > 0) {
		$articleNb = rand ( 0, $articleXml->count () - 1 );
		$imgHref = $articleXml->article [$articleNb]->resources->resource->attributes ( 'http://www.w3.org/1999/xlink' )->href;
	} else {
		$view = 1;
		if ($productId == 925) {
			$view = 3;
		}
		$imgHref = GetImageBaseUrl () . 'productTypes/' . $productId . '/views/' . $view;
	}
	$imgHref .= ',width=' . $width . ',height=' . $width;
	if ($apperanceId != '') {
		$imgHref .= ',appearanceId=' . $apperanceId;
	}
	echo '<img src="', $imgHref, '"/>';
}
function DrawRelatedArticles($locale, $shopId, $productTypeId, $apperanceId, $width, $designerUrl) {
	$articleHref = GetApiBaseUrl () . $shopId . '/articles?query=+productTypeIds:(' . $productTypeId . ')&limit=20';
	$articleXml = CallAPI ( $articleHref );
	
	if ($articleXml->count () > 0) {
		foreach ( $articleXml->article as $article ) {
			$imgHref = $article->resources->resource->attributes ( 'http://www.w3.org/1999/xlink' )->href;
			$productId = explode ( "/views/", explode ( "/products/", $imgHref ) [1] ) [0];
			$articleId=$article-> attributes() -> id;
			$designerLinkProperties = 'product=' . $productId;
			$imgUrlProperties = '" productId="' . $productId;
			$price = $article->price;
			DrawArticleHtml ( $locale, $shopId, $productTypeId, $apperanceId, $width, $designerUrl, $imgHref, $designerLinkProperties, $imgUrlProperties, $price, $articleId);
		}
	} else {
		$view = 1;
		if ($productId == 925) {
			$view = 3;
		}
		
		$imgHref = GetImageBaseUrl () . 'productTypes/' . $productTypeId . '/views/' . $view;
		
		$designerLinkProperties = 'productid=' . $productTypeId;
		$imgUrlProperties = '" productTypeId="' . $productTypeId;
		$productXml = GetProductTypeXml ( $locale, $shopId, $productTypeId );
		$price = $productXml->price;
		DrawArticleHtml ( $locale, $shopId, $productTypeId, $apperanceId, $width, $designerUrl, $imgHref, $designerLinkProperties, $imgUrlProperties, $price, '' );
	}
}
function DrawArticle($locale, $shopId, $productId, $productTypeId, $apperanceId, $width, $designerUrl) {
	$articleHref = GetApiBaseUrl () . $shopId . '/articles?query=+productTypeIds:(' . $productTypeId . ')&limit=30';
	$articleXml = CallAPI ( $articleHref );
	
	if ($articleXml->count () > 0) {
		foreach ( $articleXml->article as $article ) {
			$imgHref = $article->resources->resource->attributes ( 'http://www.w3.org/1999/xlink' )->href;
			$productIdExtr = explode ( "/views/", explode ( "/products/", $imgHref ) [1] ) [0];
			if ($productId == $productIdExtr) {
				$articleId=$article-> attributes() -> id;
				$designerLinkProperties = 'product=' . $productId;
				$imgUrlProperties = '" productId="' . $productId;
				$price = $article->price;
				DrawArticleHtml ( $locale, $shopId, $productTypeId, $apperanceId, $width, $designerUrl, $imgHref, $designerLinkProperties, $imgUrlProperties, $price, $articleId);
			}
		}
	}
}
function DrawArticleHtml($locale, $shopId, $productTypeId, $apperanceId, $width, $thumbImageUrl, $imgHref, $designerLinkProperties, $imgUrlProperties, $price, $articleId) {
	$imgHref .= ',width=' . $width . ',height=' . $width;
	$articleDesc = '';
	$baseHref = $imgHref;
	$productXml = GetProductTypeXml ( $locale, $shopId, $productTypeId );
	if ($apperanceId == '') {
		$apperanceId = $productXml->appearances->appearance->attributes ()->id;
	}
	$currencyHref = $price->currency->attributes ( 'http://www.w3.org/1999/xlink' )->href;
	$currency = CallAPI ( $currencyHref );
	$priceText = formatPrice ( $price->vatIncluded, $currency->symbol, $currency->decimalCount, $currency->pattern, $country->thousandsSeparator, $country->decimalPoint );
	$imgHref .= ',appearanceId=' . $apperanceId;
	$sizeText = "St&oslashrrelse";
	$colorText = "Farve";
	$addBasketText = "L&AEligG I KURVEN";
	//echo '<fieldset class="article', $width, '">';
	echo '<fieldset class="article">';
	echo '<form action=""method="post" name="tshirt_form" id="tshirt_form">';
	//echo '<input type="hidden" name="product" id="productId" value="132727161" />';
	echo '<input type="hidden" name="article" id="articleId" value="'.$articleId.'" />';
	echo '<input type="hidden" name="view" id="currentView16047246" value="351" />';
	echo '<input type="hidden" name="color" id="productColor16047246" value="',$apperanceId,'" />';
	echo '<input type="hidden" name="quantity" id="quantity" value="1" />';
	DrawAppearanceIcons ( $locale, $shopId, $productTypeId, $colorText );
	echo '<div class="sizeSelector">';
	echo "<div>".$sizeText.":</div>";
	echo '<select class="b-core-ui-select__select" id="size" name="size"';
	echo 'onchange="redirect(this);">';
	DrawSizeOptions($locale, $shopId, $productTypeId);
//	echo '<option selected value="3">M</option>';
	echo '</select>';
	echo '</div>';
	// echo '<div data-content="VIS DETALJER" class="designerLinkDiv', $width, '">';
	echo '<div class="thumbDiv">';
	echo '<a class="designerLink" data-content="VIS DETALJER" href="', $thumbImageUrl, '?', $designerLinkProperties, '&productcolor=', $apperanceId, '" baseUrl="', $thumbImageUrl, '?', $designerLinkProperties, '&productcolor=">';
	echo '<img class="articleThumb" baseUrl="', $baseHref, ',appearanceId="', $imgUrlProperties, '" src="', $imgHref, '"/>';
	echo '</a>';
	echo '</div>';
	echo '<div class="productDescription">';
	echo '<div class="productName">', $productXml->name, '</div>';
	echo '<div class="productShortDesc">', $productXml->shortDescription, '</div>';
	echo '<div class="productDesc">', $productXml->description, '</div>';
	echo ' 			<a href="#" class="closeProductDescription">X</a> ';
	echo '</div>';
	// echo '</div>';
	echo '<div class="smallArticleDesc">', $articleDesc, '</div>';
	echo '<div class="priceTag">', $priceText, '</div>';
	echo '<button class="addToCart">'.$addBasketText.'</button>';
	echo '</form>';
	echo '</fieldset>';
}

function DrawSizeOptions($locale, $shopId, $productTypeId){
	$productXml = GetProductTypeXml ( $locale, $shopId, $productTypeId );
	foreach ( $productXml->sizes->size as $size ) {
		echo '<option value="'.$size->attributes()->id.'">'.$size->name.'</option>';
	}
}
function DrawAppearanceIcons($locale, $shopId, $productTypeId, $colorText) {
	$productXml = GetProductTypeXml ( $locale, $shopId, $productTypeId );
	echo '<div class="appearanceIcons">';
	echo '<div>'.$colorText.':</div>';
	echo '<div class="appearanceIconDiv">';
	foreach ( $productXml->appearances->appearance as $appearance ) {
		$imgHref = $appearance->resources->resource->attributes ( 'http://www.w3.org/1999/xlink' )->href;
		$name = $appearance->name;
		$appearanceId = $appearance->attributes ()->id;
		//echo '<img class="appearanceIcon" onClick="onAppereanceClick(', $appearanceId, ')" appearanceId="', $appearanceId, '" src="', $imgHref, '" alt="', $name, '" title="', $name, '" />';
		echo '<img class="appearanceIcon" appearanceId="', $appearanceId, '" src="', $imgHref, '" alt="', $name, '" title="', $name, '" />';
	}
	echo '</div>';
	echo '</div>';
}
function DrawProduct($locale, $shopId, $productId, $baseproducturl, $drawHeader) {
	$productXml = GetProductTypeXml ( $locale, $shopId, $productId );
	echo '<fieldset class="category">';
	$refurl = $baseproducturl . '?productid=' . $productId;
	echo '<a href="', $refurl, '">';
	DrawProductImage ( $locale, $shopId, $productId );
	echo '</a>';
	if ($drawHeader) {
		echo '<a href="', $refurl, '">', $productXml->name, '</a>';
	}
	echo '</fieldset>';
}
function DrawProductDetail($locale, $shopId, $departmentId, $categoryId, $productId, $basecategoryurl, $baseproducturl, $basedesignerurl) {
	$productXml = GetProductTypeXml ( $locale, $shopId, $productId );
	echo '<div class="productName">', $productXml->name, '</div>';
	echo '<div class="productShortDesc">', $productXml->shortDescription, '</div>';
	echo '<div class="productDesc">', $productXml->description, '</div>';
//	DrawAppearanceIcons ( $locale, $shopId, $productId );
//	DrawRelatedArticles ( $locale, $shopId, $productId, '', 130, $basedesignerurl );
}
function DrawDesigns($count, $locale, $shopId, $departmentid, $categoryid, $productid, $basecategoryurl, $baseproducturl, $basedesignerurl) {
	echo 'designs';
	$designsHref = GetApiBaseUrl () . $shopId . '/articleCategories/' . $locale;
	echo '<a href="', $designsHref, '">', $designsHref, '</a>';
}
function GetProductTypeXml($locale, $shopId, $productId) {
	$productHref = GetApiBaseUrl () . $shopId . '/productTypes/' . $productId . $locale;
	// echo '<a href="',$productHref,'">',$productHref,'</a>';
	$productXml = CallAPI ( $productHref );
	return $productXml;
}

function GetShoppingBag(){
	echo '<div id="shoppingbag" class="shoppingbaghover">';
	echo '<div id="shoppingbagcontents">';
	echo '<a class="openpop" href="checkout.php"> <img id="shoppingbagimg"';
	echo '		src="app/img/bag.png" width="35" height="35" alt="" />';
	echo '		<p id="shoppingbagitems">';
	echo '		<span class="basket-counter">0</span>';
	echo '		</p>';
	echo '		</a>';
	echo '		</div>';
	echo '		</div>';
	echo '		<div class="wrapper">';
	echo '		<div class="popup">';
	echo '		<iframe class="checkoutFrame" src="">';
	echo '		<p>Your browser does not support iframes.</p>';
	echo '		</iframe>';
	echo '		<a href="#" class="closeCheckout">X</a>';
	echo '		</div>';
	echo '		</div>';
}

function QueryAttribute($xmlNode, $attr_name, $attr_value) {
	foreach ( $xmlNode as $node ) {
		if ($node [$attr_name] == $attr_value) {
			return $node;
		}
	}
}
function CallAPI($url, $data = false) {
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec ( $ch );
	
	curl_close ( $ch );
	
	$xml = simplexml_load_string ( $result );
	// $xml->registerXPathNamespace("xlink", 'http://www.w3.org/1999/xlink');
	return $xml;
}
function postData($url, $data, $header) {
	$httpRequest = curl_init ();
	
	curl_setopt ( $httpRequest, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $httpRequest, CURLOPT_HTTPHEADER, $header );
	curl_setopt ( $httpRequest, CURLOPT_POST, 1 );
	curl_setopt ( $httpRequest, CURLOPT_HEADER, 1 );
	
	curl_setopt ( $httpRequest, CURLOPT_URL, $url );
	curl_setopt ( $httpRequest, CURLOPT_POSTFIELDS, $data );
	
	$returnHeader = curl_exec ( $httpRequest );
	
	curl_close ( $httpRequest );
	
	return $returnHeader;
}
function formatPrice($price, $symbol, $decimalCount, $pattern, $thousandsSeparator, $decimalPoint) {
	// formatting settings
	$price = "" . $price;
	
	// split integer from cents
	$centsVal = "";
	$integerVal = "0";
	if (strpos ( $price, '.' ) != - 1) {
		$centsVal = "" . substr ( $price, strpos ( $price, '.' ) + 1, strlen ( $price ) - strpos ( $price, '.' ) + 1 );
		$integerVal = "" . substr ( $price, 0, strpos ( $price, '.' ) );
	} else {
		$integerVal = $price;
	}
	
	$formatted = "";
	
	$count = 0;
	for($j = strlen ( $integerVal ) - 1; $j >= 0; $j --) {
		$character = $integerVal [$j];
		$count ++;
		if ($count % 3 == 0)
			$formatted = ($thousandsSeparator . $character) . $formatted;
		else
			$formatted = $character . $formatted;
	}
	if ($formatted [0] == $thousandsSeparator)
		$formatted = substr ( $formatted, 1, strlen ( $formatted ) );
	
	$formatted .= $decimalPoint;
	
	for($j = 0; $j < $decimalCount; $j ++) {
		if ($j < strlen ( $centsVal )) {
			$formatted .= "" . $centsVal [$j];
		} else {
			$formatted .= "0";
		}
	}
	
	$out = $pattern;
	$out = str_replace ( '%', $formatted, $out );
	$out = str_replace ( '$', $symbol, $out );
	return $out;
}
function initBasket($shopId) {
}
?>