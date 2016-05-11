<?php
/**
 * @package   OSMap
 * @copyright 2007-2014 XMap - Joomla! Vargas. All rights reserved.
 * @copyright 2016 Open Source Training, LLC. All rights reserved..
 * @author    Guillermo Vargas <guille@vargas.co.cr>
 * @author    Alledia <support@alledia.com>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 * This file is part of OSMap.
 *
 * OSMap is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * OSMap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSMap. If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Alledia\Framework\Factory;

jimport('joomla.application.component.view');


/**
 * @package     OSMap
 * @subpackage  com_osmap
 * @since       2.0
 */
class OSMapViewSitemaps extends JViewLegacy
{
    /**
     * @var JRegistry
     */
    protected $state;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var JPagination
     */
    protected $pagination;

    public function display($tpl = null)
    {
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $message = $this->get('ExtensionsMessage');
        if ($message) {
            JFactory::getApplication()->enqueueMessage($message);
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));

            return false;
        }

        // We don't need toolbar or submenus in the modal window
        if ($this->getLayout() !== 'modal') {
            OSMapHelper::addSubmenu('sitemaps');
            $this->addToolbar();
        }

        // Load the extension
        $this->extension = Factory::getExtension('OSMap', 'component');
        $this->extension->loadLibrary();

        parent::display($tpl);
    }

    /**
     * Display the toolbar
     *
     * @access      protected
     */
    protected function addToolbar()
    {
        $state = $this->get('State');
        $doc   = JFactory::getDocument();

        $doc->addStyleDeclaration('.icon-48-sitemap {background-image: url(media/com_osmap/images/osmap-icon.png);}');

        JToolBarHelper::title(JText::_('COM_OSMAP_SITEMAPS_TITLE'), 'tree-2');
        JToolBarHelper::addNew('sitemap.add');
        JToolBarHelper::custom('sitemap.edit', 'edit.png', 'edit_f2.png', 'JTOOLBAR_EDIT', true);
        JToolBarHelper::custom('sitemaps.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_Publish', true);
        JToolBarHelper::custom('sitemaps.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
        JToolBarHelper::custom('sitemaps.setdefault', 'featured.png', 'featured_f2.png', 'COM_OSMAP_TOOLBAR_SET_DEFAULT', true);

        if ($state->get('filter.published') == -2) {
            JToolBarHelper::deleteList('', 'sitemaps.delete', 'JTOOLBAR_DELETE');
        } else {
            JToolBarHelper::trash('sitemaps.trash', 'JTOOLBAR_TRASH');
        }

        JToolBarHelper::divider();

        // Access check.
        if (JFactory::getUser()->authorise('core.admin', 'com_osmap')) {
            JToolbarHelper::preferences('com_osmap');
        }

        if (class_exists('JHtmlSidebar')) {
            JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_PUBLISHED'),
                'filter_published',
                JHtml::_(
                    'select.options',
                    JHtml::_('jgrid.publishedOptions'),
                    'value',
                    'text',
                    $this->state->get('filter.published'),
                    true
                )
            );

            JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_ACCESS'),
                'filter_access',
                JHtml::_(
                    'select.options',
                    JHtml::_('access.assetgroups'),
                    'value',
                    'text',
                    $this->state->get('filter.access')
                )
            );

            $this->sidebar = JHtmlSidebar::render();
        }
    }
}