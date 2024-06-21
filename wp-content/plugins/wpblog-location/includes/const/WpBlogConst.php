<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/**
 * WP Blog Post Const
 */
class WpBlogConst
{
    const IP_ADDRESS_CITY = 'city';
    const IP_ADDRESS_COUNTRY = 'country';
    const IP_ADDRESS_REGION = 'region';
    
    const WPBLOG_POST_DEFAULT_IP_CHECKER = 'local';
    const WPBLOG_POST_DEFAULT_IP_ADDRESS_FORMAT = 'city';
    const WPBLOG_POST_DEFAULT_FALSE = 'f/a/l/s/e';
}