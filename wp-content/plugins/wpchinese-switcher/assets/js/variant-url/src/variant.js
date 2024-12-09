import URI from 'urijs'

const langArr = [
    'zh-tw',
    'zh-cn',
    'zh-sg',
    'zh-hant',
    'zh-hans',
    'zh-my',
    'zh-mo',
    'zh-hk',
    'zh',
    'zh-reset',
]

export function wpcsRedirectToPage($event) {
    let variantValue = $event.value
    wpcsRedirectToVariant(variantValue)
}

export function wpcsRedirectToVariant(variantValue) {
    let newUrl = window.location.href
    let myUrl = new URI(newUrl)

    const segments = myUrl.segment().filter((item) => item !== '')
    const firstSegment = segments[0]
    const lastSegment = segments[segments.length - 1]
    if (
        !wpc_switcher_use_permalink_type ||
        wpc_switcher_use_permalink_type == '0'
    ) {
        // 'variant'
        myUrl.removeQuery('variant');
        // add 'variant'
        if (variantValue) myUrl.addQuery('variant', variantValue);
    } else {
        if (wpc_switcher_use_permalink_type == '1') {
            console.log(segments);
            // lastSegment add variantValue
            if (langArr.includes(lastSegment)) {
                // remove first segment
                myUrl.segment(segments.length - 1, variantValue);
            } else {
                segments.push(variantValue)
                myUrl.segment(segments)
            }
        } else if (wpc_switcher_use_permalink_type == '2') {
            // firstSegment add variantValue
            if (langArr.includes(firstSegment)) {
                // remove first segment
                myUrl.segment(0, variantValue);
            } else {
                segments.unshift(variantValue)
                myUrl.segment(segments)
            }
        }
    }
    window.location.href = myUrl.toString()
}