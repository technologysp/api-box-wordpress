<?php
/**
 * Skypostal Shortcodes handling
 *
 * @package Skypostal_apibox
 * @subpackage Shortcodes
 * @since 1.0.0
 */

/**
 * Handles shortcodes calls. Displays the shortcode HTML.
 * 
 */
function spapibox_shortcode_login_form_NOTINUSE() {
	ob_start();		
	$tools = new skypostalServices();		
	$file = spapibox_route_template('login_form.template.html');
	$file_content = file_get_contents($file);		
	spapibox_translate_string("@@sp_action", $_SERVER['REQUEST_URI'], $file_content, false);	
	$file_content=spapibox_translate_captions($file_content);
	if ( isset( $_GET['login'] ) &&  $_GET['login'] =='failed') {		
		$message=esc_html__( 'Invalid user or password', 'skypostal_apibox');
		echo '<div class="alert alert-danger" role="alert">'.$message.'</div>';
	}
	echo $file_content;
	return ob_get_clean();
}

function spapibox_shortcode_login_form() {
	ob_start();		
	$tools = new skypostalServices();			
	$render='';
	spapibpx_enqueue_scripts();
	spapibpx_enqueue_styles();	
	$form = spapibox_form_build_login($tools, false);
	$render= spapibox_form_render_group($form, $_POST);	
	echo $render;			
	return ob_get_clean();
}

function spapibox_shortcode_logout_action(){
	ob_start();		
	$tools = new skypostalServices();	
	//$tools->save_logout_service_session();			
	$script= '<script>
	document.cookie = "'.$tools->_login_user_key_identifier.'=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
	document.cookie = "'.$tools->_login_box_id_identifier.'=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
	</script>';
	echo $script.$tools->get_no_session_action();
	return ob_get_clean();		
}

function spapibox_shortcode_customer_registration_virtual() {
	ob_start();		
	$tools = new skypostalServices();		
	wp_enqueue_script( 'custom_js_crv', plugins_url( '/js/customer_registration_virtual.js', __FILE__ ), array(), $tools->version );	
	wp_enqueue_style( 'apibox_main',plugins_url( '/css/apibox_main.css', __FILE__ ), array(), $tools->version);
	spapibpx_enqueue_scripts();
	spapibpx_enqueue_styles();
	$form = spapibox_form_build_customer_registration_virtual($tools, false);
	$render= spapibox_form_render_group($form, $_POST);	
	echo $render;	
	return ob_get_clean();
}

function spapibox_shortcode_customer_registration_default() {
	ob_start();		
	$tools = new skypostalServices();		
	wp_enqueue_script( 'custom_js_crd', plugins_url( '/js/customer_registration_virtual.js', __FILE__ ), array(), $tools->version );	
	wp_enqueue_style( 'apibox_main',plugins_url( '/css/apibox_main.css', __FILE__ ), array(), $tools->version);

	spapibpx_enqueue_scripts();
	spapibpx_enqueue_styles();
	$form = spapibox_form_build_customer_registration_default($tools, false);
	$render= spapibox_form_render_group($form, $_POST);	
	echo $render;	
	return ob_get_clean();
}

function spapibox_shortcode_customer_update_personal_info() {
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}
	wp_enqueue_script( 'custom_js_upi', plugins_url( '/js/customer_update_personal_info.js', __FILE__ ), array(), $tools->version );	
	
	spapibpx_enqueue_scripts();
	spapibpx_enqueue_styles();
	$form = spapibox_form_build_customer_update_personal_info($tools);
		
	if(!isset($_POST[$form['#id']])){
		$info=$tools->sp_customer_get_info();			 	        	
	    if(!$info[0]->_verify){
	    	echo $tools->get_no_session_action();
			return ob_get_clean();
	    }		
	    $date_of_birth=$info[0]->customer_birth_date;
	    preg_match('/\/Date\(([0-9]+)(\+[0-9]+)?/', $date_of_birth, $time);
	    $ts = $time[1] / 1000;
		// Define Time Zone if exists
		$tz = isset($time[2]) ? new DateTimeZone($time[2]) : null;
		$dt = new DateTime('@'.$ts);
		$show_date= $dt->format('d/m/Y');
	    $data=array(
	    	'first_name'=>$info[0]->customer_first_name,
	    	'last_name'=>$info[0]->customer_last_name,
	    	'address_id_number'=>$info[0]->customer_identity_number,
	    	'date_of_birth'=>$show_date,
	    	'gender'=>$info[0]->customer_gender	    	
	    );
	}else
		$data=$_POST;

	$render= spapibox_form_render_group($form, $data);	
	echo $render;
	return ob_get_clean();	
}

function spapibox_shortcode_login_small_box(){
	ob_start();		
	$tools = new skypostalServices();

	if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}
	$info=$tools->sp_customer_get_info();			 	        	
    if(!$info[0]->_verify){
    	echo $tools->get_no_session_action();
		return ob_get_clean();
    }

	$boxid=$tools->get_session_box_id();
	echo spapibox_themes_theme_login_info_box($info[0],$boxid);	
	return ob_get_clean();
}

function spapibox_shortcode_customer_activate_box(){
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}
	$info=$tools->sp_customer_get_info();			 	        	
    if(!$info[0]->_verify){
    	echo $tools->get_no_session_action();
		return ob_get_clean();
    }

	wp_enqueue_script( 'custom_js_cab', plugins_url( '/js/customer_activate_box.js', __FILE__ ), array(), $tools->version );	
	$form = spapibox_form_build_customer_activate_box($tools);
		
	if(!isset($_POST[$form['#id']])){
		$info=$tools->sp_customer_get_info();			 	        	
	    if(!$info[0]->_verify){
	    	echo $tools->get_no_session_action();
			return ob_get_clean();
	    }		
	}else
		$data=$_POST;
	//Remove the sec code:
	$data['cc_security_code']='';
	$render='';
	if($info[0]->is_active){
		$render = spapibox_themes_theme_box_payment_method($info[0]->last_payment_method);
	}
	$render.= spapibox_form_render_group($form, $data);
	
	echo $render;
	return ob_get_clean();
}

