/**
 *                  JQuery file
 * This file modifies button display loaded from stripe website and match the site design
 */
$(document).ready(function(){
    let text_fr = 'Je paie par carte bancaire';
    let text_en = 'Pay with Card';
    if ($('html').is(':lang(fr)')) {
        $(".stripe-button-el").find("span").text(text_fr).removeAttr('style');
    } else {
        $(".stripe-button-el").find("span").text(text_en).removeAttr('style');
    }
    $(".stripe-button-el").addClass('btn btn-primary').removeClass("stripe-button-el");
});