<?php
/**
 * Styles and Scripts
 *
 * @package     SD
 * @subpackage  Functions
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


function simpledesk_admin_css() {
	wp_enqueue_style( 'simple-desk', plugins_url( '../css/simple-desk-admin.css', __FILE__ ), array(), '1.0' );
	wp_enqueue_style( 'chosen-css', plugins_url( '../css/chosen.min.css', __FILE__ ), array(), '1.1.0' );
}
add_action( 'admin_enqueue_scripts', 'simpledesk_admin_css', 1 );

function simpledesk_admin_scripts(){
	wp_enqueue_script('chosen', plugins_url('../js/chosen.jquery.min.js', __FILE__), array('jquery'), '1.1.0');
	wp_enqueue_script('simple-desk-js', plugins_url('../js/simpledesk.js', __FILE__), array('jquery', 'chosen'), '1.1.0');

}
add_action( 'admin_enqueue_scripts', 'simpledesk_admin_scripts', 1 );
