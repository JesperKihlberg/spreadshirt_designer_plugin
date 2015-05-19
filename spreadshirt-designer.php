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
include("functions.php");
include("designer.php");
include("products.php");
include("department.php");
include("categories.php");

add_shortcode( 'designer', 'designer_func' );
add_shortcode( 'products', 'products_func' );
add_shortcode( 'department', 'department_func' );
add_shortcode( 'category', 'category_func' );
