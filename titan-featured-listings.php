<?php
//ini_set("display_errors",1);
//error_reporting(E_ALL);
/*
Plugin Name:  TFLP 
Version:  0.2  
Plugin URI:  http://MightyAgent.com/
Description:  To show featured listings using various short-codes - 
[raw]
 *[titanfeaturedagents type="all" result="default" details="default" rows="5" nrdsid="502003013, 502001624, 502001753"]
 *[titanfeaturedoffices type="act" result="default" details="default" officeid = "7165, 5112"]
 *[titanfeaturedmlnumbers result="default" details="default" mlnumber="4353357"]
 *[titanfeaturedcities type="act" result="default" details="default" city="Apple Valley" price="150000-200000"]
 *[titanfeaturedneighborhoods type="act" result="default" details="default" neighborhood="Victory"]
 *[titanfeaturedzips type="act" result="default" details="default" zip="55410" sort="listprice asc"]
 *[titanfeaturedschooldistricts type="act" result="default" details="default" schooldistrict="Rosemount/Apple Valley"]
 * 
 *[titanfeaturedlakes type="act" result="default" details="default" lake="Lac Lavon" sort="listprice desc" style="(SF) Two Stories,(SF) One Story"]
 *[titanfeaturedstreets type="act" result="default" details="default" streetnumber="4349" streetname="Washburn"]				  
[/raw]
Author:  MightyAgent.com - Manish Hatwalne
Author URI:  http://MightyAgent.com/
*/
require("featured_listings.php");
/* Runs when plugin is activated */
register_activation_hook(__FILE__,'titan_featured_plugin_install');

function titan_featured_plugin_install() {
    global $wpdb;
    $the_page_title     = 'Request a Showing';
    $the_page_name      = 'request-a-showing';
    $the_page_title2     = 'Inquiry';
    $the_page_name2      = 'inquiry';
    
    $is_page = $wpdb->get_var("SELECT count(post_title) as pt FROM wp_posts WHERE `post_name` LIKE '%request-a-showing%'");
	$is_page_inquiry = $wpdb->get_var("SELECT count(post_title) as pt FROM wp_posts WHERE `post_name` LIKE '%inquiry%'");
    
    if($is_page != '' && $is_page ==0)
	{
            $wpdb->insert(  $wpdb->prefix."posts", array(
                            'post_title'    => $the_page_title,
                            'post_name'    => $the_page_name,
                            'post_content'  => "This text may be overridden by the plugin. You shouldn't edit it.",
                            'post_status'   => 'publish',
                            'post_type'     => 'page',
                            'comment_status'=> 'closed',
                            'ping_status'   => 'closed',
            )
            );
        }

    if($is_page_inquiry != '' && $is_page_inquiry ==0)
    {
            $wpdb->insert(  $wpdb->prefix."posts", array(
                            'post_title'    => $the_page_title2,
                            'post_name'    => $the_page_name2,
                            'post_content'  => "This text may be overridden by the plugin. You shouldn't edit it.",
                            'post_status'   => 'publish',
                            'post_type'     => 'page',
                            'comment_status'=> 'closed',
                            'ping_status'   => 'closed',
            )
            );
        }
}
add_filter( 'page_template', 'titan_request_page_template' );
function titan_request_page_template( $page_template )
{
    if ( is_page( 'request-a-showing' ) ) {
        $page_template = dirname( __FILE__ ) . '/templates/details/request-a-showing.php';
	//$page_template 	 = get_stylesheet_directory() . '/template-loan-application.php';
    }

    return $page_template;
}

add_filter( 'page_template', 'titan_inquiry_page_template' );
function titan_inquiry_page_template( $page_template )
{
    if ( is_page( 'inquiry' ) ) {
        $page_template = dirname( __FILE__ ) . '/templates/details/inquiry.php';
    
    }

    return $page_template;
}


$fl = new FeaturedListings();
$fl->register_shortcodes();

?>