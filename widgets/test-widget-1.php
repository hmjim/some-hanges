<?php

namespace Voxel\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Test_Widget_1 extends Base_Widget {

	public function get_name() {
		return 'ts-test-widget-1';
	}

	public function get_title() {
		return __( '27 Test Widget 1', 'voxel-elementor' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'voxel', 'basic' ];
	}

	protected function register_controls() {
		//
	}

	protected function render( $instance = [] ) {
		require locate_template( 'templates/widgets/test-widget-1.php' );
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
