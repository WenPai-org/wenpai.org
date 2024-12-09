let wpc_switcher_use_permalink_type
if (typeof wpc_switcher_use_permalink !== 'undefined') {
    wpc_switcher_use_permalink_type = wpc_switcher_use_permalink['type']
}

function wpcsRedirectToPage($event) {
    if (typeof WPCSVariant === 'undefined') return ;
    return WPCSVariant.wpcsRedirectToPage($event)
}

function wpcsRedirectToVariant(variantValue) {
  if (typeof WPCSVariant === 'undefined') return ;
  return WPCSVariant.wpcsRedirectToVariant(variantValue)
}