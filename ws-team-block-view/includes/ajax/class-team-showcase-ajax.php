<?php
/**
 * AJAX endpoints for filtering and pagination.
 *
 * @package WsTeamShowcase
 */

namespace Ws\TeamShowcase;

use Ws\TeamShowcase\Helpers\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX controller.
 */
class Team_Showcase_Ajax {

	/**
	 * Singleton instance.
	 *
	 * @var Team_Showcase_Ajax|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Team_Showcase_Ajax
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
		add_action( 'wp_ajax_blockforge_team_showcase_query', array( $this, 'handle_query' ) );
		add_action( 'wp_ajax_nopriv_blockforge_team_showcase_query', array( $this, 'handle_query' ) );
	}

	/**
	 * Handle filter and load more actions.
	 *
	 * @return void
	 */
	public function handle_query() {
		check_ajax_referer( 'blockforge_team_showcase_nonce', 'nonce' );

		$members      = isset( $_POST['members'] ) ? json_decode( wp_unslash( $_POST['members'] ), true ) : array();
		$attributes   = isset( $_POST['attributes'] ) ? json_decode( wp_unslash( $_POST['attributes'] ), true ) : array();
		$page         = isset( $_POST['page'] ) ? max( 1, absint( $_POST['page'] ) ) : 1;
		$per_page     = isset( $_POST['perPage'] ) ? max( 1, absint( $_POST['perPage'] ) ) : 9;
		$department   = isset( $_POST['department'] ) ? sanitize_text_field( wp_unslash( $_POST['department'] ) ) : '';
		$search       = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
		$popup_toggle = isset( $attributes['popupToggle'] ) ? Utils::to_bool( $attributes['popupToggle'] ) : true;

		$members  = Utils::sanitize_members( $members );
		$filtered = Utils::filter_members( $members, $department, $search );
		$offset   = ( $page - 1 ) * $per_page;
		$subset   = array_slice( $filtered, $offset, $per_page );
		$has_more = count( $filtered ) > ( $offset + $per_page );

		$block = Team_Showcase_Block::instance();

		wp_send_json_success(
			array(
				'markup'     => $block->render_members_markup( $subset, wp_parse_args( $attributes, $block->get_default_attributes() ), $popup_toggle ),
				'foundPosts' => count( $filtered ),
				'hasMore'    => $has_more,
				'nextPage'   => $page + 1,
			)
		);
	}
}
