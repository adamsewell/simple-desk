<?php
/**
 * Customer UI
 *
 * @package     SD
 * @subpackage  Admin/Payments
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

function sd_display_customers(){
    if(isset($_GET['sd_page']) && $_GET['sd_page'] == 'add_customer'){
        require_once(SIMPLEDESK_BASE_DIR . 'lib/admin/customers/add-customer.php');
    }elseif(isset($_GET['sd_page']) && $_GET['sd_page'] == 'edit_customer'){
        require_once(SIMPLEDESK_BASE_DIR . 'lib/admin/customers/edit-customer.php');
    }elseif(isset($_GET['sd_page']) && $_GET['sd_page'] == 'view_customer'){
        require_once(SIMPLEDESK_BASE_DIR . 'lib/admin/customers/view-customer.php');
    }else{
        $Customers = new SimpleDeskCustomerTable();
        $Customers->prepare_items();
?>  
        <div class="wrap">
            <h2>
                <?php _e('Customers'); ?> 
                <a href="<?php echo add_query_arg( array( 'sd_page' => 'add_customer' ), remove_query_arg('sd-message') ); ?>" class="add-new-h2">Add New</a>
            </h2>
            <form id="sd_search_form" method="get" action="<?php echo admin_url('admin.php'); ?>">
                <!-- for some reason, adding page=simple-desk to the admin_url above was
                    being stripped out. using a hidden input tag seems to have fixed the issue -->
                <input type="hidden" name="page" id="page" value="simple-desk-customer-page" />
                <?php $Customers->search_box('Search', 'sd-customers'); ?>
            </form>
            <form id="sd_ticket" method="get" action="">
                <?php $Customers->views(); ?>
                <?php $Customers->display(); ?>
            </form>
        </div>
<?php
    }
}

/*
* Customers List Table 
*/
class SimpleDeskCustomerTable extends WP_List_Table{

    function __construct(){
        global $status, $page;

        parent::__construct(array(
            'singular' => 'customer',
            'plural' => 'customers',
            'ajax' => false
        ));
    }

    function get_views(){
        return $views;
    }

    function column_default($item, $column_name){
        
        switch($column_name){
            default:
                return $item[$column_name];
        }
    }

    function column_title($item){
        //title
        $title = '<a href="' . add_query_arg(array('sd_page' => 'view_customer', 'cid' => absint($item['ID'])), remove_query_arg('sd-message')) . '">'. $item['title'] .'</a>';
       
        //Build row actions
        $row_actions = array();

        $row_actions['edit'] = '<a href="'. add_query_arg(array('sd_page' => 'edit_customer', 'cid' => absint($item['ID'])), remove_query_arg('sd-message')) . '">'. __('Edit', 'sd') .'</a>';

        if(current_user_can('delete_sd_customers')){
             $row_actions['delete'] = '<a href="'. wp_nonce_url(add_query_arg(array('sd_action' => 'delete_customer', 'cid' => absint($item['ID'])), remove_query_arg('sd-message')), 'sd-delete-customer') . '">'. __('Delete', 'sd') .'</a>';
        }

        return stripslashes($title) . $this->row_actions($row_actions);
    }

    function column_email($item){
        return sprintf('<a href="mailto:%1$s">%1$s</a>', $item['email']);
    }

    function column_phone($item){
        return sprintf('<a href="tel:%1$s">%1$s</a>', $item['phone']);
    }

    function column_tickets($item){
        $ticket_url = admin_url( 'admin.php?page=simple-desk' );
        return '<a href="' . add_query_arg(array('cid' => absint($item['ID']), 'status' => 'notresolved'), $ticket_url) . '">' . absint($item['tickets']) . '</a>';

    }

    function column_created($item){
        $time_date_format = get_option('date_format') . ' ' . get_option('time_format');
        return mysql2date($time_date_format, $item['created']);
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'], 
            /*$2%s*/ $item['ID']
        );
    }

    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => 'Name',
            'email' => 'Email',
            'phone' => 'Contact Number',
            'tickets' => 'Open Tickets',
            'created' => 'Created'
        );

        return $columns;
    }

    function no_items() {
        echo 'No customers found.';
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',true),     //true means it's already sorted
            'created'  => array('created',false)
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {   
        //Detect when a bulk action is being triggered...
        if( 'delete' === $this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function prepare_items(){
        $per_page = 25;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $data = $this->table_data();
        $this->items = $data;

        //Pagination
        $current_page = $this->get_pagenum();

        $total_items = sd_get_customers_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

    function table_data(){
        $customers_data = array();
        $per_page = 25;

        $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'title';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
        $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : null;

        $args = array(
            'post_type' => 'simple-desk-customer',
            'post_status' => 'active',
            'paged' => isset( $_GET['paged'] ) ? $_GET['paged'] : 1,
            'posts_per_page' => $per_page,
            'orderby' => $orderby,
            'order' => $order,
            's' => $search
        );

        $customers = sd_get_customers($args);

        if($customers){
            foreach($customers as $customer){
                $customers_data[] = array(
                    'ID' => $customer->ID,
                    'title' => get_the_title($customer->ID),
                    'email' => sd_get_customer_email($customer->ID),
                    'phone' => sd_get_customer_phone($customer->ID),
                    'tickets' => sd_get_tickets_count('notresolved', $customer->ID),
                    'created' => $customer->post_date
                );
            }
        }

        if($customers_data) return $customers_data;

        return false;

    }

    function search_box($text, $input_id){
        $input_id = $input_id . '-search-input';
?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
            <?php submit_button( $text, 'button', false, false, array('ID' => 'search-submit') ); ?>
        </p>
<?php
    }
}