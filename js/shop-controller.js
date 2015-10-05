var shopajaxUrl = "/app/spreadbasket.php/";
var language = "dk";
var shopId = "1034542";
var props="l=" + language + "&shop=" + shopId;

jQuery(function ($) {
    //$('form').submit(function(event) {
    $(".addToCart").click(function (event) {
        event.preventDefault();

        var $data = [];
        var $form = $(this).parent().parent().find("form");

        $data.push(
        { name: "appearance", value: $form.find("input[name=color]").val() },
        { name: "view", value: $form.find("input[name=view]").val() },
        { name: "article", value: $form.find("#articleId").val() },
        { name: "quantity", value: $form.find("#quantity").val() },
        { name: "size", value: $form.find("#size").val() }
        );

        $.post(shopajaxUrl+"?"+props, $data, function (json) {

            $("a.openpop").attr("href",  json.c.u);
            $(".basket-counter").text(json.c.q);
        }, "json");

        return false;
    });

    // color changer
    $(".appearanceIcon").click(function (e) {
        e.preventDefault();

        var $this = $(this);
        var $form = $(this).parent().parent();
        var $color = $this.attr("appearanceId");
        var $img = $form.find("img[class=articleThumb]");
        var $baseUrl = $img.attr("baseUrl");
        $img.prop('src', $baseUrl + $color);
        $form.find("input[name=color]").val($color);
    });

    // get basket on call
    $.get(shopajaxUrl+"?basket"+"&"+props, function (json) {
        $("a.openpop").attr("href", json.c.u);
        $(".basket-counter").text(json.c.q);
    }, "json");

});

$(document).ready(function () {
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
        $.get(shopajaxUrl+"?basket"+"&"+props, function (json) {
            $("a.openpop").attr("href", json.c.u);
            $(".basket-counter").text(json.c.q);
        }, "json");
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