function spapibox_customer_update_address_info() {
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}

	wp_enqueue_script( 'custom_js_cua', plugins_url( '/js/customer_update_address_info.js', __FILE__ ), array(), $tools->version );	
	
	spapibpx_enqueue_scripts();
	spapibpx_enqueue_styles();
	
		
	if(!isset($_POST[$form['#id']])){
		$info=$tools->sp_customer_get_info();			 	        	
	    if(!$info[0]->_verify){
	    	echo $tools->get_no_session_action();
			return ob_get_clean();
	    }		

	    if(isset($info[0]->customer_address[0]) && $info[0]->customer_address[0]->_verify){
	    	$source=$info[0]->customer_address[0];	    	
		    $data=array(
		    	'address_country'=>$source->country_code,
		    	'address_state'=>$source->state_code,
		    	'address_city'=>$source->city_code,
		    	'address_address'=>$source->address_01,
		    	'address_region'=>$source->neighborhood,	    	
		    	'address_postal_code'=>$source->zip_code,		    			    	
		    );	
	    }
	    if(isset($info[0]->customer_phone) ){
	    	foreach($info[0]->customer_phone as $phone){
	    		if($phone->_verify){
	    			if($phone->phone_type==1){// 1 PHONE
	    				$data['account_phone_country']=$phone->phone_area_code;
		    			$data['account_phone_number']=$phone->phone_number;
		    			$data['account_phone_ext']=$phone->phone_extension;
	    			}else{// 2 CELLPHONE
						$data['account_cellphone_country']=$phone->phone_area_code;
		    			$data['account_cellphone_number']=$phone->phone_number;
		    			$data['account_cellphone_ext']=$phone->phone_extension;
	    			}

	    		}
	    	}	    		
	    }
	}else
		$data=$_POST;

	$form = spapibox_form_build_customer_address_info($tools,$data);
	$render= spapibox_form_render_group($form, $data);
	echo $render;
	return ob_get_clean();	
}

function spapibox_customer_get_shipments(){
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}
	$info=$tools->sp_customer_get_info();			 	        	
    if(!$info[0]->_verify){
    	echo $tools->get_no_session_action();
		return ob_get_clean();
    }

	wp_enqueue_script( 'custom_js_cab', plugins_url( '/js/customer_get_shipments.js', __FILE__ ), array(), $tools->version );	
	
	spapibpx_enqueue_scripts();
	spapibpx_enqueue_styles();

	$form = spapibox_form_build_customer_get_shipments($tools);
	$searchresults=false;
	$data=array();
	if(isset($_POST[$form['#id']])){
		$searchresults=true;		
		$results_key = $form['#id'].'_result';
		$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);

		if (!$tools->validateDate($_POST['start_date'], 'Y-m-d')) $_POST[$results_key]['danger'][] =array('field'=>'start_date', 'message'=>esc_html__('Invalid start date','skypostal_apibox'));

		if (!$tools->validateDate($_POST['end_date'], 'Y-m-d')) $_POST[$results_key]['danger'][] =array('field'=>'end_date', 'message'=>esc_html__('Invalid end date','skypostal_apibox'));

		$data=$_POST;
	}else{

		$startdate=$form['account_information']['fields']['group1']['start_date']['default'];
		$enddate=$form['account_information']['fields']['group1']['end_date']['default'];
		$_POST['start_date']=$startdate;
		$_POST['end_date']=$enddate;
		$_POST[$form['#id']]=$form['#id'];
		$searchresults=true;
	}

	if( isset( $data[$results_key] ) && isset( $data[$results_key]['danger'] ) && count( $data[$results_key]['danger'] ) > 0) $searchresults=false;

	$render= spapibox_form_render_group($form, $data);
	
	$table['header']=array(
		'trck_nmr_fol'=>__('AWB','skypostal_apibox'),
		'external_tracking'=>__('External Tracking','skypostal_apibox'),
		'merchant'=>__('Merchant','skypostal_apibox'),
		'shipment_content'=>__('Contents','skypostal_apibox'),
		'shipment_status'=>__('Status','skypostal_apibox'),
		'date_received'=>__('Date','skypostal_apibox'),
		"invoice"=>__('Invoice','skypostal_apibox')
	);
	$table['body']=array();

	if(isset($_POST[$form['#id']]) && $searchresults){

		$shipments = $tools->sp_customer_get_shipments($data);
		foreach($shipments as $ship){
			if($ship->_verify){
			$date_r=$ship->date_received;
		    preg_match('/\/Date\(([0-9]+)(\+[0-9]+)?/', $date_r, $time);
		    $ts = $time[1] / 1000;
			// Define Time Zone if exists
			$tz = isset($time[2]) ? new DateTimeZone($time[2]) : null;
			$dt = new DateTime('@'.$ts);
			$show_date= $dt->format('d/m/Y');

			$inv=array('value'=>'');
			$union='?';
			if(strrpos($tools->_shipment_invoice_url,'?')!==false) $union='&';
			if($ship->invoice_required==1){

					$inv_text=__('Required','skypostal_apibox');
					
					if(isset($ship->comm_inv_detail_declared_value) && is_numeric($ship->comm_inv_detail_declared_value)) {
						if($ship->comm_inv_detail_declared_value>0) {
							$inv_text='$'.$ship->comm_inv_detail_declared_value.' ('.__('Change','skypostal_apibox').')';
						}
					}

					
					if(!empty($ship->invoice_file_name)){
						//$inv_text=__('Upload Again','skypostal_apibox');
						$inv_text=__('Change File','skypostal_apibox');
					}

					$inv=array('value'=>$inv_text, 'link'=>$tools->_shipment_invoice_url.$union.'awb='.$ship->trck_nmr_fol);
			}	
			$union_ship='?';
			if(strrpos($tools->_shipment_details_url,'?')!==false) $union_ship='&';


			$table['body'][]=array(
				'trck_nmr_fol'=>array('value'=>$ship->trck_nmr_fol, 'link'=>$tools->_shipment_details_url.$union_ship.'awb='.$ship->trck_nmr_fol),
				'external_tracking'=>array('value'=>$ship->external_tracking),/**/
				'merchant'=>array('value'=>$ship->merchant),
				'shipment_content'=>array('value'=>$ship->shipment_content),
				'shipment_status'=>array('value'=>$ship->shipment_status),
				'date_received'=>array('value'=>$show_date),
				'invoice'=>$inv
			);	
			}		
		}
	}
	$render.=spapibox_form_render_table($table,$form['#id']);
	if(isset($_POST[$form['#id']]) && count($table['body']) <=0) $render.=spapibox_get_message('warning',__('No information found', 'skypostal_apibox'));
	echo $render;
	return ob_get_clean();
}

