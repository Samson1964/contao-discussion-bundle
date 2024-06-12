<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Core
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Table tl_discussion_topics
 */
$GLOBALS['TL_DCA']['tl_discussion_topics'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_discussion_threads',
		'enableVersioning'            => true,
		'onsubmit_callback'           => array
		(
			array('tl_discussion_topics', 'saveRecord')
		),
		'sql' => array
		(
			'keys' => array
			(
				'id'        => 'primary',
				'pid'       => 'index',
				'title'     => 'index',
				'topicdate' => 'index'
			)
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('topicdate ASC'),
			'headerFields'            => array('title'),
			'panelLayout'             => 'search,limit',
			'disableGrouping'         => true,
			'child_record_callback'   => array('tl_discussion_topics', 'listRecords'),
			'child_record_class'      => 'no_padding',
			//'rootPaste'               => false
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			),
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_discussion_topics']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'copy' => array
			(
			    'label'               => &$GLOBALS['TL_LANG']['tl_discussion_topics']['copy'],
			    'href'                => 'act=paste&mode=copy',
			    'icon'                => 'copy.gif'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_discussion_topics']['cut'],
				'href'                => 'act=paste&mode=cut',
				'icon'                => 'cut.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_discussion_topics']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'                => &$GLOBALS['TL_LANG']['tl_discussion_topics']['toggle'],
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
				'label'               => &$GLOBALS['TL_LANG']['tl_discussion_topics']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{title_legend},title,name,email;{text_legend},text;{publish_legend},published'
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
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		// Erstellungsdatum, wird beim Topic-Posting gesetzt
		'topicdate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_topics']['topicdate'],
			'flag'                    => 5,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_topics']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'maxlength'=>128, 'tl_class'=>'long'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'text' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_topics']['text'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('tl_class'=>'clr'),
			'sql'                     => "mediumtext NULL"
		),
		// Benutzer-ID des Beitragserstellers (Falls leer, dann Gast)
		'userid' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_topics']['userid'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'tl_class'            => 'w50',
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		), 
		// Benutzer-Name des Beitragserstellers (Falls leer, dann Gast)
		'username' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_topics']['username'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(64) BINARY NULL"
		), 
		// Email des Antworterstellers
		'email' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_topics']['email'],
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
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_discussion_topics']['published'],
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
class tl_discussion_topics extends Backend
{

	/**
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

		if(!$dc->activeRecord->topicdate)
		{
			$zeit = time();

			// Erstellungszeitpunkt in der Antwort speichern
			$set = array
			(
				'topicdate'     => $zeit
			);
			$this->Database->prepare("UPDATE tl_discussion_topics %s WHERE id=?")
			               ->set($set)
			               ->execute($dc->id);

			// Erstellungszeitpunkt der Antwort im Thema speichern
			$set = array
			(
				'responsedate'  => $zeit
			);
			$this->Database->prepare("UPDATE tl_discussion_threads %s WHERE id=?")
			               ->set($set)
			               ->execute($dc->activeRecord->pid);

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
		$line .= date('d.m.Y H:i', $arrRow['topicdate']).' ';
		$line .= '<b>'.$arrRow['title'].'</b> ';
		$line .= '(<i>'.$arrRow['text'].'</i>)';
		$line .= "</div>";
		
		return $line;
	
	}
	
	/**
	 * Eintragsdatum schreiben
	 * @param mixed
	 * @return mixed
	 */
	public function saveInitdate($varValue)
	{
		if(!$varValue)
		{
			\System::log('[forum] New Topic created: '.\Input::post('title').' ('.\Input::post('url').')', __CLASS__.'::'.__FUNCTION__, TL_CRON);
			return time();
		}
		else
		{
			\System::log('[forum] Topic ID '.\Input::post('id').' edited: '.\Input::post('title').' ('.\Input::post('url').')', __CLASS__.'::'.__FUNCTION__, TL_ACCESS);
			return $varValue;
		}
	}

}

