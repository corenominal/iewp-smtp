<?php
/**
 * Plugin Name: IEWP SMTP
 * Plugin URI: https://github.com/corenominal/iewp-smtp
 * Description: This WordPress plugin enables SMTP mail connections.
 * Author: Philip Newborough
 * Version: 0.0.1
 * Author URI: https://corenominal.org
 */

/**
 * Insert empty values on plugin activation
 */
function iewp_smtp_activate()
{
    update_option( 'iewp_smtp_host', '' );
    update_option( 'iewp_smtp_port', '587' );
    update_option( 'iewp_smtp_username', '' );
    update_option( 'iewp_smtp_password', '' );
    update_option( 'iewp_smtp_from_email', '' );
    update_option( 'iewp_smtp_from_name', '' );
}
register_activation_hook( __FILE__, 'iewp_smtp_activate' );

/**
 * Configure the PHP Mailer
 */
function iewp_configure_smtp( $phpmailer )
{
	/**
	 * Test we have values
	 */
	if( get_option( 'iewp_smtp_host' ) != '' && 
		get_option( 'iewp_smtp_port' ) != '' && 
		get_option( 'iewp_smtp_username' ) != '' && 
		get_option( 'iewp_smtp_password' ) != '' && 
		get_option( 'iewp_smtp_from_email' ) != '' && 
		get_option( 'iewp_smtp_from_name' ) != ''
		)
	{
		$phpmailer->isSMTP();
		$phpmailer->Host = get_option( 'iewp_smtp_host' );
		$phpmailer->SMTPAuth = true;
		$phpmailer->Username = get_option( 'iewp_smtp_username' );
		$phpmailer->Password = get_option( 'iewp_smtp_password' );
		$phpmailer->Port = 587;
		$phpmailer->setFrom( get_option( 'iewp_smtp_from_email' ), get_option( 'iewp_smtp_from_name' ) );
		$phpmailer->addReplyTo( get_option( 'iewp_smtp_from_email' ), get_option( 'iewp_smtp_from_name' ) );
		/**
		 * Fix PHPmailer cryto warning on PHP 5.6+
		 * See: http://stackoverflow.com/a/32047219
		 */
		if ( version_compare( phpversion(), '5.6.0', '>' ) )
		{
			$phpmailer->SMTPOptions = array(
				'ssl' => array(
				    'verify_peer' => false,
				    'verify_peer_name' => false,
				    'allow_self_signed' => true
				)
			);
		}
	}
}
add_action( 'phpmailer_init', 'iewp_configure_smtp'  );