function spapibox_customer_get_shipment_info(){
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}
	$info=$tools->sp_customer_get_info();			 	        	
    if(!$info[0]->_verify){
    	echo $tools->get_no_session_action();
		return ob_get_clean();
    }

	$form = spapibox_form_build_customer_get_shipment_info($tools);
	$searchresults=false;
	$data=array();
	$searchawb='';
	if(!isset($_POST[$form['#id']])){				
	    if(isset($_GET['awb']) && is_numeric($_GET['awb'])){
	    	$searchresults=true;
	    	$searchawb=sanitize_text_field($_GET['awb']);
	    	$data['trck_nmr_fol']=$searchawb;
	    }	    
	}else{
		$searchresults=true;		
		$results_key = $form['#id'].'_result';
		$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);

		if(!is_numeric($_POST['trck_nmr_fol'])) $_POST[$results_key]['danger'][] =array('field'=>'trck_nmr_fol', 'message'=>esc_html__('Invalid Number','skypostal_apibox'));				

		
		if (isset($_POST['trck_nmr_fol'])) {
			$searchawb=$_POST['trck_nmr_fol'];			
			$data=$_POST;
		}	
		
	}

	if( isset( $data[$results_key] ) && isset( $data[$results_key]['danger'] ) && count( $data[$results_key]['danger'] ) > 0) $searchresults=false;

	$render= spapibox_form_render_group($form, $data);
	
	/*first table details*/
	$table['header']=array(
		'trck_nmr_fol'=>__('AWB','skypostal_apibox'),
		'external_tracking'=>__('External Tracking','skypostal_apibox'),
		'merchant'=>__('Merchant','skypostal_apibox'),
		'shipment_content'=>__('Contents','skypostal_apibox'),
		'shipment_status'=>__('Status','skypostal_apibox'),
		'shipment_address'=>__('Destination','skypostal_apibox')
	);
	$table['body']=array();
	/*SECOND TABLE DETAILS (MORE) */
	$table_two['header']=array(
		'trck_nmr_fol'=>__('Shipment Value','skypostal_apibox'),
		'external_tracking'=>__('Weight','skypostal_apibox'),
		'merchant'=>__('Shipping Charge','skypostal_apibox'),
		'shipment_content'=>__('Contents','skypostal_apibox'),
		'shipment_status'=>__('Status','skypostal_apibox'),
		'shipment_address'=>__('Destination','skypostal_apibox')
	);
	$table_two['body']=array();


	if(is_numeric($searchawb) && $searchresults){

		$shipments = $tools->sp_customer_get_shipment_info($data);
		foreach($shipments as $ship){

			$table['title']=__("Shipment Details",'skypostal_apibox');
			$table['body'][]=array(
				'trck_nmr_fol'=>array('value'=>$ship->trck_nmr_fol, 'link'=>$tools->_shipment_details_url.'?awb='.$ship->trck_nmr_fol),
				'external_tracking'=>array('value'=>$ship->external_tracking),
				'merchant'=>array('value'=>$ship->shipment->merchant_name),
				'shipment_content'=>array('value'=>$ship->shipment->content),
				'shipment_status'=>array('value'=>$ship->shipment->status_name),
				'shipment_address'=>array('value'=>$ship->shipment->address.', '.$ship->shipment->city_name.', '.$ship->shipment->state_name.', '.$ship->shipment->country_name)
			);		

			$tracking_table['title']=__("Tracking",'skypostal_apibox');
			$tracking_table['header']=array(
				'location'=>__('Location','skypostal_apibox'),
				'event_date'=>__('Event Date','skypostal_apibox'),
				'status'=>__('Status','skypostal_apibox'),
				'comment'=>__('Comments','skypostal_apibox')				
			);
			$tracking_table['body']=array();	
			foreach($ship->tracking as $track){
				$date_r=$track->entry_date;
			    preg_match('/\/Date\(([0-9]+)(\+[0-9]+)?/', $date_r, $time);
			    $ts = $time[1] / 1000;
				// Define Time Zone if exists
				$tz = isset($time[2]) ? new DateTimeZone($time[2]) : null;
				$dt = new DateTime('@'.$ts);
				$show_date= $dt->format('c');

				$tracking_table['body'][]=array(
					'location'=>array('value'=>$track->iata_code),
					'event_date'=>array('value'=>$show_date),
					'status'=>array('value'=>$track->track_description),
					'comment'=>array('value'=>$track->track_obs)
				);		
			}

		}
	}
	$render.=spapibox_form_render_table($table,$form['#id']).spapibox_form_render_table($tracking_table,$form['#id'].'_2');
	echo $render;
	return ob_get_clean();
}

function spapibox_shortcode_customer_update_email() {
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}	
	$form = spapibox_form_build_customer_update_email($tools);
		
	if(!isset($_POST[$form['#id']])){
		$info=$tools->sp_customer_get_info();			 	        	
	    if(!$info[0]->_verify){
	    	echo $tools->get_no_session_action();
			return ob_get_clean();
	    }			    
	    $data=array(
	    	'customer_email'=>$info[0]->customer_email,	    	
	    	'customer_current_email'=>$info[0]->customer_email
	    );
	}else
		$data=$_POST;

	$render= spapibox_form_render_group($form, $data);	
	echo $render;
	return ob_get_clean();	
}

function spapibox_shortcode_customer_change_password() {
	ob_start();		

	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}	
		
	$form = spapibox_form_build_customer_recover_password_update($tools);		
	$results_key = $form['#id'].'_result';
	
	if(isset($_POST[$form['#id']])){

		$_POST = spapibox_check_post($_POST);			

		$info=$tools->sp_customer_get_info();			 	        	
	    if(!$info[0]->_verify){
	    	echo $tools->get_no_session_action();
			return ob_get_clean();	
		}			    

		$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);
		if ($_POST['password'] != $_POST['confirm_password'])  $_POST[$results_key]['danger'][] =array('field'=>'password', 'message'=>esc_html__('Password does not match','skypostal_apibox'));

		if( isset( $_POST[$results_key] ) && isset( $_POST[$results_key]['danger'] ) && count( $_POST[$results_key]['danger'] ) > 0){
				//DO NOTHING			    
		 }else {
			$data=array(		    	
				"customer_email"=>$info[0]->customer_email,	
				"customer_new_password"=>$_POST['password'] 
		    );
		    $update=$tools->sp_customer_update_password($data);			 	        					    
		    if($update[0]->_verify){
				echo spapibox_get_message('success',__('Password updated','skypostal_apibox'));
				//echo ' <p><b><a href="/'.$tools->_login_no_sess_url.'">Login</a></b></p>';
				return ob_get_clean();
		    }else{
		    	$_POST[$results_key]['danger'][] =array('field'=>'', 'message'=>esc_html__('Password not updated','skypostal_apibox'));
		    }
		 }
	}

	$render= spapibox_form_render_group($form, $_POST);	
	echo $render;
	
	return ob_get_clean();
	//echo '<pre>'.print_r($_POST, true).'</pre>';
	
}

