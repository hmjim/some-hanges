<?php

namespace Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class DTag_Model extends Base_Form_Model {
	protected function template() { ?>
		<dtag-input <?= $this->attributes('v-model') ?>></dtag-input>
	<?php }
}
