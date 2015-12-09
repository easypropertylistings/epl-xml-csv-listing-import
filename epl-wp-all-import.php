<?php
/*
 * Plugin Name: Easy Property Listings Import CSV, XML WP All Import Add On
 * Plugin URL: https://www.easypropertylistings.com.au/
 * Description: Import CSV and XML into Easy Property Listings with this WP All Import Add-on
 * Version: 1.0.2
 * Text Domain: epl-import
 * Author: Merv Barrett
 * Author URI: http://www.realestateconnected.com.au
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
				if ( defined('EPL_RUNNING') ) {
					self::$instance->setup_constants();
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
				_e( 'Please activate <b>Easy Property Listings 2.3 or newer</b> to enable all functions of Easy Property Listings WP All Import Add-On', 'epl-wpimport' );
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

			if ( is_admin() || defined( 'DOING_CRON' ) ) {
				require_once EPL_WPIMPORT_PLUGIN_PATH_INCLUDES . 'hooks.php';
				require_once EPL_WPIMPORT_PLUGIN_PATH_INCLUDES . 'rapid-addon.php';
				require_once EPL_WPIMPORT_PLUGIN_PATH_INCLUDES . 'importer.php';
			}
		}

		function is_post_to_update($pid) {
			$do_not_update = get_post_meta($pid, 'do_not_update', true);
			return (!empty($do_not_update)) ? false : true;
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
EPL_WPIMPORT();
