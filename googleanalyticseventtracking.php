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

        $input = JFactory::getApplication()->input;
        if ($input->get('view') !== 'article') {
            // Only for article view. Not home, featured, category, etc
            return '';
        }

        // Load jQuery from Joomla Framework
        JHtml::_('jquery.framework');
        JFactory::getDocument()->addScript(JUri::base(true) . '/media/alligo/js/gaet.min.js');
        //var_dump($input->get('view'));

        //var_dump($context, $article, $params);

        $html = '<span data-ga-event="ready"';
        $html .= ' data-ga-category="' . $article->parent_route .'"';
        $html .= ' data-ga-action="ArticleView"';
        $html .= ' data-ga-label="' . (!empty($article->slug) ? $article->slug : ($article->id . ":Undefined")) . '"';
        $html .=  '><!-- PlgContentGoogleAnalyticsEventTracking --></span>';
        return $html;
	}

}
