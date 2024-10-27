<?php
/*
Plugin Name: Nav Menu Control for aMember
Plugin URI: https://jlogica.com/amnav-menu-control/
Description: aMember hide custom menu items based on user purchases.
Version: 1.0.3
Author: Larry Lewis
Author URI: https://jlogica.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: amnav-menu-control

Copyright 2016 Larry Lewis(email: amnav@jlogica.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA
*/


// don't load directly
if( ! function_exists( 'is_admin' ) )
{
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


if( ! class_exists( "aMNav_Menu_Control" ) ) :

	class aMNav_Menu_Control
	{

		/**
		 * @var Nav_Menu_control The single instance of the class
		 * @since 1.5
		 */
		protected static $_instance = null;

		/**
		 * @constant string donate url
		 * @since 1.5
		 */
		CONST DONATE_URL = "https://paypal.me/jenolan/25usd";

		/**
		 * @constant string version number
		 * @since 1.7.1
		 */
		CONST VERSION = '1.0.3';

		/**
		 * Main Nav Menu control Instance
		 *
		 * Ensures only one instance of Nav Menu control is loaded or can be loaded.
		 *
		 * @since 1.0
		 * @static
		 * @see Nav_Menu_control()
		 * @return Nav_Menu_control - Main instance
		 */
		public static function instance()
		{
			if( is_null( self::$_instance ) )
			{
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0
		 */
		public function __clone()
		{
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'amnav-menu-control', 'amnav-menu-control', 'aMemberMenu', 'amnav-menu-control' ), '1.5' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0
		 */
		public function __wakeup()
		{
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'amnav-menu-control', 'amnav-menu-control', 'aMemberMenu', 'amnav-menu-control' ), '1.5' );
		}

		/**
		 * Nav_Menu_control Constructor.
		 * @access public
		 * @return Nav_Menu_control
		 * @since  1.0
		 */
		public function __construct()
		{

			// Admin functions
			add_action( 'admin_init', array( $this, 'admin_init' ) );

			// load the textdomain
			add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );

			// add FAQ and Donate link to plugin
			add_filter( 'plugin_row_meta', array( $this, 'add_action_links' ), 10, 4 );

			// switch the admin walker
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_nav_menu_walker' ) );

			// add new fields via hook
			add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'custom_fields' ), 10, 4 );

			// add some JS
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// save the menu item meta
			add_action( 'wp_update_nav_menu_item', array( $this, 'nav_update' ), 10, 2 );

			// add meta to menu item
			add_filter( 'wp_setup_nav_menu_item', array( $this, 'setup_nav_item' ) );

			// exclude items via filter instead of via custom Walker
			if( ! is_admin() )
			{
				add_filter( 'wp_get_nav_menu_items', array( $this, 'exclude_menu_items' ) );
			}

			// upgrade routine
