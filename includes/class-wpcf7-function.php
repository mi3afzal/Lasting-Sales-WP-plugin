<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    die('Un-authorized access!');
}

/** this function can be used anywhere in plugin code to write logs to debug file.
 * Make sure DEBUG_SETTINGS in wp-config file should be true
 */
if (!function_exists('write_log')) {

    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}

/** this is a wpcf7 callback after the lead is submitted.
 * Calls a non blocking http API request to lasting sales with lead data.
 */
function action_wpcf7_before_send_mail($contact_form, &$abort, $submission)
{
    $lasting_sales_config = get_lasting_sales_config();
    if(empty($lasting_sales_config)) return;

    if ( $lasting_sales_config['deals_enabled'] == 1) $endpoint = LASTING_SALES_PLUGIN_WEBHOOK_DEAL_URL;
    else $endpoint = LASTING_SALES_PLUGIN_WEBHOOK_URL;

    $body = lasting_cf7_get_posted_data($contact_form, $submission, $lasting_sales_config);
    if (!$body) return;

    $body = wp_json_encode($body);
    $options = [
        'body' => $body,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        //'timeout' => 60,
        //'redirection' => 5,
        'blocking' => false,
        //'httpversion' => '1.0',
        //'sslverify' => false,
        'data_format' => 'body',
    ];
    //print_r($options);exit;
    $response = wp_remote_post($endpoint, $options);
    //print_r($response);
}

/** added action on lead submission on wpcf7 */
add_action('wpcf7_before_send_mail', 'action_wpcf7_before_send_mail', 10, 3);


/**
 * Prepares and return payload for calling lasting sales webhook url.
 */
function lasting_cf7_get_posted_data($cf7, $submission, $lasting_sales_config)
{
    $form_data = $submission->get_posted_data();
    $data = array();
    if ($submission) 
    {
        $data = array();
        $data['title'] = $cf7->title();
        $data['dynamic_columns'] = $form_data;
        $data['wp_cf_type'] = "contact_form7";

        $data['token'] = $lasting_sales_config['token'];
        $data['status'] = 'InProgress';
        $data['lead_type'] = 'type_sales';
        $data['src'] = 'web';

        foreach ($form_data as $key => $value) 
        {
            if (strpos($key, 'name') !== false) $data['name'] = $value;
            if (strpos($key, 'phone') !== false || strpos($key, 'mobile') !== false) $data['phone'] = $value;
            if (strpos($key, 'email') !== false) $data['email'] = $value;           
            if (strpos($key, 'address') !== false) $data['address'] = $value;

            if (strpos($key, 'status') !== false) $data['status'] = $value;           
            if (strpos($key, 'lead_type') !== false) $data['lead_type'] = $value;           
            if (strpos($key, 'src') !== false) $data['src'] = $value; 

            if (strpos($key, 'singleselect') !== false) $data['singleselect'] = $value;           
            if (strpos($key, 'multiselect') !== false) $data['multiselect'] = $value;           
            if (strpos($key, 'date') !== false) $data['date'] = $value;           
        }

        if ( $lasting_sales_config['deals_enabled'] == 1) 
        {
            $data['workflow_id'] = $lasting_sales_config['workflow_id'];
            $data['workflow_stage_id'] = $lasting_sales_config['workflow_stage_id'];

            $data['contact_info']['name'] = $data['name'];
            $data['contact_info']['email'] = $data['email'] ?? 'test@postman.com';
            $data['contact_info']['phone'] = $data['phone'] ?? '11111111111';
        }

        $data['contact_info']['singleselect'] = $data['dynamic_columns']['singleselect'] = $data['singleselect'] ?? '';
        $data['contact_info']['multiselect'] = $data['dynamic_columns']['multiselect'] = $data['multiselect'] ?? '';
        $data['contact_info']['date'] = $data['dynamic_columns']['date'] = $data['date'] ?? '';

        $data['contact_info']['Services'] = $data['dynamic_columns']['Services'] = $data['Services'] ?? '';
        $data['contact_info']['Birthday'] = $data['dynamic_columns']['Birthday'] = $data['Birthday'] ?? '';
    }
    return $data;
}




// add_action('woocommerce_thankyou', 'wc_send_order_to_crm', 10, 1);
function wc_send_order_to_crm( $order_id ) 
{
    if ( ! $order_id ) return;

    // Allow code execution only once 
    if( ! get_post_meta( $order_id, '_thankyou_action_done', true ) ) 
    {
        // Get an instance of the WC_Order object
        $order = wc_get_order( $order_id );

        $lasting_sales_config = get_lasting_sales_config();
        if( empty($lasting_sales_config) ) return;


        // geting lead id
        /* $lead_endpoint = LASTING_SALES_PLUGIN_WEBHOOK_LEADS_URL.$lasting_sales_config['token'].'&email='.$order->get_billing_email();
        $request = wp_remote_get($lead_endpoint);

        if( is_wp_error( $request ) ) return false; 
        $body = wp_remote_retrieve_body( $request );
        $data = json_decode($body, true);
        $lead_id = $data['response']['data'][0]['id']; */


        // building deal and sending 
        $data = array();
        $data['token'] = $lasting_sales_config['token'];
        $data['workflow_id'] = $lasting_sales_config['workflow_id'];
        $data['workflow_stage_id'] = $lasting_sales_config['workflow_stage_id'];

        $data['name'] = get_bloginfo( 'name' ); //wp_title('', false);
        //$data['lead_id'] = $lead_id;
        $data['contact_info']['name'] = $order->get_billing_first_name().' '.$order->get_billing_last_name();
        $data['contact_info']['email'] = $order->get_billing_email();
        $data['contact_info']['phone'] = $order->get_billing_phone();
        

        $deal_endpoint = LASTING_SALES_PLUGIN_WEBHOOK_DEAL_URL;
        $body = wp_json_encode($data);
        $options = [
            'body' => $body,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            //'timeout' => 60,
            //'redirection' => 5,
            'blocking' => false,
            //'httpversion' => '1.0',
            //'sslverify' => false,
            'data_format' => 'body',
        ];
        $response = wp_remote_post($deal_endpoint, $options);

        // Flag the action as done (to avoid repetitions on reload for example)
        $order->update_meta_data( '_thankyou_action_done', true );
        $order->save();
    }
} 