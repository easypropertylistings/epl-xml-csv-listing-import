<?php
/*
 * Plugin Name: Easy Property Listings Import CSV, XML WP All Import Add On
 * Plugin URL: https://wordpress.org/plugins/easy-property-listings-xml-csv-import/
 * Description: Import CSV and XML into Easy Property Listings with this WP All Import Add-on
 * Version: 1.0.11
 * Text Domain: epl-wpimport
 * Author: Merv Barrett
 * Author URI: http://www.realestateconnected.com.au/
 * Contributors: mervb
 *
 * Easy Property Listings Import CSV, XML WP All Import Add On, is free
 * software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software
 * Foundation, either version 2 of the License, or any later version.
 *
 * Easy Property Listings Import CSV, XML WP All Import Add On is
 * distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Property Listings. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EPL-Import
 * @category Importer
 * @author Merv Barrett
 * @version 1.0.11
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EPL_WP_All_Import_Add_On' ) ) :
	/*
	 * Main EPL_WP_All_Import_Add_On Class
	 *
	 * @since 1.0
	 */
	final class EPL_WP_All_Import_Add_On {
		/*
		 * @var EPL_WP_All_Import_Add_On The one true EPL_WP_All_Import_Add_On
		 * @since 1.0
		 */
		private static $instance;

		/*
		 * Main EPL_WP_All_Import_Add_On Instance
		 *
		 * Insures that only one instance of EPL_WP_All_Import_Add_On exists in memory at any one time.
		 * Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @staticvar array $instance
		 * @uses EPL_WP_All_Import_Add_On::includes() Include the required files
		 * @see EPL_TM()
		 * @return The one true EPL_WP_All_Import_Add_On
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EPL_WP_All_Import_Add_On ) ) {
				self::$instance = new EPL_WP_All_Import_Add_On;
				self::$instance->hooks();
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				if ( defined('EPL_RUNNING') ) {
					self::$instance->includes();
				}
			}
			return self::$instance;
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {
			// activation
			add_action( 'admin_init', array( $this, 'activation' ) );
			add_action( 'activated_plugin', array($this, 'epl_wpallimport_load_epl_core_first' ) );
		}

		/**
		 * Activation function fires when the plugin is activated.
		 * @since 1.0
		 * @access public
		 *
		 * @return void
		 */
		public function activation() {
			if ( ! defined('EPL_RUNNING') ) {
				// is this plugin active?
				if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
			 		// unset activation notice
			 		unset( $_GET[ 'activate' ] );
			 		// display notice
			 		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				}
			}
		}

		/**
		 * Admin notices
		 *
		 * @since 1.0
		*/
		public function admin_notices() {
			if ( ! defined('EPL_RUNNING') ) {
				echo '<div class="error"><p>';
				_e( 'Please activate <b>Easy Property Listings</b> to enable all functions of the EPL Import Add-On', 'epl-wpimport' );
				echo '</p></div>';
			}
		}
		/*
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin File
			if ( ! defined( 'EPL_WPIMPORT_PLUGIN_FILE' ) ) {
				define( 'EPL_WPIMPORT_PLUGIN_FILE', __FILE__ );
			}

			// Plugin Folder URL
			if ( ! defined( 'EPL_WPIMPORT_PLUGIN_URL' ) ) {
				define( 'EPL_WPIMPORT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Folder Path
			if ( ! defined( 'EPL_WPIMPORT_PLUGIN_PATH' ) ) {
				define( 'EPL_WPIMPORT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Sub-Directory Paths
			if ( ! defined( 'EPL_WPIMPORT_PLUGIN_PATH_INCLUDES' ) ) {
				define( 'EPL_WPIMPORT_PLUGIN_PATH_INCLUDES', EPL_WPIMPORT_PLUGIN_PATH . 'includes/' );
			}

		}
		/*
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {
			require_once EPL_WPIMPORT_PLUGIN_PATH_INCLUDES . 'hooks.php';
			require_once EPL_WPIMPORT_PLUGIN_PATH_INCLUDES . 'rapid-addon.php';
			require_once EPL_WPIMPORT_PLUGIN_PATH_INCLUDES . 'importer.php';
		}

		/*
		 * Force Easy Property Listings to load first on activation
		 *
		 * @access private
		 * @since 1.0.4
		 * @return void
		 */
		function epl_wpallimport_load_epl_core_first() {
			$epl_core_path = 'easy-property-listings/easy-property-listings.php';
			if ( is_plugin_active( $epl_core_path ) ) {
				if ( $plugins = get_option( 'active_plugins' ) ) {
					if ( $key = array_search( $epl_core_path, $plugins ) ) {
						array_splice( $plugins, $key, 1 );
						array_unshift( $plugins, $epl_core_path );
						update_option( 'active_plugins', $plugins );
					}
				}
			}
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0.4
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugin's languages directory
			$epl_lang_dir = EPL_WPIMPORT_PLUGIN_PATH . 'languages/';
			$epl_lang_dir = apply_filters( 'epl_wpimport_languages_directory', $epl_lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'epl-wpimport' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'epl', $locale );

			// Setup paths to current locale file
			$mofile_local  = $epl_lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/epl-wpimport/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/epl-wpimport folder
				load_textdomain( 'epl-wpimport', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/easy-property-listings-xml-csv-import/languages/ folder
				load_textdomain( 'epl-wpimport', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'epl-wpimport', false, $epl_lang_dir );
			}
		}
	}
endif; // End if class_exists check
/*
 * The main function responsible for returning the one true EPL_WP_All_Import_Add_On
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $epl = EPL_WPIMPORT(); ?>
 *
 * @since 1.0
 * @return object The one true EPL_WP_All_Import_Add_On Instance
 */
function EPL_WPIMPORT() {
	return EPL_WP_All_Import_Add_On::instance();
}
// Get EPL_WPIMPORT Running
add_action( 'plugins_loaded', 'EPL_WPIMPORT' );
