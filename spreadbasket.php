<?php
//session_start();
//$domain = ".monshirt.eu";
//ini_set('session.cookie_domain', $domain );
header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
$language = addslashes($_POST['l']);
$shopid = addslashes($_POST['shop']);


// if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
//  die("no direct access allowed");
// }

//if (!is_writable(session_save_path())) {
//	echo 'Session path "'.session_save_path().'" is not writable for PHP!';
//}

//if(!session_id()) {
//    $lifetime=60 * 60 * 24 * 365;
//    session_set_cookie_params($lifetime,"/",$domain);
//    @session_start();
//}


$locale = '?locale=dk_DK';
$shopId ='1034542';
$departmentId='10';
$key="53326d6f-40ea-4300-8389-25e890e6ed8c";
$secret="89667884-f05b-4c0e-98de-8679130aa6e6";

$config['ShopSource'] = "dk";
$config['ShopId'] = $shopId;
$config['ShopKey'] = $key;
$config['ShopSecret'] = $secret;


/*
 * add an article to the basket
 */
if (isset($_POST['size']) && isset($_POST['appearance']) && isset($_POST['quantity'])) {
    /*
     * create an new basket if not exist
     */
    if (!isset($_SESSION['basketUrl'])) {
        /*
         * get shop xml
         */
        $stringApiUrl = 'http://api.spreadshirt.'.$config['ShopSource'].'/api/v1/shops/' . $config['ShopId'];
        $stringXmlShop = oldHttpRequest($stringApiUrl, null, 'GET');
        if ($stringXmlShop[0]!='<') die($stringXmlShop);
        $objShop = new SimpleXmlElement($stringXmlShop);
        if (!is_object($objShop)) die('Basket not loaded');

        /*
         * create the basket
         */
        $namespaces = $objShop->getNamespaces(true);
        $basketUrl = createBasket($objShop, $namespaces);
        $_SESSION['basketUrl'] = $basketUrl;
        $_SESSION['namespaces'] = $namespaces;
        
        updateCheckout();
    }



    /*
    Workaround for not having the appearance id :(
     */
    if ($_POST['appearance']==0) {
        $stringApiArticleUrl = 'http://api.spreadshirt.'.$config['ShopSource'].'/api/v1/shops/' . $config['ShopId'].'/articles/'.intval($_POST['article']).'?fullData=true';
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
            'shopId' => $config['ShopId']

    );

    /*
     * add to basket
     */
    addBasketItem($_SESSION['basketUrl'] , $_SESSION['namespaces'] , $data);

    $basketData = prepareBasket();

    updateCheckout();

    echo json_encode(array("c" => array("u" => $_SESSION['checkoutUrl'],"q" => $basketData[0],"l" => $basketData[1])));
}




// no call, just read basket if not empty
if (isset($_GET['basket'])) {
    if (array_key_exists('basketUrl',$_SESSION) && !empty($_SESSION['basketUrl'])) {

        $basketData = prepareBasket();

        echo json_encode(array("c" => array("u" => $_SESSION['checkoutUrl'],"q" => $basketData[0],"l" => $basketData[1])));
    } else {
        echo json_encode(array("c" => array("u" => "","q" => 0,"l" => "")));
    }
}


function updateCheckout(){

    /*
     * get the checkout url
     */
    $checkoutUrl = checkout($_SESSION['basketUrl'], $_SESSION['namespaces']);

    // basket language workaround
    if ($language=="fr") {
        if (!strstr($checkoutUrl,'/fr')) {
            $checkoutUrl = str_replace("spreadshirt.".$config['ShopSource'],"spreadshirt.".$config['ShopSource']."/fr",$checkoutUrl);
        }
    }

    $_SESSION['checkoutUrl'] = $checkoutUrl;
}



function prepareBasket() {

    $intInBasket=0;

    if (isset($_SESSION['basketUrl'])) {
        $basketItems=getBasket($_SESSION['basketUrl']);

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
function addBasketItem($basketUrl, $namespaces, $data) {
    global $config;

    $basketItemsUrl = $basketUrl . "/items";

    $basketItem = new SimpleXmlElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <basketItem xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://api.spreadshirt.net">
            <quantity>' . $data['quantity'] . '</quantity>
            <element id="' . $data['articleId'] . '" type="sprd:article" xlink:href="http://api.spreadshirt.'.$config['ShopSource'].'/api/v1/shops/' . $data['shopId'] . '/articles/' . $data['articleId'] . '">
            <properties>
            <property key="appearance">' . $data['appearance'] . '</property>
            <property key="size">' . $data['size'] . '</property>
            </properties>
            </element>
            <links>
            <link type="edit" xlink:href="http://' . $data['shopId'] .'.spreadshirt.' .$config['ShopSource'].'/-A' . $data['articleId'] . '"/>
            <link type="continueShopping" xlink:href="http://' . $data['shopId'].'.spreadshirt.'.$config['ShopSource'].'"/>
            </links>
            </basketItem>');

    $header = array();
    $header[] = createAuthHeader("POST", $basketItemsUrl);
    $header[] = "Content-Type: application/xml";
    $result = oldHttpRequest($basketItemsUrl, $header, 'POST', $basketItem->asXML());
}



function createBasket($shop, $namespaces) {

    $basket = new SimpleXmlElement('<basket xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://api.spreadshirt.net">
            <shop id="' . $shop['id'] . '"/>
            </basket>');

    $attributes = $shop->baskets->attributes($namespaces['xlink']);
    $basketsUrl = $attributes->href;
    $header = array();
    $header[] = createAuthHeader("POST", $basketsUrl);
    $header[] = "Content-Type: application/xml";
    $result = oldHttpRequest($basketsUrl, $header, 'POST', $basket->asXML());
    $basketUrl = parseHttpHeaders($result, "Location");

    return $basketUrl;

}






function checkout($basketUrl, $namespaces) {

    $basketCheckoutUrl = $basketUrl . "/checkout";
    $header = array();
    $header[] = createAuthHeader("GET", $basketCheckoutUrl);
    $header[] = "Content-Type: application/xml";
    $result = oldHttpRequest($basketCheckoutUrl, $header, 'GET');
    $checkoutRef = new SimpleXMLElement($result);
    $refAttributes = $checkoutRef->attributes($namespaces['xlink']);
    $checkoutUrl = (string)$refAttributes->href;

    return $checkoutUrl;

}

/*
 * functions to build headers
 */
function createAuthHeader($method, $url) {
    global $config;

    $time = time() *1000;
    $data = "$method $url $time";
    $sig = sha1("$data ".$config['ShopSecret']);

    return "Authorization: SprdAuth apiKey=\"".$config['ShopKey']."\", data=\"$data\", sig=\"$sig\"";

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

function getBasket($basketUrl) {

    $header = array();
    $basket = "";

    if (!empty($basketUrl)) {
        $header[] = createAuthHeader("GET", $basketUrl);
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

}
?>