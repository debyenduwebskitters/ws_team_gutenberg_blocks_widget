<?php
/**
 * Main plugin bootstrap.
 *
 * @package WsTeamShowcase
 */

namespace Ws\TeamShowcase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once BLOCKFORGE_TEAM_SHOWCASE_PATH . 'includes/helpers/class-utils.php';
require_once BLOCKFORGE_TEAM_SHOWCASE_PATH . 'includes/blocks/class-team-showcase-block.php';
require_once BLOCKFORGE_TEAM_SHOWCASE_PATH . 'includes/ajax/class-team-showcase-ajax.php';

/**
 * Main plugin class.
 */
class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'register_assets' ) );

		Team_Showcase_Block::instance();
		Team_Showcase_Ajax::instance();
	}

	/**
	 * Load translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'blockforge-team-showcase', false, dirname( plugin_basename( BLOCKFORGE_TEAM_SHOWCASE_FILE ) ) . '/languages' );
	}

	/**
	 * Register shared assets.
	 *
	 * @return void
	 */
	public function register_assets() {
		wp_register_style(
			'blockforge-team-showcase-style',
			BLOCKFORGE_TEAM_SHOWCASE_URL . 'assets/css/team-showcase.css',
			array(),
			BLOCKFORGE_TEAM_SHOWCASE_VERSION
		);

		wp_register_style(
			'blockforge-team-showcase-editor-style',
			BLOCKFORGE_TEAM_SHOWCASE_URL . 'assets/css/team-showcase-editor.css',
			array( 'wp-edit-blocks' ),
			BLOCKFORGE_TEAM_SHOWCASE_VERSION
		);

		wp_register_script(
			'blockforge-team-showcase-editor',
			BLOCKFORGE_TEAM_SHOWCASE_URL . 'assets/js/team-showcase-editor.js',
			array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n', 'wp-server-side-render' ),
			BLOCKFORGE_TEAM_SHOWCASE_VERSION,
			true
		);

		wp_register_script(
			'blockforge-team-showcase-view',
			BLOCKFORGE_TEAM_SHOWCASE_URL . 'assets/js/team-showcase.js',
			array(),
			BLOCKFORGE_TEAM_SHOWCASE_VERSION,
			true
		);
	}
}
