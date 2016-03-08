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
 * Plugin settings link
 */
function iewp_smtp_action_links( $actions, $plugin_file ) 
{
	static $plugin;

	if (!isset($plugin))
		$plugin = plugin_basename(__FILE__);
	if ($plugin == $plugin_file)
	{
		$settings = array('settings' => '<a href="options-general.php?page=options-iewp-smtp">' . __('Settings', 'General') . '</a>');
	
		$actions = array_merge($settings, $actions);	
	}
	return $actions;
}
add_filter( 'plugin_action_links', 'iewp_smtp_action_links', 10, 5 );

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

/**
 * Add submenu item to the default WordPress "Settings" menu
 */
function iewp_smtp()
{
	add_submenu_page( 
		'options-general.php', // parent slug to attach to
		'SMTP', // page title
		'SMTP', // menu title
		'manage_options', // capability
		'options-iewp-smtp', // slug
		'iewp_smtp_callback' // callback function
		);

	// Activate custom settings
	add_action( 'admin_init', 'iewp_smtp_register' );
}
add_action( 'admin_menu', 'iewp_smtp' );

/**
 * Register custom settings
 */ 
function iewp_smtp_register()
{
	/**
	 * Register the settings fields
	 */
	register_setting(
		'iewp_smtp_group', // option group
		'iewp_smtp_host' // option name
		);

	register_setting(
		'iewp_smtp_group', // option group
		'iewp_smtp_port' // option name
		);

	register_setting(
		'iewp_smtp_group', // option group
		'iewp_smtp_username' // option name
		);

	register_setting(
		'iewp_smtp_group', // option group
		'iewp_smtp_password' // option name
		);

	register_setting(
		'iewp_smtp_group', // option group
		'iewp_smtp_from_email' // option name
		);

	register_setting(
		'iewp_smtp_group', // option group
		'iewp_smtp_from_name' // option name
		);
	
	/**
	 * Create the settings section for this group of settings
	 */
	add_settings_section(
		'iewp-smtp', // id
		'SMTP Options', // title
		'iewp_smtp_section', // callback
		'iewp_smtp' // page
		);
	
	/**
	 * Add the settings fields
	 */
	add_settings_field(
		'iewp-smtp-host', // id
		'Twitter Account', // title/label
		'iewp_smtp_host', // callback
		'iewp_smtp', // page
		'iewp-smtp' // settings section 
		);

	add_settings_field(
		'iewp-smtp-port', // id
		'Enable Twitter Cards', // title/label
		'iewp_smtp_port', // callback
		'iewp_smtp', // page
		'iewp-smtp' // settings section 
		);

	add_settings_field(
		'iewp-smtp-username', // id
		'Enable Open Graph', // title/label
		'iewp_smtp_username', // callback
		'iewp_smtp', // page
		'iewp-smtp' // settings section 
		);

	add_settings_field(
		'iewp-smtp-password', // id
		'Default Open Graph Image', // title/label
		'iewp_smtp_password', // callback
		'iewp_smtp', // page
		'iewp-smtp' // settings section 
		);

	add_settings_field(
		'iewp-smtp-from-email', // id
		'Default Open Graph Image', // title/label
		'iewp_smtp_from_email', // callback
		'iewp_smtp', // page
		'iewp-smtp' // settings section 
		);

	add_settings_field(
		'iewp-smtp-from-name', // id
		'Default Open Graph Image', // title/label
		'iewp_smtp_from_name', // callback
		'iewp_smtp', // page
		'iewp-smtp' // settings section 
		);

}