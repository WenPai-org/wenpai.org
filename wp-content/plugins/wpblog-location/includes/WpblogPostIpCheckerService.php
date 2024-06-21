<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// ip checker service
class WpblogPostIpCheckerService
{
    private static $instance;
    private $config;

    private function __construct()
    {
        $this->setConfig([
            'wpblog_post_show_comment_location' => get_option('wpblog_post_show_comment_location', false),
            'wpblog_post_show_author_location' => get_option('wpblog_post_show_author_location', false),
            'wpblog_post_ip_address_custom_for_admin' => get_option(
                'wpblog_post_ip_address_custom_for_admin',
                WpBlogConst::WPBLOG_POST_DEFAULT_FALSE
            ),
            'wpblog_post_show' => get_option('wpblog_post_show', true),
            'wpblog_post_ip_checker' => get_option('wpblog_post_ip_checker', WpBlogConst::WPBLOG_POST_DEFAULT_IP_CHECKER),
            'wpblog_post_ip_address_format' => get_option(
                'wpblog_post_ip_address_format',
                WpBlogConst::WPBLOG_POST_DEFAULT_IP_ADDRESS_FORMAT
            ),
            'wpblog_post_show_post_location' => get_option('wpblog_post_show_post_location', false)
        ]);
    }

    public function setConfig($configArr)
    {
        $this->config = $configArr;
    }

    public function getConfigArr($field = '')
    {
        if (!empty($field) && isset($this->config[$field])) return $this->config[$field]; 
        return $this->config;
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // format ip address
    public function format_ip_address_city($arr) {
        $city = $arr[WpBlogConst::IP_ADDRESS_CITY] ?? '';
        $country = $arr[WpBlogConst::IP_ADDRESS_COUNTRY] ?? '';
        $region = $arr[WpBlogConst::IP_ADDRESS_REGION] ?? '';

        // if empty city country region can't see
        if (empty($city) && empty($country) && empty($region)) return '';

        $result = $this->getConfigArr('wpblog_post_ip_address_format');
        $result = str_replace(WpBlogConst::IP_ADDRESS_CITY, $city, $result);
        $result = str_replace(WpBlogConst::IP_ADDRESS_COUNTRY, $country, $result);
        $result = str_replace(WpBlogConst::IP_ADDRESS_REGION, $region, $result);

        return $result;
    }

    public function getIpCheckerByIp($ip)
    {
        if (empty($ip)) return '';
        $ipChecker = $this->getConfigArr('wpblog_post_ip_checker');
        $dataArr = [
            WpBlogConst::IP_ADDRESS_CITY => '',
            WpBlogConst::IP_ADDRESS_COUNTRY => '',
            WpBlogConst::IP_ADDRESS_REGION => ''
        ];
        switch ($ipChecker) {
            case WpBlogConst::WPBLOG_POST_DEFAULT_IP_CHECKER:
            default:
                $reader = new Reader(__DIR__ . '/ipipfree.ipdb');
                try {
                    if ($reader->find($ip)) {
                        $dataArr[WpBlogConst::IP_ADDRESS_CITY] = $reader->find($ip)[2];
                        $dataArr[WpBlogConst::IP_ADDRESS_REGION] = $reader->find($ip)[1];
                        $dataArr[WpBlogConst::IP_ADDRESS_COUNTRY] = $reader->find($ip)[0];
                    }
                    return $this->format_ip_address_city($dataArr);
                } catch (\Throwable $th) {
                    return '';
                }
            case 'ipapi':
                // api url
                $url = 'http://ip-api.com/json/'. $ip .'?lang=zh-CN';
                $response = wp_remote_get($url);
                // if err != nil return empty
                if (is_wp_error($response)) return '';
                
                $body = wp_remote_retrieve_body($response);
                $arr = json_decode($body, true) ?? [];
                $dataArr[WpBlogConst::IP_ADDRESS_CITY] = $arr['city'] ?? '';
                $dataArr[WpBlogConst::IP_ADDRESS_REGION] = $arr['regionName'] ?? '';
                $dataArr[WpBlogConst::IP_ADDRESS_COUNTRY] = $arr['country'] ?? '';
                return $this->format_ip_address_city($dataArr);
        }
    }
    
    public function batchApiIpCheck($ipArr)
    {
        $api_url = 'http://ip-api.com/batch';
        $opt_arr = [];
        foreach ($ipArr as $ipItem) {
            $cache_key_item = 'wpblog_post_ip_' . $ipItem;
            if (wp_cache_get($cache_key_item, 'wpblog_post_ip')) continue;
            $opt_arr[] = [
                "query" => $ipItem,
                "fields" => 'status,message,query,country,city,regionName',
                "lang" => 'zh-CN',
            ];
        }
        $json_data = json_encode($opt_arr);

        $request_args = array(
            'body'    => $json_data,
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'timeout' => 20,
        );

        $response = wp_remote_post( $api_url, $request_args );

        if ( !is_wp_error( $response ) ) {
            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );
            foreach ($data as $item) {
                $dataArr = [];
                if ($item['status'] == 'success') {
                    $dataArr[WpBlogConst::IP_ADDRESS_CITY] = $item['city'] ?? '';
                    $dataArr[WpBlogConst::IP_ADDRESS_REGION] = $item['regionName'] ?? '';
                    $dataArr[WpBlogConst::IP_ADDRESS_COUNTRY] = $item['country'] ?? '';
                    $infoAddress = $this->format_ip_address_city($dataArr);
                    $cache_key = 'wpblog_post_ip_' . $item['query'];

                    if(!empty($infoAddress)) {
                        wp_cache_set($cache_key, $infoAddress, 'wpblog_post_ip', 86400);
                    }
                }
            }
        }
    }
}