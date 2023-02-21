<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Lasting_Sales
 * @subpackage Lasting_Sales/includes
 * @author     Lasting Sales <support@lastingsales.com>
 */
class Lasting_Sales_Activator {

	public static function activate() {
	    create_table_lasting_sales_user_info();
	}

}

function create_table_lasting_sales_user_info()
{
    /**
     * Create user_info table if not already exists.
     */
    global $wpdb;
    $table_name = LASTING_SALES_USER_CONFIG_TABLE_NAME;
    $charset_collate = $wpdb->get_charset_collate();
    if ($wpdb->get_var("show tables like '{$table_name}'") != $table_name) {
        $sql = "CREATE TABLE " . $table_name . " (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`email` varchar(100) NOT NULL,
				`token` varchar(100) NOT NULL,
                `deals_enabled` tinyint(1) NOT NULL DEFAULT 0,
                `workflow_id` int(11) NOT NULL DEFAULT 0,
                `workflow_stage_id` int(11) NOT NULL DEFAULT 0,
				UNIQUE KEY id (id)
		)$charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
