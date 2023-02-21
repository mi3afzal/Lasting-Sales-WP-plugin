<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Lasting_Sales
 * @subpackage Lasting_Sales/includes
 * @author     Lasting Sales <support@lastingsales.com>
 */
class Lasting_Sales_Deactivator {

	public static function deactivate() {
	    global $wpdb;
        // delete Lasting Sales user cnfig table on plugin deactivation
        $wpdb->query("DROP TABLE ".LASTING_SALES_USER_CONFIG_TABLE_NAME);
	}
}
