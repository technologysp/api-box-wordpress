<?php 
/**
 * Skypostal Services handling
 *
 * @package Skypostal_apibox
 * @subpackage Services
 * @since 1.0.0
 */

/**
 * Handles all the API-BOX service calls, gets admin configuration parameters and handles user login sessions.
 * 
 */
class skypostalServices
{
	public $version;
	public $_url_test;//
	public $_url_prod;//
	public $_api_test_mode;//
	public $login_session_time_minutes;//
	
	private $_app_key;//
	private $_user_code;	//
	public $_login_user_key_identifier;//
	public $_login_box_id_identifier;//
	
	public $_verbose;//
	private $_copa_id;//
	
	//Redirect URLS	
	public $_login_success_redirect_url;
	public $_invoice_upload_path;
	public $_login_no_sess_red_opt;
	public $_login_no_sess_url;
	public $_login_logout_url;	
	public $_shipment_details_url;
	public $_shipment_invoice_url;
	public $_login_recovery_pass_code_url;
	public $_login_recovery_pass_update_url;
	public $_terms_conditions_path;
	public $_reg_email_mode;

    public function __construct($arg= NULL){
    	$this->_verbose= false;

		$this->version = '1.0.1.9';
		$this->_app_key= get_option( 'fapibox_api_app_key' );//'zgo4oD0DiMOVN02172dhMXC4o739TwdH';
		$this->_url_test= get_option( 'fapibox_api_test_url' );//'https://api-box-test.skypostal.com/wcf-services';
		$this->_url_prod= get_option( 'fapibox_api_production_url' );//'https://api-box.skypostal.com/wcf-services';
		$opt = get_option( 'fapibox_select_exec_mode' );
		$this->_api_test_mode= true;
		if(is_array($opt) && count($opt)>0){
			$this->_api_test_mode= $opt[0]=='production' ? false:true;	
		}		
		$this->_user_code= get_option( 'fapibox_api_user_code' );//120;
		$this->_login_user_key_identifier= get_option( 'fapibox_login_uk_idef' );//'spab_uk';
		$this->_login_box_id_identifier= get_option( 'fapibox_login_bo_idef' );//'spab_bi';
		$this->login_session_time_minutes= get_option( 'fapibox_login_sess_time' );//120;
		$this->_login_success_redirect_url= get_option( 'fapibox_login_success_url' );//'my-account';
		
		$this->_copa_id = get_option( 'fapibox_api_copa_id' );// 616;
		$opt=get_option( 'fapibox_login_no_sess_red_opt' );
		$this->_login_no_sess_red_opt= 'message';
		if(is_array($opt) && count($opt)>0){
			$this->_login_no_sess_red_opt= $opt[0];
		}
		
		$this->_login_no_sess_url=get_option( 'fapibox_login_no_sess_url' );
		$this->_invoice_upload_path=get_option( 'fapibox_invoice_upload_path' );
		$this->_login_logout_url=get_option( 'fapibox_login_logout_url' );
		$this->_shipment_details_url=get_option( 'fapibox_shipment_details_url' );
		$this->_shipment_invoice_url=get_option( 'fapibox_shipment_invoice_url' );
		$this->_login_recovery_pass_code_url=get_option( 'fapibox_login_recovery_pass_code' );
		$this->_login_recovery_pass_update_url=get_option( 'fapibox_login_recovery_pass_update' );
		$this->_terms_conditions_path=get_option( 'fapibox_terms_conditions_path' );
		$this->_reg_email_mode='custom';
		$opt = get_option( 'fapibox_reg_email_mode' );
		if(is_array($opt) && count($opt)>0){
			$this->_reg_email_mode=$opt[0];
		}
		
    }

