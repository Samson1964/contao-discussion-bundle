<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   fen
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2013
 */

/**
 * palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{discussion_legend:hide},discussion_defaultBoardImage,discussion_defaultBoardImageSize,discussion_defaultThreadImage,discussion_defaultThreadImageSize,discussion_defaultTopicImage,discussion_defaultTopicImageSize,discussion_fromName,discussion_fromMail,discussion_empfaenger';

/**
 * fields
 */

$GLOBALS['TL_DCA']['tl_settings']['fields']['discussion_defaultBoardImage'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['discussion_defaultBoardImage'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'tl_class'            => 'w50'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['discussion_defaultBoardImageSize'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['discussion_defaultBoardImageSize'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	'options_callback' => static function ()
	{
		return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
	},
); 

$GLOBALS['TL_DCA']['tl_settings']['fields']['discussion_defaultThreadImage'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['discussion_defaultThreadImage'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'tl_class'            => 'w50 clr'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['discussion_defaultThreadImageSize'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['discussion_defaultThreadImageSize'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	'options_callback' => static function ()
	{
		return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
	},
); 

$GLOBALS['TL_DCA']['tl_settings']['fields']['discussion_defaultTopicImage'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['discussion_defaultTopicImage'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'tl_class'            => 'w50 clr'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['discussion_defaultTopicImageSize'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['discussion_defaultTopicImageSize'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	'options_callback' => static function ()
	{
		return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
	},
); 

$GLOBALS['TL_DCA']['tl_settings']['fields']['discussion_fromName'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['discussion_fromName'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'tl_class'            => 'w50 clr',
	),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['discussion_fromMail'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['discussion_fromMail'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'tl_class'            => 'w50',
	),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['discussion_empfaenger'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['discussion_empfaenger'],
	'exclude'                 => true,
	'inputType'               => 'multiColumnWizard',
	'eval'                    => array
	(
		'tl_class'            => 'clr long',
		'buttonPos'           => 'top',
		'columnFields'        => array
		(
			'name' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_settings']['discussion_empfaenger_name'],
				'exclude'               => true,
				'inputType'             => 'text',
				'eval'                  => array
				(
					'style'             => 'width:90%',
					'valign'            => 'middle'
				)
			),
			'email' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_settings']['discussion_empfaenger_email'],
				'exclude'               => true,
				'inputType'             => 'text',
				'eval'                  => array
				(
					'style'             => 'width:90%',
					'valign'            => 'middle'
				),
			),
			'aktiv' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_settings']['discussion_empfaenger_aktiv'],
				'exclude'               => true,
				'inputType'             => 'checkbox',
				'eval'                  => array
				(
					'style'             => 'width:90%',
					'valign'            => 'middle'
				),
			),
		)
	),
);
