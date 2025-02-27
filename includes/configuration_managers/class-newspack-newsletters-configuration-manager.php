<?php
/**
 * Newspack Newsletters plugin configuration manager.
 *
 * @package Newspack
 */

namespace Newspack;

defined( 'ABSPATH' ) || exit;

require_once NEWSPACK_ABSPATH . '/includes/configuration_managers/class-configuration-manager.php';

/**
 * Provide an interface for configuring and querying the configuration of Newspack Newsletters.
 */
class Newspack_Newsletters_Configuration_Manager extends Configuration_Manager {

	/**
	 * The slug of the plugin.
	 *
	 * @var string
	 */
	public $slug = 'newspack-newsletters';

	/**
	 * Configure Newspack Newsletters use.
	 *
	 * @return bool || WP_Error Return true if successful, or WP_Error if not.
	 */
	public function configure() {
		return true;
	}

	/**
	 * Is Newspack Newsletters installed and connected?
	 *
	 * @return bool Plugin ready state.
	 */
	public function is_configured() {
		return class_exists( 'Newspack_Newsletters_Settings' );
	}

	/**
	 * Get Newspack Newsletters settings.
	 *
	 * @return object Newspack Newsletters settings.
	 */
	public function get_settings() {
		if ( ! $this->is_configured() ) {
			return [];
		}
		return \Newspack_Newsletters_Settings::get_settings_list();
	}

	/**
	 * Is Newspack Newsletters set up (any settings edited)?
	 *
	 * @return bool Plugin set up state.
	 */
	public function is_esp_set_up() {
		if ( $this->is_configured() ) {
			return \Newspack_Newsletters::is_service_provider_configured();
		}
	}

	/**
	 * Get "unconfigured" error.
	 */
	private static function get_unconfigured_error() {
		return new \WP_Error(
			'newspack_missing_required_plugin',
			esc_html__( 'The Newspack Newsletters plugin is not installed and activated. Install and/or activate it to access this feature.', 'newspack' ),
			[
				'status' => 400,
				'level'  => 'fatal',
			]
		);
	}

	/**
	 * Get configured ESP's lists.
	 *
	 * @return array Lists.
	 */
	public function get_lists() {
		if ( ! $this->is_configured() ) {
			return self::get_unconfigured_error();
		}
		$lists = \Newspack_Newsletters_Subscription::get_lists();
		if ( is_wp_error( $lists ) ) {
			return $lists;
		}
		return array_values( $lists );
	}

	/**
	 * Update configured ESP's lists.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return array Lists.
	 */
	public function api_update_lists( $request ) {
		if ( ! $this->is_configured() ) {
			return self::get_unconfigured_error();
		}
		return \Newspack_Newsletters_Subscription::api_update_lists( $request );
	}

	/**
	 * Get ESP's lists enabled in wizard for sending.
	 *
	 * @return array Lists.
	 */
	public function get_enabled_lists() {
		if ( $this->is_configured() ) {
			return \Newspack_Newsletters_Subscription::get_lists();
		} else {
			return new \WP_Error(
				'newspack_missing_required_plugin',
				esc_html__( 'The Newspack Newsletters plugin is not installed and activated. Install and/or activate it to access this feature.', 'newspack' ),
				[
					'status' => 400,
					'level'  => 'fatal',
				]
			);
		}
	}

	/**
	 * Add a contact to ESP's list.
	 *
	 * @param array  $contact Contact info.
	 * @param string $list_id List ID.
	 * @return array Lists.
	 */
	public function add_contact( $contact, $list_id ) {
		if ( $this->is_configured() ) {
			return \Newspack_Newsletters_Subscription::add_contact( $contact, $list_id );
		}
	}

	/**
	 * Update Newspack Newsletters settings.
	 *
	 * @param object $update Settings update.
	 * @return object Newspack Newsletters settings.
	 */
	public function update_settings( $update ) {
		if ( $this->is_configured() ) {
			return \Newspack_Newsletters_Settings::update_settings( $update );
		}
	}
}
