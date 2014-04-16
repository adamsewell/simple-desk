<?php
/**
 * Ticket UI
 *
 * @package     SD
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

function sd_display_tickets(){
    if(isset($_GET['sd_page']) && $_GET['sd_page'] == 'add_ticket'){
        require_once(SIMPLEDESK_BASE_DIR . 'lib/admin/tickets/add-ticket.php');
    }elseif(isset($_GET['sd_page']) && $_GET['sd_page'] == 'edit_ticket'){
        require_once(SIMPLEDESK_BASE_DIR . 'lib/admin/tickets/edit-ticket.php');
    }else{
        $Tickets = new SimpleDeskTicketTable();
        $Tickets->prepare_items();
?>  
        <div class="wrap">
            <div id="icon-edit-comments" class="icon32"></div>
            <h2>
                <?php _e('Tickets'); ?> 
                <a href="<?php echo add_query_arg( array( 'sd_page' => 'add_ticket' ) ); ?>" class="add-new-h2">Add New</a>
            </h2>
            <form id="sd_search_form" method="get" action="<?php echo admin_url('admin.php'); ?>">
                <!-- for some reason, adding page=simple-desk to the admin_url above was
                    being stripped out. using a hidden input tag seems to have fixed the issue -->
                <input type="hidden" name="page" id="page" value="simple-desk" />
                <?php $Tickets->search_box('Search', 'sd-tickets'); ?>
            </form>
            <form id="sd_ticket" method="get" action="">
                <?php $Tickets->views(); ?>
                <?php $Tickets->display(); ?>
            </form>
        </div>
<?php
    }
}


/*
* Ticket List Table 
*/
class SimpleDeskTicketTable extends WP_List_Table{

    function __construct(){
        global $status, $page;

        parent::__construct(array(
            'singular' => 'ticket',
            'plural' => 'tickets',
            'ajax' => false
        ));
    }

