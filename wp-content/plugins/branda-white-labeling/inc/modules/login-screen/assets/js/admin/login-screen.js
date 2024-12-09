jQuery( document ).ready( function( $ ) {
    /**
     * Show Template on first time
     */
    function branda_login_template_auto_show() {
        var branda_login_template_big_button = $( '.branda-settings-tab-content-login-screen .branda-section-theme button.branda-big-button' );
        var dialog_id = branda_login_template_big_button.data('modal-open');
        var current_tab = $('.sui-wrap .sui-sidenav .sui-vertical-tabs .sui-vertical-tab.current a').data('tab')
        if ( 'login-screen' !== current_tab ) {
            return;
        }
        if ( 'yes' === branda_login_template_big_button.data('has-configuration' ) ) {
            return;
        }
        if (
            'undefined' === typeof SUI ||
            'undefined' === typeof $( '#' + dialog_id )
        ) {
            window.setTimeout( branda_login_template_auto_show, 100 );
        } else {
            SUI.openModal( dialog_id, this, null, true );
        }
    }
    branda_login_template_auto_show();
    $('.sui-wrap .sui-sidenav .sui-vertical-tabs .sui-vertical-tab a[data-tab=login-screen]').on( 'click', function() {
        branda_login_template_auto_show();
    });
    /**
     * Radio selector change.
     */
    $( 'input[name=branda-login-screen-template]' ).on( 'change', function() {
        $( '.branda-login-screen-choose-template-dialog li').removeClass( 'branda-selected' );
        if( $(this).is(':checked') ) {
            $(this).closest('li').addClass( 'branda-selected' );
        }
    });
    /**
     * Show/hide Form Shadow options
     */
    $( '.ub-radio.branda-login-screen-form-style' ).on( 'change', function() {
        if (
            $(this).is( ':checked' )
            && 'shadow' === $(this).val()
        ) {
            $( '.sui-row.branda-login-screen-form-style' ).show();
            return;
        }
        $( '.sui-row.branda-login-screen-form-style' ).hide();
    });
    /**
     * Set selected template
     */
    $('.branda-login-screen-choose-template').on( 'click', function() {
        var id = $( 'input[name=branda-login-screen-template]:checked' ).val();
        if ( id ) {
            var data = {
                action: 'branda_login_screen_set_template',
                _wpnonce: $(this).data('nonce'),
                id: id
            };
            $.post( ajaxurl, data, function( response ) {
                if ( response.success ) {
                    window.location.reload();
                } else {
                    SUI.openFloatNotice( response.data.message );
                }
            });
        }
    });
});
