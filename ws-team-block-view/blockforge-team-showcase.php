<?php
/**
 * Plugin Name: WS Team Block View(Debyendu)
 * Plugin URI: https://example.com/blockforge-team-showcase
 * Description: A reusable Gutenberg block for responsive, filterable, and interactive team showcase sections.
 * Version: 1.0.0
 * Author: Debyendu Bhunia
 * Text Domain: blockforge-team-showcase
 * Requires at least: 6.3
 * Requires PHP: 7.4
 *
 * @package WsTeamShowcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BLOCKFORGE_TEAM_SHOWCASE_VERSION', '1.0.0' );
define( 'BLOCKFORGE_TEAM_SHOWCASE_FILE', __FILE__ );
define( 'BLOCKFORGE_TEAM_SHOWCASE_PATH', plugin_dir_path( __FILE__ ) );
define( 'BLOCKFORGE_TEAM_SHOWCASE_URL', plugin_dir_url( __FILE__ ) );

require_once BLOCKFORGE_TEAM_SHOWCASE_PATH . 'includes/class-plugin.php';

\Ws\TeamShowcase\Plugin::instance();
