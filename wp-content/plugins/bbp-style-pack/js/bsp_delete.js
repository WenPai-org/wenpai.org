jQuery( function($) {       
        $('a.bbp-topic-trash-link').click( function( event ) {
                if( ! confirm( 'Are you sure you want to delete this topic?' ) ) {
                        event.preventDefault();
                }           
        });
});
