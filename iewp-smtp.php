<?php
if ( ! defined( 'WPINC' ) ) { die('Direct access prohibited!'); }
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
		$phpmailer->SMTPSecure = 'tls';
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
 * Enable debugging of SMTP
 */
function iewp_configure_smtp_debug( $phpmailer )
{
	$phpmailer->SMTPDebug = 3;
}

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
		'iewp-smtp-options', // id
		'', // title
		'iewp_smtp_options', // callback
		'iewp_smtp_options' // page
		);

	/**
	 * Add the settings fields
	 */
	add_settings_field(
		'iewp-smtp-host', // id
		'Hostname', // title/label
		'iewp_smtp_host', // callback
		'iewp_smtp_options', // page
		'iewp-smtp-options' // settings section
		);

	add_settings_field(
		'iewp-smtp-port', // id
		'Port Number', // title/label
		'iewp_smtp_port', // callback
		'iewp_smtp_options', // page
		'iewp-smtp-options' // settings section
		);

	add_settings_field(
		'iewp-smtp-username', // id
		'Username', // title/label
		'iewp_smtp_username', // callback
		'iewp_smtp_options', // page
		'iewp-smtp-options' // settings section
		);

	add_settings_field(
		'iewp-smtp-password', // id
		'Password', // title/label
		'iewp_smtp_password', // callback
		'iewp_smtp_options', // page
		'iewp-smtp-options' // settings section
		);

	add_settings_field(
		'iewp-smtp-from-email', // id
		'From email', // title/label
		'iewp_smtp_from_email', // callback
		'iewp_smtp_options', // page
		'iewp-smtp-options' // settings section
		);

	add_settings_field(
		'iewp-smtp-from-name', // id
		'From name', // title/label
		'iewp_smtp_from_name', // callback
		'iewp_smtp_options', // page
		'iewp-smtp-options' // settings section
		);
}

/**
 * Produce the form elements/input fields
 */
function iewp_smtp_host()
{
	$setting = get_option( 'iewp_smtp_host' );
	echo '<input type="text" class="regular-text" name="iewp_smtp_host" value="'.$setting.'" placeholder="mail.example.com">';
	echo '<p class="description">Hostname of the SMTP server.</p>';
}

function iewp_smtp_port()
{
	$setting = get_option( 'iewp_smtp_port' );
	echo '<input type="text" class="regular-text" name="iewp_smtp_port" value="'.$setting.'" placeholder="587">';
	echo '<p class="description">SMTP port no.</p>';
}

function iewp_smtp_username()
{
	$setting = get_option( 'iewp_smtp_username' );
	echo '<input type="text" class="regular-text" name="iewp_smtp_username" value="'.$setting.'" placeholder="user@example.com">';
	echo '<p class="description">SMTP username i.e. user@example.com.</p>';
}

function iewp_smtp_password()
{
	$setting = get_option( 'iewp_smtp_password' );
	echo '<input type="password" class="regular-text" name="iewp_smtp_password" value="'.$setting.'" placeholder="password">';
	echo '<p class="description">SMTP password.</p>';
}

function iewp_smtp_from_email()
{
	$setting = get_option( 'iewp_smtp_from_email' );
	echo '<input type="text" class="regular-text" name="iewp_smtp_from_email" value="'.$setting.'" placeholder="user@example.com">';
	echo '<p class="description">Emails sent from address.</p>';
}

function iewp_smtp_from_name()
{
	$setting = get_option( 'iewp_smtp_from_name' );
	echo '<input type="text" class="regular-text" name="iewp_smtp_from_name" value="'.$setting.'" placeholder="Example Ltd">';
	echo '<p class="description">Emails sent from name.</p>';
}

/**
 * Call back function for settings section. Do nothing.
 */
function iewp_smtp_options()
{
	return;
}

/**
 * Enqueue additional JavaScript
 */
function iewp_smtp_enqueue_scripts( $hook )
{
	if( 'settings_page_options-iewp-smtp' != $hook )
	{
		return;
	}
	wp_register_script( 'iewp_smtp_js', plugin_dir_url( __FILE__ ) . 'assets/iewp_smtp.js', array('jquery'), '0.0.1', true );
	wp_enqueue_script( 'iewp_smtp_js' );

	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'iewp_smtp_enqueue_scripts' );

/**
 * iewp_google_analytics callback function.
 */
function iewp_smtp_callback()
{
	?>

		<?php
			$data = $_REQUEST;
			if( isset( $data['email'] ) )
			{
				echo '<h1>Test Email Debug</h1>';
				echo '<hr>';
				echo '<pre><code>';
				add_action( 'phpmailer_init', 'iewp_configure_smtp_debug' );
				wp_mail( $data['email'],
						 'IEWP SMTP Test Email',
						 "This is a test message, there is nothing to see here, please move along.\n\n" .
						 "--\nIEWP SMTP Test Email\nhttps://github.com/corenominal/iewp-smtp"
					);
				echo '</code></pre>';
				echo '<hr>';
			}
		?>
		<div class="wrap">
			<h1>SMTP Settings</h1>

			<p>Enter your SMTP credentials below to enable.</p>

			<hr>

			<form method="POST" action="options.php">

				<?php settings_fields( 'iewp_smtp_group' ); ?>
				<?php do_settings_sections( 'iewp_smtp_options' ); ?>
				<?php submit_button(); ?>

			</form>

			<hr>

			<h1>Send Test Email</h1>
			<form id="email-test" method="GET" action="<?php echo site_url( 'wp-admin/options-general.php' ); ?>">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							Recipient
						</th>
						<td>
							<?php
							$email = '';
							if( isset($data['email']) )
							{
								$email = $data['email'];
							}
							?>
							<input type="text" class="regular-text" id="recipient-email" name="email" value="<?php echo $email ?>" placeholder="test@example.com">
							<p class="description">Email address to send test to.</p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="hidden" name="page" value="options-iewp-smtp">
				<input type="submit" value="Send Test Email" class="button " id="test-email-submit" name="submit"></p>
			</form>

		</div>

	<?php
}
