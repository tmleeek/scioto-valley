// Fixes overlapping list items in product grid for IE9

jQuery(document).ready(function($){
     // init Isotope
    var grid = $('.grid').isotope({
        // options...
    });
// layout Isotope after each image loads
    grid.imagesLoaded().progress( function() {
        grid.isotope('layout');
    });
    jQuery('li .item .isotope-item').each(function(){
        jQuery(this).css('left','margin-left');
    });
});