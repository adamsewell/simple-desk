<?php
/*
Plugin Name: Simple Desk
Version: 0.0.3
Description: Simple Desk is a simple ticket and customer management system for WordPress.
Plugin URI: http://simpledesk.io
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
define( 'SIMPLEDESK_VERSION', '0.0.3');

$SimpleDesk = new SimpleDesk();

class SimpleDesk {

	function __construct() {
		$this->includes();
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

			//email
			require_once SIMPLEDESK_BASE_DIR . 'lib/emails/template.php';
			require_once SIMPLEDESK_BASE_DIR . 'lib/emails/functions.php';

			//import and exports
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/import/ui.php';

			//settings
			require_once SIMPLEDESK_BASE_DIR . 'lib/admin/settings/settings.php';
		}

		require_once SIMPLEDESK_BASE_DIR . 'lib/install.php';
	}

}
