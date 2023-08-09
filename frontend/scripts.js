$(document).ready(function(){
    $('.your-slider').slick({
        autoplay: true,
        autoplaySpeed: 2000,
        arrows: true,
        // dots: true,
        infinite: true,
        speed: 500,
        adaptiveHeight: true

    });
});

// this function retrieves the value of a URL parameter by its name
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

// use the function to get the value of the 'recommendation' parameter.
const recommendation = getParameterByName('recommendation');

// display the recommendation in the HTML container
document.getElementById('recommendation').innerText = recommendation;
