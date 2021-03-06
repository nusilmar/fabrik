<?php
/**
 * F3 Form Template: Group
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2015 fabrikar.com - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

?>
<ul>
<?php foreach ($this->elements as $element) { ?>
	<li <?php echo @$element->column;?> class="<?php echo $element->containerClass;?>">
		<?php echo $element->label;?>
		<?php echo $element->errorTag; ?>
		<div class="fabrikElement">
			<?php echo $element->element;?>
		</div>
		<div style="clear:both"></div>
	</li>
<?php }?>
</ul>
