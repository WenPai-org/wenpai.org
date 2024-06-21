jQuery(document).ready(function($){
    
// initialize the colorpicker
        $('.bsp-color-picker').wpColorPicker();
	
    
// handle back to top button and clicks
        var btn = $( '#back-to-top' );
        // scroll actions
        $(window).on( 'scroll', function() {
                // show button if scrolled away from top
                if ( $( window ).scrollTop() > 120 ) {
                        btn.addClass( 'show' );
                // else, hide button
                } else {
                        btn.removeClass( 'show' );
                }
        });
        // click actions
        btn.on( 'click', function( e ) {
                // prevent page loading
                e.preventDefault();
                // do fancy srolling animation
                $( 'html, body' ).animate({
                        scrollTop: 0
                }, '300' );
        });

});
