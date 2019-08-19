<?php
/**
 * Front End Functions
 *
 * @package     EPL-IMPORTER-ADD-ON
 * @subpackage  Functions/Global
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get EPL meta fields
 *
 * @return mixed|void
 * @since 2.0
 */
function epl_wpimport_get_meta_fields() {

	if ( function_exists( 'epl_get_meta_field_label' ) ) {
		$epl_ai_meta_fields = epl_get_meta_boxes();
	} else {
		include 'meta-boxes-compat.php';
		$epl_ai_meta_fields = epl_allimport_get_meta_fields();
	}

	return $epl_ai_meta_fields;
}

/**
 * Custom EPL settings in import settings
 *
 * @since  2.0
 * @param  array  $entry Import entry.
 * @param  string $post  Post ID.
 */
function epl_wpimport_pmxi_reimport( $entry, $post ) {

	if ( ! in_array( $entry, epl_get_core_post_types() ) ) {
		return;
	}

	$epl_ai_meta_fields = epl_wpimport_get_meta_fields();
	$all_existing_epl   = array();

	if ( ! empty( $epl_ai_meta_fields ) ) {
		foreach ( $epl_ai_meta_fields as $epl_meta_box ) {
			if ( ! empty( $epl_meta_box['groups'] ) ) {
				foreach ( $epl_meta_box['groups'] as $group ) {

							$fields = $group['fields'];
							$fields = array_filter( $fields );
					if ( ! empty( $fields ) ) {
						foreach ( $fields as $field ) {

							$field_name = '[' . $field['name'] . '] ' . $field['label'];

							if ( ! in_array( $field_name, $all_existing_epl ) ) {
								$all_existing_epl[] = $field_name;
							}
						}
					}
				}
			}
		}
	}
	$update_epl_logic = $post['update_epl_logic'];
	$update_epl_logic = $update_epl_logic == '' ? 'full_update' : $update_epl_logic;

	?>

	<div class="input">
		<input type="hidden" name="epl_list" value="0" />
		<input type="hidden" name="is_update_epl" value="0" />
		<input type="checkbox" id="is_update_epl_<?php echo $entry; ?>" name="is_update_epl" value="1" <?php echo $post['is_update_epl'] ? 'checked="checked"' : ''; ?>  class="switcher"/>
		<label for="is_update_epl_<?php echo $entry; ?>"><?php _e( 'Easy Property Listings Custom Fields', 'epl-wpimport' ); ?></label>
		<div class="switcher-target-is_update_epl_<?php echo $entry; ?>" style="padding-left:17px;">
			<div class="input">
				<input type="radio" id="update_epl_logic_full_update_<?php echo $entry; ?>" name="update_epl_logic" value="full_update" <?php echo ( 'full_update' == $update_epl_logic ) ? 'checked="checked"' : ''; ?> class="switcher"/>
				<label for="update_epl_logic_full_update_<?php echo $entry; ?>"><?php _e( 'Update all EPL fields', 'epl-wpimport' ); ?></label>
			</div>

			<div class="input">
				<input type="radio" id="update_epl_logic_only_<?php echo $entry; ?>" name="update_epl_logic" value="only" <?php echo ( 'only' == $post['update_epl_logic'] ) ? 'checked="checked"' : ''; ?> class="switcher"/>
				<label for="update_epl_logic_only_<?php echo $entry; ?>"><?php _e( 'Update only these EPL fields, leave the rest alone', 'epl-wpimport' ); ?></label>
				<div class="switcher-target-update_epl_logic_only_<?php echo $entry; ?> pmxi_choosen" style="padding-left:17px;">

					<span class="hidden choosen_values">
					<?php
					if ( ! empty( $all_existing_epl ) ) {
						echo implode( ',', $all_existing_epl );}
					?>
					</span>
					<input class="choosen_input" value="
					<?php
					if ( ! empty( $post['epl_list'] ) and 'only' == $post['update_epl_logic'] ) {
						echo implode( ',', $post['epl_list'] );}
					?>
					" type="hidden" name="epl_only_list"/>
				</div>
			</div>
			<div class="input">
				<input type="radio" id="update_epl_logic_all_except_<?php echo $entry; ?>" name="update_epl_logic" value="all_except" <?php echo ( 'all_except' == $post['update_epl_logic'] ) ? 'checked="checked"' : ''; ?> class="switcher"/>
				<label for="update_epl_logic_all_except_<?php echo $entry; ?>"><?php _e( 'Leave these EPL fields alone, update all other fields', 'epl-wpimport' ); ?></label>
				<div class="switcher-target-update_epl_logic_all_except_<?php echo $entry; ?> pmxi_choosen" style="padding-left:17px;">

					<span class="hidden choosen_values">
					<?php
					if ( ! empty( $all_existing_epl ) ) {
						echo implode( ',', $all_existing_epl );}
					?>
					</span>
					<input class="choosen_input" value="
					<?php
					if ( ! empty( $post['epl_list'] ) and 'all_except' == $post['update_epl_logic'] ) {
						echo implode( ',', $post['epl_list'] );}
					?>
					" type="hidden" name="epl_except_list"/>
				</div>
			</div>
		</div>
	</div> 
	<?php
}
add_action( 'pmxi_reimport', 'epl_wpimport_pmxi_reimport', 10, 2 );

/**
 * Filter to check which meta fields will be updated
 *
 * @param string $field_to_update Meta field name.
 * @param string $post_type Post type name.
 * @param array  $options Options.
 * @param string $m_key Meta key.
 *
 * @return bool|mixed|string|void
 * @since 2.0
 */
