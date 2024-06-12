<?php
/**
 * Avatar for Contao Open Source CMS
 *
 * Copyright (C) 2013 Kirsten Roschanski
 * Copyright (C) 2013 Tristan Lins <http://bit3.de>
 *
 * @package    Avatar
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Add palette to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['discussion'] = '{title_legend},name,headline,type;{forum_legend},discussion_boards;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['discussion_boards'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['discussion_boards'],
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_module_discussion', 'getBoards'),
	'eval'                    => array('multiple'=>true, 'mandatory'=>true),
	'sql'                     => "blob NULL"
); 

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @internal
 */
class tl_module_discussion extends \Backend
{
	/**
	 * Get all news archives and return them as array
	 *
	 * @return array
	 */
	public function getBoards()
	{
		$arrBoards = array();
		$objBoards = \Database::getInstance()->execute("SELECT * FROM tl_discussion ORDER BY title ASC");

		while($objBoards->next())
		{
			$arrBoards[$objBoards->id] = $objBoards->title;
		}

		return $arrBoards;
	}

}
