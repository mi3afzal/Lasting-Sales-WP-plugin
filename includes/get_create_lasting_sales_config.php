<?php


$form_email = isset($_POST['form_email']) ? sanitize_email($_POST['form_email']) : null;
$form_token = isset($_POST['form_token']) ? sanitize_text_field($_POST['form_token']) : null;
$form_deals = isset($_POST['form_deals']) ? sanitize_text_field($_POST['form_deals']) : 0;
$form_wf_id = isset($_POST['form_wf_id']) ? sanitize_text_field($_POST['form_wf_id']) : 0;
$form_wfs_id = isset($_POST['form_wfs_id']) ? sanitize_text_field($_POST['form_wfs_id']) : 0;

if (isset($form_email) && isset($form_token)){
    save_lasting_sales_config_in_db($form_email, $form_token, $form_deals, $form_wf_id, $form_wfs_id);
}

/**
 * This function deletes existing lasting sales config.
 *
 */
function delete_existing_lasting_sales_config()
{
    global $wpdb;
    // Clean data from config table.
    $wpdb->query("DELETE FROM " . LASTING_SALES_USER_CONFIG_TABLE_NAME);
}

/**
 * There should be only one config entry at a time. So this function deletes data before creating new entry.
 * @param $email
 * @param $token
 * @param $cf_ref
 * @return string
 */

function save_lasting_sales_config_in_db($email, $token, $deals_enabled, $workflow_id, $workflow_stage_id)
{
    global $wpdb;
    // clean data from config table
    delete_existing_lasting_sales_config();

    //If field name not match with current entry then new entry insert in DB
    $wpdb->query($wpdb->prepare('INSERT INTO ' . LASTING_SALES_USER_CONFIG_TABLE_NAME . 
        '(`email`, `token`, `deals_enabled`, `workflow_id`, `workflow_stage_id`) 
        VALUES (%s, %s, %s, %s, %s)', $email, $token, $deals_enabled, $workflow_id, $workflow_stage_id));
    return "saved to DB successfully";
}

/** Can use this function anywhere to get lasting sales config fields */
function get_lasting_sales_config()
{
    global $wpdb;
    $res = $wpdb->get_results("SELECT * FROM " . LASTING_SALES_USER_CONFIG_TABLE_NAME . " LIMIT 1");
    if (!empty($res)) {
        return get_object_vars($res[0]);
    }
    return array();
}