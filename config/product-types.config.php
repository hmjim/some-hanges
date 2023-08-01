<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

return [
	'field_types' => apply_filters( 'voxel/product-types/field-types', [
		'text' => \Voxel\Product_Types\Information_Fields\Text_Field::class,
		'textarea' => \Voxel\Product_Types\Information_Fields\Textarea_Field::class,
		'number' => \Voxel\Product_Types\Information_Fields\Number_Field::class,
		'email' => \Voxel\Product_Types\Information_Fields\Email_Field::class,
		'phone' => \Voxel\Product_Types\Information_Fields\Phone_Field::class,
		'url' => \Voxel\Product_Types\Information_Fields\Url_Field::class,
		'switcher' => \Voxel\Product_Types\Information_Fields\Switcher_Field::class,
		'file' => \Voxel\Product_Types\Information_Fields\File_Field::class,
	] ),

	'addition_types' => apply_filters( 'voxel/product-types/addition-types', [
		'numeric' => \Voxel\Product_Types\Additions\Numeric_Addition::class,
		'checkbox' => \Voxel\Product_Types\Additions\Checkbox_Addition::class,
		'select' => \Voxel\Product_Types\Additions\Select_Addition::class,
	] ),
];
