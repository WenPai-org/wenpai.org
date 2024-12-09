/**
 * JS for Email Headers Module
 */

/**
 * Validate email. Email is also validated on php side.
 * @param {type} param
 */
jQuery( document ).ready( function($) {

    $('.module-branda-form.module-emails-headers-php').on( 'submit', function(e) {
        e.preventDefault();

        var hasError = false,
            emailInputs = [
                'simple_options_headers_email',
                'simple_options_reply-to_email',
            ];
        
        for (var i = 0; i < emailInputs.length; i++) {
            var input = $( `#${emailInputs[i]}` );

            input.css( 'border-color', '#ddd' ).closest( 'div' ).find( '.sui-description.branda-email-input-error' ).remove();

            if ( '' !== input.val() && ! brandaValidateEmail( input.val() ) ) {
                hasError = true;

                input.css( 'border-color', '#ff6d6d' ).closest( 'div' ).append(
                    $('<div/>',{
                        'class' : 'sui-description branda-email-input-error branda-error',
                        'style' : 'font-weight: bold;color:#ff6d6d',
                        text : ub_admin.messages.email_headers.invalid_email
                    })
                );

                input.on( 'change', function() {
                    $(this).css( 'border-color', '#ddd' ).closest( 'div' ).find( '.sui-description.branda-email-input-error' ).remove();
                });
            }
        }

        if ( ! hasError ) {
            e.currentTarget.submit();
        }    
    });

    var brandaValidateEmail = (email) => {
        return email.match(
            /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
    };
    
});