    public function validateDate($date, $format = 'd/m/Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}
	public function save_logout_service_session(){
		setcookie($this->_login_user_key_identifier, '', time() + $session_time,COOKIEPATH,COOKIE_DOMAIN );
		setcookie($this->_login_box_id_identifier, '', time() + $session_time, COOKIEPATH,COOKIE_DOMAIN);
		//Also saving pre-cached customer name:
		setcookie($this->_login_user_key_identifier.'idef', '', time() + $session_time,COOKIEPATH,COOKIE_DOMAIN );		
	}
    public function save_login_service_session($customer_key, $customer_box_id,$dispname){
    	$session_time = $this->login_session_time_minutes * 60;//Setting cookie session time in minutes:
    	setcookie($this->_login_user_key_identifier, $customer_key, time() + $session_time,COOKIEPATH,COOKIE_DOMAIN );
		setcookie($this->_login_box_id_identifier, $customer_box_id, time() + $session_time, COOKIEPATH,COOKIE_DOMAIN);
		//Also saving pre-cached customer name:
		setcookie($this->_login_user_key_identifier.'idef', $dispname, time() + $session_time,COOKIEPATH,COOKIE_DOMAIN );		
    }
    public function get_default_email($data){
    	$email='';		
		if( $this->_reg_email_mode=='default'){
			$email='http://service.puntomio.com/App_files/cpostactivation_mail.aspx?PathUrl=http://service.puntomio.com' .
			'&nombre=' . urldecode($data['name']) .
			'&suite='. urldecode($data['suite']) .
			'&clave=No_pass&usuario=' . urldecode($data['email']) .
			'&delivery=0&alternate_name='.
			'&sc='.$this->_copa_id.
			'&lang=ESP&box_user_firstname=' . urldecode($data['name']) ;
		}
		return $email;	
    }
    public function send_html_email($html_link,$emailsent,$subject){
    	try{
			$to = $emailsent;		
			$body = file_get_contents($html_link);
			$headers = array('Content-Type: text/html; charset=UTF-8');
			 
			wp_mail( $to, $subject, $body, $headers );
		} catch (Exception $e) {
    		return false;
		}
		return true;
    }
    public function get_session_display_idef(){
    	if(isset($_COOKIE[$this->_login_user_key_identifier.'idef'])) {//User Key
			return $_COOKIE[$this->_login_user_key_identifier.'idef'];
		}
		return '';
    }
    public function get_session_box_id(){
    	if(isset($_COOKIE[$this->_login_box_id_identifier])) {//User Key
			return $_COOKIE[$this->_login_box_id_identifier];
		}
		return '';
    }
    public function get_no_session_action(){
    	$action='<div class="alert alert-danger">'.__('Invalid session','skypostal_apibox').'</div>';
    	if($this->_login_no_sess_red_opt=='redirect')
    	{
    		$action.= '<script>window.location="'.$this->_login_no_sess_url.'";</script>';
    	}
    	return $action;
    }
	public function is_logged_in_simple(){
		if(isset($_COOKIE[$this->_login_user_key_identifier]) && isset($_COOKIE[$this->_login_box_id_identifier]) && is_numeric($_COOKIE[$this->_login_box_id_identifier])){
		if(!empty($_COOKIE[$this->_login_user_key_identifier]) && !empty($_COOKIE[$this->_login_box_id_identifier])) return true;	
		} 
		return false;
	}
	private function _add_login_service_parameters($parameters){
		
		if(isset($_COOKIE[$this->_login_user_key_identifier])) {//User Key
			$parameters['customer_key']=$_COOKIE[$this->_login_user_key_identifier];
		}
		if(isset($_COOKIE[$this->_login_box_id_identifier]) && is_numeric($_COOKIE[$this->_login_box_id_identifier])) {//Box ID
			$parameters['customer_box_id']=$_COOKIE[$this->_login_box_id_identifier];
		}
		return $parameters;
	}
	private function _sp_execute_method($method,$parameters,$headers=NULL,$noverbose=false){
		$executeurl=$this->_url_prod;
		if($this->_api_test_mode) $executeurl=$this->_url_test;
		$executeurl.=$method;		
		$ch = curl_init($executeurl);	 		
		//Adding static variables from module (user_code and app_key):
		$parameters['user_info']=array('user_code'=>$this->_user_code, 'app_key'=>$this->_app_key);		
		//Adding login-service parameters automatically(if any):
		$parameters = $this->_add_login_service_parameters($parameters);		
		$jsonDataEncoded = json_encode($parameters);	 
		
		if($this->_verbose && !$noverbose) echo '<pre>'.$executeurl.'</pre><pre>'.$jsonDataEncoded.'</pre>';
		curl_setopt($ch, CURLOPT_POST, 1);	 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);	 		
		
		if(!is_array($headers)){
			$headers=array();
		}
		
		$headers[]='Content-Type: application/json';

		if($this->_verbose) echo '<pre>HEADERS='.print_r($headers, true).'</pre>';
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 	 		
		$result = curl_exec($ch);		
		curl_close($ch);
		if($this->_verbose) echo '<pre>'.$result.'</pre>';
		return json_decode($result);
	}
	private function _sp_execute_method_stream($method,$data_stream,$parameters,$headers=NULL){
		$executeurl=$this->_url_prod;
		if($this->_api_test_mode) $executeurl=$this->_url_test;
		$executeurl.=$method;		
		$ch = curl_init($executeurl);	 		
		//Adding static variables from module (user_code and app_key):
		$parameters['user_code']=$this->_user_code;
		$parameters['app_key']=$this->_app_key;		
		//Adding login-service parameters automatically(if any):
		$parameters = $this->_add_login_service_parameters($parameters);		
		
		if($this->_verbose) echo '<pre>'.$executeurl.'</pre>';
		curl_setopt($ch, CURLOPT_POST, 1);	 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_stream);	 		
		
