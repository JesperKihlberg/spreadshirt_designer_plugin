<?php
/**
 * @package Spreadshirt-Designer
 */
/*
Plugin Name: Spreadshirt Designer
Plugin URI: http://jesperkihlberg.dk/spreadshirtdesigner
Description: Allows you to add a spreadshirt designer on your wordpress site. The designer allows for url parameters to be passed. The following parameters are supported: productid, product, designid, productcolor, designcolor1, designcolor2, department. The correspondent ids are found by using the spreadshirt api. Work is in progress to add this information to the plugin as well.
Version: 1.0.0
Author: Jesper Kihlberg
Author URI: http://blog.jesperkihlberg.dk/
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: spreadshirt-designer
*/

/*
This theme, like WordPress, is licensed under the GPL.
Use it to make something cool, have fun, and share what you've learned with others.
*/
include("sd-functions.php");
include("designer.php");
include("product.php");
include("department.php");
include("category.php");
//include("designs.php");

add_shortcode( 'designer', 'designer_func' );
add_shortcode( 'product', 'product_func' );
add_shortcode( 'department', 'department_func' );
add_shortcode( 'category', 'category_func' );
//add_shortcode( 'designs', 'designs_func');

add_action('wp_enqueue_scripts', 'add_css_func');
add_action('wp_enqueue_scripts', 'add_js_func');

function add_css_func()
{
  wp_register_style( 'custom-style', plugins_url( '/spreaddesign.css', __FILE__ ), array(), '20120208', 'all' );
  wp_register_style( 'custom-style', get_template_directory_uri() . '/css/spreaddesign.css', array(), '20120208', 'all' );
  wp_enqueue_style( 'custom-style' );

}

function add_js_func(){
    wp_enqueue_script( 'custom-script', plugins_url( '/spreaddesign.js', __FILE__ ) );
}
?>
