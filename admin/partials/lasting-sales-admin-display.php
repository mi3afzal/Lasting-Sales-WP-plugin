<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    die('Un-authorized access!');
}

/**
 * Detect plugin. For use in Admin area only.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://lastingsales.com
 * @since      1.0.0
 *
 * @package    Lasting_Sales
 * @subpackage Lasting_Sales/admin/partials
 */
function render_admin_page() {
    $lasting_config = get_lasting_sales_config(); ?>
    <div id="main-content" class="container container-768" style="margin-bottom: 70px; margin-top: 2.0em;">
        <div class="row">
            <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                <div class="mb-3 text-center">
                    <img src="<?php echo plugins_url('images/lasting-sales-logo-dark.png', dirname(__FILE__)) ; ?>" class="img-fluid">
                </div>

                <div>
                    <p class="mb-2 text-center" style="font-size: 1.4em; font-weight: 400;">LastingSales CRM Integration</p>
                    <p class="text-dark mb-4 text-center" style="font-size: 1.1em; line-height: 1.3;">
                    LastingSales is a CRM that simplifies the business by providing a centralized platform to manage Leads from Facebook, 
                    Website and Sales Calls and helps to track and measure the Sales Team Performance.
                    </p>

                    <!-- START: CONNECTED STATUS -->
                    <div id="ls_crm_connected_alert" class="alert alert-success text-left" role="alert"
                         style="<?php echo isset($lasting_config['token']) ? '' : 'display: none' ?>">
                        <p class="mb-2" style="font-size: 1.2em; font-weight: bold">Integration Status: Connected</p>
                        <p class="mb-0">You've successfully connected to your LastingSales account. You can try submitting
                            a test lead on your website to confirm it is received into your LastingSales account.</p>
                    </div>
                    <!-- END: CONNECTED STATUS -->

                    <div id="ls_crm_error_alert" class="alert alert-danger text-left" role="alert" style="display:none;"></div>
                </div>

                <div class="text-left">
                    <form onsubmit="return false;">

                        <div class="form-group">
                            <label for="email" style="font-weight: bold">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="e.g. abc@xyz.com" value="<?php echo isset($lasting_config['email']) ? esc_html($lasting_config['email']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="token" style="font-weight: bold">Token</label>
                            <input type="text" class="form-control" id="token" placeholder="e.g. abcd1234" value="<?php echo isset($lasting_config['token']) ? esc_html($lasting_config['token']) : ''; ?>" required>
                        </div>

                        
                        <div id="workflow_setup" style="<?php echo isset($lasting_config['token']) ? '' : 'display:none;' ?>" >
                            <div class="form-group">
                                <label for="deals_enabled" style="font-weight: bold">Enable Deals</label>
                                <input type="checkbox" class="form-control" id="deals_enabled" <?php echo !empty($lasting_config['deals_enabled']) ? 'checked' : ''; ?>>
                                <small class="form-text text-muted" style="line-height: 1.3;display: block;">Enable Deals to send form data directly into pipeline and its stage, instead of sending it in to contacts.</small>
                            </div>

                            <div id="choose_pipeline_box" style="<?php echo empty($lasting_config['deals_enabled']) ? 'display:none;' : '' ?>" >
                                <div class="form-group">
                                    <label for="workflow_id" style="font-weight: bold">Choose Pipeline</label>

                                    <select name="workflow_id" id="workflow_id" class="form-control">
                                        <option value="0">Select Pipline</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="workflow_stage_id" style="font-weight: bold">Choose Pipeline Stage</label>
                                    <select name="workflow_stage_id" id="workflow_stage_id" class="form-control">
                                        <option value="0">Select Pipeline First</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($lasting_config['token'])): ?>
                            <button id="submitDetailsBtn" class="btn btn-block btn-secondary">
                                UPDATE
                            </button>
                        <?php else: ?>
                            <button id="submitDetailsBtn" class="btn btn-block btn-info">
                                SAVE
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">
        jQuery('#main-content').on('click', '#deals_enabled', function(){
            jQuery('#choose_pipeline_box').slideToggle();
        });


        jQuery('#main-content').on('click', '#submitDetailsBtn', submitLastingSalesToken);
        function submitLastingSalesToken() {
            var lastingSalesEmail = jQuery("#email").val();
            var lastingSalesToken = jQuery("#token").val();

            // Lasting Sales Email and Token is required
            if (!lastingSalesEmail || !lastingSalesToken) return false;

            // hide connected alert and error
            jQuery("#ls_crm_connected_alert").hide();
            jQuery("#ls_crm_error_alert").hide();


            // Check if Lasting Sales Token is valid
            // set the loading animation on the submit button
            jQuery("#submitDetailsBtn").html("Saving...").prop('disabled', true);
            jQuery.ajax({
                url: "<?php echo LASTING_SALES_PLUGIN_WEBHOOK_SUBSCRIPTION_URL?>",
                type: "post",
                dataType: "json",
                data: {"email": lastingSalesEmail, 'token': lastingSalesToken},
                success: function(response) {
                    console.log('Success: ', response);
                    // update token in DB
                    updateTokenInDB();
                    if(workflow_data == '') get_workflows();
                    jQuery("#workflow_setup").show();

                    jQuery("#submitDetailsBtn").html("UPDATE").prop('disabled', false);
                    jQuery("#submitDetailsBtn").addClass("btn-secondary").removeClass("btn-info");
                    jQuery("#ls_crm_connected_alert").show();
                },
                error: function(error){
                    console.log('Error: ', error.responseJSON);
                    jQuery("#ls_crm_error_alert").html('<p class="mb-0">'+error.responseJSON.response+'</p>').show();
                    jQuery("#submitDetailsBtn").addClass("btn-info").removeClass("btn-secondary").html("SAVE").prop('disabled', false);

                    return;
                }
            });
        }

        function updateTokenInDB() {
            jQuery.ajax({
                url: "#",
                type: "POST",
                async: false,
                data: {
                    form_email: jQuery("#email").val(),
                    form_token: jQuery("#token").val(),
                    form_deals: jQuery("#deals_enabled").is(':checked') ? 1 : 0,
                    form_wf_id: jQuery("#workflow_id").val(),
                    form_wfs_id: jQuery("#workflow_stage_id").val(),
                }
            }).done(function (data, textStatus, jqXHR) {
                console.log("done");
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log("fail");
            });
        }

        var workflow_data = '';
        var selected_workflow_id = '<?php echo isset($lasting_config['workflow_id']) ? absint($lasting_config['workflow_id']) : '0'; ?>';
        var selected_workflow_stage_id = '<?php echo isset($lasting_config['workflow_stage_id']) ? absint($lasting_config['workflow_stage_id']) : '0'; ?>';
        function get_workflows(){
            let token = jQuery("#token").val();
            if (token == '') return false;

            jQuery.ajax({
                url: "<?php echo LASTING_SALES_PLUGIN_WEBHOOK_WORKFLOW_URL?>"+token,
                type: "GET",
                dataType: "json",
                success: function(res) {
                    //console.log('Success: ', res);
                    workflow_data = res.response.data;
                    display_workflows();
                },
                error: function(error){
                    console.log('Error: ', error.responseJSON);
                    jQuery("#ls_crm_connected_alert").hide();
                    jQuery("#ls_crm_error_alert").html('<p class="mb-0">'+error.responseJSON.response+'</p>').show();
                    return;
                }
            });

        }
        get_workflows();

        function display_workflows()
        {
            let options_html = '';
            for(const workflow of workflow_data){
                let sel = '';
                if(workflow.id == selected_workflow_id) sel = 'selected';
                options_html += '<option value="'+workflow.id+'" '+sel+'>'+workflow.name+'</option>';
            }
            jQuery('#workflow_id').append(options_html);
            display_workflows_stages();
        }

        jQuery('#workflow_setup').on('change', '#workflow_id', display_workflows_stages);
        function display_workflows_stages()
        {
            selected_workflow_id = jQuery('#workflow_id option:selected').val();
            if(selected_workflow_id == '0') return false;
            let stages = '';
            for(const workflow of workflow_data)
            {
                if(workflow.id == selected_workflow_id){
                    stages = workflow.stages;
                    break;
                }
            }

            let options_html = '';
            for(const stage of stages){
                let sel = '';
                if(stage.id == selected_workflow_stage_id) sel = 'selected';
                options_html += '<option value="'+stage.id+'" '+sel+'>'+stage.name+'</option>';
            }
            jQuery('#workflow_stage_id').html(options_html);
        }
        

    </script>
    <?php
}
