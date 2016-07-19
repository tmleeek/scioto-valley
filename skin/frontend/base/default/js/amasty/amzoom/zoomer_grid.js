AmZoomergrid = Class.create();
AmZoomergrid.prototype = {
        /*preloader image*/
        preloader : null,
        /*json data from block with images*/
        data : null,
        /*settings*/
        options : null,

        initialize : function(data, options) {
            this.data = data;
            this.options = options;
        },

        loadZoom : function() {
            var options = this.options;
            this.data.each(function(item){
                $$('img[src*="' + item.grid_image + '"]').each(function (img) {
                    img.setAttribute('data-zoom-image', item.orig_image);
                    jQuery(img).elevateZoom(options);
                    return false;
                });
            });
        }
}
