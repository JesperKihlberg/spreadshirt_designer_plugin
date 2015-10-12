var shopajaxUrl = ShopAjax.ajaxurl;
var language = "dk";
var shopId = "1034542";
//var props="l=" + language + "&shop=" + shopId;

jQuery(function ($) {
    //$('form').submit(function(event) {
    $(".addToCart").click(function (event) {
        event.preventDefault();

        var $form = $(this).parent().parent().find("form");

        var data ={
         action : 'spreadshirtdesignershop-additem' ,
        shopid : shopId ,
        language : language ,
         appearance : $form.find("input[name=color]").val() ,
         view : $form.find("input[name=view]").val() ,
         article : $form.find("#articleId").val() ,
         quantity : $form.find("#quantity").val() ,
         size : $form.find("#size").val() 
        };

        $.post(shopajaxUrl, data, function (json) {

            $("a.openpop").attr("href",  json.c.u);
            $(".basket-counter").text(json.c.q);
        }, "json");

        return false;
    });

    // color changer
    $(".appearanceIcon").click(function (e) {
        e.preventDefault();

        var $this = $(this);
        var $form = $(this).parent().parent().parent();
        var $color = $this.attr("appearanceId");
        var $img = $form.find("img[class=articleThumb]");
        var $baseUrl = $img.attr("baseUrl");
        $img.prop('src', $baseUrl + $color);
        $form.find("input[name=color]").val($color);
    });
    var data ={
            action : 'spreadshirtdesignershop-getbasket' ,
            shopid : shopId ,
            language : language
            };
    // get basket on call
    $.get(shopajaxUrl, data, function (json) {
        $("a.openpop").attr("href", json.c.u);
        $(".basket-counter").text(json.c.q);
    }, "json");

});

jQuery(document).ready(function($){
    $(".popup").hide();
    $(".openpop").click(function (e) {
        e.preventDefault();
        $("iframe").attr("src", $(this).attr("href"));
        $(".links").fadeOut("fast");
        $(".popup").fadeIn("fast");
    });

    $(".closeCheckout").click(function () {
        $(this).parent().fadeOut("fast");
        $(".links").fadeIn("fast");
    	var data ={
            action : 'spreadshirtdesignershop-getbasket' ,
            shopid : shopId ,
            language : language
            };
        $.get(shopajaxUrl, data, function (json) {
            $("a.openpop").attr("href", json.c.u);
            $(".basket-counter").text(json.c.q);
        }, "json");
    });

    $(".productDescription").hide();
    $(".designerLink").click(function (e) {
        e.preventDefault();
        $(this).parent().parent().find(".productDescription").fadeIn("fast");
    });

    $(".closeProductDescription").click(function () {
        $(this).parent().fadeOut("fast");
    });
});

/*
function getArticleThumbs() {
    return document.getElementsByClassName("articleThumb");
}

function getDesignerLinks() {
    return document.getElementsByClassName("designerLink");
}
*/

