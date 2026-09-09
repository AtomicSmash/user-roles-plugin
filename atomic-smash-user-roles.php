<?php
/**
 * Plugin Name:       Atomic Smash user roles
 * Description:       This adds the user roles used by Launchpad to simplify the editing experience.
 * Requires at least: 6.9
 * Requires PHP:      8.2
 * Version:           0.1.0
 * Author:            Atomic Smash
 * Author URI:        https://www.atomicsmash.co.uk/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       atomic-smash-user-roles
 */

namespace AtomicSmash\UserRoles;

define( 'ATOMIC_SMASH_USER_ROLES_VERSION', '0.1.0' );

/**
 * Set custom capability for MC4WP so we can enable access to settings to AS client role.
 */
function set_custom_cap_for_mc4wp(): string {
	return 'manage_mc4wp_options';
}
add_filter( 'mc4wp_admin_required_capability', __NAMESPACE__ . '\\set_custom_cap_for_mc4wp' );

/**
 * Add or change custom roles
 */
function update_custom_roles(): void {
	if ( get_option( 'add_as_client_role' ) < 1 ) {
		if ( get_role( 'as-client' ) ) {
			remove_role( 'as-client' );
		}
		$as_client_role = add_role( 'as-client', 'AS Client', get_role( 'editor' )->capabilities );
		// Let clients to edit template parts etc.
		$as_client_role->add_cap( 'edit_theme_options' );
		// Add gravity forms access to all users
		$as_client_role->add_cap( 'gform_full_access' );
		// Add access to edit users
		$as_client_role->add_cap( 'list_users' );
		$as_client_role->add_cap( 'promote_users' );
		$as_client_role->add_cap( 'remove_users' );
		$as_client_role->add_cap( 'edit_users' );
		$as_client_role->add_cap( 'add_users' );
		$as_client_role->add_cap( 'create_users' );
		$as_client_role->add_cap( 'delete_users' );

		update_option( 'add_as_client_role', 1 );
	}
	if ( get_option( 'add_as_client_role' ) < 2 ) {
		$as_client_role = get_role( 'as-client' );
		$as_client_role->add_cap( 'manage_mc4wp_options' );

		update_option( 'add_as_client_role', 2 );
	}

	if ( get_option( 'add_tester_role' ) < 1 || get_option( 'add_as_client_role' ) < 2 ) {
		if ( get_role( 'tester' ) ) {
			remove_role( 'tester' );
		}
		add_role( 'tester', 'Tester', get_role( 'as-client' )->capabilities );
		update_option( 'add_tester_role', 1 );
	}

	if ( get_option( 'update_admin_role' ) < 1 ) {
		$admin_role = get_role( 'administrator' );
		$admin_role->add_cap( 'manage_mc4wp_options' );
		update_option( 'update_admin_role', 1 );
	}
}
add_action( 'init', __NAMESPACE__ . '\\update_custom_roles' );
