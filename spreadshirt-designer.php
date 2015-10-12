<?php
/**
 * @package Spreadshirt-Designer
 */
/*
Plugin Name: Spreadshirt Designer
Plugin URI: http://jesperkihlberg.dk/spreadshirtdesigner
Description: Allows you to add a spreadshirt designer on your wordpress site. The designer allows for url parameters to be passed. The following parameters are supported: productid, product, designid, productcolor, designcolor1, designcolor2, department. The correspondent ids are found by using the spreadshirt api. Work is in progress to add this information to the plugin as well.
Version: 1.0.0
Author: Jesper Kihlberg
Author URI: http://blog.jesperkihlberg.dk/
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: spreadshirt-designer
*/

/*
This theme, like WordPress, is licensed under the GPL.
Use it to make something cool, have fun, and share what you've learned with others.
*/
include("sd-functions.php");
include("designer.php");
include("product.php");
include("department.php");
include("category.php");
include("article.php");
include("productlink.php");
//include("designs.php");

add_shortcode( 'designer', 'designer_func' );
add_shortcode( 'product', 'product_func' );
add_shortcode( 'department', 'department_func' );
add_shortcode( 'category', 'category_func' );
add_shortcode( 'article', 'article_func' );
add_shortcode( 'productlink', 'productlink_func' );
//add_shortcode( 'designs', 'designs_func');

add_action('wp_enqueue_scripts', 'add_css_func');
add_action('wp_enqueue_scripts', 'add_js_func');

add_action('init','register_session', 1);

add_action('wp_head', 'spreadshirt_header_basket');

function spreadshirt_header_basket(){
	echo ' 	<div id="shoppingbag" class="shoppingbaghover"> ';
	echo ' 		<div id="shoppingbagcontents"> ';
	echo ' 			<a class="openpop" href="checkout.php"> <img id="shoppingbagimg" ';
	echo ' 				src="'.plugins_url( '/img/bag.png', __FILE__ ).'" width="35" height="35" alt="" /> ';
	echo ' 				<p id="shoppingbagitems"> ';
	echo ' 					<span class="basket-counter">0</span> ';
	echo ' 				</p> ';
	echo ' 			</a> ';
	echo ' 		</div> ';
	echo ' 	</div> ';
	echo ' 	<div class="wrapper"> ';
	echo ' 		<div class="popup"> ';
	echo ' 			<iframe class="checkoutFrame" src=""> ';
	echo ' 				<p>Your browser does not support iframes.</p> ';
	echo ' 			</iframe> ';
	echo ' 			<a href="#" class="closeCheckout">X</a> ';
	echo ' 		</div> ';
	echo ' 	</div> ';
}


function register_session(){
	if( !session_id() )
		session_start();
}

add_action( 'wp_ajax_nopriv_spreadshirtdesignershop-additem', 'spreadshirtdesignershop_additem' );
add_action( 'wp_ajax_spreadshirtdesignershop-additem', 'spreadshirtdesignershop_additem' );

add_action( 'wp_ajax_nopriv_spreadshirtdesignershop-getbasket', 'spreadshirtdesignershop_getbasket' );
add_action( 'wp_ajax_spreadshirtdesignershop-getbasket', 'spreadshirtdesignershop_getbasket' );


function add_css_func()
{
  wp_register_style( 'custom-style', plugins_url( '/css/spreaddesign.css', __FILE__ ), array(), '20120208', 'all' );
  wp_register_style( 'custom-style', get_template_directory_uri() . '/css/spreaddesign.css', array(), '20120208', 'all' );
  wp_enqueue_style( 'custom-style' );

}

