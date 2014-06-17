<?php
/**
 * Base Admin
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SimpleDeskAdmin extends SimpleDesk{

    public function __construct(){
        $this->includes();
    }

    private static function includes(){
        require_once SIMPLEDESK_BASE_DIR . 'views/customers/customer-actions.php';
    }

    /*
    * Display Settings Page 
    */
	public static function display_settings() {
		$options = get_option('simple-desk-settings');
?>
		<div class="wrap">
			<div id="icon-themes" class="icon32"></div> 
			<h2><?php _e('Simple Desk Settings', 'sd'); ?></h2>
			<?php settings_errors(); ?> 

			<p class="description"><?php _e('This is a test', 'sd'); ?></p>

			<form post="post" action="options.php">
				<?php settings_fields( 'simple-desk-settings-general' ); ?>
				<?php do_settings_sections('simple-desk-settings-general'); ?>
				<?php submit_button(); ?>  
			</form>
		</div>
<?php

	}

    /*
    * Customers Page 
    */

	public static function display_general_description(){
		echo '<p></p>';
	}

    /*
    * Display Tickets Page 
    */
	public static function display_tickets(){
		$options = get_option('simple-desk-settings');

		$Tickets = new SimpleDeskTicketTable();
		$Tickets->prepare_items();
?>
		<div class="wrap">
			<div id="icon-users" class="icon32"></div>
			<h2><?php _e('Tickets'); ?></h2>
			<form id="sd_tickets" method="get" action="">
				<?php $Tickets->search_box('Search', 'sd-tickets'); ?>
				<?php $Tickets->display(); ?>
			</form>
		</div>
<?php
	}

}



/*
* Tickets List Table 
*/
class SimpleDeskTicketTable extends WP_List_Table{

	function prepare_items(){
		$columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
	}

	function get_columns(){
        $columns = array(
            'customer' => 'Customer',
            'title' => 'Title',
            'id' => 'Number',
            'modified' => 'Last Modified'
        );

        return $columns;
    }

    function get_hidden_columns(){
    	return array();
    }

    function column_default($item, $column_name){
    	
    	switch($column_name){
    		default:
    			return $item[$column_name];
    	}
    }

    function get_sortable_columns(){
        return array('customer' => array('customer', false));
    }

    function table_data(){
    	$data = array();

        $data[] = array(
                    'id'          => 1,
                    'customer'       => 'The Shawshank Redemption',
                    'title' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                    'modified'        => '1994'
                    );

        $data[] = array(
                    'id'          => 2,
                    'customer'       => 'The Godfather',
                    'title' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                    'modified'        => '1972'
                    );

        return $data;
    }

    function sort_data( $a, $b ){
    	$orderby = 'modified';
    	$order = 'asc';
    }

    function search_box($text, $input_id){
?>
    	<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button( $text, 'button', false, false, array('ID' => 'search-submit') ); ?>
		</p>
<?php
    }
}
