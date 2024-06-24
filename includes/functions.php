<?php
/**
 * Front End Functions
 *
 * @package     EPL-IMPORTER-ADD-ON
 * @subpackage  Functions/Global
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
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
 * Check if post type is allowed in epl all import.
 *
 * @return     boolean
 * @since      2.0.4
 */
function epl_wpimport_allowed_post_types() {
	$post_types = epl_get_core_post_types();
	return apply_filters( 'epl_wpimport_allowed_post_types', $post_types );
}

/**
 * Custom EPL settings in import settings
 *
 * @param string $entry Entry.
 * @param object $post Post.
 *
 * @return void
 * @since  2.0
 * @since  2.0.1 Removed global $epl_ai_meta_fields
 * @since  2.0.4 Added check if post type is allowed in epl all import.
 */
function epl_wpimport_pmxi_reimport( $entry, $post ) {

	if ( ! in_array( $entry, epl_wpimport_allowed_post_types(), true ) ) {
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
	$update_epl_logic = isset( $post['update_epl_logic'] ) ? $post['update_epl_logic'] : '';
	$update_epl_logic = '' == $update_epl_logic ? 'full_update' : $update_epl_logic; //phpcs:ignore
	$is_update_epl    = isset( $post['is_update_epl'] ) ? $post['is_update_epl'] : '';
	?>

	<div class="input">
		<input type="hidden" name="epl_list" value="0" />
		<input type="hidden" name="is_update_epl" value="0" />
		<input type="checkbox" id="is_update_epl_<?php echo esc_attr( $entry ); ?>" name="is_update_epl" value="1" <?php echo $is_update_epl ? 'checked="checked"' : ''; ?>  class="switcher"/>
		<label for="is_update_epl_<?php echo esc_attr( $entry ); ?>"><?php esc_html_e( 'Easy Property Listings Custom Fields', 'epl-wpimport' ); ?></label>
		<div class="switcher-target-is_update_epl_<?php echo esc_attr( $entry ); ?>" style="padding-left:17px;">
			<div class="input">
				<input type="radio" id="update_epl_logic_full_update_<?php echo esc_attr( $entry ); ?>" name="update_epl_logic" value="full_update" <?php echo ( 'full_update' === $update_epl_logic ) ? 'checked="checked"' : ''; ?> class="switcher"/>
				<label for="update_epl_logic_full_update_<?php echo esc_attr( $entry ); ?>"><?php esc_html_e( 'Update all EPL fields', 'epl-wpimport' ); ?></label>
			</div>

			<div class="input">
				<input type="radio" id="update_epl_logic_only_<?php echo esc_attr( $entry ); ?>" name="update_epl_logic" value="only" <?php echo ( 'only' === $update_epl_logic ) ? 'checked="checked"' : ''; ?> class="switcher"/>
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
					if ( ! empty( $post['epl_list'] ) && 'only' === $update_epl_logic ) {
						echo esc_attr( implode( ',', $post['epl_list'] ) );}
					?>
					" type="hidden" name="epl_only_list"/>
				</div>
			</div>
			<div class="input">
				<input type="radio" id="update_epl_logic_all_except_<?php echo esc_attr( $entry ); ?>" name="update_epl_logic" value="all_except" <?php echo ( 'all_except' === $update_epl_logic ) ? 'checked="checked"' : ''; ?> class="switcher"/>
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
 * @since 2.0.1 Removed global $epl_ai_meta_fields
 * @since 2.0.4 Added check if post type is allowed in epl all import.
 */
function epl_wpimport_pmxi_custom_field_to_update( $field_to_update, $post_type, $options, $m_key ) {

	global $epl_wpimport;

	if ( ! in_array( $m_key, epl_wpimport_get_meta_keys(), true ) ) {
		return $field_to_update;
	}

	if ( false === $field_to_update || ! in_array( $post_type, epl_wpimport_allowed_post_types(), true ) ) {
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
 * @return false|string
 * @since 2.0
 * @since 2.0.0 Removed epl custom field deleting process.
 * @since 2.0.4 Added check if post type is allowed in epl all import.
 */
function epl_wpimport_pmxi_custom_field_to_delete( $field_to_delete, $pid, $post_type, $options, $cur_meta_key ) {

	if ( ! in_array( $post_type, epl_wpimport_allowed_post_types(), true ) ) {
		return $field_to_delete;
	}

	if ( in_array( $cur_meta_key, epl_wpimport_get_meta_keys(), true ) ) {
		return false; // Do not delete EPL fields.
	}

	// Don't let wp all import pro delete image mod date.
	if ( 'property_images_mod_date_old' === $cur_meta_key ) {
		return false;
	}

	return $field_to_delete;
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
		$ignore_list = array();
		if ( ! empty( $options['epl_list'] ) && is_array( $options['epl_list'] ) ) {
			foreach ( $options['epl_list'] as $key => $epl_field ) {

				$parts_temp    = explode( ' ', $epl_field );
				$field_name    = trim( array_shift( $parts_temp ), '[]' );
				$ignore_list[] = $field_name;
			}
		}

		if ( in_array( $cur_meta_key, $ignore_list ) ) {
			return apply_filters( 'pmai_is_epl_update_allowed', false, $cur_meta_key, $options );
		} else {
			return apply_filters( 'pmai_is_epl_update_allowed', true, $cur_meta_key, $options );
		}
	}

	return apply_filters( 'pmai_is_epl_update_allowed', false, $cur_meta_key, $options );
}

/**
 * Don't update these fields
 *
 * This function further filters default skip list using 'epl_wpimport_skip_fields'.
 * Removing default fields using this filter will bypass 'import'   =>  'preserve' check in meta field
 *
 * @return mixed|void
 * @since 2.0
 */
function epl_wpimport_skip_fields() {

	$fields = epl_wpimport_default_skip_fields_list();

	return apply_filters( 'epl_wpimport_skip_fields', $fields );
}

/**
 * List of fields to be skipped while importing : Default List.
 *
 * More fields can be added using 'epl_wpimport_default_skip_fields_list' filter.
 * This list is furthered filtered by  'epl_wpimport_skip_fields'.
 *
 * @return mixed|void
 * @since 2.0
 */
function epl_wpimport_default_skip_fields_list() {

	$fields = array(
		'property_featured',
		'property_year_built',
		'property_owner',
	);

	return apply_filters( 'epl_wpimport_default_skip_fields_list', $fields );
}

/**
 * Existing meta keys.
 *
 * @param array  $existing_meta_keys Meta keys.
 * @param string $custom_type Field type.
 *
 * @return array
 *
 * @since  2.0.4 Added check if post type is allowed in epl all import.
 */
function epl_wp_all_import_existing_meta_keys( $existing_meta_keys, $custom_type ) {

	if ( in_array( $custom_type, epl_wpimport_allowed_post_types(), true ) ) {

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
 * @since  2.0.1 Removed global $epl_ai_meta_fields
 */
function epl_wpimport_get_meta_keys() {

	$epl_ai_meta_fields = epl_wpimport_get_meta_fields();
	$meta_keys          = array();
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
/**
 * Determine if a field needs to be skipped while importing.
 *
 * @param      string $field  The field.
 *
 * @return     boolean
 * @since      2.0.0
 */
function epl_wpimport_is_field_skipped( $field ) {

	$default_list  = epl_wpimport_default_skip_fields_list();
	$filtered_list = epl_wpimport_skip_fields();
	$array_diff    = array_diff( $default_list, $filtered_list );

	if ( in_array( $field['name'], $array_diff, true ) ) {
		return false;
	}

	if ( ( isset( $field['import'] ) && 'preserve' === $field['import'] ) ||
		in_array( $field['name'], $filtered_list, true )
	) {
		return true;
	}

	return false;
}

/**
 * Check if an attachment exists with the given URL.
 *
 * @param string $image_url Image URL.
 *
 * @return int|WP_Post|null
 * @since 2.1
 */
function epl_wpimport_get_attachment_by_url( $image_url ) {

	$attachment = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_wp_attached_file',
			'meta_value'     => $image_url,
			'posts_per_page' => 1,
		)
	);

	return ! empty( $attachment ) ? $attachment[0] : null;
}

/**
 * Callback for wp_all_import_handle_upload, checks if duplicate attachments exists & halts the process.
 *
 * @param array $handle_image Image data.
 *
 * @return array
 * @since 2.1
 */
function epl_wpimport_wp_all_import_handle_upload( $handle_image ) {

	$image_filepath = $handle_image['file'];
	$image_url      = $handle_image['url'];
	$file_mime_type = $handle_image['type'];

	$existing_attachment = epl_wpimport_get_attachment_by_url( $image_url );

	if ( $existing_attachment ) {
		return array();
	}

	return $handle_image;
}

add_filter( 'wp_all_import_handle_upload', 'epl_wpimport_wp_all_import_handle_upload' );

/**
 * Get list of duplicate attachments for EPL post types.
 *
 * @param int   $limit      Number of entries.
 * @param array $post_types Post Types.
 *
 * @return array
 * @since 2.1
 * @since 2.2 Image counter for deleting unattached images.
 */
function epl_wpimport_get_duplicate_attachments( $limit = 50, $post_types = array() ) {

	if ( empty( $post_types ) ) {
		$post_types = epl_get_core_post_types();
	}

	global $wpdb;

        $offset = 0;
        $page_num = isset( $_GET['page_num'] ) ? intval( $_GET['page_num'] ) : 1;

        if ( $page_num > 1 ) {
                $offset = $limit * ( $page_num - 1 );
        }

	// Query to find duplicate attachment URLs.
	$sql = "SELECT meta_value, COUNT(*) AS count
        FROM {$wpdb->postmeta}
        WHERE meta_key = '_wp_attached_file'
        GROUP BY meta_value
        HAVING count > 1
        LIMIT $offset, $limit";

	$duplicate_urls = $wpdb->get_results( $sql );
        
	$duplicate_attachments = array();

	if ( $duplicate_urls ) {
		foreach ( $duplicate_urls as $duplicate_url ) {

			$sql = "SELECT post_id
                        FROM {$wpdb->postmeta}
                        WHERE meta_key = '_wp_attached_file'
                        AND meta_value = %s";

			$post_ids = $wpdb->get_col( $wpdb->prepare( $sql, $duplicate_url->meta_value ) );
                        
			$parent_posts = array();
			foreach ( $post_ids as $post_id ) {
				$post_object   = get_post( $post_id );
				$parent_object = get_post( $post_object->post_parent );

				if ( is_object( $parent_object ) && in_array( $parent_object->post_type, $post_types ) ) {
					$parent_posts[ $post_id ] = $post_object->post_parent;
				}
			}

			if ( ! empty( $parent_posts ) ) {
				$duplicate_attachments[] = array(
					'attachment_url' => $duplicate_url->meta_value,
					'post_ids'       => $post_ids,
					'parent_posts'   => $parent_posts,
				);
			}
		}
	}

	return $duplicate_attachments;
}

/**
 * Delete the duplicate attachments from the listings.
 *
 * @param bool $dryrun Testing option.
 *
 * @since 2.1
 */
function epl_wpimport_delete_duplicate_attachments( $dryrun = true ) {

	if ( $dryrun ) {
		echo '<b>' . esc_html__( 'Dry run enabled, no actual queries will be performed', 'epl-wpimport' ) . '</b><br><br>';
	} else {
		echo '<b>' . esc_html__( 'Dry run disabled, actual queries to delete duplicate attachments will be performed', 'epl-wpimport' ) . '</b><br><br>';
	}

	$duplicate_attachments = epl_wpimport_get_duplicate_attachments();

	if ( ! empty( $duplicate_attachments ) ) {

		foreach ( $duplicate_attachments as $duplicate ) {

			if ( count( $duplicate['post_ids'] ) > 1 ) {

				$keep_post_id = array_shift( $duplicate['post_ids'] );

				foreach ( $duplicate['post_ids'] as $post_id ) {

					if ( $post_id !== get_post_thumbnail_id( $duplicate['parent_posts'][ $post_id ] ) ) {

						if ( ! $dryrun ) {
							wp_delete_post( $post_id, true );
						}

						echo esc_html__( 'Deleted duplicate attachment with post ID:', 'epl-wpimport' ) . esc_html( $post_id ) . '<br>';
					} else {
						if ( ! $dryrun ) {
							wp_delete_post( $keep_post_id, true );
						}
						// Translators: the %d is the image ID.
						echo sprintf( esc_html__( 'Deleted duplicate attachment with post ID: %d (non-featured image).', 'epl-wpimport' ), $keep_post_id ) . '<br>';
					}
				}
			}
		}
	} else {
		echo esc_html__( 'No duplicate attachments found.', 'epl-wpimport' );
	}
}

/**
 * Trigger the deletion of duplicate attachments.
 *
 * @since 2.1
 */
function epl_wpimport_trigger_duplicate_deletion() {

	if ( ! is_user_logged_in() || ! current_user_can( 'administrator' ) ) {
			return;
	}

	if ( ! isset( $_GET['epl_wpimport_delete_duplicate_attachments'] ) ) {
			return;
	}

	$runmode = isset( $_GET['mode'] ) ? sanitize_key( $_GET['mode'] ) : '';
	$runmode = 'live' === $runmode ? false : true;

	epl_wpimport_delete_duplicate_attachments( $runmode );

	wp_die();
}

add_action( 'admin_init', 'epl_wpimport_trigger_duplicate_deletion' );

/**
 * Function checks if there are pending attachments after import pro is done deleting them.
 * Deletes if found.
 * 
 * @since 2.2
 */
function epl_wpimport_check_deleted_attachments( $parent_id, $epl_wpimport = null ) {

        $attachments = get_posts(array('post_parent' => $parent_id, 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null));
        
        $ids = [];
        foreach ($attachments as $attach) {

                if ( wp_attachment_is_image( $attach->ID ) ) {

                        if ( !empty( $attach->ID ) ) {

                                $file = get_attached_file( $attach->ID );
                                        
                                wp_delete_attachment($attach->ID, TRUE);
                                $ids[] = $attach->ID;
                        }
                }
        }

        if ( ! empty( $ids ) && is_object( $epl_wpimport ) ) {
                $epl_wpimport->log( __( 'EPL IMPORTER', 'epl-wpimport' ) . ': ' . __( 'Deleted duplicate attachments : ', 'epl-wpimport' ) . implode(',', $ids ) );
        }
}
