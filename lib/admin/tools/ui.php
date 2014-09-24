<?php
/**
 * Import and Export UI
 *
 * @package     SD
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function sd_display_tools_page(){
?>  
        <div class="wrap">
            <div id="icon-edit-comments" class="icon32"></div>
            <h2>
                <?php _e('Import and Export'); ?> 
            </h2>
            <form id="sd_import_export" method="get" action="">
            </form>
        </div>
<?php
}