function spapibox_shortcode_customer_update_password() {
	ob_start();		
	$tools = new skypostalServices();	
	
	$k= (isset($_GET['k'])? $_GET['k']:'');
	$e= (isset($_GET['e'])? urldecode($_GET['e']):'');
	$d= (isset($_GET['d'])? $_GET['d']:'');

	$e=sanitize_text_field($e);
	$data=array("customer_email"=>$e);
	$info=$tools->sp_partner_customer_get_info($data);		
	if($info[0]->_verify){	    	
	    	//email exists and partner has access:
	    	$emailsent = strtolower($e);
	    	$diff_rand= $d;
	    	$code =hexdec( substr(md5($info[0]->customer_key.$diff_rand.$emailsent), 0, 8) );
	    	if($code!=$k){
	    		echo spapibox_get_message('danger',__('Invalid recovery code','skypostal_apibox'));
	    		return ob_get_clean();
	    	}	    	
	    	
			$form = spapibox_form_build_customer_recover_password_update($tools);		
			$results_key = $form['#id'].'_result';
			
			if(isset($_POST[$form['#id']])){							    

				$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);
				if ($_POST['password'] != $_POST['confirm_password'])  $_POST[$results_key]['danger'][] =array('field'=>'password', 'message'=>esc_html__('Password does not match','skypostal_apibox'));

				if( isset( $_POST[$results_key] ) && isset( $_POST[$results_key]['danger'] ) && count( $_POST[$results_key]['danger'] ) > 0){
						//DO NOTHING			    
				 }else {
					$data=array(
				    	"customer_box_id"=>$info[0]->customer_box_id,
						"customer_key"=>$info[0]->customer_key,			
						"customer_email"=>$e,	
						"customer_new_password"=>$_POST['password'] 
				    );
				    $update=$tools->sp_customer_update_password($data);			 	        					    
				    if($update[0]->_verify){
						echo spapibox_get_message('success',__('Password updated','skypostal_apibox'));
						echo ' <p><b><a href="/'.$tools->_login_no_sess_url.'">Login</a></b></p>';
						return ob_get_clean();
				    }else{
				    	$_POST[$results_key]['danger'][] =array('field'=>'', 'message'=>esc_html__('Password not updated','skypostal_apibox'));
				    }
				 }
			}

			$render= spapibox_form_render_group($form, $_POST);	
			echo $render;
			
	    }else{
	    	echo spapibox_get_message('danger',__('Invalid recovery code','skypostal_apibox'));
	    	return ob_get_clean();
	    	//$_POST[$results_key]['danger'][] =array('field'=>'', 'message'=>esc_html__('Account not found','skypostal_apibox'));
	    }
	return ob_get_clean();
	//echo '<pre>'.print_r($_POST, true).'</pre>';
	
}

function spapibox_shortcode_customer_reset_password_send_code() {
	ob_start();		
	$tools = new skypostalServices();	

	$form = spapibox_form_build_customer_recover_password_code($tools);
	$results_key = $form['#id'].'_result';
	$render='';
	if(isset($_POST[$form['#id']])){

		$_POST = spapibox_check_post($_POST);

		$info=$tools->sp_partner_customer_get_info($_POST);			 

	    if($info[0]->_verify){
	    	//print_r($info);
	    	//email exists and partner has access:
	    	$emailsent = strtolower($_POST['customer_email']);
	    	$diff_rand= rand(100,999);
	    	$code =hexdec( substr(md5($info[0]->customer_key.$diff_rand.$emailsent), 0, 8) );
	    	$r=md5(rand());
			
			$link= get_site_url().'/'.$tools->_login_recovery_pass_update_url;

			$query = 'q2=two';			
			$parsedUrl = parse_url($link);
			if ($parsedUrl['path'] == null) {
				$link .= '/';
			}
			$separator = ($parsedUrl['query'] == NULL) ? '?' : '&';
			$link .= $separator .'k='.$code.'&e='.urlencode($emailsent).'&d='.$diff_rand.'&r='.urlencode($r);

			$to = $emailsent;
			$subject = __('Password Recovery','skypostal_apibox');
			$body = __('To recover your password','skypostal_apibox') . ', <a href="'.$link	. '"> ' . __('Click Here','skypostal_apibox') . ' </a><br />' . __('Regards','skypostal_apibox') ;
			$headers = array('Content-Type: text/html; charset=UTF-8');
			 
			wp_mail( $to, $subject, $body, $headers );

			$_POST[$results_key]['success'][] =array('field'=>'',  'message'=>esc_html__('Email sent','skypostal_apibox'));    				
	    }else
	    	$_POST[$results_key]['danger'][] =array('field'=>'', 'message'=>esc_html__('Account not found','skypostal_apibox'));	    
	}

	$data=$_POST;
	$render.= spapibox_form_render_group($form, $data);
	echo $render;
	return ob_get_clean();	
}

function spapibox_shortcode_customer_box_status(){
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo '';
		return ob_get_clean();
	}
	$info=$tools->sp_customer_get_info();			 	        	
    if(!$info[0]->_verify){
    	echo '';
		return ob_get_clean();
    }
    $render=spapibox_themes_theme_box_status_alert($info[0]);
    echo $render;
	return ob_get_clean();
}

