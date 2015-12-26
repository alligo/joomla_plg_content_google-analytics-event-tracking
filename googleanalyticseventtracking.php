<?php
/**
 * @package    Alligo.PlgContentGoogleAnalyticsEventTracking
 * @author     Emerson Rocha Luiz <emerson@alligo.com.br>
 * @copyright  Copyright (C) 2015 Alligo Ltda. All rights reserved.
 * @license    GNU General Public License version 3. See license.txt
 */
defined('_JEXEC') or die;


/**
 * Content - Google Analytics Event Tracking from Alligo
 *
 * @package  Alligo.PlgContentGoogleAnalyticsEventTracking
 * @since    3.4
 */
class PlgContentGoogleAnalyticsEventTracking extends JPlugin
{

	/**
	 * Example before display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param	string		The context for the content passed to the plugin.
	 * @param	object		The content object.  Note $article->text is also available
	 * @param	object		The content params
	 * @param	int		The 'page' number
	 * @return	string
	 * @since	1.6
	 */
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart)
	{
		$document = JFactory::getDocument();

		//print_r($article);print_r($context);die; //com_content.article
		//Forçar execução apenas se não é página inicial
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		if ($menu->getActive() == $menu->getDefault())
		{
			return;
		}

		if (!isset($article->text) || !$article->text)
		{
			return;
		}

		//Get 'Global' params
		$u = JURI::getInstance();
		$url = $u->toString();
		$buttonsstyle = new stdClass();
		$buttonsstyle->size = $this->params->get('showbuttonsbig', 1); //1 big, 0 small
		$buttonsstyle->alignment = $this->params->get('showbuttonsleft', 1); //1 left, 0 rigth
		$buttonsstyle->showbuttonshorizoltal = $this->params->get('showbuttonshorizoltal', 1); //1 horizontal, 0 vertical
		$buttonsstyle->showbuttonscount = $this->params->get('showbuttonscount', 1); //1 show, 0 do not show
		$lang = JFactory::getLanguage();
		$buttonsstyle->lang = $lang->getTag();
		$buttonsstyle->lang_ = str_replace('-', '_', $buttonsstyle->lang);

		if ($this->params->get('showbuttonsafter', 1))
		{
			$article->text .= $this->_getSocialButtons($url, $buttonsstyle);
		}
		else
		{
			$article->text = $this->_getSocialButtons($url, $buttonsstyle) . $article->text;
		}

		if ($this->params->get('showfacebookcomments', 1))
		{
			if ($this->params->get('jsloadfacebookcomment', 1))
			{
				$document->addScript('http://connect.facebook.net/' . $buttonsstyle->lang_ . '/all.js#xfbml=1');
			}

			$article->text .= $this->_getFacebookComment($url); //Add after
		}

		// Add styles
		if ($this->params->get('cssloadsocializeme', 1))
		{
			$document->addStyleSheet(JURI::base(true) . '/plugins/content/socializeme/css/socializeme.css');
		}
		return;
	}

	/*
	 * Function to build the Socialize-me html output
	 * @return      String      HTML result for add to article
	 */

	protected function _getSocialButtons($url, $buttonsstyle)
	{

		$document = JFactory::getDocument();

		$socializemeMainClass = ( $buttonsstyle->size ) ? 'socializeme' : 'socializeme-small';
		$socializemeMainClass .= ( $buttonsstyle->alignment ) ? '-l' : '-r';
		$socializemeMainClass .= ( $buttonsstyle->showbuttonshorizoltal ) ? '-h' : '-v';

		$socializeme = '<div class="' . $socializemeMainClass . '">';

		if ($this->params->get('showgoogleplus', 1))
		{
			if ($this->params->get('jsloadgoogleplus', 1))
			{
				$document->addScript('https://apis.google.com/js/plusone.js');
			}
			$socializeme .= $this->_getGooglePlus($url, $buttonsstyle);
			;
		}
		if ($this->params->get('showtwittertweet', 1))
		{
			if ($this->params->get('jsloadtwitterbutton', 1))
			{
				$document->addScript('http://platform.twitter.com/widgets.js');
			}
			$socializeme .= $this->_getTwitterTwitterButton($url, $buttonsstyle);
		}
		if ($this->params->get('showfacebooklike', 1))
		{
			if ($this->params->get('jsloadfacebooklike', 1))
			{
				$document->addScript('http://connect.facebook.net/' . $buttonsstyle->lang_ . '/all.js#xfbml=1');
			}
			$socializeme .= $this->_getFacebookLike($url, $buttonsstyle);
		}
		$socializeme .= '</div>';

		return $socializeme;
	}

	/*
	 * Function to build the Facebook Comments html output
	 * @return      String      HTML result for add to article
	 */

	protected function _getFacebookComment($url)
	{

		$width = $this->params->get('widthfbcomments', '640px');

		$facebookComment = '<div id="fb-root"></div><fb:comments href="' . $url . '" num_posts="' . $num_posts . '" width="' . $width . '"></fb:comments>';

		return $facebookComment;
	}

	/*
	 * Function to build the Facebook Like html output
	 * @return      String      HTML result for add to article
	 */

	protected function _getFacebookLike($url, $buttonsstyle)
	{

		$datasize = ($buttonsstyle->size) ? 'data-size="tall"' : 'data-size="medium"';
		//$datacount = ($buttonsstyle->showbuttonscount) ? 'data-count="true"' : 'data-count="false"';
		if ($buttonsstyle->size == 1 && $buttonsstyle->showbuttonscount == 1)
		{
			$layout = 'box_count';
		}
		elseif ($buttonsstyle->size != 1 && $buttonsstyle->showbuttonscount == 1)
		{
			$layout = 'button_count';
		}
		else
		{
			$layout = 'standart';
		}
		$facebookLike = '<div class="sm-fblike"><iframe src="http://www.facebook.com/plugins/like.php?locale=' . $buttonsstyle->lang_ . '&amp;href=' . rawurlencode($url) . '&amp;layout=' . $layout . '&amp;show_faces=false&amp;action=like&amp;colorscheme=light"></iframe></div>';
		return $facebookLike;
	}

	/*
	 * Function to build the GooglePlus html output
	 * @return      String      HTML result for add to article
	 */

	protected function _getGooglePlus($url, $buttonsstyle)
	{
		$datasize = ($buttonsstyle->size) ? 'tall' : 'medium';
		$datacount = ($buttonsstyle->showbuttonscount) ? 'true' : 'false';
		$googleplus = '<div class="sm-gpb"><div class="g-plusone" data-size="' . $datasize . '" data-count="' . $datacount . '" data-lang="' . $buttonsstyle->lang . '"></div></div>';
		return $googleplus;
	}

	/*
	 * Function to build the TwitterButton html output
	 * @return      String      HTML result for add to article
	 */

	protected function _getTwitterTwitterButton($url, $buttonsstyle)
	{
		$tdata = '';

		if ($buttonsstyle->size == 1 && $buttonsstyle->showbuttonscount == 1)
		{
			$datacount = 'vertical';
		}
		elseif ($buttonsstyle->size != 1 && $buttonsstyle->showbuttonscount == 1)
		{
			$datacount = 'horizoltal';
		}
		else
		{
			$datacount = 'none';
		}

		if ($this->params->get('ttb-via', NULL))
		{
			$via = ' data-via="' . $this->params->get('ttb-via', NULL) . '"';
		}
		else
		{
			$via = NULL;
		}
		if ($this->params->get('data-related', NULL))
		{
			$related = ' data-related="' . $this->params->get('data-related', NULL) . '"';
		}
		else
		{
			$related = NULL;
		}

		$twitterbutton = '<div class="sm-ttb"><a href="http://twitter.com/share" class="twitter-share-button" data-url="' . $url . '" data-count="' . $datacount . '" data-lang="' . $buttonsstyle->lang . '"' . $via . $related . '>Twitter</a></div>';

		return $twitterbutton;
	}

}
