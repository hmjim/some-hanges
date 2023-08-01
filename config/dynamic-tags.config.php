<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

return [
	'groups' => apply_filters( 'voxel/dynamic-tags/groups', [
		'post' => \Voxel\Dynamic_Tags\Post_Group::class,
		'author' => \Voxel\Dynamic_Tags\Author_Group::class,
		'user' => \Voxel\Dynamic_Tags\User_Group::class,
		'site' => \Voxel\Dynamic_Tags\Site_Group::class,
		'term' => \Voxel\Dynamic_Tags\Term_Group::class,
	] ),

	'modifiers' => apply_filters( 'voxel/dynamic-tags/modifiers', [
		'append' => \Voxel\Dynamic_Tags\Modifiers\Append::class,
		'capitalize' => \Voxel\Dynamic_Tags\Modifiers\Capitalize::class,
		'date_format' => \Voxel\Dynamic_Tags\Modifiers\Date_Format::class,
		'time_diff' => \Voxel\Dynamic_Tags\Modifiers\Time_Diff::class,
		'to_age' => \Voxel\Dynamic_Tags\Modifiers\To_Age::class,
		'number_format' => \Voxel\Dynamic_Tags\Modifiers\Number_Format::class,
		'currency_format' => \Voxel\Dynamic_Tags\Modifiers\Currency_Format::class,
		'prepend' => \Voxel\Dynamic_Tags\Modifiers\Prepend::class,
		'fallback' => \Voxel\Dynamic_Tags\Modifiers\Fallback::class,
		'list' => \Voxel\Dynamic_Tags\Modifiers\List_Modifier::class,

		'then' => \Voxel\Dynamic_Tags\Control_Structures\Then_Block::class,
		'else' => \Voxel\Dynamic_Tags\Control_Structures\Else_Block::class,
		'is_empty' => \Voxel\Dynamic_Tags\Control_Structures\Is_Empty::class,
		'is_not_empty' => \Voxel\Dynamic_Tags\Control_Structures\Is_Not_Empty::class,
		'is_equal_to' => \Voxel\Dynamic_Tags\Control_Structures\Is_Equal_To::class,
		'is_not_equal_to' => \Voxel\Dynamic_Tags\Control_Structures\Is_Not_Equal_To::class,
		'contains' => \Voxel\Dynamic_Tags\Control_Structures\Contains::class,
		'is_greater_than' => \Voxel\Dynamic_Tags\Control_Structures\Is_Greater_Than::class,
		'is_less_than' => \Voxel\Dynamic_Tags\Control_Structures\Is_Less_Than::class,
		'is_checked' => \Voxel\Dynamic_Tags\Control_Structures\Is_Checked::class,
	] ),

	'visibility_rules' => apply_filters( 'voxel/dynamic-tags/visibility-rules', [
		'dtag' => \Voxel\Dynamic_Tags\Visibility_Rules\DTag_Rule::class,

		'user:logged_in' => \Voxel\Dynamic_Tags\Visibility_Rules\User_Is_Logged_In::class,
		'user:logged_out' => \Voxel\Dynamic_Tags\Visibility_Rules\User_Is_Logged_Out::class,
		'user:plan' => \Voxel\Dynamic_Tags\Visibility_Rules\User_Plan_Is::class,
		'user:role' => \Voxel\Dynamic_Tags\Visibility_Rules\User_Role_Is::class,
		'user:is_author' => \Voxel\Dynamic_Tags\Visibility_Rules\User_Is_Author::class,
		'user:can_create_post' => \Voxel\Dynamic_Tags\Visibility_Rules\User_Can_Create_Post::class,
		'user:can_edit_post' => \Voxel\Dynamic_Tags\Visibility_Rules\User_Can_Edit_Post::class,
		'user:is_verified' => \Voxel\Dynamic_Tags\Visibility_Rules\User_Is_Verified::class,

		'author:plan' => \Voxel\Dynamic_Tags\Visibility_Rules\Author_Plan_Is::class,
		'author:role' => \Voxel\Dynamic_Tags\Visibility_Rules\Author_Role_Is::class,
		'author:is_verified' => \Voxel\Dynamic_Tags\Visibility_Rules\Author_Is_Verified::class,

		'post:is_verified' => \Voxel\Dynamic_Tags\Visibility_Rules\Post_Is_Verified::class,
	] ),
];
