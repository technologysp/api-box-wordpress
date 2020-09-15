<?php
/**
 * Skypostal Admin handling
 *
 * @package Skypostal_apibox
 * @subpackage Admin
 * @since 1.0.0
 */

/**
 * Handles the plugin administrative panel. Also defines and creates the available shortcodes
 * 
 */
/* CONFIGURATION SETTINGS*/
  function spabibox_create_plugin_settings_page() {
    	// Add the menu item and page
    	$page_title = 'Skypostal API BOX Integration';
    	$menu_title = 'Skypostal API-BOX';
    	$capability = 'manage_options';
    	$slug = 'smashing_fields';
    	$callback ='spabibox_plugin_settings_page_content';
    	$icon = 'dashicons-admin-plugins';
    	$position = 100;
    	add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
    }
   function spabibox_plugin_settings_page_content() {?>
        <style>
        .alert-warning-message {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            position: relative;
            padding: .75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: .25rem;
            display:none;
        }
        </style>
    	<div class="wrap" >
    		<h2>Skypostal API-BOX Integration</h2>
            <h3 class="alert-warning-message" id="form_spapibox_main_wrap_err">This form has invalid options</h3>
            <?php
            if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ){
                  spabibox_admin_notice();
            } ?>
    		<form method="POST" action="options.php">
                <?php
                    settings_fields( 'smashing_fields' );
                    do_settings_sections( 'smashing_fields' );
                    spapibox_admin_render_shortcodes();
                    submit_button();
                ?>
    		</form>    		    		
    	<script type="text/javascript">
              var spbxerr = document.getElementsByClassName("spapibox_invalid_field_data");
            if(spbxerr.length>0){
                let sbapiboxformx = document.getElementById("form_spapibox_main_wrap_err"); 
                sbapiboxformx.style.display = "block";

            }
        </script >
          
        
    	</div> <?php
    }

    function spapibox_admin_render_shortcodes(){
    	$shortcodes=spapibox_admin_build_available_shortcodes();
    	$render='<h2 style="cursor:pointer; text-decoration: underline;" onclick="jQuery(\'#sp_admin_shortcodes\').toggle(\'slow\')">'.__('Plugin Available Shortcodes','skypostal_apibox').'</h2>';
    	$render.='<div id="sp_admin_shortcodes" style="display:none;">';
    	$render.='<table class="form-table"><thead>';
    	$render.='<tr><th>'.__('Shortcode','skypostal_apibox').'</th><th>'.__('Description').'</th><th>'.__('Method Call').'</th><th>'.__('Require Session').'</th></tr></thead><tbody>';	
    	foreach($shortcodes as $item){
    		$render.='<tr><td>['.$item['key'].']</td><td>'.$item['description'].'</td><td>'.$item['callback'].'</td><td>'.($item['req_session'] ? __('Yes','skypostal_apibox'):__('No','skypostal_apibox') ).'</td></tr>';	
    	}
    	
    	$render.='</tbody></table></div>';
    	echo $render;
    }

    function spapibox_admin_get_shortcode($shortcode){
    	$shorts=spapibox_admin_build_available_shortcodes();
    	foreach($shorts as $short)	{
    		if($short['key']==$shortcode){
    			return $short;
    		}
    	}
    	return null;
    }

    function spapibox_admin_build_available_shortcodes(){

    	$shortcuts=array(
    			array(
    				'key'=>'sp_login_form',
    				'callback'=>'spapibox_shortcode_login_form',
    				'description'=>__('Session Login Form','skypostal_apibox'),
    				'form_id'=>'sp_customer_login',
    				'form_init_post'=>'sp_customer_login',
    				'req_session'=>false
    			),
    			array(
    				'key'=>'sp_customer_registration_virtual',
    				'callback'=>'spapibox_shortcode_customer_registration_virtual',
    				'description'=>__('Register Virtual Customer Form','skypostal_apibox'),
    				'req_session'=>false
    			),
    			array(
    				'key'=>'spapibox_login_small_box',
    				'callback'=>'spapibox_shortcode_login_small_box',
    				'description'=>__('Session small name display','skypostal_apibox'),
    				'req_session'=>true
    			),
    			array(
    				'key'=>'spapibox_customer_update_personal_info',
    				'callback'=>'spapibox_shortcode_customer_update_personal_info',
    				'description'=>__('Update personal information form','skypostal_apibox'),
    				'req_session'=>true
    			),
    			array(
    				'key'=>'sp_customer_registration_default',
    				'callback'=>'spapibox_shortcode_customer_registration_default',
    				'description'=>__('Register Customer Default Form','skypostal_apibox'),
    				'req_session'=>false
    			),
    			array(
    				'key'=>'sp_customer_activate_box',
    				'callback'=>'spapibox_shortcode_customer_activate_box',
    				'description'=>__('Customer Activation Form','skypostal_apibox'),
    				'req_session'=>true
    			),
    			array(
    				'key'=>'sp_customer_update_address_info',
    				'callback'=>'spapibox_customer_update_address_info',
    				'description'=>__('Update address and phone form','skypostal_apibox'),
    				'req_session'=>true
    			),
    			array(
    				'key'=>'sp_customer_get_shipments',
    				'callback'=>'spapibox_customer_get_shipments',
    				'description'=>__('Display customer shipments form','skypostal_apibox'),
    				'req_session'=>true
    			),
    			array(
    				'key'=>'sp_customer_get_shipment_info',
    				'callback'=>'spapibox_customer_get_shipment_info',
    				'description'=>__('Display customer shipment Information','skypostal_apibox'),
    				'req_session'=>true
    			),
    			array(
    				'key'=>'sp_customer_update_email',
    				'callback'=>'spapibox_shortcode_customer_update_email',
    				'description'=>__('Update customer email','skypostal_apibox'),
    				'req_session'=>true
    			),
    			array(
    				'key'=>'sp_customer_reset_password_send_code',
    				'callback'=>'spapibox_shortcode_customer_reset_password_send_code',
    				'description'=>__('Requests Recovery password Code','skypostal_apibox'),
    				'req_session'=>false
    			),    			
    			array(
    				'key'=>'sp_customer_password_recovery_update',
    				'callback'=>'spapibox_shortcode_customer_update_password',
    				'description'=>__('Requests Recovery password Code','skypostal_apibox'),
    				'req_session'=>false
    			),
    			array(
    				'key'=>'sp_customer_shipment_invoice',
    				'callback'=>'spapibox_shortcode_shipment_invoice_handler',
    				'description'=>__('Shipment required invoice control','skypostal_apibox'),
    				'req_session'=>true
    			),
                array(
                    'key'=>'sp_customer_shipment_invoice_custom',
                    'callback'=>'spapibox_shortcode_shipment_invoice_handler_custom',
                    'description'=>__('Shipment required invoice control Custom detail','skypostal_apibox'),
                    'req_session'=>true
                ),
    			array(
    				'key'=>'sp_customer_change_password',
    				'callback'=>'spapibox_shortcode_customer_change_password',
    				'description'=>__('Change password','skypostal_apibox'),
    				'req_session'=>true
    			),
                array(
                    'key'=>'sp_customer_box_status',
                    'callback'=>'spapibox_shortcode_customer_box_status',
                    'description'=>__('Account status info box','skypostal_apibox'),
                    'req_session'=>true
                ),
                array(
                    'key'=>'sp_customer_box_account_inactive_alert',
                    'callback'=>'spapibox_shortcode_customer_inactive_alert',
                    'description'=>__('Account inactive status alert','skypostal_apibox'),
                    'req_session'=>true
                ),
                array(
                    'key'=>'sp_customer_box_logout_action',
                    'callback'=>'spapibox_shortcode_logout_action',
                    'description'=>__('Account Logout','skypostal_apibox'),
                    'req_session'=>false
                ),                
                array(
                    'key'=>'sp_customer_calculator_action',
                    'callback'=>'spapibox_shortcode_calculator',
                    'description'=>__('Calculator','skypostal_apibox'),
                    'req_session'=>false
                ),                
                array(
                    'key'=>'sp_customer_shortcode_email_test',
                    'callback'=>'spapibox_shortcode_email_test',
                    'description'=>__('Email Test GET','skypostal_apibox'),
                    'req_session'=>false
                ),
                array(
                    'key'=>'sp_spapibox_customer_get_shipments_for_consolidation',
                    'callback'=>'spapibox_customer_get_shipments_for_consolidation',
                    'description'=>__('Shipments Consolidation','skypostal_apibox'),
                    'req_session'=>true
                ),
                array(
                    'key'=>'sp_spapibox_customer_box_consolidation_status',
                    'callback'=>'spapibox_shortcode_customer_box_consolidation_status',
                    'description'=>__('Get/Toggles Consolidation Status','skypostal_apibox'),
                    'req_session'=>true
                )

                
    	);
    	return $shortcuts;
    }

    function spapibox_admin_create_shortcodes(){
    	$shortcodes=spapibox_admin_build_available_shortcodes();

    	foreach($shortcodes as $item){
    		add_shortcode( $item['key'], $item['callback'] );
    	}

    }
    
    function spabibox_admin_notice() { ?>
        <div class="notice notice-success is-dismissible">
            <p>Your settings have been updated!</p>
        </div><?php
    }
    function spabibox_setup_sections() {
        add_settings_section( 'first_section', 'API Settings',  'spabibox_section_callback' , 'smashing_fields' );
        add_settings_section( 'second_section', 'General Settings', 'spabibox_section_callback' , 'smashing_fields' );
        add_settings_section( 'third_section', 'URL Redirection',  'spabibox_section_callback' , 'smashing_fields' );
    }
    function spabibox_section_callback( $arguments ) {
    	switch( $arguments['id'] ){
    		case 'first_section':
    			echo 'General Settings and API configuration';
    			break;
    		case 'second_section':
    			echo 'Site general settings';
    			break;    	
    		case 'third_section':
    			echo 'URL relative paths to manage logic redirections. All URLS are relative to the current site URL';
            case 'fourth_section':
                echo 'URLS to render email HTML for website';
    			break;    		
    	}
    }
     function spabibox_setup_fields() {
        $fields = array(
        	array(
        		'uid' => 'fapibox_api_production_url',
        		'label' => 'API Production URL',
        		'section' => 'first_section',
        		'type' => 'text',
        		'placeholder' => 'Prod services URL ',
        		'helper' => 'Do not include slash (/) in the end.',
        		'supplimental' => 'Example: https://api-box.skypostal.com/wcf-services',
                'default'=>'https://api-box.skypostal.com/wcf-services'
        	),
        	array(
        		'uid' => 'fapibox_api_test_url',
        		'label' => 'API Test URL',
        		'section' => 'first_section',
        		'type' => 'text',
        		'placeholder' => 'Test services URL ',
        		'helper' => 'Do not include slash (/) in the end.',
        		'supplimental' => 'Example: https://api-box-text.skypostal.com/wcf-services',
                'default'=>'https://api-box-test.skypostal.com/wcf-services'
        	),
        	array(
        		'uid' => 'fapibox_api_app_key',
        		'label' => 'API Application Key',
        		'section' => 'first_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => 'Provided by skypostal',
        		'supplimental' => 'Example: 1656d700707ad9a77f737e5df3db088a',
        	),
        	array(
        		'uid' => 'fapibox_api_user_code',
        		'label' => 'API User Code',
        		'section' => 'first_section',
        		'type' => 'number',
        		'placeholder' => '',
        		'helper' => 'Provided by skypostal',
        		'supplimental' => 'Example: 396',
        	),
        	array(
        		'uid' => 'fapibox_api_copa_id',
        		'label' => 'API Default Partner Identifier',
        		'section' => 'first_section',
        		'type' => 'number',
        		'placeholder' => '',
        		'helper' => 'Provided by skypostal',
        		'supplimental' => 'Example: 616',
        	),
        	array(
        		'uid' => 'fapibox_select_exec_mode',
        		'label' => 'Execution Mode',
        		'section' => 'first_section',
        		'type' => 'select',
        		'options' => array(
        			'test' => 'Test Environment',
        			'production' => 'Production'        			
        		),
                'default' => array()
        	),//END FIRST SECTION
            array(
                'uid' => 'fapibox_reg_email_mode',
                'label' => 'Virtual Registration Email',
                'section' => 'first_section',
                'type' => 'select',
                'options' => array(
                    'custom' => 'Use Events handler, Don\'t send default email' ,
                    'default' => 'Send default email automatically'                    
                ),
                'default' => array()
            ),//END FIRST SECTION
        	array(
        		'uid' => 'fapibox_login_uk_idef',
        		'label' => 'Login user key prefix',
        		'section' => 'second_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => '',
        		'supplimental' => 'Prefix for user session cookies after login, example: spab_uk',
                'default' => 'spab_uk'
        	),
        	array(
        		'uid' => 'fapibox_login_bo_idef',
        		'label' => 'Login box key prefix',
        		'section' => 'second_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => '',
        		'supplimental' => 'Prefix for box session cookies after login, example: spab_uk',
                'default' => 'spab_bi'
        	),
        	array(
        		'uid' => 'fapibox_login_sess_time',
        		'label' => 'Login session time',
        		'section' => 'second_section',
        		'type' => 'number',
        		'placeholder' => '',
        		'helper' => 'Minutes',
        		'supplimental' => 'Max session time in minutes',
                'default' => '60'
        	),        	
        	array(
        		'uid' => 'fapibox_login_no_sess_red_opt',
        		'label' => 'No session behavior',
        		'section' => 'second_section',
        		'type' => 'select',
        		'options' => array(
        			'redirect' => 'Redirect to no session URL',
        			'message' => 'Display error message'        			
        		),
        		'supplimental' => 'Execution of login-required modules without active/valid session',
                'default' => array()
        	),        	
        	array(
        		'uid' => 'fapibox_invoice_upload_path',
        		'label' => 'Invoice upload main path',
        		'section' => 'second_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => '',
        		'supplimental' => 'Path to store uploaded invoice files by customers',
                'default' => 'apibox_invoices'
        	),
        	array(
        		'uid' => 'fapibox_render_base_markup',
        		'label' => 'Base render markup',
        		'section' => 'second_section',
        		'type' => 'select',
        		'options' => array(
        			'bootstrap' => 'Bootstrap'        			
        		),
                'default' => array()
        	),
        	array(
        		'uid' => 'fapibox_reg_forms_multistep',
        		'label' => 'Registration forms layout',
        		'section' => 'second_section',
        		'type' => 'select',
        		'options' => array(
        			'multistep' => 'By Steps',       			
        			'single' => 'Single Form'        			
        		),
                'default' => array()
        	),
        	array(
        		'uid' => 'fapibox_login_success_url',
        		'label' => 'Login success URL',
        		'section' => 'third_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => '',
        		'supplimental' => 'URL to redirect after a successful login',
                'default' => 'my-account'
        	),
        	array(
        		'uid' => 'fapibox_login_no_sess_url',
        		'label' => 'Login no session URL',
        		'section' => 'third_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => '',
        		'supplimental' => 'URL to redirect unauthorized modules access (If redirection enabled)',
                'default' => 'login'
        	),
        	array(
        		'uid' => 'fapibox_login_logout_url',
        		'label' => 'Logout URL',
        		'section' => 'third_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => '',
        		'supplimental' => 'URL to detect logout action. Will redirect to no session URL when executed. Sample: service-logout',
                'default' => 'service-logout'
        	),
        	array(
        		'uid' => 'fapibox_shipment_details_url',
        		'label' => 'Shipment details URL',
        		'section' => 'third_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => '',
        		'supplimental' => 'URL to redirect when selecting a shipment to view details',
                'default' => 'view-shipment'
        	),
        	array(
        		'uid' => 'fapibox_shipment_invoice_url',
        		'label' => 'Shipment invoice upload URL',
        		'section' => 'third_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => '',
        		'supplimental' => 'URL to redirect when selecting a shipment to upload invoice',
                'default' => 'view-invoice'
        	),
        	array(
        		'uid' => 'fapibox_login_recovery_pass_code',
        		'label' => 'Recovery Password Code URL',
        		'section' => 'third_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => '',
        		'supplimental' => 'URL to redirect when requesting a code to recover password, must display valid shortcut',
                'default' => 'recovery-password'
        	),
        	array(
        		'uid' => 'fapibox_login_recovery_pass_update',
        		'label' => 'Recovery Password Update URL',
        		'section' => 'third_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => '',
        		'supplimental' => 'URL to validate recovery code and update password, must display valid shortcut',
                'default' => 'new-password'
        	),
            array(
                'uid' => 'fapibox_terms_conditions_path',
                'label' => 'Terms & Conditions path',
                'section' => 'third_section',
                'type' => 'text',
                'placeholder' => '',
                'helper' => '',
                'supplimental' => 'URL to link terms & conditions page',
                'default' => 'terms-and-conditions'
            ),
            array(
                'uid' => 'fapibox_activate_box_path',
                'label' => 'Box Activation URL',
                'section' => 'third_section',
                'type' => 'text',
                'placeholder' => '',
                'helper' => '',
                'supplimental' => 'URL to link users to the activation path when not activated',
                'default' => ''
            ),
            array(
                'uid' => 'fapibox_calc_custom_percent_add',
                'label' => 'Customs calculation additional percentage (0%-100%)',
                'section' => 'second_section',
                'type' => 'text',
                'placeholder' => '',
                'helper' => '',
                'supplimental' => 'Additional % fee to be added to customs calculation',
                'default' => '0'
            ),
            array(
                'uid' => 'fapibox_calc_shipment_percent_add',
                'label' => 'Shipment calculation additional percentage (0%-100%)',
                'section' => 'second_section',
                'type' => 'text',
                'placeholder' => '',
                'helper' => '',
                'supplimental' => 'Additional % fee to be added to shipment calculation',
                'default' => '0'
            )
        );
    	foreach( $fields as $field ){
        	add_settings_field( $field['uid'], $field['label'],  'spabibox_field_callback' , 'smashing_fields', $field['section'], $field );
            register_setting( 'smashing_fields', $field['uid'] );
    	}
    }
    function spabibox_field_callback( $arguments ) {
        $value = get_option( $arguments['uid'] );
        if( ! $value ) {
            $value = $arguments['default'];
        }
        switch( $arguments['type'] ){
            case 'text':
            case 'password':
            case 'number':
                $isvalid=true;
                $message='';
                if($arguments['uid']=='fapibox_calc_custom_percent_add' || $arguments['uid']=='fapibox_calc_shipment_percent_add'){
                    if(is_numeric($value)){

                        if($value <0 || $value > 100) {
                            $isvalid=false;
                            $message=' Invalid option ';    
                        }

                    }else{
                        $isvalid=false;
                        $message=' Invalid option ';
                    }
                }
                if($isvalid){
                    printf( '<input style="min-width:400px;" name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                }else{
                    printf( '<input class="spapibox_invalid_field_data" style="min-width:400px; border:1px solid red;" name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" /><span style="color:red;"> '.$message.' </span>', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                }
                break;
            case 'textarea':
                printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
                break;
            case 'select':
            case 'multiselect':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $attributes = '';
                    $options_markup = '';
                    foreach( $arguments['options'] as $key => $label ){
                        $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, $value, true ) ], $key, false ), $label );
                    }
                    if( $arguments['type'] === 'multiselect' ){
                        $attributes = ' multiple="multiple" ';
                    }
                    printf( '<select style="min-width:400px;" name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup );
                }
                break;
            case 'radio':
            case 'checkbox':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    $iterator = 0;
                    foreach( $arguments['options'] as $key => $label ){
                        $iterator++;
                        $options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator );
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
                break;
        }
        if( $helper = $arguments['helper'] ){
            printf( '<span class="helper"> %s</span>', $helper );
        }
        if( $supplimental = $arguments['supplimental'] ){
            printf( '<p class="description">%s</p>', $supplimental );
        }
    }
/* END CONFIGURATION SETTINGS */
?>