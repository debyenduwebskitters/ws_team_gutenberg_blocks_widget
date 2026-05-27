<?php
/**
 * Dynamic Gutenberg block implementation.
 *
 * @package WsTeamShowcase
 */

namespace Ws\TeamShowcase;

use Ws\TeamShowcase\Helpers\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Team showcase block.
 */
class Team_Showcase_Block {

	/**
	 * Singleton instance.
	 *
	 * @var Team_Showcase_Block|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Team_Showcase_Block
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
		add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Register block metadata and render callback.
	 *
	 * @return void
	 */
	public function register_block() {
		register_block_type(
			BLOCKFORGE_TEAM_SHOWCASE_PATH . 'includes/blocks/team-showcase',
			array(
				'editor_script'   => 'blockforge-team-showcase-editor',
				'editor_style'    => 'blockforge-team-showcase-editor-style',
				'style'           => 'blockforge-team-showcase-style',
				'script'          => 'blockforge-team-showcase-view',
				'render_callback' => array( $this, 'render_block' ),
			)
		);
	}

	/**
	 * Render block markup.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content Block content.
	 * @return string
	 */
	public function render_block( $attributes, $content = '' ) {
		$defaults = $this->get_default_attributes();
		$settings = wp_parse_args( $attributes, $defaults );
		$members  = Utils::sanitize_members( $settings['teamMembers'] );

		if ( empty( $members ) ) {
			if ( is_admin() ) {
				return '<div class="blockforge-team-showcase__empty">' . esc_html__( 'Add team members in the block sidebar to preview the showcase.', 'blockforge-team-showcase' ) . '</div>';
			}

			return '';
		}

		$block_id       = 'blockforge-team-showcase-' . wp_unique_id();
		$per_page       = max( 1, absint( $settings['itemsPerPage'] ) );
		$total_members  = count( $members );
		$enable_load    = $total_members > $per_page;
		$departments    = Utils::get_departments( $members );
		$popup_enabled  = Utils::to_bool( $settings['popupToggle'] );
		$dark_mode      = Utils::to_bool( $settings['darkModeToggle'] );
		$wrapper_class  = 'blockforge-team-showcase' . ( $dark_mode ? ' is-dark-mode' : '' );
		$wrapper_styles = Utils::build_style_attr(
			array(
				'--bfts-columns'         => max( 1, (int) $settings['gridColumns'] ),
				'--bfts-card-bg'         => sanitize_hex_color( $settings['cardBackground'] ) ? $settings['cardBackground'] : '#ffffff',
				'--bfts-card-radius'     => absint( $settings['borderRadius'] ) . 'px',
				'--bfts-card-min-height' => sanitize_text_field( $settings['cardHeight'] ),
				'--bfts-text-align'      => sanitize_text_field( $settings['cardAlignment'] ),
				'--bfts-button-bg'       => sanitize_hex_color( $settings['buttonBackground'] ) ? $settings['buttonBackground'] : '#111827',
				'--bfts-button-color'    => sanitize_hex_color( $settings['buttonTextColor'] ) ? $settings['buttonTextColor'] : '#ffffff',
				'--bfts-font-size'       => absint( $settings['typographyFontSize'] ) . 'px',
				'--bfts-line-height'     => sanitize_text_field( $settings['typographyLineHeight'] ),
			)
		);
		$wrapper_attrs = $this->get_wrapper_attributes( $wrapper_class );

		wp_enqueue_style( 'blockforge-team-showcase-style' );

		if ( ! is_admin() ) {
			wp_enqueue_script( 'blockforge-team-showcase-view' );

			wp_localize_script(
				'blockforge-team-showcase-view',
				'WsTeamShowcase',
				array(
					'ajaxUrl' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
					'nonce'   => wp_create_nonce( 'blockforge_team_showcase_nonce' ),
					'i18n'    => array(
						'loading'  => __( 'Loading team members...', 'blockforge-team-showcase' ),
						'noResults' => __( 'No teammates found', 'blockforge-team-showcase' ),
						'search'   => __( 'Search teammates', 'blockforge-team-showcase' ),
						'loadMore' => __( 'Load More', 'blockforge-team-showcase' ),
						'close'    => __( 'Close', 'blockforge-team-showcase' ),
					),
				)
			);
		}

		ob_start();
		?>
		<section
			id="<?php echo esc_attr( $block_id ); ?>"
			<?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			style="<?php echo esc_attr( $wrapper_styles ); ?>"
			data-block-id="<?php echo esc_attr( $block_id ); ?>"
			data-members="<?php echo esc_attr( wp_json_encode( $members ) ); ?>"
			data-per-page="<?php echo esc_attr( $per_page ); ?>"
			data-popup-enabled="<?php echo esc_attr( $popup_enabled ? 'true' : 'false' ); ?>"
			data-image-position="<?php echo esc_attr( $settings['imagePosition'] ); ?>"
		>
			<div class="blockforge-team-showcase__toolbar">
				<div class="blockforge-team-showcase__filters" role="tablist" aria-label="<?php esc_attr_e( 'Filter team by department', 'blockforge-team-showcase' ); ?>">
					<button class="blockforge-team-showcase__filter is-active" type="button" data-department="">
						<?php esc_html_e( 'All', 'blockforge-team-showcase' ); ?>
					</button>
					<?php foreach ( $departments as $department_slug => $department_label ) : ?>
						<button class="blockforge-team-showcase__filter" type="button" data-department="<?php echo esc_attr( $department_slug ); ?>">
							<?php echo esc_html( $department_label ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="blockforge-team-showcase__toolbar-actions">
					<label class="blockforge-team-showcase__search">
						<span class="screen-reader-text"><?php esc_html_e( 'Search team members', 'blockforge-team-showcase' ); ?></span>
						<input type="search" class="blockforge-team-showcase__search-input" placeholder="<?php esc_attr_e( 'Search teammates', 'blockforge-team-showcase' ); ?>" />
					</label>
					<?php if ( Utils::to_bool( $settings['darkModeToggle'] ) ) : ?>
						<button
							type="button"
							class="blockforge-team-showcase__dark-toggle"
							aria-pressed="true"
							data-label="<?php echo esc_attr__( 'Dark Mode', 'blockforge-team-showcase' ); ?>"
							data-state-on="<?php echo esc_attr__( 'On', 'blockforge-team-showcase' ); ?>"
							data-state-off="<?php echo esc_attr__( 'Off', 'blockforge-team-showcase' ); ?>"
						>
							<span class="blockforge-team-showcase__dark-toggle-text">
								<span class="blockforge-team-showcase__dark-toggle-label"><?php esc_html_e( 'Dark Mode:', 'blockforge-team-showcase' ); ?></span>
								<span class="blockforge-team-showcase__dark-toggle-state"><?php esc_html_e( 'On', 'blockforge-team-showcase' ); ?></span>
							</span>
							<span class="blockforge-team-showcase__dark-toggle-switch" aria-hidden="true">
								<span class="blockforge-team-showcase__dark-toggle-knob"></span>
							</span>
						</button>
					<?php endif; ?>
				</div>
			</div>

			<div class="blockforge-team-showcase__skeleton" hidden>
				<?php for ( $i = 0; $i < min( 4, $per_page ); $i++ ) : ?>
					<div class="blockforge-team-showcase__skeleton-card"></div>
				<?php endfor; ?>
			</div>

			<div class="blockforge-team-showcase__grid" data-grid>
				<?php echo $this->render_members_markup( array_slice( $members, 0, $per_page ), $settings, $popup_enabled ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<?php if ( $enable_load ) : ?>
				<div class="blockforge-team-showcase__footer">
					<button
						type="button"
						class="blockforge-team-showcase__load-more"
						data-next-page="2"
						data-total="<?php echo esc_attr( $total_members ); ?>"
						data-current-department=""
						data-current-search=""
					>
						<?php esc_html_e( 'Load More', 'blockforge-team-showcase' ); ?>
					</button>
				</div>
			<?php endif; ?>

			<?php if ( $popup_enabled ) : ?>
				<div class="blockforge-team-showcase__modal" hidden aria-hidden="true">
					<div class="blockforge-team-showcase__modal-backdrop" data-close-modal></div>
					<div class="blockforge-team-showcase__modal-dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Team member details', 'blockforge-team-showcase' ); ?>">
						<button type="button" class="blockforge-team-showcase__modal-close" data-close-modal aria-label="<?php esc_attr_e( 'Close', 'blockforge-team-showcase' ); ?>">
							<span aria-hidden="true"><?php echo $this->get_close_icon_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</button>
						<div class="blockforge-team-showcase__modal-content"></div>
					</div>
				</div>
			<?php endif; ?>
		</section>
		<?php

		return (string) ob_get_clean();
	}

	/**
	 * Render team cards.
	 *
	 * @param array $members Members to display.
	 * @param array $settings Block settings.
	 * @param bool  $popup_enabled Whether modal mode is enabled.
	 * @return string
	 */
	public function render_members_markup( $members, $settings, $popup_enabled = true ) {
		ob_start();

		foreach ( $members as $index => $member ) :
			$card_classes = array(
				'blockforge-team-showcase__card',
				'has-hover-' . sanitize_html_class( $settings['hoverEffect'] ),
				'is-image-' . sanitize_html_class( $settings['imagePosition'] ),
			);
			?>
			<article
				class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>"
				data-member="<?php echo esc_attr( wp_json_encode( $member ) ); ?>"
				data-department="<?php echo esc_attr( sanitize_title( $member['department'] ) ); ?>"
				data-index="<?php echo esc_attr( $index ); ?>"
				tabindex="0"
			>
				<div class="blockforge-team-showcase__media">
					<?php if ( $member['profileImage'] ) : ?>
						<img src="<?php echo esc_url( $member['profileImage'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy" />
					<?php else : ?>
						<div class="blockforge-team-showcase__avatar-fallback" aria-hidden="true">
							<span><?php esc_html_e( 'No Image', 'blockforge-team-showcase' ); ?></span>
						</div>
					<?php endif; ?>
				</div>

				<div class="blockforge-team-showcase__content">
					<span class="blockforge-team-showcase__department"><?php echo esc_html( $member['department'] ); ?></span>
					<h3 class="blockforge-team-showcase__name"><?php echo esc_html( $member['name'] ); ?></h3>
					<p class="blockforge-team-showcase__bio"><?php echo esc_html( wp_trim_words( $member['shortBio'], 15, '...' ) ); ?></p>

					<?php if ( ! empty( $member['socialLinks'] ) ) : ?>
						<div class="blockforge-team-showcase__socials">
							<?php foreach ( $member['socialLinks'] as $social_link ) : ?>
								<a class="blockforge-team-showcase__social-link is-<?php echo esc_attr( $this->get_social_network_key( $social_link['network'] ) ); ?>" href="<?php echo esc_url( $social_link['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social_link['network'] ); ?>">
									<?php echo $this->get_social_icon_svg( $social_link['network'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $member['buttonText'] && $member['buttonUrl'] ) : ?>
						<a class="blockforge-team-showcase__button" href="<?php echo esc_url( $member['buttonUrl'] ); ?>">
							<?php echo esc_html( $member['buttonText'] ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $popup_enabled ) : ?>
						<button type="button" class="blockforge-team-showcase__details-trigger" data-open-modal>
							<?php esc_html_e( 'View Profile', 'blockforge-team-showcase' ); ?>
						</button>
					<?php endif; ?>
				</div>
			</article>
			<?php
		endforeach;

		return (string) ob_get_clean();
	}

	/**
	 * Default block attributes.
	 *
	 * @return array
	 */
	public function get_default_attributes() {
		return array(
			'teamMembers'          => array(),
			'gridColumns'          => 3,
			'itemsPerPage'         => 9,
			'cardAlignment'        => 'left',
			'cardHeight'           => '100%',
			'imagePosition'        => 'top',
			'popupToggle'          => true,
			'cardBackground'       => '#ffffff',
			'borderRadius'         => 24,
			'hoverEffect'          => 'lift',
			'buttonBackground'     => '#111827',
			'buttonTextColor'      => '#ffffff',
			'typographyFontSize'   => 16,
			'typographyLineHeight' => '1.6',
			'darkModeToggle'       => true,
			'acfFieldGroup'        => '',
		);
	}

	/**
	 * Build wrapper attributes without triggering block support warnings when no block render context exists.
	 *
	 * @param string $wrapper_class Wrapper class list.
	 * @return string
	 */
	private function get_wrapper_attributes( $wrapper_class ) {
		if (
			class_exists( '\WP_Block_Supports' ) &&
			is_array( \WP_Block_Supports::$block_to_render ) &&
			! empty( \WP_Block_Supports::$block_to_render['blockName'] )
		) {
			return get_block_wrapper_attributes(
				array(
					'class' => $wrapper_class,
				)
			);
		}

		return sprintf( 'class="%s"', esc_attr( $wrapper_class ) );
	}

	/**
	 * Get SVG markup for a social icon.
	 *
	 * @param string $network Social network label.
	 * @return string
	 */
	private function get_social_icon_svg( $network ) {
		$network = $this->get_social_network_key( $network );

		$icons = array(
			'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M13.5 22v-8h2.7l.4-3h-3.1V9.1c0-.9.3-1.6 1.7-1.6h1.5V4.8c-.3 0-1.1-.1-2.1-.1-2.1 0-3.5 1.3-3.5 3.7V11H8v3h2.4v8h3.1Z"/></svg>',
			'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M7.8 3h8.4A4.8 4.8 0 0 1 21 7.8v8.4a4.8 4.8 0 0 1-4.8 4.8H7.8A4.8 4.8 0 0 1 3 16.2V7.8A4.8 4.8 0 0 1 7.8 3Zm0 1.8A3 3 0 0 0 4.8 7.8v8.4a3 3 0 0 0 3 3h8.4a3 3 0 0 0 3-3V7.8a3 3 0 0 0-3-3H7.8Zm8.85 1.35a1.05 1.05 0 1 1 0 2.1 1.05 1.05 0 0 1 0-2.1ZM12 7.5A4.5 4.5 0 1 1 7.5 12 4.5 4.5 0 0 1 12 7.5Zm0 1.8A2.7 2.7 0 1 0 14.7 12 2.7 2.7 0 0 0 12 9.3Z"/></svg>',
			'linkedin'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.4 8.8H3.6V21h2.8V8.8ZM5 3A2 2 0 1 0 5 7a2 2 0 0 0 0-4Zm5.8 5.8H8.1V21h2.7v-6.4c0-1.7.3-3.3 2.4-3.3s2.1 2 2.1 3.4V21H18V14c0-3.4-.7-6-4.7-6-1.9 0-3.2 1-3.7 2.1h-.1V8.8Z"/></svg>',
			'twitter'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M18.9 3H22l-6.8 7.8L23 21h-6.1l-4.8-6.3L6.6 21H3.5l7.3-8.4L1 3h6.3l4.3 5.7L18.9 3Zm-1.1 16.1h1.7L6.4 4.8H4.6l13.2 14.3Z"/></svg>',
			'x'         => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M18.9 3H22l-6.8 7.8L23 21h-6.1l-4.8-6.3L6.6 21H3.5l7.3-8.4L1 3h6.3l4.3 5.7L18.9 3Zm-1.1 16.1h1.7L6.4 4.8H4.6l13.2 14.3Z"/></svg>',
			'wordpress' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm8.35 10a8.3 8.3 0 0 1-1.4 4.62l-2.99-8.19a2.93 2.93 0 0 0 1.7-.49A8.32 8.32 0 0 1 20.35 12ZM12 3.65a8.27 8.27 0 0 1 4.86 1.57h-.38c-.63 0-1.6.08-1.6.08a.26.26 0 0 0 .04.52s.52.04 1.07.08l1.59 4.35-2.38 7.12-3.95-11.47c.55-.04 1.04-.08 1.04-.08a.26.26 0 0 0 .04-.52s-1 .08-1.66.08c-.63 0-1.6-.08-1.6-.08a.26.26 0 0 0 .04.52s.52.04 1.07.08l2.36 6.47-.99 2.97-3.28-9.44c.55-.04 1.04-.08 1.04-.08a.26.26 0 0 0 .04-.52s-1 .08-1.66.08h-.32A8.29 8.29 0 0 1 12 3.65Zm-7.7 8.36a8.26 8.26 0 0 1 1.67-5l4.6 12.61A8.35 8.35 0 0 1 4.3 12Zm8.06 8.31a8.58 8.58 0 0 1-1.93-.22l2.05-5.95 2.1 5.75c.02.05.04.1.07.14a8.47 8.47 0 0 1-2.29.28Zm3.23-.65 2.09-6.04 1.07 2.93.02.08a8.35 8.35 0 0 1-3.18 3.03Z"/></svg>',
			'youtube'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M21.6 7.2a3 3 0 0 0-2.1-2.1C17.7 4.5 12 4.5 12 4.5s-5.7 0-7.5.6A3 3 0 0 0 2.4 7.2 31.7 31.7 0 0 0 1.8 12c0 1.6.2 3.2.6 4.8a3 3 0 0 0 2.1 2.1c1.8.6 7.5.6 7.5.6s5.7 0 7.5-.6a3 3 0 0 0 2.1-2.1c.4-1.6.6-3.2.6-4.8s-.2-3.2-.6-4.8ZM9.8 15.3V8.7l5.7 3.3-5.7 3.3Z"/></svg>',
		);

		if ( isset( $icons[ $network ] ) ) {
			return $icons[ $network ];
		}

		return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm6.9 9h-3.1a15.9 15.9 0 0 0-1.2-5 8.2 8.2 0 0 1 4.3 5Zm-6.9-6.2c.8 1.1 1.6 3.1 1.9 6.2h-3.8c.3-3.1 1.1-5.1 1.9-6.2ZM5.1 13h3.1a15.9 15.9 0 0 0 1.2 5 8.2 8.2 0 0 1-4.3-5Zm3.1-2H5.1a8.2 8.2 0 0 1 4.3-5 15.9 15.9 0 0 0-1.2 5Zm3.8 8.2c-.8-1.1-1.6-3.1-1.9-6.2h3.8c-.3 3.1-1.1 5.1-1.9 6.2Zm.4-8.2c-.3-2.5-.9-4.3-1.7-5.6.4-.1.8-.1 1.3-.1s.9 0 1.3.1c-.8 1.3-1.4 3.1-1.7 5.6Zm2.5 2h3.1a8.2 8.2 0 0 1-4.3 5 15.9 15.9 0 0 0 1.2-5Z"/></svg>';
	}

	/**
	 * Normalize a social network label for icon/color lookup.
	 *
	 * @param string $network Social network label.
	 * @return string
	 */
	private function get_social_network_key( $network ) {
		$key = strtolower( trim( (string) $network ) );
		$key = preg_replace( '/[^a-z0-9_-]+/', '-', $key );
		$key = trim( (string) $key, '-' );

		$aliases = array(
			'fb'          => 'facebook',
			'face-book'   => 'facebook',
			'insta'       => 'instagram',
			'linkedin-in' => 'linkedin',
			'linked-in'   => 'linkedin',
			'linkedincom' => 'linkedin',
			'twitter-x'   => 'x',
			'x-twitter'   => 'x',
			'wp'          => 'wordpress',
			'wp-org'      => 'wordpress',
			'word-press'  => 'wordpress',
			'you-tube'    => 'youtube',
		);

		return isset( $aliases[ $key ] ) ? $aliases[ $key ] : $key;
	}

	/**
	 * Get SVG markup for the modal close icon.
	 *
	 * @return string
	 */
	private function get_close_icon_svg() {
		return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="m13.4 12 5.3-5.3-1.4-1.4-5.3 5.3-5.3-5.3-1.4 1.4 5.3 5.3-5.3 5.3 1.4 1.4 5.3-5.3 5.3 5.3 1.4-1.4Z"/></svg>';
	}
}
