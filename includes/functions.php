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

// phpcs:disable WordPress.Security.NonceVerification

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
 * @param string $entry Entry.
 * @param object $post Post.
 *
 * @return void
 * @since  2.0
 */
function epl_wpimport_pmxi_reimport( $entry, $post ) {

	if ( ! in_array( $entry, epl_get_core_post_types(), true ) ) {
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

							if ( ! in_array( $field_name, $all_existing_epl, true ) ) {
								$all_existing_epl[] = $field_name;
							}
						}
					}
				}
			}
		}
	}
	$update_epl_logic = $post['update_epl_logic'];
	$update_epl_logic = '' == $update_epl_logic ? 'full_update' : $update_epl_logic; //phpcs:ignore

	?>

	<div class="input">
		<input type="hidden" name="epl_list" value="0" />
		<input type="hidden" name="is_update_epl" value="0" />
		<input type="checkbox" id="is_update_epl_<?php echo esc_attr( $entry ); ?>" name="is_update_epl" value="1" <?php echo $post['is_update_epl'] ? 'checked="checked"' : ''; ?>  class="switcher"/>
		<label for="is_update_epl_<?php echo esc_attr( $entry ); ?>"><?php esc_html_e( 'Easy Property Listings Custom Fields', 'epl-wpimport' ); ?></label>
		<div class="switcher-target-is_update_epl_<?php echo esc_attr( $entry ); ?>" style="padding-left:17px;">
			<div class="input">
				<input type="radio" id="update_epl_logic_full_update_<?php echo esc_attr( $entry ); ?>" name="update_epl_logic" value="full_update" <?php echo ( 'full_update' === $update_epl_logic ) ? 'checked="checked"' : ''; ?> class="switcher"/>
				<label for="update_epl_logic_full_update_<?php echo esc_attr( $entry ); ?>"><?php esc_html_e( 'Update all EPL fields', 'epl-wpimport' ); ?></label>
			</div>

			<div class="input">
				<input type="radio" id="update_epl_logic_only_<?php echo esc_attr( $entry ); ?>" name="update_epl_logic" value="only" <?php echo ( 'only' === $post['update_epl_logic'] ) ? 'checked="checked"' : ''; ?> class="switcher"/>
				<label for="update_epl_logic_only_<?php echo esc_attr( $entry ); ?>"><?php esc_html_e( 'Update only these EPL fields, leave the rest alone', 'epl-wpimport' ); ?></label>
				<div class="switcher-target-update_epl_logic_only_<?php echo esc_attr( $entry ); ?> pmxi_choosen" style="padding-left:17px;">

					<span class="hidden choosen_values">
					<?php
					if ( ! empty( $all_existing_epl ) ) {
						echo wp_kses_post( implode( ',', $all_existing_epl ) );
					}
					?>
					</span>
					<input class="choosen_input" value="
					<?php
					if ( ! empty( $post['epl_list'] ) && 'only' === $post['update_epl_logic'] ) {
						echo esc_attr( implode( ',', $post['epl_list'] ) );}
					?>
					" type="hidden" name="epl_only_list"/>
				</div>
			</div>
			<div class="input">
				<input type="radio" id="update_epl_logic_all_except_<?php echo esc_attr( $entry ); ?>" name="update_epl_logic" value="all_except" <?php echo ( 'all_except' === $post['update_epl_logic'] ) ? 'checked="checked"' : ''; ?> class="switcher"/>
				<label for="update_epl_logic_all_except_<?php echo esc_attr( $entry ); ?>"><?php esc_html_e( 'Leave these EPL fields alone, update all other fields', 'epl-wpimport' ); ?></label>
				<div class="switcher-target-update_epl_logic_all_except_<?php echo esc_attr( $entry ); ?> pmxi_choosen" style="padding-left:17px;">

					<span class="hidden choosen_values">
					<?php
					if ( ! empty( $all_existing_epl ) ) {
						echo esc_attr( implode( ',', $all_existing_epl ) );}
					?>
					</span>
					<input class="choosen_input" value="
					<?php
					if ( ! empty( $post['epl_list'] ) && 'all_except' === $post['update_epl_logic'] ) {
						echo esc_attr( implode( ',', $post['epl_list'] ) );}
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
 * @param string $field_to_update Meta key to update.
 * @param string $post_type Post type.
 * @param array  $options Options.
 * @param string $m_key Meta key.
 *
 * @return bool|mixed|void
 * @since 2.0
 */
