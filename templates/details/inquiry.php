<?php
//include_once("../../../../../wp-load.php");
wp_head();
 /*
 Template Name: Inquiry box
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="MSSmartTagsPreventParsing" content="true" />
  <meta http-equiv="Imagetoolbar" content="No" />
  <title>Inquiry</title>
  
  <style type="text/css">
  	body {font-size:13px;}
  </style>
  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
  <link rel='stylesheet' id='gforms_css-css'  href='<?php bloginfo('url'); ?>/wp-content/plugins/gravityforms/css/forms.css?ver=3.0.1' type='text/css' media='all' />
  <script type='text/javascript' src='<?php bloginfo('url'); ?>/wp-content/plugins/gravityforms/js/conditional_logic.js?ver=1.3.13.1'></script>
  <?php wp_head();?>
  </head>
<body>
 <?php gravity_form(12, $display_title=false, $display_description=false, $display_inactive=false, $field_values=array('mlnumber' => $_REQUEST['mlsnumber'],'mlnumber_inquiry'=>$_REQUEST['mlsnumber']), $ajax=true, $tabindex);
 	//echo do_shortcode("[gravityform id=9 name=ExampleForm ajax=true]");
  ?>
  <?php wp_footer(); ?>
</body>
</html>
