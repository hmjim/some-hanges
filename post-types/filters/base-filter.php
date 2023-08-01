<?php

namespace Voxel\Post_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Filter {
	use Traits\Model_Helpers;

	/**
	 * Post type object which this filter belongs to.
	 *
	 * @since 1.0
	 */
	protected $post_type;

	/**
	 * List of filter properties/configuration. Values below are available for
	 * all filter types, but there can be additional props for specific filter types.
	 *
	 * @since 1.0
	 */
	protected $props = [];

	/**
	 * Used to cache/memoize various method calls.
	 *
	 * @since 1.0
	 */
	protected $cache = [];

	protected $value;

	protected $elementor_config = [];

	protected $search_widget;

	protected $frontend_config;

	protected $resets_to = null;

	public function __construct( $props = [] ) {
		$this->props = array_merge( [
			'type' => 'keywords',
			'key' => '',
			'label' => '',
			'description' => '',
			'icon' => '',
		], $this->props );

		// override props if any provided as a parameter
		foreach ( $props as $key => $value ) {
			if ( array_key_exists( $key, $this->props ) ) {
				$this->props[ $key ] = $value;
			}
		}
	}

	abstract public function get_models(): array;

	public function setup( \Voxel\Post_Types\Index_Table $table ): void {
		//
	}

	public function index( \Voxel\Post $post ): array {
		return [];
	}

	public function query( \Voxel\Post_Types\Index_Query $query, array $args ): void {
		//
	}

	public function frontend_props() {
		return [];
	}

	public function parse_value( $value ) {
		return null;
	}

	public function get_frontend_config() {
		if ( $this->frontend_config === null ) {
			$is_valid_value = $this->parse_value( $this->get_value() ) !== null;

			$this->frontend_config = [
				'id' => sprintf( '%s.%s', $this->post_type->get_key(), $this->get_key() ),
				'type' => $this->get_type(),
				'key' => $this->get_key(),
				'label' => $this->get_label(),
				'description' => $this->get_description(),
				'icon' => \Voxel\get_icon_markup( $this->get_icon() ),
				'value' => $is_valid_value ? $this->get_value() : null,
				'props' => $this->frontend_props(),
				'resets_to' => $this->parse_value( $this->resets_to ),
			];
		}

		return $this->frontend_config;
	}

	public function reset_frontend_config() {
		$this->frontend_config = null;
	}

	public function is_ui() {
		return false;
	}

	public function get_elementor_controls(): array {
		return [];
	}

	public function get_default_value_from_elementor( $controls ) {
		return $controls['value'] ?? null;
	}

	public function resets_to( $value ) {
		$this->resets_to = $value;
	}

	/* Getters */
	public function get_type() {
		return $this->props['type'];
	}

	public function get_key() {
		return $this->props['key'];
	}

	public function db_key() {
		return '_'.$this->get_key();
	}

	public function get_label() {
		return $this->props['label'];
	}

	public function get_description() {
		return $this->props['description'];
	}

	public function get_icon() {
		return $this->props['icon'];
	}

	public function get_prop( $prop ) {
		if ( ! isset( $this->props[ $prop ] ) ) {
			return null;
		}

		return $this->props[ $prop ];
	}

	public function get_props() {
		return $this->props;
	}

	public function ssr( array $args ) {
		if ( $template = locate_template( sprintf( 'templates/widgets/search-form/ssr/%s-ssr.php', $this->get_type() ) ) ) {
			require $template;
			return;
		}

		$value = $this->parse_value( $this->get_value() );
		?>
		<div v-if="false" class="<?= $args['wrapper_class'] ?>">
			<?php if ( ! empty( $args['show_labels'] ) ): ?>
				<label><?= $this->get_label() ?></label>
			<?php endif ?>
			<div class="ts-filter ts-popup-target <?= $value ? 'ts-filled' : '' ?>">
				<span><?= \Voxel\get_icon_markup( $this->get_icon() ) ?></span>
				<div class="ts-filter-text"><?= $value ?? ( ! empty( $this->props['placeholder'] ) ? $this->props['placeholder'] : $this->get_label() ) ?></div>
			</div>
		</div>
	<?php }

	public function set_post_type( \Voxel\Post_Type $post_type ) {
		$this->post_type = $post_type;
	}

	public function get_value() {
		return $this->value;
	}

	public function set_value( $value ) {
		$this->value = $value;
	}

	public function set_elementor_config( $controls ) {
		$this->elementor_config = $controls;
	}

	public function set_search_widget( $widget ) {
		$this->search_widget = $widget;
	}
}
