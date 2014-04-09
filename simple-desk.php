<?php
/*
Plugin Name: Simple Desk
Version: 0.0.2
Description: Simple Desk is a ticketing and CRM system for WordPress that is completly email driven.
Plugin URI: http://tinyelk.com
Author: Adam Sewell
Author URI: http://tinyelk.com

	Simple Desk is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Simple Desk is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Simple Desk.  If not, see <http://www.gnu.org/licenses/>.

*/

if ( !defined( 'ABSPATH' ) ) die();

//constants
define( 'SIMPLEDESK_BASE_URI', dirname( __FILE__ ) );
define( 'SIMPLEDESK_BASE_DIR', plugin_dir_path( __FILE__ ));
define( 'SIMPLEDESK_FILE', __FILE__);
define( 'SIMPLEDESK_VERSION', '0.0.2');

$SimpleDesk = new SimpleDesk();

class SimpleDesk {

	function __construct() {
		$this->includes();

		add_action('admin_init', array($this, 'sd_register_settings'));

		register_activation_hook( __FILE__, array( $this, 'on_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'on_deactivation' ) );
	}

	function includes(){
		require_once SIMPLEDESK_BASE_DIR . 'lib/custom-post-types.php';
		require_once SIMPLEDESK_BASE_DIR . 'lib/custom-statuses.php';


		if(is_admin()){
			//general
			require_once SIMPLEDESK_BASE_DIR . 'lib/scripts.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/ajax.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/functions.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/admin-actions.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/admin-pages.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/admin-notices.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/emails/template.php';

			//customers
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/customers/customer-actions.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/customers/customer-functions.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/customers/ui.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/customers/metaboxes.php';

			//tickets
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/tickets/ticket-actions.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/tickets/ticket-functions.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/tickets/ui.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/tickets/metaboxes.php';

			//import and exports
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/import/ui.php';
		}

		require_once SIMPLEDESK_BASE_DIR . 'lib/install.php';


	}

	//register out settings page
	function sd_register_settings(){

		//Our general section
		add_settings_section(
			'sd_general_settings',
			'General Settings',
			array('SimpleDeskAdmin', 'display_general_description'),
			'simple-desk-settings-general'
		);

		add_settings_field(
			'test_field',
			'Test Field',
			array('SimpleDeskAdmin', 'display_general_description'),
			'simple-desk-settings-general',
			'sd_general_settings',
			array('this is a test')
		);

		register_setting('simple-desk-settings-general', 'test_field');
	}

	function on_activation() {

	}

	function on_deactivation() {
	}

}