    function get_views(){
        $base_url = admin_url('admin.php?page=simple-desk');

        $current = isset( $_GET['status'] ) ? $_GET['status'] : '';
        $all_count = '&nbsp;<span class="count">(' . sd_get_tickets_count('all') . ')</span>';
        $mine_count = '&nbsp;<span class="count">(' . sd_get_tickets_count('mine') . ')</span>';
        $new_count = '&nbsp;<span class="count">(' . sd_get_tickets_count('new') . ')</span>';
        $inprogress_count = '&nbsp;<span class="count">(' . sd_get_tickets_count('inprogress') . ')</span>';
        $waitingonme_count = '&nbsp;<span class="count">(' . sd_get_tickets_count('waitingonme') . ')</span>';
        $waitingoncustomer_count = '&nbsp;<span class="count">(' . sd_get_tickets_count('waitingonustomer') . ')</span>';
        $resovled_count = '&nbsp;<span class="count">(' . sd_get_tickets_count('resolved') . ')</span>';

        $views = array(
            'all' => sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( 'status', $base_url ), $current === 'all' || $current == '' ? ' class="current"' : '', __('All', 'sd') . $all_count ),
            'mine' => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'mine', $base_url ), $current === 'mine' ? ' class="current"' : '', __('Mine', 'sd') . $mine_count ),
            'new' => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'new', $base_url ), $current === 'new' ? ' class="current"' : '', __('New', 'sd') . $new_count ),
            'inprogress' => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'inprogress', $base_url ), $current === 'inprogress' ? ' class="current"' : '', __('In Progress', 'sd') . $inprogress_count ),
            'waitingonme' => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'waitingonme', $base_url ), $current === 'waitingonme' ? ' class="current"' : '', __('Waiting On Me', 'sd') . $waitingonme_count ),
            'waitingoncustomer' => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'waitingoncustomer', $base_url ), $current === 'waitingoncustomer' ? ' class="current"' : '', __('Waiting On Customer', 'sd') . $waitingoncustomer_count ),
            'resolved' => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'resolved', $base_url ), $current === 'resolved' ? ' class="current"' : '', __('Resolved', 'sd') . $resovled_count ),
        );

        return $views;
    }

    function column_default($item, $column_name){
        
        switch($column_name){
            default:
                return $item[$column_name];
        }
    }

    function column_issue($item){
        $title = '<a href="' . add_query_arg(array('sd_page' => 'edit_ticket', 'tid' => absint($item['ID']))) . '">'. $item['issue'] .'</a>';
       
        //Build row actions
        $row_actions = array();

        $row_actions['edit'] = '<a href="'. add_query_arg(array('sd_page' => 'edit_ticket', 'tid' => absint($item['ID']))) . '">'. __('Edit', 'sd') .'</a>';

        if(current_user_can('delete_sd_tickets')){
             $row_actions['delete'] = '<a href="'. wp_nonce_url(add_query_arg(array('sd_action' => 'delete_ticket', 'tid' => absint($item['ID']))), 'sd-delete-ticket') . '">'. __('Delete', 'sd') .'</a>';
        }

        return stripslashes($title) . $this->row_actions($row_actions);
    }

    function column_customer($item){
        return sprintf('<a href="?page=%1$s&sd_page=%2$s&cid=%3$s">%4$s</a>',
            'simple-desk-customer-page',
            'edit_customer',
            $item['customer'],
            sd_get_customer_display_name($item['customer'])
        );
    }

    function column_status($item){
        $obj = get_post_status_object($item['status']);
        if(is_object($obj)){
            return sprintf('<span class="status-%1$s">%2$s</span>', $item['status'], $obj->label);
        }
    }

    function column_modified($item){
        $time_date_format = get_option('date_format') . ' ' . get_option('time_format');
        return mysql2date($time_date_format, $item['modified']);
    }

    function column_assign($item){
        $user = get_userdata($item['assign']);

        if (empty($user)) {
            return __('Unassigned');
        }

        return $user->display_name;
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
            'issue' => 'Issue',
            'customer' => 'Customer',
            'status' => 'Status',
            'ID' => 'Ticket #',
            'assign' => 'Assigned',
            'modified' => 'Last Modified',
        );

        return $columns;
    }

    function no_items() {
        echo 'No tickets found.';
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'id'     => array('id',true),     //true means it's already sorted
            'modified'  => array('modified',false)
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

        $status = isset( $_GET['status'] ) ? $_GET['status'] : 'all';
        $total_items = sd_get_tickets_count($status);

        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

    function table_data(){
        $tickets_data = array();
        $per_page = 25;
        $current_user = wp_get_current_user();

        $status = isset( $_GET['status'] ) ? $_GET['status'] : array('new', 'inprogress', 'waitingonme', 'waitingoncustomer');
        $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : null;

        $args = array(
            'post_type' => 'simple-desk-ticket',
            'paged' => isset( $_GET['paged'] ) ? $_GET['paged'] : 1,
            'meta_key' => $status == 'mine' ? '_sd_ticket_assign' : '',
            'meta_value' => $status == 'mine' ? $current_user->ID : '',
            'posts_per_page' => $per_page,
            'orderby' => $orderby,
            'post_status' => $status == 'mine' ? array('new', 'inprogress', 'waitingonme', 'waitingoncustomer') : $status,
            'order' => $order,
            's' => $search
        );

        add_filter('posts_orderby', 'sd_modify_get_tickets_default');

        $tickets = sd_get_tickets($args);

        remove_filter('posts_orderby', 'sd_modify_get_tickets_default');

        if(is_array($tickets)){
            foreach($tickets as $ticket){
                $tickets_data[] = array(
                    'issue' => sd_get_ticket_issue($ticket->ID),
                    'ID' => $ticket->ID,
                    'status' => get_post_status($ticket->ID),
                    'customer' => sd_get_ticket_customer($ticket->ID),
                    'modified' => $ticket->post_modified,
                    'assign' => sd_get_ticket_tech($ticket->ID)
                );
            }
        }

        if($tickets_data) return $tickets_data;

        return false;

    }

    function search_box($text, $input_id){
        if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
            return;

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