function epl_wpimport_pmxi_custom_field_to_update( $field_to_update, $post_type, $options, $m_key ) {

	global $epl_wpimport;

	if ( in_array( $m_key, epl_wpimport_skip_fields() ) ) {

		/* Translators: %s is the meta field name. */
        $epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . sprintf( __( 'Skipping field : %s', 'epl-wpimport' ), $m_key ) );
		return false;
	}

	if ( false === $field_to_update ) {
		return $field_to_update;
	}

	return pmai_is_epl_update_allowed( $m_key, $options );
}
add_filter( 'pmxi_custom_field_to_update', 'epl_wpimport_pmxi_custom_field_to_update', 10, 4 );

/**
 * Filter to check which meta fields will get deleted
 *
 * @param string $field_to_delete Meta field name to delete.
 * @param string $pid Importer element ID.
 * @param string $post_type Post type name.
 * @param array  $options Options.
 * @param string $cur_meta_key Meta key name.
 *
 * @return bool|mixed|string|void
 * @since 2.0
 */
function epl_wpimport_pmxi_custom_field_to_delete( $field_to_delete, $pid, $post_type, $options, $cur_meta_key ) {

	// Dont let wp all import pro delete image mod date.
	if ( 'property_images_mod_date' === $cur_meta_key || 'property_images_mod_date_old' === $cur_meta_key ) {
		return false;
	}

	$live_import = function_exists( 'epl_get_option' ) ? epl_get_option( 'epl_wpimport_skip_update' ) : 'off';

	if ( 'on' === $live_import && in_array( $cur_meta_key, epl_wpimport_skip_fields() ) ) {

		return false;
	}

	if ( false === $field_to_delete ) {
		return $field_to_delete;
	}

	return pmai_is_epl_update_allowed( $cur_meta_key, $options );
}
add_filter( 'pmxi_custom_field_to_delete', 'epl_wpimport_pmxi_custom_field_to_delete', 10, 5 );

/**
 * Save custom EPL settings for WP ALL Import
 *
 * @since 2.0
 * @param  string $post Post ID.
 * @return object
 */
function epl_wpimport_pmxi_save_options( $post ) {

	if ( PMXI_Plugin::getInstance()->getAdminCurrentScreen()->action == 'options' ) {

		$post['is_update_epl']    = isset( $_POST['is_update_epl'] ) ? $_POST['is_update_epl'] : '';
		$post['update_epl_logic'] = isset( $_POST['update_epl_logic'] ) ? $_POST['update_epl_logic'] : '';
		$post['epl_only_list']    = isset( $_POST['epl_only_list'] ) ? $_POST['epl_only_list'] : '';
		$post['epl_except_list']  = isset( $_POST['epl_except_list'] ) ? $_POST['epl_except_list'] : '';
		$post['epl_list']         = isset( $_POST['epl_list'] ) ? $_POST['epl_list'] : '';

		if ( 'only' === $post['update_epl_logic'] ) {
			$post['epl_list'] = explode( ',', $post['epl_only_list'] );
		} elseif ( 'all_except' === $post['update_epl_logic'] ) {
			$post['epl_list'] = explode( ',', $post['epl_except_list'] );
		}
	}
	return $post;
}
add_filter( 'pmxi_save_options', 'epl_wpimport_pmxi_save_options' );

/**
 * Check if meta field update is allowed on not
 *
 * @param string $cur_meta_key Meta key name.
 * @param array  $options Options.
 *
 * @return mixed|void
 * @since 2.0
 */
function pmai_is_epl_update_allowed( $cur_meta_key, $options ) {

	if ( 'yes' === $options['update_all_data'] ) {

		return apply_filters( 'epl_wpimport_is_epl_update_allowed', true, $cur_meta_key, $options );
	}

	if ( 'no' === $options['update_all_data'] && $options['is_update_epl'] && 'full_update' === $options['update_epl_logic'] ) {

		return apply_filters( 'epl_wpimport_is_epl_update_allowed', true, $cur_meta_key, $options );
	}

	if ( 'no' === $options['update_all_data'] && $options['is_update_epl'] && 'only' === $options['update_epl_logic'] ) {

		if ( ! empty( $options['epl_list'] ) && is_array( $options['epl_list'] ) ) {

			foreach ( $options['epl_list'] as $key => $epl_field ) {

				$parts_temp = explode( ' ', $epl_field );
				$field_name = trim( array_shift( $parts_temp ), '[]' );

				if ( $cur_meta_key == $field_name ) {
					return apply_filters( 'pmai_is_epl_update_allowed', true, $cur_meta_key, $options );
						break;
				}
			}
			return apply_filters( 'pmai_is_epl_update_allowed', false, $cur_meta_key, $options );
		}
	}

	// Leave these epl alone, update all other epl.
	if ( 'no' === $options['update_all_data'] && $options['is_update_epl'] && 'all_except' === $options['update_epl_logic'] ) {

		if ( ! empty( $options['epl_list'] ) && is_array( $options['epl_list'] ) ) {
			foreach ( $options['epl_list'] as $key => $epl_field ) {

				$parts_temp = explode( ' ', $epl_field );
				$field_name = trim( array_shift( $parts_temp ), '[]' );

				if ( $cur_meta_key == $field_name ) {
					return apply_filters( 'pmai_is_epl_update_allowed', false, $cur_meta_key, $options );
						break;
				} else {
					return apply_filters( 'pmai_is_epl_update_allowed', true, $cur_meta_key, $options );
				}
			}
		}
	}

	return apply_filters( 'pmai_is_epl_update_allowed', false, $cur_meta_key, $options );
}

/**
 * Don't update these fields
 *
 * @return mixed|void
 * @since 2.0
 */
function epl_wpimport_skip_fields() {

	$fields = array(
		'property_featured',
		'property_year_built',
		'property_owner',
	);

	return apply_filters( 'epl_wpimport_skip_fields', $fields );
}