function spapibox_shortcode_customer_box_consolidation_status(){
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo '';
		return ob_get_clean();
	}
	$info=$tools->sp_customer_get_info();			 	        	
    if(!$info[0]->_verify){
    	echo '';
		return ob_get_clean();
    }
    $data=array();

    $form = spapibox_form_build_customer_consolidation_status($tools,$info[0],false);
    if(isset($_POST[$form['#id']])){
    	$data=$_POST;

    	if($info[0]->box_status !=5 ){//Not consolidation, toggle to consolidation enabled
    		$tools->sp_customer_start_consolidation(array());
    	}else{
			$tools->sp_customer_end_consolidation(array());
    	}

    	//Reload data:
    	$info=$tools->sp_customer_get_info();			 	        	
    	$form = spapibox_form_build_customer_consolidation_status($tools,$info[0],false);
    }


    $render=spapibox_form_render_group($form, $data);	
    //$render=spapibox_themes_theme_consolidation_status($info[0]);
    echo $render;
	return ob_get_clean();
}

function spapibox_shortcode_customer_inactive_alert(){
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo '';
		return ob_get_clean();
	}
	$info=$tools->sp_customer_get_info();			 	        	
    if(!$info[0]->_verify){
    	echo '';
		return ob_get_clean();
    }   

    $render=spapibox_themes_theme_box_status_alert($info[0],true);

    echo $render;
	return ob_get_clean();	
}

function spapibox_shortcode_shipment_invoice_handler(){
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}
	$info=$tools->sp_customer_get_info();			 	        	
    if(!$info[0]->_verify){
    	echo $tools->get_no_session_action();
		return ob_get_clean();
    }

	wp_enqueue_script( 'custom_js_inv', plugins_url( '/js/customer_shipment_invoice.js', __FILE__ ), array(), $tools->version );	
	$render ='';
	/*	spapibpx_enqueue_scripts();
	spapibpx_enqueue_styles();*/

	$form = spapibox_form_build_customer_shipment_invoice($tools);
	//$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);
	$searchresults=true;
	$data=array();
	$searchawb='';

	if(isset($_GET['awb']) && is_numeric($_GET['awb'])) $searchawb=sanitize_text_field($_GET['awb']);
	if(isset($_POST[$form['#id']])) $data=$_POST;	    
	

	$table['header']=array(
		'trck_nmr_fol'=>__('AWB','skypostal_apibox'),
		'external_tracking'=>__('External Tracking','skypostal_apibox'),
		'merchant'=>__('Merchant','skypostal_apibox'),
		'shipment_content'=>__('Contents','skypostal_apibox'),
		'shipment_status'=>__('Status','skypostal_apibox'),
		'shipment_address'=>__('Destination','skypostal_apibox')
	);
	$table['body']=array();

	if(is_numeric($searchawb) && $searchresults){
		$datashipsearch=array();
		$datashipsearch['trck_nmr_fol']=$searchawb;
		$data['trck_nmr_fol']=$searchawb;
		$shipments = $tools->sp_customer_get_shipment_info($datashipsearch);
		foreach($shipments as $ship){

			$table['title']=__("Shipment Details",'skypostal_apibox');
			$table['body'][]=array(
				'trck_nmr_fol'=>array('value'=>$ship->trck_nmr_fol, 'link'=>$tools->_shipment_details_url.'?awb='.$ship->trck_nmr_fol),
				'external_tracking'=>array('value'=>$ship->external_tracking),
				'merchant'=>array('value'=>$ship->shipment->merchant_name),
				'shipment_content'=>array('value'=>$ship->shipment->content),
				'shipment_status'=>array('value'=>$ship->shipment->status_name),
				'shipment_address'=>array('value'=>$ship->shipment->address.', '.$ship->shipment->city_name.', '.$ship->shipment->state_name.', '.$ship->shipment->country_name)
			);		

			$tracking_table['title']=__("Tracking",'skypostal_apibox');
			$tracking_table['header']=array(
				'location'=>__('Location','skypostal_apibox'),
				'event_date'=>__('Event Date','skypostal_apibox'),
				'status'=>__('Status','skypostal_apibox'),
				'comment'=>__('Comments','skypostal_apibox')				
			);
			$tracking_table['body']=array();	
		}
	}	
	$render.=spapibox_form_render_table($table,$form['#id']);
	$render.= spapibox_form_render_group($form, $data);	
	echo $render;
	return ob_get_clean();
}

function spapibox_read_invoice_detail_from_data($detail_info){

  $details=array();

  $currentid=0;

  if(is_array($detail_info)){
      foreach($detail_info as $key=>$value){
          //Check if ID exists:

          if (strpos($key, 'skptinvdetidx') !== false) {
              //Get the main index for array:
            $findid = explode('_',$key);
            if(!is_array($details[$currentid])) $details[$currentid]=array();
            $details[$value]['idui']=$value;
            if(count($findid)>1){
              $currentid=$findid[1];
              if(!is_array($details[$currentid])) $details[$currentid]=array();
              if(isset($details[$currentid])) $details[$currentid]['idui']=$value;
            }
          }

          if (strpos($key, 'skptinvdet-qty') !== false) {
              //Get the main index for array:
            $findid = explode('_',$key);
            //if(count($findid)>1){
              $currentid=$findid[1];
              if(!is_array($details[$currentid])) $details[$currentid]=array();

              $details[$currentid]['qty']=$value;
              
            //}
          }

          if (strpos($key, 'skptinvdet-price') !== false) {
              //Get the main index for array:
            $findid = explode('_',$key);
            //if(count($findid)>1){
              $currentid=$findid[1];
              if(!is_array($details[$currentid])) $details[$currentid]=array();

              $details[$currentid]['price']=$value;
              
            //}
          }

          if (strpos($key, 'skptinvdetdesc_') !== false) {
              //Get the main index for array:
            $findid = explode('_',$key);
            //if(count($findid)>1){
              $currentid=$findid[1];
              if(!is_array($details[$currentid])) $details[$currentid]=array();
              $details[$currentid]['desc']=$value;
              
           // }
          }
      }
  }
  return $details;
}

