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

// Esta función obtiene el valor de un parámetro de la URL por su nombre
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

// Usa la función para obtener el valor del parámetro 'recommendation'
const recommendation = getParameterByName('recommendation');

// Muestra la recomendación en el contenedor HTML
document.getElementById('recommendation').innerText = recommendation;
