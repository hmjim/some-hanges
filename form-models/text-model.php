<?php

namespace Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Text_Model extends Base_Form_Model {

	protected function template() { ?>
		<input type="text" <?= $this->attributes('v-model', 'required') ?>>
	<?php }

}
