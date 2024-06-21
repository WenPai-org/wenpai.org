<?php


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WpBlogTemplate 
{
    static $tag = 'wpblog-location';

    public static function return_post_author_location($city) 
    {
        $header = '<div class="post-author-location"><span class="dashicons dashicons-location"></span>';
        $footer = '</div>';
        $body = esc_html__('Author from', self::$tag) . '' . $city;
        return $header . $body . $footer ;
    }

    public static function echo_post_author_location($city)
    {
        echo self::return_post_author_location($city);
    }

    public static function return_comment_man_location($city, $isAbsolute = false) 
    {
        $headerClass = 'post-comment-location';
        if ($isAbsolute) $headerClass .= ' post-comment-absolute';
        $header = '<div class="'. $headerClass .'"><span class="dashicons dashicons-location"></span>';
        $footer = '</div>';
        $body = esc_html__( 'From', self::$tag ) . '' . $city;
        return $header . $body . $footer ;
    }

    public static function echo_comment_man_location($city)
    {
        echo self::return_comment_man_location($city);
    }
}



