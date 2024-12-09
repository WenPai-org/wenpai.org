( function ( $ ) {
    /**
     * bind
     */
    $( document ).ready( function () {
        var value;
        $( ub_cookie_notice.id + ' .ub-cn-set-cookie' ).on( 'click', function ( e ) {
            e.preventDefault();
            $( this ).setUBCookieNotice();
        } );
        /**
         * it ws already shown
         */
        value = $.fn.BrandaGetCookieValue( ub_cookie_notice.cookie.name + '_close' );
        if ( 'hide' === value ) {
            $( ub_cookie_notice.id ).hide();
        }
    } );

    /**
     * get cookie value
     */
    $.fn.BrandaGetCookieValue = function( cname ) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    /**
     * set Cookie Notice
     */
    $.fn.setUBCookieNotice = function () {
        var notice = $( ub_cookie_notice.id );
        var expires = new Date();
        var value = parseInt( expires.getTime() );
        var cookie = '';
        /**
         * set time
         */
        value = parseInt( expires.getTime() );
        /**
         * add time
         */
        value += parseInt( ub_cookie_notice.cookie.value ) * 1000;
        /**
         * add time zone
         */
        value += parseInt( ub_cookie_notice.cookie.timezone ) * 1000;
        /**
         * set time
         */
        expires.setTime( value );
        /**
         * add cookie timestamp
         */
        cookie = ub_cookie_notice.cookie.name + '=' + value/1000 + ';';
        cookie += ' expires=' + expires.toUTCString() + ';';
        if ( ub_cookie_notice.cookie.domain ) {
            cookie += ' domain=' + ub_cookie_notice.cookie.domain + ';';
        }
        /**
         * Add cookie now (fix cache issue)
         */
        cookie += ' path=' + ub_cookie_notice.cookie.path + ';';
        if ( 'on' === ub_cookie_notice.cookie.secure ) {
            cookie += ' secure;'
        }
        document.cookie = cookie;
        cookie = ub_cookie_notice.cookie.name + '_close=hide;';
        cookie += ' expires=;';
        if ( ub_cookie_notice.cookie.domain ) {
            cookie += ' domain=' + ub_cookie_notice.cookie.domain + ';';
        }
        cookie += ' path=' + ub_cookie_notice.cookie.path + ';';
        if ( 'on' === ub_cookie_notice.cookie.secure ) {
            cookie += ' secure;'
        }
        document.cookie = cookie;
        /**
         * set user meta
         */
        if ( undefined !== ub_cookie_notice.logged && 'yes' === ub_cookie_notice.logged ) {
            var data = {
                'action': 'ub_cookie_notice',
                'user_id': ub_cookie_notice.user_id,
                'nonce': ub_cookie_notice.nonce
            };
            $.post( ub_cookie_notice.ajaxurl, data );
        } else {
            // Dimiss the notice for visitor.
            var data = {
                'action': 'ub_dismiss_visitor_notice',
                'nonce': ub_cookie_notice.nonce
            };
            $.post( ub_cookie_notice.ajaxurl, data );
        }
        /**
         * reload
         */
        if ( undefined !== ub_cookie_notice.reloading && 'on' === ub_cookie_notice.reloading ) {
            document.location.reload( true );
            return;
        }
        /**
         * hide
         */
        var animation = undefined !== ub_cookie_notice.animation? ub_cookie_notice.animation:'none';
        switch( animation ) {
            case 'fade':
                notice.fadeOut( 400 );
                break;
            case 'slide':
                notice.slideUp( 400 );
                break;
            default:
                notice.hide();
        }
    };

} )( jQuery );