function spapibox_shortcode_shipment_invoice_handler_custom() {
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}
	$info=$tools->sp_customer_get_info();			 	        	
    if(!$info[0]->_verify){
    	echo $tools->get_no_session_action();
		return ob_get_clean();
    }

	wp_enqueue_script( 'custom_js_inv_custom', plugins_url( '/js/customer_shipment_invoice_custom.js', __FILE__ ), array(), $tools->version );	
	wp_enqueue_style( 'apibox_invoce',plugins_url( '/css/apibox_invoce.css', __FILE__ ), array(), $tools->version);
	/*	spapibpx_enqueue_scripts();
	spapibpx_enqueue_styles();*/
	$render='';

	$results_key = 'sp_customer_invoice_uploader_custom_result';

	//$render .='<pre>'.print_r($_POST,true).'</pre>';
	$pre_data_detail=spapibox_read_invoice_detail_from_data($_POST);

	if(isset($_POST['sp_customer_invoice_uploader_custom'])){
		//sp_customer_upload_invoice_custom
		$post_detail=array();
		foreach($pre_data_detail as $key=>$val){
			$post_detail[]=array('quantity'=>$val['qty'], 'description'=>$val['desc'], 'price_value'=>$val['price'], 'reference_code'=>'');
		}
		$_POST['detail']=$post_detail;
		
		$result = $tools->sp_customer_upload_invoice_custom($_POST);
		if($result[0]->_verify){
        	$_POST[$results_key]['success'][] =array('field'=>'',  'message'=>esc_html__('Information updated','skypostal_apibox'));
        }else
        	$_POST[$results_key]['danger'][] =array('field'=>'', 'message'=>esc_html__('Information not updated','skypostal_apibox'));
	}

	$searchresults=true;
	$data=array();
	$searchawb='';
	
	if(isset($_GET['awb']) && is_numeric($_GET['awb'])) $searchawb=sanitize_text_field($_GET['awb']);
		    

	if(is_numeric($searchawb) && $searchresults){

		$datashipsearch=array();
		$datashipsearch['trck_nmr_fol']=$searchawb;
		$data['trck_nmr_fol']=$searchawb;
		$shipments = $tools->sp_customer_get_shipment_info($datashipsearch);


		if(isset($shipments[0]) && isset($shipments[0]->_verify)) {

			$details=array();
			$currentid=0;

			//$render.=print_r($shipments[0]->invoice->detail, true);

			if(isset($shipments[0]->invoice) && isset($shipments[0]->invoice->detail)) {

				foreach($shipments[0]->invoice->detail as $inv_det){

					$details[$currentid]=array();
					$details[$currentid]['idui']=$inv_det->$currentid;
					$details[$currentid]['qty']=$inv_det->quantity;
					$details[$currentid]['price']=$inv_det->price_value;
					$details[$currentid]['desc']=$inv_det->description;
					$currentid+=1;

				}
				if(count($details)>0) $pre_data_detail=$details;
			}
		}

		$current_detail_rows_idx = count($pre_data_detail);

		//$render.=print_r($pre_data_detail, true);
		$form = spapibox_form_build_customer_shipment_invoice_custom($tools,$pre_data_detail);
		if(isset($_POST[$form['#id']])) $data=$_POST;

		$table['header']=array(
			'trck_nmr_fol'=>__('AWB','skypostal_apibox'),
			'external_tracking'=>__('External Tracking','skypostal_apibox'),
			'merchant'=>__('Merchant','skypostal_apibox'),
			'shipment_content'=>__('Contents','skypostal_apibox'),
			'shipment_status'=>__('Status','skypostal_apibox'),
			'shipment_address'=>__('Destination','skypostal_apibox')
		);
		$table['body']=array();
		
		foreach($shipments as $ship){

			$table['title']=__("Shipment Details",'skypostal_apibox');
			$table['body'][]=array(
				'trck_nmr_fol'=>array('value'=>$ship->trck_nmr_fol, 'link'=>$tools->_shipment_details_url.'?awb='.$ship->trck_nmr_fol),
				'external_tracking'=>array('value'=>$ship->external_tracking),
				'merchant'=>array('value'=>$ship->shipment->merchant_name),
				'shipment_content'=>array('value'=>$ship->shipment->content),
				'shipment_status'=>array('value'=>$ship->shipment->status_name),
				'shipment_address'=>array('value'=>$ship->shipment->address.', '.$ship->shipment->city_name.', '.$ship->shipment->state_name.', '.$ship->shipment->country_name)
			);		

			$tracking_table['title']=__("Tracking",'skypostal_apibox');
			$tracking_table['header']=array(
				'location'=>__('Location','skypostal_apibox'),
				'event_date'=>__('Event Date','skypostal_apibox'),
				'status'=>__('Status','skypostal_apibox'),
				'comment'=>__('Comments','skypostal_apibox')				
			);
			$tracking_table['body']=array();	
		}

		$render.=spapibox_form_render_table($table,$form['#id']);
		
		$render.= spapibox_form_render_group($form, $data);	
		$render.='<script> var skpt_current_detail_rows_idx = '.$current_detail_rows_idx.';</script>';
	}	
	

	echo $render;
	return ob_get_clean();
}

