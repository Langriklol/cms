HTMLCollection.prototype.indexOf = [].indexOf;

$(document).ready(function(){
    var stars = $(".review-star");
    stars.click(setStars);
    stars.mouseenter(starHover);
    stars.mouseleave(returnStarState);
    stars[2].click(); // Sets default rating value to 3
}); //Zkurvené anonymní funkce :))

//Set stars value
function setStars() {
    var selectedStars = this.parentElement.children.indexOf(this) + 1;
    $("#rating").val(selectedStars);
    nette.ajax(window.location.href, {
        type: 'POST',
        url: window.location.href,
        data: {"starCount": selectedStars},
        dataType: 'text',
        async: true,
        error: function () {
            alert("An error occurred during rating.");
        }

    });
    returnStarState();
}

//Resets all stars and select all to cursor
function starHover() {
    var stars = $(".review-star");
    var selectedStars = this.parentElement.children.indexOf(this) + 1;
    stars.addClass("glyphicon-star-empty");
    stars.removeClass("glyphicon-star");

    for (var i = 0; i < selectedStars; i++) {
        $(stars[i]).removeClass("glyphicon-star-empty");
        $(stars[i]).addClass("glyphicon-star");
    }
}

function returnStarState() {
    var stars = $(".review-star");
    stars.addClass("glyphicon-star-empty");
    stars.removeClass("glyphicon-star");

    for (var i = 0; i < $("#rating").val(); i++) {
        $(stars[i]).removeClass("glyphicon-star-empty");
        $(stars[i]).addClass("glyphicon-star");
    }
}