/**
 * Created by somalia on 3/19/2015.
 */
$(document).ready(function(){
    var distanceFromTop;
    var absoluteY;
    var container = document.querySelector('#pantry #container');

    $("#overlay").css("height", $(document).height());
    centerDivOverlay("#accountSettings");
    centerDivOverlay("#changeEmail");
    centerDivOverlay("#changeUsername");
    centerDivOverlay("#changePassword");
    centerDivOverlay("#changesMade")


    $('#accountSettingsLink').click(function(){
        $("#overlay").fadeIn("fast");
        $("#accountSettings").fadeIn("fast");
    });

    $('#overlay').click(function(){
        $("#overlay").fadeOut("fast");
        $("#accountSettings").fadeOut("fast");
        $("#changeEmail").fadeOut("fast");
        $("#changeUsername").fadeOut("fast");
        $("#changePassword").fadeOut("fast");
        $("#changesMade").fadeOut("fast");
    });

    $('div.return').click(function(){
        $("#overlay").fadeOut("fast");
        $("#accountSettings").fadeOut("fast");
        $("#changeEmail").fadeOut("fast");
        $("#changeUsername").fadeOut("fast");
        $("#changePassword").fadeOut("fast");
        $("#changesMade").fadeOut("fast");

    });

    $('div.back').click(function(){
        $("#accountSettings").fadeIn("fast");
        $("#changeEmail").fadeOut("fast");
        $("#changeUsername").fadeOut("fast");
        $("#changePassword").fadeOut("fast");
        $("#changesMade").fadeOut("fast");
    });

    $('#changeEmailLink').click(function(){
        $("#accountSettings").fadeOut("fast");
        $("#changeEmail").fadeIn("fast");
    });

    $('#changeUsernameLink').click(function(){
        $("#accountSettings").fadeOut("fast");
        $("#changeUsername").fadeIn("fast");
    });

    $('#changePasswordLink').click(function(){
        $("#accountSettings").fadeOut("fast");
        $("#changePassword").fadeIn("fast");
    });
});

function centerDivOverlay(divID){
    var windowWidth = $( window ).width();
    var accountSettingsWidth = $(divID).width();
    var centerLocationX = (windowWidth - accountSettingsWidth)/2;

    var windowHeight = $( window ).height();
    var accountSettingsHeight = 500; //height in pixel
    var centerLocationY = (windowHeight - accountSettingsHeight)/2;

    $(divID).css("left", centerLocationX+"px");
    $(divID).css("top", centerLocationY+"px");

    $( document ).scroll(function() {
        distanceFromTop = window.pageYOffset;
        absoluteY = distanceFromTop+centerLocationY;
        $(divID).css("top", absoluteY+"px");
    });
}