//			add_action( 'plugins_loaded', array( $this, 'maybe_upgrade' ) );

		}

		/**
		 * Include the custom admin walker
		 *
		 * @access public
		 * @return void
		 */
		public function admin_init()
		{
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if( ! is_plugin_active( 'nav-menu-roles/nav-menu-roles.php' ) AND ! class_exists( 'Walker_Nav_Menu_Edit_Roles' ) )
			{
				require_once( plugin_dir_path( __FILE__ ) . 'class.Walker_Nav_Menu_Edit_Roles.php' );
			}
			// Register Importer
			$this->register_importer();
		}


		/**
		 * Register the Importer
		 * the regular Importer skips post meta for the menu items
		 *
		 * @access private
		 * @return void
		 */
		public function register_importer()
		{
			// Register the new importer
			if( defined( 'WP_LOAD_IMPORTERS' ) )
			{
				require_once( plugin_dir_path( __FILE__ ) . 'class.aMNav_Menu_Control_Import.php' );
				// Register the custom importer we've created.
				$control_import = new aMNav_Menu_control_Import();
				register_importer( 'amnav_menu_control',
					__( 'aMNav Menu control', 'amnav-menu-control', 'amnav-menu-control', 'aMemberMenu', 'amnav-menu-control' ),
					sprintf( __( 'Import %samnav menu control%s and other menu item meta skipped by the default importer', 'amnav-menu-control', 'amnav-menu-control', 'aMemberMenu', 'amnav-menu-control' ), '<strong>', '</strong>' ),
					array( $control_import, 'dispatch' ) );
			}
		}

		/**
		 * Make Plugin Translation-ready
		 * CALLBACK FUNCTION FOR:  add_action( 'plugins_loaded', array( $this,'load_text_domain'));
		 * @since 1.0
		 */

		public function load_text_domain()
		{
			load_plugin_textdomain( 'amnav-menu-control', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Add docu link
		 * @since 1.0
		 */
		public function add_action_links( $plugin_meta, $plugin_file, $plugin_data, $status )
		{
			if( $plugin_file == 'amnav-menu-control/amnav-menu-control.php' )
			{
				$plugin_meta[] = sprintf( '<a class="dashicons-before dashicons-welcome-learn-more" href="https://wordpress.org/plugins/amnav-menu-control/faq/#conflict">%s</a>', __( 'FAQ', 'amnav-menu-control', 'amnav-menu-control', 'aMemberMenu', 'amnav-menu-control' ) );
				$plugin_meta[] = '<a class="dashicons-before dashicons-awards" href="' . self::DONATE_URL . '" target="_blank">' . __( 'Donate', 'amnav-menu-control', 'amnav-menu-control', 'aMemberMenu', 'amnav-menu-control' ) . '</a>';
			}

			return $plugin_meta;
		}


		/**
		 * Override the Admin Menu Walker
		 * @since 1.0
		 */
		public function edit_nav_menu_walker( $walker )
		{
			return 'Walker_Nav_Menu_Edit_Roles';
		}


		/**
		 * Add fields to hook added in Walker
		 * This will allow us to play nicely with any other plugin that is adding the same hook
		 *
		 * @params obj $item - the menu item
		 * @params array $args
		 *
		 * @since 1.0
		 */
		public function custom_fields( $item_id, $item, $depth, $args )
		{
			/* Get the control saved for the post. */
			$products = Am_Lite::getInstance()->getProducts();

			// by default nothing is checked (will match "everyone" radio)
			$logged_in_out = '';

			/* Get the options saved for the post. */
			$control = get_post_meta( $item->ID, '_amnav_menu_control', true );

			// specific control are saved as an array, so "in" or an array equals "in" is checked
			if( is_array( $control ) || $control == 'in' )
			{
				$logged_in_out = 'in';
			}
			else if( $control == 'out' )
			{
				$logged_in_out = 'out';
			}

			// the specific control to check
			$checked_control = is_array( $control ) ? $control : false;

			// whether to display the role checkboxes
			$hidden = $logged_in_out == 'in' ? '' : 'display: none;';

			?>

			<input type = "hidden" name = "amnav-menu-control-nonce"
			       value = "<?php echo wp_create_nonce( 'amnav-menu-nonce-name' ); ?>"/>

			<div class = "field-nav_menu_control nav_menu_logged_in_out_field description-wide" style = "margin: 5px 0;">
				<span
					class = "description"><?php _e( "aMember", 'amnav-menu-control', 'amnav-menu-control', 'aMemberMenu', 'amnav-menu-control', 'amnav-menu-control' ); ?></span>
				<br/>

				<input type = "hidden" class = "amnav-menu-id" value = "<?php echo $item->ID; ?>"/>

				<div class = "logged-input-holder" style = "float: left; width: 35%;">
					<input type = "radio" class = "amnav-menu-logged-in-out"
					       name = "amnav-menu-logged-in-out[<?php echo $item->ID; ?>]"
					       id = "nav_menu_logged_in-for-<?php echo $item->ID; ?>" <?php checked( 'in', $logged_in_out ); ?>
					       value = "in"/>
					<label for = "nav_menu_logged_in-for-<?php echo $item->ID; ?>">
						<?php _e( 'Logged In', 'amnav-menu-control', 'amnav-menu-control', 'aMemberMenu', 'amnav-menu-control', 'amnav-menu-control' ); ?>
					</label>
				</div>

				<div class = "logged-input-holder" style = "float: left; width: 35%;">
					<input type = "radio" class = "amnav-menu-logged-in-out"
					       name = "amnav-menu-logged-in-out[<?php echo $item->ID; ?>]"
					       id = "nav_menu_logged_out-for-<?php echo $item->ID; ?>" <?php checked( 'out', $logged_in_out ); ?>
					       value = "out"/>
					<label for = "nav_menu_logged_out-for-<?php echo $item->ID; ?>">
						<?php _e( 'Logged Out', 'amnav-menu-control', 'amnav-menu-control', 'aMemberMenu', 'amnav-menu-control', 'amnav-menu-control' ); ?>
					</label>
				</div>

				<div class = "logged-input-holder" style = "float: left; width: 30%;">
					<input type = "radio" class = "amnav-menu-logged-in-out"
					       name = "amnav-menu-logged-in-out[<?php echo $item->ID; ?>]"
					       id = "nav_menu_by_control-for-<?php echo $item->ID; ?>" <?php checked( '', $logged_in_out ); ?>
					       value = ""/>
					<label for = "nav_menu_by_control-for-<?php echo $item->ID; ?>">
						<?php _e( 'Everyone', 'amnav-menu-control', 'amnav-menu-control', 'aMemberMenu', 'amnav-menu-control', 'amnav-menu-control' ); ?>
					</label>
				</div>

			</div>

			<div class = "field-amnav_menu_control amnav_menu_control_field description-wide"
			     style = "margin: 5px 0; <?php echo $hidden; ?>">
				<span
					class = "description"><?php _e( "Restrict product(s)", 'amnav-menu-control', 'amnav-menu-control', 'aMemberMenu', 'amnav-menu-control', 'amnav-menu-control' ); ?></span>
				<br/>

				<?php

				/* Loop through each of the available control. */
				foreach( $products as $control => $name )
				{
					$control = intval( $control );
					/* If the role has been selected, make sure it's checked. */
					$checked = checked( true, ( is_array( $checked_control ) && in_array( $control, $checked_control ) ), false );
					?>
					<div class = "control-input-holder" style = "float: left; width: 49%; margin: 2px 0;">
						<input type = "checkbox"
						       name = "amnav-menu-control[<?php echo $item->ID; ?>][<?php echo $control; ?>]"
						       id = "amnav_menu_control-<?php echo $control; ?>-for-<?php echo $item->ID; ?>" <?php echo $checked; ?>
						       value = "<?php echo $control; ?>"/>
						<label for = "amnav_menu_control-<?php echo $control; ?>-for-<?php echo $item->ID; ?> style=''">
							<?php echo esc_html( $name ); ?>
						</label>
					</div>
				<?php } ?>
			</div>
			<?php
		}


		/**
		 * Save the control as menu item meta
		 * @return null
		 * @since 1.0
		 *
		 */
		public function enqueue_scripts( $hook )
		{
			if( $hook == 'nav-menus.php' )
			{
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_script( 'amnav-menu-control', plugins_url( '../js/amnav-menu-control' . $suffix . '.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
			}
		}

		/**
		 * Save the control as menu item meta
		 * @return string
		 * @since 1.0
		 */
		public function nav_update( $menu_id, $menu_item_db_id )
		{
			/* Get the control saved for the post. */
			$products = Am_Lite::getInstance()->getProducts();

			// verify this came from our screen and with proper authorization.
			if( ! isset( $_POST['amnav-menu-control-nonce'] ) || ! wp_verify_nonce( $_POST['amnav-menu-control-nonce'], 'amnav-menu-nonce-name' ) )
			{
				return;
			}

			$saved_data = false;

			if( isset( $_POST['amnav-menu-logged-in-out'][ $menu_item_db_id ] ) && $_POST['amnav-menu-logged-in-out'][ $menu_item_db_id ] == 'in' && ! empty ( $_POST['amnav-menu-control'][ $menu_item_db_id ] ) )
			{
				$custom_control = array();
				// only save allowed control
				foreach( $_POST['amnav-menu-control'][ $menu_item_db_id ] as $control )
				{
					$control = intval( $control );
					if( array_key_exists( $control, $products ) )
					{
						$custom_control[] = $control;
					}
				}
				if( ! empty ( $custom_control ) )
				{
					$saved_data = $custom_control;
				}
			} else if( isset( $_POST['amnav-menu-logged-in-out'][ $menu_item_db_id ] ) && in_array( $_POST['amnav-menu-logged-in-out'][ $menu_item_db_id ], array(
					'in',
					'out'
				) )
			)
			{
				$saved_data = ( $_POST['amnav-menu-logged-in-out'][ $menu_item_db_id ] == 'in' ) ? 'in' : 'out';
			}

			if( $saved_data )
			{
				update_post_meta( $menu_item_db_id, '_amnav_menu_control', $saved_data );
			} else
			{
				delete_post_meta( $menu_item_db_id, '_amnav_menu_control' );
			}
		}

		/**
		 * Adds value of new field to $item object
		 * is be passed to Walker_Nav_Menu_Edit_Custom
		 * @since 1.0
		 */
		public function setup_nav_item( $menu_item )
		{
			$control = get_post_meta( $menu_item->ID, '_amnav_menu_control', true );
			if( ! empty( $control ) )
			{
				$menu_item->control = $control;
			}
			return $menu_item;
		}

		/**
		 * Exclude menu items via wp_get_nav_menu_items filter
		 * this fixes plugin's incompatibility with theme's that use their own custom Walker
		 * Thanks to Evan Stein @vanpop http://vanpop.com/
		 * @since 1.0
		 */
		public function exclude_menu_items( $items )
		{
			/* Get the control saved for the post. */
			//$products = Am_Lite::getInstance()->getProducts();

			$hide_children_of = array();
			// Iterate over the items to search and destroy
			foreach( $items as $key => $item )
			{
				$visible = true;
				// hide any item that is the child of a hidden item
				if( in_array( $item->menu_item_parent, $hide_children_of ) )
				{
					$visible            = false;
					$hide_children_of[] = $item->ID; // for nested menus
				}
				// check any item that has NMR control set
				if( $visible && isset( $item->control ) )
				{
					// check all logged in, all logged out, or role
					switch( $item->control )
					{
						case 'in' :
							$visible = Am_Lite::getInstance()->isLoggedIn() ? true : false;
							break;
						case 'out' :
							$visible = ! Am_Lite::getInstance()->isLoggedIn() ? true : false;
							break;
						default:
							$visible = false;
							if( Am_Lite::getInstance()->isLoggedIn() )
							{
								$visible = true;
								if( is_array( $item->control ) && ! empty( $item->control ) )
								{
									foreach( $item->control as $control )
									{
										if( ! Am_Lite::getInstance()->haveSubscriptions( $control ) )
										{
											$visible = false;
											break;
										}
									}
								}

							}
							break;
					}
				}
				// add filter to work with plugins that don't use traditional control
				$visible = apply_filters( 'nav_menu_control_item_visibility', $visible, $item );
				// unset non-visible item
				if( ! $visible )
				{
					$hide_children_of[] = $item->ID; // store ID of item
					unset( $items[ $key ] );
				}
			}

			return $items;
		}


		/**
		 * Maybe upgrade
		 *
		 * @access public
		 * @return void
		 */
		public function maybe_upgrade()
		{
			$db_version = get_option( 'amnav_menu_control_db_version', false );
			if( $db_version === false )
			{
				update_option( 'amnav_menu_control_db_version', self::VERSION );
			}
		}

	} // end class

endif; // class_exists check
