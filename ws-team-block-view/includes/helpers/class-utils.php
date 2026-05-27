<?php
/**
 * Shared helper methods.
 *
 * @package WsTeamShowcase
 */

namespace Ws\TeamShowcase\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Utility helper class.
 */
class Utils {

	/**
	 * Normalize a team member record.
	 *
	 * @param array $member Raw member payload.
	 * @return array
	 */
	public static function sanitize_member( $member ) {
		$defaults = array(
			'name'         => '',
			'designation'  => '',
			'department'   => '',
			'shortBio'     => '',
			'profileImage' => '',
			'experience'   => '',
			'buttonText'   => '',
			'buttonUrl'    => '',
			'socialLinks'  => array(),
		);

		$member = wp_parse_args( is_array( $member ) ? $member : array(), $defaults );

		$social_links = array();
		if ( is_array( $member['socialLinks'] ) ) {
			foreach ( $member['socialLinks'] as $social_link ) {
				$network = isset( $social_link['network'] ) ? sanitize_text_field( $social_link['network'] ) : '';
				$url     = isset( $social_link['url'] ) ? esc_url_raw( $social_link['url'] ) : '';

				if ( $network && $url ) {
					$social_links[] = array(
						'network' => $network,
						'url'     => $url,
					);
				}
			}
		}

		return array(
			'name'         => sanitize_text_field( $member['name'] ),
			'designation'  => sanitize_text_field( $member['designation'] ),
			'department'   => sanitize_text_field( $member['department'] ),
			'shortBio'     => sanitize_textarea_field( $member['shortBio'] ),
			'profileImage' => esc_url_raw( $member['profileImage'] ),
			'experience'   => sanitize_text_field( $member['experience'] ),
			'buttonText'   => sanitize_text_field( $member['buttonText'] ),
			'buttonUrl'    => esc_url_raw( $member['buttonUrl'] ),
			'socialLinks'  => $social_links,
		);
	}

	/**
	 * Normalize a members collection.
	 *
	 * @param mixed $members Member payload list.
	 * @return array
	 */
	public static function sanitize_members( $members ) {
		$sanitized = array();

		if ( ! is_array( $members ) ) {
			return $sanitized;
		}

		foreach ( $members as $member ) {
			$sanitized[] = self::sanitize_member( $member );
		}

		return $sanitized;
	}

	/**
	 * Normalize boolean-ish values.
	 *
	 * @param mixed $value Value to normalize.
	 * @return bool
	 */
	public static function to_bool( $value ) {
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Get unique department labels from members.
	 *
	 * @param array $members Member payload list.
	 * @return array
	 */
	public static function get_departments( $members ) {
		$departments = array();

		foreach ( $members as $member ) {
			if ( empty( $member['department'] ) ) {
				continue;
			}

			$departments[ sanitize_title( $member['department'] ) ] = $member['department'];
		}

		return $departments;
	}

	/**
	 * Filter members by department and search text.
	 *
	 * @param array  $members Members list.
	 * @param string $department Department filter.
	 * @param string $search Search filter.
	 * @return array
	 */
	public static function filter_members( $members, $department = '', $search = '' ) {
		return array_values(
			array_filter(
				$members,
				static function ( $member ) use ( $department, $search ) {
					$department_match = true;
					$search_match     = true;

					if ( $department ) {
						$department_match = sanitize_title( $member['department'] ) === sanitize_title( $department );
					}

					if ( $search ) {
						$haystack = strtolower(
							implode(
								' ',
								array(
									$member['name'],
									$member['designation'],
									$member['department'],
									$member['shortBio'],
								)
							)
						);

						$search_match = false !== strpos( $haystack, strtolower( $search ) );
					}

					return $department_match && $search_match;
				}
			)
		);
	}

	/**
	 * Build a safe inline style string.
	 *
	 * @param array $styles Property => value map.
	 * @return string
	 */
	public static function build_style_attr( $styles ) {
		$fragments = array();

		foreach ( $styles as $property => $value ) {
			if ( '' === $value || null === $value ) {
				continue;
			}

			$safe_property = preg_replace( '/[^a-zA-Z0-9\-_]/', '', (string) $property );

			if ( ! $safe_property ) {
				continue;
			}

			$fragments[] = $safe_property . ':' . wp_strip_all_tags( (string) $value );
		}

		return implode( ';', $fragments );
	}
}
