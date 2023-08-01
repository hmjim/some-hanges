<?php

namespace Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Icon_Model extends Base_Form_Model {

	protected function template() { ?>
		<icon-picker <?= $this->attributes('v-model') ?>></icon-picker>
	<?php }

}