		if(!is_array($headers)){
			$headers=array();
		}		
		$headers[]='Content-Type: text/plain';
		foreach($parameters as $pakey=>$paval){
			$headers[]=$pakey.': '.$paval;
		}

		if($this->_verbose) echo '<pre>HEADERS='.print_r($headers, true).'</pre>';
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 	 		
		$result = curl_exec($ch);		
		curl_close($ch);
		if($this->_verbose) echo '<pre>'.$result.'</pre>';
		return json_decode($result);
	}
	
	public function sp_do_login($user,$pass){
		$method = '/service-customer.svc/customer/customer-login';			
		$parameters = array(		
			'customer_email' => $user,
			'customer_password' => $pass
		);	 
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}
	public function sp_geographic_get_countries(){
		$method = '/service-geographic.svc/geographic/geographic-get-countries';			
		$parameters = array();					 
		$result = $this->_sp_execute_method($method,$parameters,NULL,true);		
		$countries=array();
		foreach($result as $key=>$value){
			$countries[$value->country_code] = $value->country_name;
		}
		return $countries;
	}
	public function sp_geographic_get_states($country_code){
		$method = '/service-geographic.svc/geographic/geographic-get-states';			
		$parameters = array(		
			'country_code' => $country_code			
		);	 
		$result = $this->_sp_execute_method($method,$parameters,NULL,true);		
		$states=array();
		$states[0]=esc_html__("Please select",'skypostal_apibox');
		foreach($result as $key=>$value){
			$states[$value->state_code] = $value->state_name;
		}
		return $states;
	}
	public function sp_geographic_get_cities($state_code){
		$method = '/service-geographic.svc/geographic/geographic-get-cities';			
		$parameters = array(		
			'state_code' => $state_code			
		);	 
		$result = $this->_sp_execute_method($method,$parameters,NULL,true);		
		$cities=array();
		$cities[0]=esc_html__("Please select",'skypostal_apibox');
		foreach($result as $key=>$value){
			$cities[$value->city_code] = $value->city_name;
		}
		return $cities;
	}

	public function sp_customer_get_info(){
		$method = '/service-customer.svc/customer/customer-get-info';			
		$parameters = array();	 
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}

	public function sp_customer_registration_virtual($data){
		$method = '/service-customer.svc/customer/customer-registration';					
		
		$date_bt_o=sanitize_text_field($data['date_of_birth']);		
		$date_bt = DateTime::createFromFormat('d/m/Y', $date_bt_o);
		$tax = sanitize_text_field($data['tax_number']);
		if(!is_numeric($tax) || empty($tax)) $tax=0;

		//Validate COPA by URL
		$url = get_site_url();
		$copaid=$this->_copa_id;
		$copa_info = $this->sp_partner_getcopa_id_by_url(array('copa_url'=>$url));
		if(isset($copa_info['copa_id'])){
			if($copa_info['copa_id'] > 0) $copaid=$copa_info['copa_id'];
		}else
			$copaid=$this->_copa_id;

		$parameters = array(
			"language_code"=>sanitize_text_field('ESP'),
			"copa_id"=>$copaid,
			"customer_first_name"=>sanitize_text_field($data['first_name']),
			"customer_last_name"=>sanitize_text_field($data['last_name']),
			"customer_identity_number"=>sanitize_text_field($data['address_id_number']),
			"customer_tax_number"=>$tax,			
			"customer_birth_date"=>$date_bt->format("Y-m-d"),
			"customer_email"=>sanitize_text_field($data['email']),
			"customer_password"=>sanitize_text_field($data['password']),
			"customer_gender"=>sanitize_text_field($data['gender']),
			"customer_address"=>array(		
					array(			
					"city_code"=>sanitize_text_field($data['address_city']),
					"locality_town"=>sanitize_text_field($data['']),
					"neighborhood"=>sanitize_text_field($data['address_region']),			
					"zip_code"=>sanitize_text_field($data['address_postal_code']),
					"address_01"=>sanitize_text_field($data['address_address'])
					)
			),			
			"customer_phone"=>array(		
			array(		
				"phone_area_code"=>sanitize_text_field($data['account_phone_country']),
				"phone_number"=>sanitize_text_field($data['account_phone_number']),
				"phone_extension"=>sanitize_text_field($data['account_phone_ext']),
				"phone_type"=>sanitize_text_field(1)
			),
			array(		
				"phone_area_code"=>sanitize_text_field($data['account_cellphone_country']),
				"phone_number"=>sanitize_text_field($data['account_cellphone_number']),
				"phone_extension"=>sanitize_text_field($data['account_cellphone_ext']),
				"phone_type"=>sanitize_text_field(2)
			),
			)
		);
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}

	public function sp_customer_registration_default($data){
		$method = '/service-customer.svc/customer/customer-register-info';					
		
		$date_bt_o=sanitize_text_field($data['date_of_birth']);		
		$date_bt = DateTime::createFromFormat('d/m/Y', $date_bt_o);

		$parameters = array(
			"language_code"=>sanitize_text_field('ESP'),
			"copa_id"=>$this->_copa_id,
			"customer_first_name"=>sanitize_text_field($data['first_name']),
			"customer_last_name"=>sanitize_text_field($data['last_name']),
			"customer_identity_number"=>sanitize_text_field($data['address_id_number']),
			"customer_birth_date"=>$date_bt->format("Y-m-d"),
			"customer_email"=>sanitize_text_field($data['email']),
			"customer_password"=>sanitize_text_field($data['password']),
			"customer_gender"=>sanitize_text_field($data['gender']),
			"customer_address"=>array(		
					array(			
					"city_code"=>sanitize_text_field($data['address_city']),
					"locality_town"=>sanitize_text_field($data['']),
					"neighborhood"=>sanitize_text_field($data['address_region']),			
					"zip_code"=>sanitize_text_field($data['address_postal_code']),
					"address_01"=>sanitize_text_field($data['address_address'])
					)
			),			
			"customer_phone"=>array(		
			array(		
				"phone_area_code"=>sanitize_text_field($data['account_phone_country']),
				"phone_number"=>sanitize_text_field($data['account_phone_number']),
				"phone_extension"=>sanitize_text_field($data['account_phone_ext']),
				"phone_type"=>sanitize_text_field(1)
			),
			array(		
				"phone_area_code"=>sanitize_text_field($data['account_cellphone_country']),
				"phone_number"=>sanitize_text_field($data['account_cellphone_number']),
				"phone_extension"=>sanitize_text_field($data['account_cellphone_ext']),
				"phone_type"=>sanitize_text_field(2)
			),
			)
		);
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}
	
	public function sp_customer_update_personal_info($data){
		$method = '/service-customer.svc/customer/customer-update-personal-info';					
		
		$date_bt_o=sanitize_text_field($data['date_of_birth']);		
		$date_bt = DateTime::createFromFormat('d/m/Y', $date_bt_o);

		$parameters = array(			
			"customer_first_name"=>sanitize_text_field($data['first_name']),
			"customer_last_name"=>sanitize_text_field($data['last_name']),
			"customer_identity_number"=>sanitize_text_field($data['address_id_number']),
			"customer_birth_date"=>$date_bt->format("Y-m-d"),			
			"customer_gender"=>sanitize_text_field($data['gender'])			
		);
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}

	public function sp_customer_activate_box($data){
		$method = '/service-customer.svc/customer/customer-activate-box';					
		
		$date_month=sanitize_text_field($data['cc_expiration_month']);		
		$date_year=sanitize_text_field($data['cc_expiration_year']);		

		$date_bt =$date_year.'-'.$date_month.'-01'; //DateTime::createFromFormat('d/m/Y', $date_bt_o);

		$parameters = array(			
			"customer_credit_card"=>array(
				array(
					"cc_type_name"=>sanitize_text_field($data['cc_type_name']),
					"cc_holder_name"=>sanitize_text_field($data['cc_holder_name']),
					"cc_number"=>sanitize_text_field($data['cc_number']),
					"cc_expiration_date"=>$date_bt,			
					"cc_security_code"=>sanitize_text_field($data['cc_security_code'])			
				)
			)
		);
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}

	public function sp_customer_update_address_info($data){
		$method = '/service-customer.svc/customer/customer-update-address-info';					

		$mainarea=(empty(($data['account_phone_country']))?'0':$data['account_phone_country']);
		$mainnumber=(empty(($data['account_phone_number']))?'0':$data['account_phone_number']);;
		$mainextension=(empty(($data['account_phone_ext']))?'0':$data['account_phone_ext']);;
		$parameters = array(
			"customer_address"=>array(		
					array(			
					"city_code"=>sanitize_text_field($data['address_city']),
					"locality_town"=>sanitize_text_field($data['']),
					"neighborhood"=>sanitize_text_field($data['address_region']),			
					"zip_code"=>sanitize_text_field($data['address_postal_code']),
					"address_01"=>sanitize_text_field($data['address_address']),
					"phone_area"=>$mainarea,
      				"phone_number"=>$mainnumber,
      				"phone_extension"=>$mainextension
					)
			),			
			"customer_phone"=>array(		
			array(		
				"phone_area_code"=>sanitize_text_field($data['account_phone_country']),
				"phone_number"=>sanitize_text_field($data['account_phone_number']),
				"phone_extension"=>sanitize_text_field($data['account_phone_ext']),
				"phone_type"=>sanitize_text_field(1)
			),
			array(		
				"phone_area_code"=>sanitize_text_field($data['account_cellphone_country']),
				"phone_number"=>sanitize_text_field($data['account_cellphone_number']),
				"phone_extension"=>sanitize_text_field($data['account_cellphone_ext']),
				"phone_type"=>sanitize_text_field(2)
			),
			)
		);
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}

	public function sp_customer_get_shipments($data){
		$method = '/service-customer.svc/customer/customer-get-shipments';					

		$parameters = array(
			"start_date"=>sanitize_text_field($data['start_date']),
			"end_date"=>sanitize_text_field($data['end_date'])						
		);
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}

	public function sp_customer_get_shipment_info($data){
		$method = '/service-shipment.svc/shipment/get-shipment-info';					

		$parameters = array(
			"trck_nmr_fol"=>sanitize_text_field($data['trck_nmr_fol']),
			"language_code"=>""							
		);
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}

	public function sp_customer_update_email($data){
		$method = '/service-customer.svc/customer/customer-update-email';					
		$parameters = array(			
			"customer_current_email"=>sanitize_text_field($data['customer_current_email']),
			"customer_new_email"=>sanitize_text_field($data['customer_email']),			
		);
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}

	public function sp_customer_update_password($data){
		$method = '/service-customer.svc/customer/customer-update-password';					
		$parameters = array(			
			"customer_box_id"=>sanitize_text_field($data['customer_box_id']),
			"customer_key"=>sanitize_text_field($data['customer_key']),			
			"customer_email"=>sanitize_text_field($data['customer_email']),	
			"customer_new_password"=>sanitize_text_field($data['customer_new_password'])
		);		
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}

	public function sp_partner_customer_get_info($data){
		$method = '/service-partner.svc/partner/customer-get-info';					
		$parameters = array(			
			"customer_email"=>sanitize_text_field($data['customer_email'])			
		);
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}
	public function sp_customer_upload_invoice_data($data){
		$method = '/service-customer.svc/customer/customer-upload-invoice-data';					
		$parameters = array(			
			"trck_nmr_fol"=>sanitize_text_field($data['trck_nmr_fol']),
			"invoice_file_name"=>sanitize_text_field($data['invoice_file_name']),			
		);
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}

	public function sp_customer_upload_invoice_file($data_stream,$data){
		$method = '/service-customer.svc/customer/customer-upload-invoice-file';					
		$parameters = array(			
			"invoice_file_guid"=>sanitize_text_field($data['invoice_file_guid']),			
		);
		$result = $this->_sp_execute_method_stream($method,$data_stream,$parameters);		
		return $result;
	}

	public function sp_partner_getcopa_id_by_url($data){
		$return_data=array();
		$method = '/service-partner.svc/partner/get-copa-by-url';					
		$parameters = array(			
			"copa_url"=>sanitize_text_field($data['copa_url']),			
		);
		$result = $this->_sp_execute_method($method,$parameters);

		$selectedcopa=0;
		$copa_id_default=0;
		if($result)	{
			foreach($result as $copa){
				if($copa->_verify){
					if($copa_id_default<=0) $copa_id_default=$copa->copa_id_default;
					if($selectedcopa<=0) $selectedcopa=$copa->copa_id;

					if($copa->copa_domain_group_default==1) $selectedcopa=$copa->copa_id;
				}
			}
		}
		if($copa_id_default>0 || $selectedcopa>0){
			$return_data=array('copa_id'=>$selectedcopa, 'copa_id_default'=>$copa_id_default);
		}
		return $return_data;
	}
	
	public function sp_shipment_get_ship_rate($data){
		$method = '/service-customer.svc/customer/TEMP';					
		$parameters = array(			
			"trck_nmr_fol"=>sanitize_text_field($data['trck_nmr_fol']),
			"invoice_file_name"=>sanitize_text_field($data['invoice_file_name']),			
		);
		$result = $this->_sp_execute_method($method,$parameters);		
		return $result;
	}
	
	
	
}

?>