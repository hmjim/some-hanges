<?php

namespace Voxel\Dynamic_Tags;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Author_Group extends User_Group {

	public $key = 'author';
	public $label = 'Author';

	public $user;


	public function get_user() {
		if ( $this->user === null ) {
			$current_post = \Voxel\get_current_post();
			if ( $current_post && ( $author = $current_post->get_author() ) ) {
				$this->user = $author;
			} else {
				$this->user = \Voxel\User::dummy();
			}
		}

		return $this->user;
	}
}
