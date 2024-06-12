<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package News
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Table tl_discussion_threads
 */
$GLOBALS['TL_DCA']['tl_discussion_threads'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_discussion',
		'ctable'                      => array('tl_discussion_topics'),
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'onsubmit_callback'           => array
		(
			array('tl_discussion_threads', 'saveRecord')
		),
		'sql' => array
		(
			'keys' => array
			(
				'id'             => 'primary',
				'pid'            => 'index',
				'title'          => 'index',
				'responsedate'   => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('responsedate'),
			'headerFields'            => array('title', 'title_published', 'admin_name', 'host_name'),
			'panelLayout'             => 'filter;sort,search,limit',
			'disableGrouping'         => true,
			'child_record_callback'   => array('tl_discussion_threads', 'listRecords'),
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_discussion_threads']['edit'],
				'href'                => 'table=tl_discussion_topics',
				'icon'                => 'edit.gif'
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_discussion_threads']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif',
			), 
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_discussion_threads']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_discussion_threads']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_discussion_threads']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_discussion_threads']['toggle'],
				'attributes'           => 'onclick="Backend.getScrollOffset()"',
				'haste_ajax_operation' => array
				(
					'field'            => 'published',
					'options'          => array
					(
						array('value' => '', 'icon' => 'invisible.svg'),
						array('value' => '1', 'icon' => 'visible.svg'),
					),
				),
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_discussion_threads']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{title_legend},title;{author_legend},author_id,author_name,author_email;{text_legend},text;{publish_legend},published'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_discussion.title',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		// Erstellungsdatum, wird beim Anlegen des Themas gesetzt
		'initdate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_threads']['initdate'],
			'flag'                    => 5,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		), 
		// Aktualisierungsdatum, wird bei einer Antwort gesetzt
		'responsedate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_threads']['responsedate'],
			'flag'                    => 12,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		), 
		// ID des Mitglieds mit der letzten Antwort
		'actname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_threads']['actname'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'foreignKey'              => 'tl_member.username',
			'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'choosen'=>true),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		), 
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_threads']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>128, 'tl_class'=>'long'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		// Benutzer-ID des Threaderstellers (Falls leer, dann Gast)
		'userid' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_threads']['userid'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'tl_class'            => 'w50',
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		), 
		// Benutzer-Name des Threaderstellers (Falls leer, dann Gast)
		'author_name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_threads']['author_name'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(128) NOT NULL default ''"
		), 
		// Email des Threaderstellers
		'author_email' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_threads']['author_email'],
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'rgxp'                => 'emails', 
				'decodeEntities'      => true, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(128) NOT NULL default ''"
		), 
		// Anzahl der Antworten
		'answers' => array
		(
			'exclude'                 => true,
			'inputType'               => 'text',
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		), 
		// Letzte IP, die zugegriffen hat (wichtig für hits)
		'last_ip' => array
		(
			'exclude'                 => true,
			'inputType'               => 'text',
			'sql'                     => "varchar(128) NOT NULL default ''"
		), 
		// Anzahl der Zugriffe
		'hits' => array
		(
			'exclude'                 => true,
			'inputType'               => 'text',
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		), 
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_threads']['published'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'default'                 => 1,
			'eval'                    => array('doNotCopy'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		), 
	)
);

/**
 * Provide miscellaneous methods that are used by the data configuration array
 */
class tl_discussion_threads extends \Backend
{

	/**
	 * Funktion saveRecord (onsubmit_callback: Wird beim Abschicken des Backend-Formulars ausgeführt)
	 * Beim Speichern eines Datensatzes zusätzliche Änderungen vornehmen
	 * @param DataContainer
	 * @return -
	 */
	public function saveRecord(DataContainer $dc)
	{
		// Frontend-Aufruf
		if(!$dc instanceof DataContainer)
		{
			return;
		}

		// Zurück, wenn kein aktiver Datensatz vorhanden ist
		if(!$dc->activeRecord)
		{
			return;
		}

		if(!$dc->activeRecord->initdate)
		{
			// Eröffnungszeitpunkt im Thema speichern
			$zeit = time();
			$set = array
			(
				'initdate'     => $zeit,
				'responsedate' => $zeit
			);
			$this->Database->prepare("UPDATE tl_forum_threads %s WHERE id=?")
			               ->set($set)
			               ->execute($dc->id);
			return;
		}

	}
	
	/**
	 * Generiere eine Zeile als HTML
	 * @param array
	 * @return string
	 */
	public function listRecords($arrRow)
	{
		static $class;
		$class == 'odd' ? 'even' : 'odd';
		
		$line = '';
		$line .= '<div class="tl_content_left '.$class.'">';
		$line .= date('d.m.Y H:i', $arrRow['responsedate']).' ';
		$line .= '<b>'.$arrRow['title'].'</b>';
		$line .= "</div>";
		
		return $line;
	
	}
	
}
