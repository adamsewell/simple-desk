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
    if(isset($_GET['sd_page']) && $_GET['sd_page'] === 'add_ticket'){
        require_once(SIMPLEDESK_BASE_DIR . 'lib/admin/tickets/add-ticket.php');
    }elseif(isset($_GET['sd_page']) && $_GET['sd_page'] === 'edit_ticket'){
        require_once(SIMPLEDESK_BASE_DIR . 'lib/admin/tickets/edit-ticket.php');
    }else{
        $Tickets = new SimpleDeskTicketTable();
        $Tickets->prepare_items();
?>
        <div class="wrap">
            <div id="icon-edit-comments" class="icon32"></div>
            <h2>
                <?php _e('Tickets'); ?>
                <a href="<?php echo add_query_arg( array( 'sd_page' => 'add_ticket' ), remove_query_arg('sd-message') ); ?>" class="add-new-h2">Add New</a>
            </h2>
            <form id="sd_search_form" method="get">
                <input type="hidden" name="page" value="<?php echo absint($_REQUEST['page']); ?>" />

                <!-- maintain status if present on search so we can search through the different statuses -->
                <?php if(!empty($_GET['status'])): ?>
                    <input type="hidden" name="status" id="status" value="<?php echo esc_attr($_GET['status']); ?>" />
                <?php endif; ?>

                <?php $Tickets->search_box('Search', 'sd-tickets'); ?>
            </form>
            <form id="sd_ticket" method="get">
                <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>" />
                <!-- used to retain view when filtering -->
                <input type="hidden" name="view" value="<?php echo sanitize_text_field($_GET['view']) ?>" />

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

        $current = isset( $_GET['view'] ) ? sanitize_text_field($_GET['view']) : '';
        $notresolved_count = '&nbsp;<span class="count">(' . sd_get_tickets_count('notresolved') . ')</span>';
        $mine_count = '&nbsp;<span class="count">(' . sd_get_tickets_count('mine') . ')</span>';
        $resovled_count = '&nbsp;<span class="count">(' . sd_get_tickets_count('resolved') . ')</span>';
        $unassigned_count = '&nbsp;<span class="count">(' . sd_get_tickets_count('unassigned') . ')</span>';

        $views = array(
            'mine' => sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( 'view', $base_url ), $current === 'mine' || $current == '' ? ' class="current"' : '', __('My Queue', 'sd') . $mine_count ),
            'unassigned' => sprintf('<a href="%s"%s>%s</a>', add_query_arg('view', 'unassigned', $base_url), $current === 'unassigned' ? ' class="current"' : '', __('Unassigned', 'sd') . $unassigned_count),
            'all_open' => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'view', 'notresolved', $base_url ), $current === 'notresolved' ? ' class="current"' : '', __('All Open', 'sd') . $notresolved_count )
        );

        return $views;
    }

    function extra_tablenav($which){

        $status = false;
        $tech = false;

        if(isset($_GET['status'])) $status = sanitize_text_field($_GET['status']);
        if(isset($_GET['tech'])) $tech = sanitize_text_field($_GET['tech']);

        if($which === 'top'){
?>
            <div class="alignleft actions">
              <?php if($status): ?>
                <?php if(array_key_exists($status, sd_get_ticket_statuses())) $selected = $status; ?>
              <?php endif; ?>

              <?php $statuses = sd_get_ticket_statuses(); ?>
                <select name="status">
                    <option value=""><?php _e('Status', 'sd'); ?></option>
                    <?php echo sd_menuoptions($statuses, $selected, true); ?>
                </select>

              <?php if($tech): ?>
                <?php if(array_key_exists($tech, sd_get_technicians(true))) $selected = $tech; ?>
              <?php endif; ?>

              <?php $techs = sd_get_technicians(true); ?>
                <select name="tech">>
                    <option value=""><?php _e('Tech', 'sd'); ?></option>
                    <?php echo sd_menuoptions($techs, $selected, true); ?>
                </select>

                <input type="submit" class="button" value="<?php _e('Filter', 'sd'); ?>" />
            </div>
<?php
        }
    }

    function column_default($item, $column_name){

        switch($column_name){
            default:
                return $item[$column_name];
        }
    }

    function column_issue($item){
        $title = '<a href="' . add_query_arg(array('sd_page' => 'edit_ticket', 'tid' => absint($item['ID'])), remove_query_arg('sd-message')) . '">'. $item['issue'] .'</a>';

        //Build row actions
        $row_actions = array();

        $row_actions['edit'] = '<a href="'. add_query_arg(array('sd_page' => 'edit_ticket', 'tid' => absint($item['ID'])), remove_query_arg('sd-message')) . '">'. __('Edit', 'sd') .'</a>';

        if(current_user_can('delete_sd_tickets')){
             $row_actions['delete'] = '<a href="'. wp_nonce_url(add_query_arg(array('sd_action' => 'delete_ticket', 'tid' => absint($item['ID'])), remove_query_arg('sd-message')), 'sd-delete-ticket') . '">'. __('Delete', 'sd') .'</a>';
        }

        return stripslashes($title) . $this->row_actions($row_actions);
    }

    function column_customer($item){
        return '<a href="' . add_query_arg(array('sd_page' => 'view_customer', 'cid' => absint($item['customer'])), admin_url('admin.php?page=simple-desk-customer-page')) . '">'. sd_get_ticket_contact_name($item['ID']) .'</a>';
    }

    function column_status($item){
        $obj = get_post_status_object($item['status']);
        if(is_object($obj)){
            return sprintf('<span class="status-%1$s">%2$s</span>', $item['status'], $obj->label);
        }
    }

    function column_modified($item){
        if(strtotime($item['modified']) > strtotime('-1 week')){
            return human_time_diff( strtotime($item['modified']), current_time('timestamp')) . ' ago';
        }else{
            $time_date_format = get_option('date_format') . ' ' . get_option('time_format');
            return mysql2date($time_date_format, $item['modified']);
        }

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
            'delete' => __('Delete', 'sd')
        );
        return $actions;
    }

    function process_bulk_action() {
        $ids = isset( $_GET['ticket'] ) ? $_GET['ticket'] : false;

        if ( ! is_array( $ids ) )
            $ids = array( $ids );

        foreach($ids as $id){
            //Detect when a bulk action is being triggered...
            if( 'delete' === $this->current_action() ) {
                sd_remove_ticket($ids);
            }
        }
    }

    function prepare_items(){
        $per_page = 25;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $this->table_data();
        $this->items = $data;

        //Pagination
        $current_page = $this->get_pagenum();

        $view = isset( $_GET['view'] ) ? $_GET['view'] : 'mine';
        $status = isset( $_GET['status'] ) ? $_GET['status'] : 'mine';
        $cid = isset( $_GET['cid'] ) ? absint($_GET['cid']) : '';
        $total_items = sd_get_tickets_count($status, $cid);

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
        $meta_query = array();

        $status = isset( $_GET['status'] ) ? $_GET['status'] : '';
        $view = isset( $_GET['view'] ) ? $_GET['view'] : 'mine';
        $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
        $customer = isset( $_GET['cid'] ) ? absint($_GET['cid']) : '';
        $orderby  = isset( $_GET['orderby'] ) ? $_GET['orderby'] : '';
        $order  = isset( $_GET['order'] ) ? $_GET['order'] : '';
        $tech  = isset( $_GET['tech'] ) ? $_GET['tech'] : '';
        $date = isset( $_GET['date'] ) ? $_GET['date'] : '';

        //set views - my queue, unassigned queue and all open (notresolved)
        if($view == 'mine'){
            $meta_query[] = array(
                'key' => '_sd_ticket_assign',
                'value' => $current_user->ID,
                'compare' => '='
            );
        }elseif($view == 'unassigned'){
            $meta_query[] = array(
                'key' => '_sd_ticket_assign',
                'value' => 0,
                'compare' => '='
            );
        }elseif($view == 'custom'){ //if view is custom, show all queues
            //do nothing
        }

        //filter based on customer if present - in addition to view above
        if(!empty($customer)){
            $meta_query[] = array(
                'key' => '_sd_ticket_customer',
                'value' => $customer,
                'compare' => '='
            );
        }

        //add filter for technician
        if(!empty($tech)){
            $meta_query[] = array(
                'key' => '_sd_ticket_assign',
                'value' => $tech,
                'compare' => '='
            );
        }

        //date_query
        if(!empty($date)){
            $date = array(
                'after' => '-7 days'
            );
        }

        $args = array(
            'post_type' => 'simple-desk-ticket',
            'paged' => isset( $_GET['paged'] ) ? $_GET['paged'] : 1,
            'meta_query' => $meta_query,
            'posts_per_page' => $per_page,
            'orderby' => $orderby,
            'post_status' => $status,
            'order' => $order,
            's' => $search,
            'date_query' => $date
        );

        $tickets = sd_get_tickets($args);

        if(is_array($tickets)){
            foreach($tickets as $ticket){
                $tickets_data[] = array(
                    'issue' => sd_get_ticket_title($ticket->ID),
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
