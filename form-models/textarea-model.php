<?php

namespace Voxel\Form_Models;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Textarea_Model extends Base_Form_Model {

	protected function template() { ?>
		<textarea <?= $this->attributes('v-model', 'required') ?>></textarea>
	<?php }

}
