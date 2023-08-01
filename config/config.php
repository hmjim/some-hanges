<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

return [
	'controllers'   => require_once locate_template( 'app/config/controllers.config.php' ),
	'post_types'    => require_once locate_template( 'app/config/post-types.config.php' ),
	'product_types' => require_once locate_template( 'app/config/product-types.config.php' ),
	'dynamic_tags'  => require_once locate_template( 'app/config/dynamic-tags.config.php' ),
	'assets'        => require_once locate_template( 'app/config/assets.config.php' ),
	'widgets'       => require_once locate_template( 'app/config/widgets.config.php' ),
];
