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


// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// check for plugin using plugin name
if( is_plugin_active( 'amember4/amember4.php' ) )
{
	/**
	 * It's OK to load the main plugin file
	 */
	require_once( plugin_dir_path( __FILE__ ) . 'inc/class.aMNav_Menu_Control.php');
	$GLOBALS['aMNav_Menu_Control'] = aMNav_Menu_Control::instance();
}
else
{
	/**
	 * We are running under PHP 5.2.x
	 * Display an admin notice and do nothing.
	 */
	is_admin() && add_action( 'admin_notices', create_function( '', "echo '
		<div class=\"error\"><p>
		JLogica aMember Menu Control Check prerequisites:
		<strong>This plugin requires aMember plugin. Please deactivate or install the aMember integration plugin</strong>.
		</p></div>';" ) );
}
# --- EOF
