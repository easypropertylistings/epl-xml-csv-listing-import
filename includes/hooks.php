<?php
/**
 * Settings
 *
 * @package     EPL-IMPORTER-ADD-ON
 * @subpackage  Functions/Admin
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display options configured from admin panel
 *
 * @param null|array $epl_fields Custom fields array.
 *
 * @return null|array
 * @since 1.0.0
 */
function epl_wpimport_extensions_options_filter( $epl_fields = null ) {
	$fields        = array();
	$epl_of_fields = array(
		'label' => __( 'WP All Import Add-On', 'epl-wpimport' ),
	);

	$intro = '<p>' . __( '<strong>Getting Started</strong>: Once you have configured your imports using WP All Import, enable record skipping for fast efficient listing import.', 'epl-wpimport' ) . '</p>';

	$intro .= '<p>' . __(
		'When this setting is enabled and the import is run, the listings unique modified time is compared to the value in the imported data.
		If the data is newer then the value in WordPress the listing will update.
		This also performs a similar check with the modified image time allowing the data to update and not the images, greatly improving import performance.', 'epl-wpimport'
	) . '</p>';

	$intro .= '<p>' . sprintf(
		/* Translators: %1$s and %2$s are links. */
		__( 'Visit the <a href="%1$s">codex</a> for detailed instructions.', 'epl-wpimport' ),
		esc_url( 'https://codex.easypropertylistings.com.au/category/343-wp-all-import-add-on' )
	) . '</p>';

	$fields[] = array(
		'label'  => __( 'Settings', 'epl-wpimport' ),
		'intro'  => '<h3 style="margin-top:0;">' . __( 'Importer Settings.', 'epl-wpimport' ) . '</h3>',
		'fields' => array(

			array(
				'name'    => 'epl_wpimport_help',
				'type'    => 'help',
				'content' => '<p style="margin-top:0">' . $intro . '</p><hr>',
			),

			array(
				'name'    => 'epl_wpimport_skip_update',
				'label'   => __( 'Activate once initial import is set', 'epl-wpimport' ),
				'type'    => 'radio',
				'opts'    => array(
					'on'  => __( 'Enable (Skip record import based on modified time)', 'epl-wpimport' ),
					'off' => __( 'Disable (Operates as per WP All Import Pro default)', 'epl-wpimport' ),
				),
				'default' => 'off',
			),
		),
	);

	$epl_of_fields['fields']    = $fields;
	$epl_fields['epl_wpimport'] = $epl_of_fields;
	return $epl_fields;
}
add_filter( 'epl_extensions_options_filter_new', 'epl_wpimport_extensions_options_filter', 10, 1 );
