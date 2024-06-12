<?php

namespace Schachbulle\ContaoDiscussionBundle\Modules;

/*
 */

class Discussion extends \Module
{

	protected $strTemplate = 'mod_discussion';
	
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### DISKUSSIONSFORUM ###';
			$objTemplate->title = $this->name;
			$objTemplate->id = $this->id;

			return $objTemplate->parse();
		}
		else
		{
			// FE-Modus: URL mit allen möglichen Parametern auflösen
			\Input::setGet('view', \Input::get('view')); // Ansichtsmodus: categories (Foren), threads (Themen), newthread (neues Thema)
			\Input::setGet('id', \Input::get('id')); // ID des Forums/Themas
		}

		$this->import('FrontendUser', 'User');

		return parent::generate(); // Weitermachen mit dem Modul
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;

		/*********************************************************
		** Mitglieder auslesen
		*/

		$objMembers = \Database::getInstance()->prepare('SELECT id, email, firstname, lastname, username FROM tl_member')
		                                      ->execute();

		if($objMembers->numRows > 0)
		{
			// Datensätze auswerten
			while($objMembers->next())
			{
				$this->member[$objMembers->id] = array
				(
					'email'     => $objMembers->email,
					'firstname' => $objMembers->firstname,
					'lastname'  => $objMembers->lastname,
					'username'  => $objMembers->username,
				);
			}
		}

		/*********************************************************
		** Ansichtsmodus
		*/

		$ebene = 1;
		if(\Input::get('view'))
		{
			switch(\Input::get('view'))
			{
				case 'forum': // Forum anzeigen
					self::ViewThreads();
					self::ViewThreadForm();
					$ebene = 2;
					break;
				case 'thread': // Thema anzeigen
					self::ViewTopics();
					self::ViewTopicForm();
					$ebene = 3;
					break;
				default: // Keine Parameter in der URL
					self::ViewBoards();
			}
		}
		else
		{
			// Keine Ansicht ausgewählt, deshalb Startseite anzeigen
			self::ViewBoards();
		}

		self::Breadcrumb($ebene); // Navigationsleiste erzeugen
		return;

	}

	/***********************************************************************************
	 * Funktion Breadcrumb
	 * Erzeugt eine Navigationsleiste
	 ***********************************************************************************/
	protected function Breadcrumb($ebene)
	{
		global $objPage;
		$breadcrumb = '';

		if($ebene == 1)
		{
			// Forum auf höchster Ebene, also Standardansicht
			$breadcrumb = 'Startseite';
		}
		elseif($ebene == 2)
		{
			// Forum auf Themenebene
			$breadcrumb = '<a href="'.\Controller::generateFrontendUrl($objPage->row()).'">Startseite</a>';
			// Name des Forums ermitteln
			$objBoard = \Database::getInstance()->prepare('SELECT * FROM tl_discussion WHERE id = ?')
			                                    ->execute(\Input::get('id'));
			$breadcrumb .= ' &#10141; '.($objBoard->title_published ? $objBoard->title_published : $objBoard->title);
		}
		elseif($ebene == 3)
		{
			// Forum auf Antwortenebene
			$breadcrumb = '<a href="'.\Controller::generateFrontendUrl($objPage->row()).'">Startseite</a>';
			// Name des Themas ermitteln
			$objThread = \Database::getInstance()->prepare('SELECT * FROM tl_discussion_threads WHERE id = ?')
			                                     ->execute(\Input::get('id'));
			// Name des Forums ermitteln
			$objBoard = \Database::getInstance()->prepare('SELECT * FROM tl_discussion WHERE id = ?')
			                                    ->execute($objThread->pid);
			$breadcrumb .= ' &#10141; <a href="'.\Controller::generateFrontendUrl($objPage->row(), '/view/forum/id/'.$objBoard->id).'">'.($objBoard->title_published ? $objBoard->title_published : $objBoard->title).'</a>';
			$breadcrumb .= ' &#10141; '.$objThread->title;
		}
		$this->Template->breadcrumb = $breadcrumb;
	}
	
	/***********************************************************************************
	 * Funktion ViewBoards
	 * Foren anzeigen bzw. Startseite (Forenübersicht)
	 ***********************************************************************************/
	protected function ViewBoards()
	{
		global $objPage;

		// Gewünschte Foren laden
		$viewboards = unserialize($this->discussion_boards);
		$boards = array();

		// Standardbild und Bildgröße ermitteln
		$bildgroesse = unserialize(@$GLOBALS['TL_CONFIG']['discussion_defaultBoardImageSize']);
		$standardbild = @$GLOBALS['TL_CONFIG']['discussion_defaultBoardImage'];

		if(is_array($viewboards))
		{
			foreach($viewboards as $board_id)
			{
				// Forum laden wenn veröffentlicht
				$objBoard = \Database::getInstance()->prepare('SELECT * FROM tl_discussion WHERE published = ? AND id = ?')
				                                    ->execute(1, $board_id);
				if($objBoard->numRows)
				{

					// Bild extrahieren
					if($objBoard->singleSRC)
					{
						// Foto aus der Datenbank
						$objFile = \FilesModel::findByPk($objBoard->singleSRC);
						if(!$objFile)
						{
							// Model findet keine gültige Datei
							log_message('Kein gültiges Bild gefunden auf Seite '.$objPage->alias.': '.print_r($objBoard->singleSRC, true), 'discussion.log');
							// Deshalb Standardfoto verwenden
							$objFile = \FilesModel::findByUuid($standardbild);
						}
					}
					else
					{
						// Standardfoto
						$objFile = \FilesModel::findByUuid($standardbild);
					}
					$objBild = new \stdClass();
					\Controller::addImageToTemplate($objBild, array('singleSRC' => $objFile->path, 'size' => $bildgroesse), \Config::get('maxImageWidth'), null, $objFile);

					$boards[] = array
					(
						'title'       => $objBoard->title_published ? $objBoard->title_published : $objBoard->title,
						'url'         => \Controller::generateFrontendUrl($objPage->row(), '/view/forum/id/'.$objBoard->id),
						'image'       => '<img src="'.$objBild->src.'">',
						'description' => $objBoard->description,
					);
				}
			}
		}

		// Template füllen
		$this->Template->mode = 'boards';
		$this->Template->boards = $boards;
	}

	/***********************************************************************************
	 * Funktion ViewThreads
	 * Themen eines Forums anzeigen (Themenübersicht)
	 ***********************************************************************************/
	protected function ViewThreads()
	{
		global $objPage;

		// Themen laden wenn veröffentlicht
		$threads = array();
		$objThreads = \Database::getInstance()->prepare('SELECT * FROM tl_discussion_threads WHERE published = ? AND pid = ? ORDER BY responsedate DESC')
		                                      ->execute(1, \Input::get('id'));

		if($objThreads->numRows)
		{
			while($objThreads->next())
			{
				$threads[] = array
				(
					'title'       => $objThreads->title,
					'url'         => \Controller::generateFrontendUrl($objPage->row(), '/view/thread/id/'.$objThreads->id),
					'answers'     => $objThreads->answers,
					'hits'        => $objThreads->hits,
					'last'        => date('d.m.Y H:i', $objThreads->responsedate),
				);
			}
		}

		// Template füllen
		$this->Template->mode = 'threads';
		$this->Template->threads = $threads;
	}

	/***********************************************************************************
	 * Funktion ViewThreadForm
	 * Formular für neues Thema anzeigen
	 ***********************************************************************************/
	protected function ViewThreadForm()
	{
		// Der 1. Parameter ist die Formular-ID (hier "linkform")
		// Der 2. Parameter ist GET oder POST
		// Der 3. Parameter ist eine Funktion, die entscheidet wann das Formular gesendet wird (Third is a callable that decides when your form is submitted)
		// Der optionale 4. Parameter legt fest, ob das ausgegebene Formular auf Tabellen basiert (true)
		// oder nicht (false) (You can pass an optional fourth parameter (true by default) to turn the form into a table based one)
		$objForm = new \Codefog\HasteBundle\Form\Form('newthreadForm', 'POST', function($objHaste)
		{
			return \Input::post('FORM_SUBMIT') === $objHaste->getFormId();
		});
		
		// URL für action festlegen. Standard ist die Seite auf der das Formular eingebunden ist.
		//$objForm->setFormActionFromUri(\Controller::generateFrontendUrl($objPage->row(), '/category/'.\Input::get('category')));
		//$objForm->setFormActionFromUri(\Controller::generateFrontendUrl($objPage->row()));

		$objForm->addFormField('forum', array(
			'inputType'     => 'hidden',
			'value'         => \Input::get('id')
		));
		$objForm->addFormField('title', array(
			'label'         => 'Titel',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>true, 'class'=>'form-control')
		));
		$objForm->addFormField('text', array(
			'label'         => 'Text',
			'inputType'     => 'textarea',
			'eval'          => array('mandatory'=>true, 'rte'=>'tinyMCE', 'class'=>'form-control')
		));
		// Submit-Button hinzufügen
		$objForm->addFormField('submit', array(
			'label'         => 'Absenden',
			'inputType'     => 'submit',
			'eval'          => array('class'=>'btn btn-primary')
		));
		$objForm->addCaptchaFormField('captcha');
		
		// validate() prüft auch, ob das Formular gesendet wurde
		if($objForm->validate())
		{
			// Alle gesendeten und analysierten Daten holen (funktioniert nur mit POST)
			$arrData = $objForm->fetchAll();
			self::SaveThread($arrData); // Daten sichern
			// Seite neu laden
			\Controller::addToUrl('send=1'); // Hat keine Auswirkung, verhindert aber das das Formular ausgefüllt ist
			\Controller::reload(); 
		}

		// Formular als String zurückgeben
		$this->Template->form = $objForm->generate();
	}

	protected function SaveThread($data)
	{
		// Datenbank aktualisieren
		$zeit = time();
		$data['title'] = html_entity_decode($data['title']);
		$data['text'] = html_entity_decode($data['text']);

		// Threads-Tabelle aktualisieren
		$set = array
		(
			'pid'          => $data['forum'],
			'tstamp'       => $zeit,
			'initdate'     => $zeit,
			'responsedate' => $zeit,
			'userid'       => $this->User->id ? $this->User->id : 0,
			'author_name'  => $this->User->username ? $this->User->username : '',
			'title'        => $data['title'],
			'published'    => 1,
		);
		$objThread = \Database::getInstance()->prepare('INSERT INTO tl_discussion_threads %s')
		                                     ->set($set)
		                                     ->execute();
		$insertId = $objThread->insertId;

		// Topics-Tabelle aktualisieren
		$set = array
		(
			'pid'       => $insertId,
			'tstamp'    => $zeit,
			'topicdate' => $zeit,
			'userid'    => $this->User->id ? $this->User->id : 0,
			'username'  => $this->User->username,
			'title'     => $data['title'],
			'text'      => $data['text'],
			'published' => 1,
		);
		$objTopic = \Database::getInstance()->prepare('INSERT INTO tl_discussion_topics %s')
		                                    ->set($set)
		                                    ->execute();

	}
	
	/***********************************************************************************
	 * Funktion ViewTopics
	 * Einträge eines Themas anzeigen (Antwortenübersicht)
	 ***********************************************************************************/
	protected function ViewTopics()
	{
		global $objPage;

		// Themen laden wenn veröffentlicht
		$topics = array();
		$objTopics = \Database::getInstance()->prepare('SELECT * FROM tl_discussion_topics WHERE published = ? AND pid = ? ORDER BY topicdate ASC')
		                                     ->execute(1, \Input::get('id'));

		if($objTopics->numRows)
		{
			while($objTopics->next())
			{
				$topics[] = array
				(
					'date'        => date('d.m.Y H:i', $objTopics->topicdate),
					'user'        => $objTopics->username ? $objTopics->username : '<i>Gast</i>',
					'title'       => $objTopics->title,
					'text'        => nl2br($objTopics->text),
				);
			}
		}

		$objThread = \Database::getInstance()->prepare('SELECT * FROM tl_discussion_threads WHERE id = ?')
		                                     ->execute(\Input::get('id'));
		$ip = $_SERVER['REMOTE_ADDR'];
		if($ip != $objThread->last_ip)
		{
			// Hits nur hochzählen, wenn IP-Adresse unterschiedlich
			$set = array
			(
				'last_ip'        => $ip,
				'hits'           => ($objThread->hits+1),
			);
			$objThread = \Database::getInstance()->prepare('UPDATE tl_discussion_threads %s WHERE id = ?')
			                                     ->set($set)
			                                     ->execute(\Input::get('id'));
		}

		// Template füllen
		$this->Template->mode = 'topics';
		$this->Template->thread = $objThread->title;
		$this->Template->topics = $topics;
	}

	/***********************************************************************************
	 * Funktion ViewTopicForm
	 * Formular für neues Thema anzeigen
	 ***********************************************************************************/
	protected function ViewTopicForm()
	{
		// Der 1. Parameter ist die Formular-ID (hier "linkform")
		// Der 2. Parameter ist GET oder POST
		// Der 3. Parameter ist eine Funktion, die entscheidet wann das Formular gesendet wird (Third is a callable that decides when your form is submitted)
		// Der optionale 4. Parameter legt fest, ob das ausgegebene Formular auf Tabellen basiert (true)
		// oder nicht (false) (You can pass an optional fourth parameter (true by default) to turn the form into a table based one)
		$objForm = new \Codefog\HasteBundle\Form\Form('newtopicForm', 'POST', function($objHaste)
		{
			return \Input::post('FORM_SUBMIT') === $objHaste->getFormId();
		});
		
		// URL für action festlegen. Standard ist die Seite auf der das Formular eingebunden ist.
		//$objForm->setFormActionFromUri(\Controller::generateFrontendUrl($objPage->row(), '/category/'.\Input::get('category')));
		//$objForm->setFormActionFromUri(\Controller::generateFrontendUrl($objPage->row()));

		$objForm->addFormField('thread', array(
			'inputType'     => 'hidden',
			'value'         => \Input::get('id')
		));
		$objForm->addFormField('title', array(
			'label'         => 'Titel',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>false, 'class'=>'form-control')
		));
		$objForm->addFormField('text', array(
			'label'         => 'Text',
			'inputType'     => 'textarea',
			'eval'          => array('mandatory'=>true, 'rte'=>'tinyMCE', 'class'=>'form-control')
		));
		// Submit-Button hinzufügen
		$objForm->addFormField('submit', array(
			'label'         => 'Absenden',
			'inputType'     => 'submit',
			'eval'          => array('class'=>'btn btn-primary')
		));
		$objForm->addCaptchaFormField('captcha');
		
		// validate() prüft auch, ob das Formular gesendet wurde
		if($objForm->validate())
		{
			// Alle gesendeten und analysierten Daten holen (funktioniert nur mit POST)
			$arrData = $objForm->fetchAll();
			self::SaveTopic($arrData); // Daten sichern
			// Seite neu laden
			\Controller::addToUrl('send=1'); // Hat keine Auswirkung, verhindert aber das das Formular ausgefüllt ist
			\Controller::reload(); 
		}

		// Formular als String zurückgeben
		$this->Template->form = $objForm->generate();
	}
	
	protected function SaveTopic($data)
	{
		$zeit = time();
		$data['title'] = html_entity_decode($data['title']);
		$data['text'] = html_entity_decode($data['text']);

		// Thread laden
		$objThread = \Database::getInstance()->prepare('SELECT * FROM tl_discussion_threads WHERE id = ?')
		                                     ->execute($data['thread']);

		// Threads-Tabelle aktualisieren
		$set = array
		(
			'tstamp'         => $zeit,
			'responsedate'   => $zeit,
			'answers'        => ($objThread->answers + 1),
			'actname'        => $this->User->id ? $this->User->id : 0,
		);
		$objThread = \Database::getInstance()->prepare('UPDATE tl_discussion_threads %s WHERE id = ?')
		                                     ->set($set)
		                                     ->execute($data['thread']);

		// Topics-Tabelle aktualisieren
		$set = array
		(
			'pid'       => $data['thread'],
			'tstamp'    => $zeit,
			'topicdate' => $zeit,
			'userid'    => $this->User->id ? $this->User->id : 0,
			'username'  => $this->User->username,
			'title'     => $data['title'],
			'text'      => $data['text'],
			'published' => 1,
		);
		$objTopic = \Database::getInstance()->prepare('INSERT INTO tl_discussion_topics %s')
		                                    ->set($set)
		                                    ->execute();

	}

	static function showBBcodes($text)
	{
		// BBcode array
		$find = array(
			'~\[b\](.*?)\[/b\]~s',
			'~\[i\](.*?)\[/i\]~s',
			'~\[u\](.*?)\[/u\]~s',
			'~\[quote\](.*?)\[/quote\]~s',
			'~\[size=(.*?)\](.*?)\[/size\]~s',
			'~\[color=(.*?)\](.*?)\[/color\]~s',
			'~\[url\]((?:ftp|https?)://.*?)\[/url\]~s',
			'~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s',
			'~\[img\](.*?)\[/img\]~s',
		);
		// HTML tags to replace BBcode
		$replace = array(
			'<b>$1</b>',
			'<i>$1</i>',
			'<span style="text-decoration:underline;">$1</span>',
			'<pre>$1</'.'pre>',
			'<span style="font-size:$1px;">$2</span>',
			'<span style="color:$1;">$2</span>',
			'<a href="$1">$1</a>',
			'<img src="$1" alt="" />',
			'<img src="$1" alt="" />'
		);
		// Replacing the BBcodes with corresponding HTML tags
		return preg_replace($find,$replace,$text);
	}

}
