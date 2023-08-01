<?php

namespace Voxel\Dynamic_Tags\Visibility_Rules;

if ( ! defined('ABSPATH') ) {
	exit;
}

class DTag_Rule extends Base_Visibility_Rule {

	public function get_type(): string {
		return 'dtag';
	}

	public function get_label(): string {
		return _x( 'Dynamic tag', 'visibility rules', 'voxel-backend' );
	}

	public function props(): array {
		return [
			'tag' => null,
			'compare' => null,
			'arguments' => null,
		];
	}

	public function evaluate(): bool {
		$tag = $this->props['tag'];
		if ( empty( $tag ) ) {
			return false;
		}

		$modifier = \Voxel\Dynamic_Tags\Dynamic_Tags::get_modifier_instance( $this->props['compare'] ?? '' );
		if ( ! ( $modifier && $modifier->get_type() === 'control-structure' ) ) {
			return false;
		}

		$arguments = is_array( $this->props['arguments'] ) ? join( ',', $this->props['arguments'] ) : '';
		$statement = sprintf( '%s.%s(%s).then(yes).else(no)', $tag, $modifier->get_key(), $arguments );
		$result = \Voxel\render( $statement );

		return $result === 'yes';
	}

	public function render_settings() {
		require locate_template( 'templates/dynamic-tags/_dtag-rule-settings.php' );
	}
}
