<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   bdf
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2014
 */

// Standard-CSS einbinden

if(TL_MODE == 'FE') 
{
	$GLOBALS['TL_CSS'][] = 'bundles/contaodiscussion/css/default.css';
}

/**
 * Backend-Module
 */

$GLOBALS['BE_MOD']['content']['discussion'] = array
(
	'tables'         => array('tl_discussion', 'tl_discussion_threads', 'tl_discussion_topics'),
	'icon'           => 'bundles/contaodiscussion/images/icon.png',
);

/**
 * Frontend-Module
 */
$GLOBALS['FE_MOD']['application']['discussion'] = 'Schachbulle\ContaoDiscussionBundle\Modules\Discussion';  