function spapibox_shortcode_calculator(){
	ob_start();		
	$tools = new skypostalServices();	
	$render ='';
	/*if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}
	$info=$tools->sp_customer_get_info();			 	        	
    if(!$info[0]->_verify){
    	echo $tools->get_no_session_action();
		return ob_get_clean();
    }*/

    wp_enqueue_script( 'custom_js_calc', plugins_url( '/js/customer_sent_calculator.js', __FILE__ ), array(), $tools->version );	

	$form = spapibox_form_build_calculator($tools);
	$searchresults=false;
	$data=array();
	$searchawb='';
	$render_result = '';
	if(!isset($_POST[$form['#id']])){		

	  /*  SET DEFAULTS    */
	}else{		
		$_POST = spapibox_check_post($_POST);

		$searchresults=true;		
		$results_key = $form['#id'].'_result';
		$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);

		if(!is_numeric($_POST['weight'])) $_POST[$results_key]['danger'][] =array('field'=>'weight', 'message'=>esc_html__('Invalid Weight','skypostal_apibox'));				
		if(!is_numeric($_POST['price_value'])) $_POST[$results_key]['danger'][] =array('field'=>'price_value', 'message'=>esc_html__('Invalid Price','skypostal_apibox'));				
		//if(!is_numeric($_POST['weight'])) $_POST[$results_key]['danger'][] =array('field'=>'weight', 'message'=>esc_html__('Invalid Weight','skypostal_apibox'));				

		if(!empty($_POST['dim_length']) && !is_numeric($_POST['dim_length'])) $_POST[$results_key]['danger'][] =array('field'=>'dim_length', 'message'=>esc_html__('Invalid Length','skypostal_apibox'));	
		if(!empty($_POST['dim_width']) && !is_numeric($_POST['dim_width'])) $_POST[$results_key]['danger'][] =array('field'=>'dim_width', 'message'=>esc_html__('Invalid Width','skypostal_apibox'));	
		if(!empty($_POST['dim_height']) && !is_numeric($_POST['dim_height'])) $_POST[$results_key]['danger'][] =array('field'=>'dim_height', 'message'=>esc_html__('Invalid Height','skypostal_apibox'));	

		$data=$_POST;
		//if (isset($_POST['trck_nmr_fol'])) {$searchawb=$_POST['trck_nmr_fol'];}	

		$result = $tools->sp_shipment_get_ship_rate($data);	


		if(is_array($result) && $result[0]->_verify==1){

			$shiptotal=$result[0]->ship_total_rate;
			$ship_discount=$result[0]->ship_discount;
			$ship_customs=$result[0]->total_customs;

			//Validating additionals
			$x_customs_add= get_option( 'fapibox_calc_custom_percent_add' );
			$x_shipment_add= get_option( 'fapibox_calc_shipment_percent_add' );

			if(is_numeric($x_customs_add) && $x_customs_add>=0 && $x_customs_add <=100){
				$ship_customs+= ($ship_customs*($x_customs_add/100));
			}

			if(is_numeric($x_shipment_add) && $x_shipment_add>=0 && $x_shipment_add <=100){
				$shiptotal+= ($shiptotal*($x_shipment_add/100));
			}

			$render_result = '<div class="row" id="sp_apibox_calculation_result">
			<div class="col-12">
			<h2>Resultado (US$)</h2>
			<table class="table">
			<tr><td>Costo de envio y entrega:</td><td>$'.number_format ($shiptotal,2).'</td></tr>
			<tr><td>Impuestos estimados de aduana</td><td>$'.number_format ($ship_customs,2).'</td></tr>
			</table>
			</div>
			</div>';			
		}else{
			$_POST[$results_key]['danger'][] =array('field'=>'sp_customer_calc_form', 'message'=>esc_html__('Calculation failed. Check your data and try again.','skypostal_apibox'));
		}		
	}

	if( isset( $data[$results_key] ) && isset( $data[$results_key]['danger'] ) && count( $data[$results_key]['danger'] ) > 0) $searchresults=false;

	$render.= spapibox_form_render_group($form, $data);
		
	echo $render.$render_result;
	return ob_get_clean();
}