function epl_wpimport_pmxi_custom_field_to_update( $field_to_update, $post_type, $options, $m_key ) {

	global $epl_wpimport;

	if ( ! in_array( $m_key, epl_wpimport_get_meta_keys(), true ) ) {
		return $field_to_update;
	}

	if ( false === $field_to_update || ! in_array( $post_type, epl_get_core_post_types(), true ) ) {
		return $field_to_update;
	}

	if ( in_array( $m_key, epl_wpimport_skip_fields(), true ) ) {

		/* Translators: %s is the meta key name. */
		$epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . sprintf( __( 'Skipping field : %s', 'epl-wpimport' ), $m_key ) );
		return false;
	}

	return pmai_is_epl_update_allowed( $m_key, $options );
}
add_filter( 'pmxi_custom_field_to_update', 'epl_wpimport_pmxi_custom_field_to_update', 10, 4 );

/**
 * Filter to check which meta fields will get deleted
 *
 * @param string $field_to_delete Meta key.
 * @param string $pid WP All Import ID.
 * @param string $post_type Post Type.
 * @param array  $options Options.
 * @param string $cur_meta_key Current meta key.
 *
 * @return bool|mixed|void
 * @since 2.0
 */
function epl_wpimport_pmxi_custom_field_to_delete( $field_to_delete, $pid, $post_type, $options, $cur_meta_key ) {

	// TODO: Correct undefined variables $m_key and $field_to_update.

	if ( ! in_array( $cur_meta_key, epl_wpimport_get_meta_keys(), true ) ) {
		return $field_to_delete;
	}

	// Don't let wp all import pro delete image mod date.
	if ( 'property_images_mod_date' === $cur_meta_key || 'property_images_mod_date_old' === $cur_meta_key ) {
		return false;
	}

	if ( false === $field_to_delete || ! in_array( $post_type, epl_get_core_post_types(), true ) ) {
		return $field_to_delete;
	}

	if ( in_array( $cur_meta_key, epl_wpimport_skip_fields(), true ) ) {

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
 * @param  object $post The post object.
 *
 * @return mixed
 * @since 2.0
 */
function epl_wpimport_pmxi_save_options( $post ) {

	if ( 'options' === PMXI_Plugin::getInstance()->getAdminCurrentScreen()->action ) {

		$post['is_update_epl']    = isset( $_POST['is_update_epl'] ) ? sanitize_text_field( wp_unslash( $_POST['is_update_epl'] ) ) : '';
		$post['update_epl_logic'] = isset( $_POST['update_epl_logic'] ) ? sanitize_text_field( wp_unslash( $_POST['update_epl_logic'] ) ) : '';
		$post['epl_only_list']    = isset( $_POST['epl_only_list'] ) ? sanitize_text_field( wp_unslash( $_POST['epl_only_list'] ) ) : '';
		$post['epl_except_list']  = isset( $_POST['epl_except_list'] ) ? sanitize_text_field( wp_unslash( $_POST['epl_except_list'] ) ) : '';
		$post['epl_list']         = isset( $_POST['epl_list'] ) ? sanitize_text_field( wp_unslash( $_POST['epl_list'] ) ) : '';

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
 * @param string $cur_meta_key Meta key.
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

				if ( $cur_meta_key === $field_name ) {
					return apply_filters( 'pmai_is_epl_update_allowed', true, $cur_meta_key, $options );
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

				if ( $cur_meta_key === $field_name ) {
					return apply_filters( 'pmai_is_epl_update_allowed', false, $cur_meta_key, $options );
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

/**
 * Existing meta keys.
 *
 * @param array  $existing_meta_keys Meta keys.
 * @param string $custom_type Field type.
 *
 * @return array
 */
function epl_wp_all_import_existing_meta_keys( $existing_meta_keys, $custom_type ) {

	if ( in_array( $custom_type, epl_get_core_post_types(), true ) ) {

		$hide_fields = epl_wpimport_get_meta_keys();

		$hide_fields[] = 'property_images_mod_date_old';

		foreach ( $existing_meta_keys as $key => $value ) {

			if ( in_array( $value, $hide_fields, true ) ) {
				unset( $existing_meta_keys[ $key ] );
			}
		}

		$existing_meta_keys = array_values( $existing_meta_keys );
	}

	return $existing_meta_keys;
}
add_filter( 'wp_all_import_existing_meta_keys', 'epl_wp_all_import_existing_meta_keys', 10, 2 );

/**
 * Get EPL meta fields
 *
 * @return array
 * @since  2.0
 */
function epl_wpimport_get_meta_keys() {

	global $epl_ai_meta_fields;
	$meta_keys = array();
	if ( ! empty( $epl_ai_meta_fields ) ) {

		foreach ( $epl_ai_meta_fields as $epl_meta_box ) {

			if ( ! empty( $epl_meta_box['groups'] ) ) {
				foreach ( $epl_meta_box['groups'] as $group ) {

						$fields = $group['fields'];
						$fields = array_filter( $fields );

					if ( ! empty( $fields ) ) {
						foreach ( $fields as $field ) {
							$meta_keys[] = $field['name'];
						}
					}
				}
			}
		}
	}
	return $meta_keys;
}
