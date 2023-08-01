<?php

namespace Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Form_Model {

	protected $args = [];

	protected function __construct( $args ) {
		$this->args = array_merge( [
			'label' => '',
			'description' => '',
			'sublabel' => '',
			'footnote' => '',
			'required' => false,
			'width' => '1/1',
			'classes' => [],
			'v-model' => '',
			'v-if' => '',
			':class' => '',
		], $this->args );

		foreach ( $args as $key => $value ) {
			if ( array_key_exists( $key, $this->args ) ) {
				if ( is_array( $this->args[ $key ] ) ) {
					$this->args[ $key ] = $this->args[ $key ] + ((array) $value );
				} else {
					$this->args[ $key ] = $value;
				}
			}
		}

		$this->init();
	}

	abstract protected function template();

	protected function init() {
		//
	}

	protected function get_wrapper_classes(): string {
		return sprintf(
			'ts-form-group ts-col-%s %s',
			str_replace( '/', '-', $this->args['width'] ),
			join( ' ', $this->args['classes'] )
		);
	}

	protected function attr( $key, $as = null ): string {
		$as = is_string( $as ) ? $as : $key;
		if ( ! empty( $this->args[ $key ] ) || in_array( $this->args[ $key ], [ 0, '0', 0.0 ], true ) ) {
			return sprintf( '%s="%s"', esc_attr( $as ), esc_attr( $this->args[ $key ] ) );
		}

		return '';
	}

	protected function attributes(): string {
		$attributes = [];
		foreach ( func_get_args() as $key ) {
			$attributes[] = $this->attr( $key );
		}
		return join( ' ', $attributes );
	}

	public static function render( $args ): void {
		$input = new static( $args ); ?>
		<div class="<?= esc_attr( $input->get_wrapper_classes() ) ?>" <?= $input->attr('v-if') ?> <?= $input->attr(':class') ?>>
			<?php if ( $input->args['label'] || $input->args['description'] ): ?>
				<label>
					<?= $input->args['label'] ?>
					<?php if ( $description = $input->args['description']): ?>
						<span title="<?= esc_attr( $description ) ?>">[?]</span>
					<?php endif ?>
				</label>
			<?php endif ?>
			<?php if ( $input->args['sublabel'] ): ?>
				<p class="mt15"><?= $input->args['sublabel'] ?></p>
			<?php endif ?>
			<?php $input->template() ?>
			<?php if ( $input->args['footnote'] ): ?>
				<p><?= $input->args['footnote'] ?></p>
			<?php endif ?>
		</div>
	<?php }
}