function spapibox_customer_get_shipments_for_consolidation(){
	ob_start();		
	$tools = new skypostalServices();	
	if (!$tools->is_logged_in_simple()){
		echo $tools->get_no_session_action();
		return ob_get_clean();
	}
	$info=$tools->sp_customer_get_info();			 	        	
    if(!$info[0]->_verify){
    	echo $tools->get_no_session_action();
		return ob_get_clean();
    }

	wp_enqueue_script( 'custom_js_cab', plugins_url( '/js/customer_get_shipments_consolidation.js', __FILE__ ), array(), $tools->version );	
	
	spapibpx_enqueue_scripts();
	spapibpx_enqueue_styles();

	$form = spapibox_form_build_customer_get_shipments_consolidation($tools);
	$searchresults=true;
	$data=array();
	$results_key = $form['#id'].'_result';
	$topmessages='';
	/*if(isset($_POST[$form['#id']])){
		$searchresults=true;		
		$results_key = $form['#id'].'_result';
		$_POST[$results_key]= spapibox_form_validate_required_groups($form , $_POST);

		if (!$tools->validateDate($_POST['start_date'], 'Y-m-d')) $_POST[$results_key]['danger'][] =array('field'=>'start_date', 'message'=>esc_html__('Invalid start date','skypostal_apibox'));

		if (!$tools->validateDate($_POST['end_date'], 'Y-m-d')) $_POST[$results_key]['danger'][] =array('field'=>'end_date', 'message'=>esc_html__('Invalid end date','skypostal_apibox'));

		$data=$_POST;
	}else{

		$startdate=$form['account_information']['fields']['group1']['start_date']['default'];
		$enddate=$form['account_information']['fields']['group1']['end_date']['default'];
		$_POST['start_date']=$startdate;
		$_POST['end_date']=$enddate;
		$_POST[$form['#id']]=$form['#id'];
		$searchresults=true;
	}*/

	if( isset( $data[$results_key] ) && isset( $data[$results_key]['danger'] ) && count( $data[$results_key]['danger'] ) > 0) $searchresults=false;

	
	
	$table['header']=array(
		'check'=>'<input type="checkbox" class="checkable_item_head" value="">',
		'trck_nmr_fol'=>__('AWB','skypostal_apibox'),
		'external_tracking'=>__('External Tracking','skypostal_apibox'),
		//'merchant'=>__('Merchant','skypostal_apibox'),
		'shipment_content'=>__('Contents','skypostal_apibox'),
		//'shipment_status'=>__('Status','skypostal_apibox'),
		//'date_received'=>__('Date','skypostal_apibox'),
		"consolidation_status"=>__('Consolidation','skypostal_apibox'),
		"invoice"=>__('Invoice','skypostal_apibox')
	);
	$table['body']=array();

	if($searchresults){

		$shipments = $tools->sp_customer_get_shipments_hbc($data);

		if(isset($_POST[$form['#id']])){

			$_POST = spapibox_check_post($_POST);			

			if(count($shipments)>0 && $shipments[0]->_verify ){	

				$selected_awbs = explode(",",$_POST['trck_nmr_fol_list']);
				$coonsolidation_detail =array();
				if(count($selected_awbs) > 0 && is_numeric($selected_awbs[0]) ) {

					$count_awb_to_conso =count($selected_awbs);
					$good_to_go_awbs = 0;


					foreach($selected_awbs as $selectawb){

						$current_error=false;
						$found=false;
						$current_value=0;
						$current_url = '';

						foreach($shipments as $ship){

							if(intval ($ship->trck_nmr_fol)==intval ($selectawb)){

								$found=true;

								$invoicegood=true;
								if($ship->invoice_required==1){
									if(empty($ship->invoice_file_name) && $ship->comm_inv_detail_declared_value<=0){
										$invoicegood=false;
									}
								}

								if(isset($ship->comm_inv_detail_declared_value)) $current_value= $ship->comm_inv_detail_declared_value;
								if($current_value<=0) $current_value=1;

								if(!empty($ship->invoice_file_url)) $current_url= $ship->invoice_file_url;

								if($ship->consolidation_requests>0) {								
									$topmessages.=	spapibox_get_message('danger',__('Shipment Not eligible for consolidation, already requested', 'skypostal_apibox').': '.$selectawb );
									$current_error=true;
								}

								if(!$invoicegood){
									$topmessages.=	spapibox_get_message('danger',__('Shipment Not eligible for consolidation, invoice is required', 'skypostal_apibox').': '.$selectawb );
									$current_error=true;
								}

							}

						}

						//Check if AWB was found.
						if($found){
							if(!$current_error){
								$coonsolidation_detail[]=array(
									'trck_nmr_fol'=>$selectawb,
									'awb_declared_value'=>$current_value,
									'awb_invoice_url'=>$current_url
								);
							}
						}else
							$topmessages.=	spapibox_get_message('danger',__('Shipment not available', 'skypostal_apibox').': '.$selectawb);
					}

					//Final check
					if(count($coonsolidation_detail) == $count_awb_to_conso){
						$topmessages.=	spapibox_get_message('success',__('Consolidation requested successfully', 'skypostal_apibox').' ('.$_POST['trck_nmr_fol_list'].')');	

						$response = $tools->sp_customer_send_consolidation(array('trck_nmr_fol_list'=>$coonsolidation_detail));
						
						$shipments = $tools->sp_customer_get_shipments_hbc($data);						

					}else
						$topmessages.=	spapibox_get_message('danger',__('Shipments to consolidate count missmatch', 'skypostal_apibox'));
				}else
					$topmessages.=	spapibox_get_message('danger',__('No available shipments to consolidate', 'skypostal_apibox'));
				
			}else{				
				$topmessages.=	spapibox_get_message('danger',__('No available shipments to consolidate', 'skypostal_apibox'));
			}
		}

		foreach($shipments as $ship){
			if($ship->_verify){
			$date_r=$ship->date_received;
		    preg_match('/\/Date\(([0-9]+)(\+[0-9]+)?/', $date_r, $time);
		    $ts = $time[1] / 1000;
			// Define Time Zone if exists
			$tz = isset($time[2]) ? new DateTimeZone($time[2]) : null;
			$dt = new DateTime('@'.$ts);
			$show_date= $dt->format('d/m/Y');

			$inv=array('value'=>'');
			$union='?';
			if(strrpos($tools->_shipment_invoice_url,'?')!==false) $union='&';
			$invoicegood=true;
			if($ship->invoice_required==1){

					$inv_text=__('Required','skypostal_apibox');
					
					if(isset($ship->comm_inv_detail_declared_value) && is_numeric($ship->comm_inv_detail_declared_value)) {
						if($ship->comm_inv_detail_declared_value>0) {
							$inv_text='$'.$ship->comm_inv_detail_declared_value;
						}
					}					
					if(!empty($ship->invoice_file_name)){
						//$inv_text=__('Upload Again','skypostal_apibox');
						$inv_text=__('File Uploaded','skypostal_apibox');
					}

					if(empty($ship->invoice_file_name) && $ship->comm_inv_detail_declared_value<=0){
						$invoicegood=false;
					}

					$inv=array('value'=>$inv_text, 'link'=>$tools->_shipment_invoice_url.$union.'awb='.$ship->trck_nmr_fol);
					//$inv=array('value'=>$inv_text);
			}	
			$union_ship='?';
			if(strrpos($tools->_shipment_details_url,'?')!==false) $union_ship='&';

			$consolidation_requests='';
			$check = array('value'=>'<input type="checkbox" class="checkable_item" value="">', 'attributes'=>array('prop-awb'=>$ship->trck_nmr_fol));
			if($ship->consolidation_requests>0) {
				$consolidation_requests='Consolidation requested';
				$check=array('value'=>'');
			}

			if(!$invoicegood){
				$consolidation_requests='Invoice is required';
				$check=array('value'=>'');	
			}


			$table['body'][]=array(
				'check'=>$check,
				'trck_nmr_fol'=>array('value'=>$ship->trck_nmr_fol, 'link'=>$tools->_shipment_details_url.$union_ship.'awb='.$ship->trck_nmr_fol),
				'external_tracking'=>array('value'=>$ship->external_tracking),/**/
				'merchant'=>array('value'=>$ship->merchant),
				'shipment_content'=>array('value'=>$ship->shipment_content),
				'shipment_status'=>array('value'=>$ship->shipment_status),
				'date_received'=>array('value'=>$show_date),
				'consolidation_status'=>array('value'=>$consolidation_requests),
				'invoice'=>$inv
			);	
			}		
		}
	}
	$render.='<div class="consolidation_shipments_lists">'.$topmessages;
	$render.=spapibox_form_render_table($table,$form['#id']);	
	$render.='<div class="row"><div class="col-12"><input class="btn btn-primary form-submit  form-control" type="submit" id="sp_customer_get_shipments_consolidation" name="sp_customer_get_shipments" value="Continue" onclick="spapibox_continue_consolidation()"></div></div>';
	$render.='</div >';
	$render.='<div class="consolidation_shipments_confirm" style="display:none;">';
	$render.='<div class="consolidation_title">';
	$render.=__('Are you sure you want to consolidate the following shipments?', 'skypostal_apibox');
	$render.='</div >';
	$render.='<div class="consolidation_shipments_selected">';

	$render.='</div >';
	$render.= spapibox_form_render_group($form, $data);
	$render.='</div >';
	
	if(isset($_POST[$form['#id']]) && count($table['body']) <=0) $render.=spapibox_get_message('warning',__('No information found', 'skypostal_apibox'));
	echo $render;
	return ob_get_clean();
}

function spapibox_shortcode_email_test(){
	ob_start();
	$tools = new skypostalServices();	
	$render='';
	$suite='';
	$name='';
	$email='';
	if(isset($_GET['suite'])) $suite=$_GET['suite'];
	if(isset($_GET['name'])) $name=$_GET['name'];
	if(isset($_GET['email'])) $email=$_GET['email'];

	$emaillink = $tools->get_default_email(array('suite'=>$suite, 'name'=>$name,'email'=>$email));
	$render ='<pre>Email link: ' . $emaillink . '</pre>';

	$render .='<pre>Email content (Just testing): </pre><pre>' . $tools->get_email_contents($emaillink) . '</pre>';
	echo $render;
	return ob_get_clean();
}

?>