function add_js_func(){
	wp_enqueue_script("jquery");
    wp_enqueue_script( 'custom-script', plugins_url( '/js/spreaddesign.js', __FILE__ ) );
    wp_enqueue_script( 'shop-controller', plugins_url( '/js/shop-controller.js', __FILE__ ) );
    
    wp_localize_script( 'shop-controller', 'ShopAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}


function spreadshirtdesignershop_additem(){

	if( !session_id() )
		session_start();
	
	// 	$language = addslashes($_POST['l']);
	//	$shopid = addslashes($_POST['shop']);

	$locale = '?locale=dk_DK';
	$shopId ='1034542';
	$departmentId='10';
	$key="53326d6f-40ea-4300-8389-25e890e6ed8c";
	$secret="89667884-f05b-4c0e-98de-8679130aa6e6";

	$shopSource = "dk";
	$shopKey = $key;
	$shopSecret = $secret;

	if (isset($_POST['size']) && isset($_POST['appearance']) && isset($_POST['quantity'])) {
		/*
		 * create an new basket if not exist
		 */
		if (!isset($_SESSION['basketUrl'])) {
			/*
			 * get shop xml
			 */
			$stringApiUrl = 'http://api.spreadshirt.'.$shopSource.'/api/v1/shops/' . $shopId;
			$stringXmlShop = oldHttpRequest($stringApiUrl, null, 'GET');
			if ($stringXmlShop[0]!='<') die($stringXmlShop);
			$objShop = new SimpleXmlElement($stringXmlShop);
			if (!is_object($objShop)) die('Basket not loaded');

			/*
			 * create the basket
			*/
			$namespaces = $objShop->getNamespaces(true);
			$basketUrl = createBasket($objShop, $namespaces, $shopKey, $shopSecret);
			$_SESSION['basketUrl'] = $basketUrl;
			$_SESSION['namespaces'] = $namespaces;
			 
			updateCheckout($shopSource, $shopKey, $shopSecret);
		}



		/*
		 Workaround for not having the appearance id :(
		 */
		if ($_POST['appearance']==0) {
			$stringApiArticleUrl = 'http://api.spreadshirt.'.$shopSource.'/api/v1/shops/' . $shopId.'/articles/'.intval($_POST['article']).'?fullData=true';
			$stringXmlArticle = oldHttpRequest($stringApiArticleUrl, null, 'GET');
			if ($stringXmlArticle[0]!='<') die($stringXmlArticle);
			$objArticleShop = new SimpleXmlElement($stringXmlArticle);
			if (!is_object($objArticleShop)) die('Article not loaded');
			$_POST['appearance'] = intval($objArticleShop->product->appearance['id']);
		}


		/*
		 * article data to be sent to the basket resource
		 */
		$data = array(

				'articleId' => intval($_POST['article']),
				'size' => intval($_POST['size']),
				'appearance' => intval($_POST['appearance']),
				'quantity' => intval($_POST['quantity']),
				'shopId' => $shopId

		);

		/*
		 * add to basket
		*/
		addBasketItem($_SESSION['basketUrl'] , $_SESSION['namespaces'] , $data, $shopSource, $shopKey, $shopSecret);

		$basketData = prepareBasket($shopKey, $shopSecret);
		updateCheckout($shopSource);

		header( "Content-Type: application/json" );
		echo json_encode(array("c" => array("u" => $_SESSION['checkoutUrl'],"q" => $basketData[0],"l" => $basketData[1])));
		exit;
	}
}

function spreadshirtdesignershop_getbasket(){
	
	if( !session_id() )
		session_start();
	
	$locale = '?locale=dk_DK';
	$shopId ='1034542';
	$departmentId='10';
	$key="53326d6f-40ea-4300-8389-25e890e6ed8c";
	$secret="89667884-f05b-4c0e-98de-8679130aa6e6";

	$shopSource = "dk";
	$shopKey = $key;
	$shopSecret = $secret;

	prepareBasket($shopKey, $shopSecret);
	updateCheckout($shopSource,$shopKey, $shopSecret);
	
	header( "Content-Type: application/json" );
	if (array_key_exists('basketUrl',$_SESSION) && !empty($_SESSION['basketUrl'])) {

		$basketData = prepareBasket($shopKey, $shopSecret);

		echo json_encode(array("c" => array("u" => $_SESSION['checkoutUrl'],"q" => $basketData[0],"l" => $basketData[1])));
		exit;
	} else {
		echo json_encode(array("c" => array("u" => "","q" => 0,"l" => "")));
		exit;
	}

}

function updateCheckout($shopSource, $shopKey, $shopSecret){

    /*
     * get the checkout url
     */
    $checkoutUrl = checkout($_SESSION['basketUrl'], $_SESSION['namespaces'], $shopKey, $shopSecret);

    // basket language workaround
    if ($language=="fr") {
        if (!strstr($checkoutUrl,'/fr')) {
            $checkoutUrl = str_replace("spreadshirt.".$shopSource,"spreadshirt.".$shopSource."/fr",$checkoutUrl);
        }
    }
    $_SESSION['checkoutUrl'] = $checkoutUrl;
}



function prepareBasket($shopKey, $shopSecret) {

    $intInBasket=0;

    if (isset($_SESSION['basketUrl'])) {
        $basketItems=getBasket($_SESSION['basketUrl'], $shopKey, $shopSecret);

        if(!empty($basketItems)) {
            foreach($basketItems->basketItems->basketItem as $item) {
                $intInBasket += $item->quantity;
            }
        }
    }

    $l = "";
    $pQ = parse_url($_SESSION['checkoutUrl']);
    if (preg_match("#^basketId\=([0-9a-f\-])*$#i", $pQ['query'])) {
        $l = $pQ['query'];
    }

    return array($intInBasket,$l);
}

// Additional functions
function addBasketItem($basketUrl, $namespaces, $data, $shopSource, $shopKey, $shopSecret) {

	$basketItemsUrl = $basketUrl . "/items";

    $basketItem = new SimpleXmlElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <basketItem xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://api.spreadshirt.net">
            <quantity>' . $data['quantity'] . '</quantity>
            <element id="' . $data['articleId'] . '" type="sprd:article" xlink:href="http://api.spreadshirt.'.$shopSource.'/api/v1/shops/' . $data['shopId'] . '/articles/' . $data['articleId'] . '">
            <properties>
            <property key="appearance">' . $data['appearance'] . '</property>
            <property key="size">' . $data['size'] . '</property>
            </properties>
            </element>
            <links>
            <link type="edit" xlink:href="http://' . $data['shopId'] .'.spreadshirt.' .$shopSource.'/-A' . $data['articleId'] . '"/>
            <link type="continueShopping" xlink:href="http://' . $data['shopId'].'.spreadshirt.'.$shopSource.'"/>
            </links>
            </basketItem>');

    $header = array();
    $header[] = createAuthHeader("POST", $basketItemsUrl, $shopKey, $shopSecret);
    $header[] = "Content-Type: application/xml";
    $result = oldHttpRequest($basketItemsUrl, $header, 'POST', $basketItem->asXML());
}



function createBasket($shop, $namespaces, $shopKey, $shopSecret) {

    $basket = new SimpleXmlElement('<basket xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://api.spreadshirt.net">
            <shop id="' . $shop['id'] . '"/>
            </basket>');

    $attributes = $shop->baskets->attributes($namespaces['xlink']);
    $basketsUrl = $attributes->href;
    $header = array();
    $header[] = createAuthHeader("POST", $basketsUrl, $shopKey, $shopSecret);
    $header[] = "Content-Type: application/xml";
    $result = oldHttpRequest($basketsUrl, $header, 'POST', $basket->asXML());
    $basketUrl = parseHttpHeaders($result, "Location");

    return $basketUrl;

}






function checkout($basketUrl, $namespaces, $shopKey, $shopSecret) {

    $basketCheckoutUrl = $basketUrl . "/checkout";
    $header = array();
    $header[] = createAuthHeader("GET", $basketCheckoutUrl, $shopKey, $shopSecret);
    $header[] = "Content-Type: application/xml";
//    echo createAuthHeader("GET", $basketCheckoutUrl, $shopKey, $shopSecret);
    $result = oldHttpRequest($basketCheckoutUrl, $header, 'GET');
//    echo $result;
    $checkoutRef = new SimpleXMLElement($result);
    $refAttributes = $checkoutRef->attributes($namespaces['xlink']);
    $checkoutUrl = (string)$refAttributes->href;

    return $checkoutUrl;

}

/*
 * functions to build headers
 */
function createAuthHeader($method, $url, $shopKey, $shopSecret) {
    $time = time() *1000;
    $data = "$method $url $time";
    $sig = sha1("$data ".$shopSecret);

    return "Authorization: SprdAuth apiKey=\"".$shopKey."\", data=\"$data\", sig=\"$sig\"";

}


function parseHttpHeaders($header, $headername) {

    $retVal = array();
    $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));

    foreach($fields as $field) {

        if (preg_match('/(' . $headername . '): (.+)/m', $field, $match)) {
            return $match[2];
        }

    }

    return $retVal;

}

function getBasket($basketUrl, $shopKey, $shopSecret) {

    $header = array();
    $basket = "";

    if (!empty($basketUrl)) {
        $header[] = createAuthHeader("GET", $basketUrl, $shopKey, $shopSecret);
        $header[] = "Content-Type: application/xml";
        $result = oldHttpRequest($basketUrl, $header, 'GET');
        $basket = new SimpleXMLElement($result);
    }

    return $basket;

}




function oldHttpRequest($url, $header = null, $method = 'GET', $data = null, $len = null) {

    switch ($method) {

        case 'GET':

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);

            if (!is_null($header)) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            break;

        case 'POST':

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POST, true); //not createBasket but addBasketItem
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            break;

    }

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;

}?>
