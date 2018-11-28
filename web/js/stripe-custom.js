/**
 *                  JQuery file
 * This file modifies button display loaded from stripe website and match the site design
 */
$(document).ready(function(){
    let textFr = "Je paie par carte bancaire";
    let textEn = "Pay with Card";
    if ($("html").is(":lang(fr)")) {
        $(".stripe-button-el").find("span").text(textFr).removeAttr("style");
    } else {
        $(".stripe-button-el").find("span").text(textEn).removeAttr("style");
    }
    $(".stripe-button-el").addClass("btn btn-primary").removeClass("stripe-button-el");
});