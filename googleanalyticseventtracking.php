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

        // Only for article view. Not home, featured, category, etc
        if ($context !== 'com_content.article') {
            return '';
        }

        // Load jQuery from Joomla Framework
        JHtml::_('jquery.framework');
        //JFactory::getDocument()->addScript(JUri::base(true) . '/media/alligo/js/gaet.min.js', null, true, true);

        // @todo Olhar melhor a estratégia de sobrepor js/css (fititnt, 2015-12-26 19:11)
        // @see http://joomla.stackexchange.com/questions/3861/best-way-to-include-css-js-files-in-my-custom-extension
        // @see https://www.babdev.com/blog/139-use-the-media-folder-allow-overridable-media
        // @see hhttps://docs.joomla.org/Understanding_Output_Overrides#Media_Files_Override
        // Protip: You can override this file, placing a copy on /templates/yourtemplate/js/alligo/gaet.min.js
        JHtml::script('alligo/gaet.min.js', false, true, false);

        $html = '<span data-ga-event="ready"';
        $html .= ' data-ga-category="' . $article->parent_route .'"';
        $html .= ' data-ga-action="ArticleView"';
        $html .= ' data-ga-label="' . (!empty($article->slug) ? $article->slug : ($article->id . ":Undefined")) . '"';
        $html .=  '><!-- PlgContentGoogleAnalyticsEventTracking --></span>';
        return $html;
	}

}
