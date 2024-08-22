<?php
/**
 * Shortcode Functions
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Add ICP shortcode
add_shortcode('wpicp_license', 'wpicp_license_shortcode');
function wpicp_license_shortcode() {
    $wpicp_license = get_option('wpicp_license');
    if ($wpicp_license) {
        $license_text = '' . $wpicp_license;
        $license_url = 'https://beian.miit.gov.cn';
        $target = '_blank';
        $nofollow = 'nofollow';
        $license_link = '<a href="' . esc_url($license_url) . '" target="' . esc_attr($target) . '" rel="' . esc_attr($nofollow) . '">' . $license_text . '</a>';
        return $license_link;
    }
}

// Add Wangan shortcode
add_shortcode('wpicp_wangan', 'wpicp_wangan_shortcode');
function wpicp_wangan_shortcode() {
    $wpicp_wangan = get_option('wpicp_wangan');
    $wpicp_province = get_option('wpicp_province');

    if ($wpicp_wangan) {
         $wangan_text = '<img src="' . plugins_url('wpicp-license/assets/images/gongan.png') . '" alt="Wangan License" style="vertical-align:middle;" />' . $wpicp_province . '公网安备' . $wpicp_wangan . '号';
         $wangan_url = 'https://www.beian.gov.cn/portal/registerSystemInfo?recordcode=' . urlencode($wpicp_wangan);
         $target = '_blank';
         $nofollow = 'nofollow';
         $wangan_link = '<a href="' . esc_url($wangan_url) . '" target="' . esc_attr($target) . '" rel="' . esc_attr($nofollow) . '">' . $wangan_text . '</a>';
        return $wangan_link;
    }
}

// Add wpicp_province shortcode
add_shortcode('wpicp_province', 'wpicp_province_shortcode');
function wpicp_province_shortcode() {
    $wpicp_province = get_option('wpicp_province');
    $provinces = array(
        '京' => __('Beijing', 'wpicp-license'),
        '津' => __('Tianjin', 'wpicp-license'),
        '冀' => __('Hebei', 'wpicp-license'),
        '晋' => __('Shanxi', 'wpicp-license'),
        '蒙' => __('Inner Mongolia', 'wpicp-license'),
        '辽' => __('Liaoning', 'wpicp-license'),
        '吉' => __('Jilin', 'wpicp-license'),
        '黑' => __('Heilongjiang', 'wpicp-license'),
        '沪' => __('Shanghai', 'wpicp-license'),
        '苏' => __('Jiangsu', 'wpicp-license'),
        '浙' => __('Zhejiang', 'wpicp-license'),
        '皖' => __('Anhui', 'wpicp-license'),
        '闽' => __('Fujian', 'wpicp-license'),
        '赣' => __('Jiangxi', 'wpicp-license'),
        '鲁' => __('Shandong', 'wpicp-license'),
        '豫' => __('Henan', 'wpicp-license'),
        '鄂' => __('Hubei', 'wpicp-license'),
        '湘' => __('Hunan', 'wpicp-license'),
        '粤' => __('Guangdong', 'wpicp-license'),
        '桂' => __('Guangxi', 'wpicp-license'),
        '琼' => __('Hainan', 'wpicp-license'),
        '渝' => __('Chongqing', 'wpicp-license'),
        '川' => __('Sichuan', 'wpicp-license'),
        '黔' => __('Guizhou', 'wpicp-license'),
        '滇' => __('Yunnan', 'wpicp-license'),
        '藏' => __('Tibet', 'wpicp-license'),
        '陕' => __('Shaanxi', 'wpicp-license'),
        '甘' => __('Gansu', 'wpicp-license'),
        '青' => __('Qinghai', 'wpicp-license'),
        '宁' => __('Ningxia', 'wpicp-license'),
        '新' => __('Xinjiang', 'wpicp-license')
    );
    if (isset($provinces[$wpicp_province])) {
        return $provinces[$wpicp_province];
    } else {
        return '';
    }
}

// Add wpicp_p shortcode
add_shortcode( 'wpicp_p', 'wpicp_province_abbr_shortcode' );
function wpicp_province_abbr_shortcode() {
    $wpicp_province = get_option( 'wpicp_province' );

    if ( $wpicp_province ) {
        return $wpicp_province;
    }
}


// Add wpicp_company shortcode
add_shortcode('wpicp_company', 'wpicp_company_shortcode');
function wpicp_company_shortcode() {
    $wpicp_company = get_option('wpicp_company');
    if ($wpicp_company) {
        return esc_html($wpicp_company);
    }
}

// Add wpicp_email shortcode
add_shortcode('wpicp_email', 'wpicp_email_shortcode');
function wpicp_email_shortcode() {
    $wpicp_email = get_option('wpicp_email');
    if ($wpicp_email) {
        return esc_html($wpicp_email);
    }
}

// Add wpicp_phone shortcode
add_shortcode('wpicp_phone', 'wpicp_phone_shortcode');
function wpicp_phone_shortcode() {
    $wpicp_phone = get_option('wpicp_phone');
    if ($wpicp_phone) {
        return esc_html($wpicp_phone);
    }
}

// Add wpicp_edi shortcode
add_shortcode('wpicp_edi', 'wpicp_edi_shortcode');
function wpicp_edi_shortcode() {
    $wpicp_edi = get_option('wpicp_edi');
    if ($wpicp_edi) {
        return esc_html($wpicp_edi);
    }
}

// Add wpicp_app shortcode
add_shortcode('wpicp_app', 'wpicp_app_shortcode');
function wpicp_app_shortcode() {
    $wpicp_app = get_option('wpicp_app');
    if ($wpicp_app) {
        return esc_html($wpicp_app);
    }
}

// Add wpicp_miniapp shortcode
add_shortcode('wpicp_miniapp', 'wpicp_miniapp_shortcode');
function wpicp_miniapp_shortcode() {
    $wpicp_miniapp = get_option('wpicp_miniapp');
    if ($wpicp_miniapp) {
        return esc_html($wpicp_miniapp);
    }
}
