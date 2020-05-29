<?php

/**
 * Skypostal Events handling
 *
 * @package Skypostal_apibox
 * @subpackage Admin
 * @since 1.0.0
 */

/**
 * Handles the plugin events to include custom logic
 * 
 */

function spapibox_events_after_virtual_registration_success($data){
	do_action( 'spapibox_after_virtual_registration_success', $data );
}

function sapibox_events_after_invoice_uploaded_success($data){
	do_action( 'spapibox_after_invoice_uploaded_success', $data );
}

 ?>