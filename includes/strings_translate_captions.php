<?php 
function spapibox_translate_string($code,$text,&$target,$translate=true){
	
	$target =($translate ? str_replace($code, esc_html__($text,'skypostal_apibox'), $target) : $target = str_replace($code, $text, $target));
	return true;
}
function spapibox_generate_captions(){

	$strings= array();
	$strings["@@title_str_email"]="Email";
	$strings["@@title_str_password"]="Password";
	$strings["@@title_str_Login"]="Login";
	$strings["@@title_str_forgot_password"]="Forgot Password";		
	$strings["@@title_account_information"]="Account Information";
	$strings["@@title_confirm_password "]="Confirm Password";
	$strings["@@tittle_date_of_birth"]="Date of Birth";
	$strings["@@title_gender"]="Gender";
	$strings["@@title_male"]="Male";
	$strings["@@title_female"]="Female";
	$strings["@@title_delivery_address"]="Delivery Address";
	$strings["@@title_country"]="Country";
	$strings["@@title_state_province "]="State / Province";
	$strings["@@title_first_name"]="First Name";
	$strings["@@title_last_name"]="Last Name";
	$strings["@@title_city"]="City";
	$strings["@@title_del_address "]="Address";
	$strings["@@title_region"]="Region";
	$strings["@@title_postal_code"]="Postal Code";
	$strings["@@title_telephone"]="Telephone";
	$strings["@@title_phone_country_code"]="Country code";
	$strings["@@title_phones_mumber"]="Number";
	$strings["@@title_phones_extension"]="Extension";
	$strings["@@title_cell_phone"]="Cell Phone";
	$strings["@@title_optional"]="Optional";
	$strings["@@title_Id_number"]="ID Number";
	$strings["@@text_what_is_this"]="What is this?";
	$strings["@@id_number_description"]="Identification Number is a personal identification document from your country such as a passport or national identification card. It is used to validate your identity at the time of customs clearance in country.";
	$strings["@@title_newsletter_check"]="Yes, I would like to receive specials offers and important information regarding my address.";
	$strings["@@title_terms_and_conditions"]="I accept the terms and conditions of ". get_bloginfo( 'name' ) .".";
	$strings["@@tittle_view"]="View";
	$strings["@@field_is_required"]="This field is required.";		
	return $strings;
}
function spapibox_translate_captions($content){
	$strs=spapibox_generate_captions();
	foreach($strs as $key=>$caption){
		spapibox_translate_string($key, $caption, $content);
	}
	return $content;
}

?>