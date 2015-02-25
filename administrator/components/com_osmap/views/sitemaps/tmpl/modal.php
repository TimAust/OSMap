<?php
/**
 * @package   OSMap
 * @copyright 2007-2014 XMap - Joomla! Vargas. All rights reserved.
 * @copyright 2015 Alledia.com, All rights reserved.
 * @author    Guillermo Vargas <guille@vargas.co.cr>
 * @author    Alledia <support@alledia.com>
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * This file is part of OSMap.
 *
 * OSMap is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

$function = JRequest::getVar('function', 'jSelectSitemap');
$n = count($this->items);
?>
<form action="<?php echo JRoute::_('index.php?option=com_osmap&view=sitemaps');?>" method="post" name="adminForm">
    <fieldset class="filter clearfix">
        <div class="left">
            <label for="search">
                <?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
            </label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" size="60" title="<?php echo JText::_('OSMAP_FILTER_SEARCH_DESC'); ?>" />

            <button type="submit">
                <?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="$('filter_search').value='';this.form.submit();">
                <?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>

        <div class="right">
            <select name="filter_access" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
            </select>

            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
            </select>

        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th class="title">
                    <?php echo JHtml::_('grid.sort', 'OSMap_Heading_Sitemap', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'OSMap_Heading_Published', 'a.state', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
                </th>
                <th width="10%">
                    <?php echo JHtml::_('grid.sort',  'JGrid_Heading_Access', 'access_level', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
                </th>
                <th width="10%" nowrap="nowrap">
                    <?php echo JText::_('OSMAP_HEADING_HTML_STATS'); ?><br />
                    (<?php echo JText::_('OSMAP_HEADING_NUM_LINKS') . ' / '. JText::_('OSMAP_HEADING_NUM_HITS') . ' / ' . JText::_('OSMAP_HEADING_LAST_VISIT'); ?>)
                </th>
                <th width="10%" nowrap="nowrap">
                    <?php echo JText::_('OSMAP_HEADING_XML_STATS'); ?><br />
                    <?php echo JText::_('OSMAP_HEADING_NUM_LINKS') . '/'. JText::_('OSMAP_HEADING_NUM_HITS') . '/' . JText::_('OSMAP_HEADING_LAST_VISIT'); ?>
                </th>
                <th width="1%" nowrap="nowrap">
                    <?php echo JHtml::_('grid.sort', 'JGrid_Heading_ID', 'a.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="15">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        foreach ($this->items as $i => $item) :

            $now = JFactory::getDate()->toUnix();
            if ( !$item->lastvisit_html ) {
                $htmlDate = JText::_('DATE_NEVER');
            }elseif ( $item->lastvisit_html > ($now-3600)) { // Less than one hour
                $htmlDate = JText::sprintf('Date_Minutes_Ago',intval(($now-$item->lastvisit_html)/60));
            } elseif ( $item->lastvisit_html > ($now-86400)) { // Less than one day
                $hours = intval (($now-$item->lastvisit_html)/3600 );
                $htmlDate = JText::sprintf('Date_Hours_Minutes_Ago',$hours,($now-($hours*3600)-$item->lastvisit_html)/60);
            } elseif ( $item->lastvisit_html > ($now-259200)) { // Less than three days
                $days = intval(($now-$item->lastvisit_html)/86400);
                $htmlDate = JText::sprintf('Date_Days_Hours_Ago',$days,intval(($now-($days*86400)-$item->lastvisit_html)/3600));
            } else {
                $date = new JDate($item->lastvisit_html);
                $htmlDate = $date->toFormat('%Y-%m-%d %H:%M');
            }

            if ( !$item->lastvisit_xml ) {
                $xmlDate = JText::_('DATE_NEVER');
            } elseif ( $item->lastvisit_xml > ($now-3600)) { // Less than one hour
                $xmlDate = JText::sprintf('Date_Minutes_Ago',intval(($now-$item->lastvisit_xml)/60));
            } elseif ( $item->lastvisit_xml > ($now-86400)) { // Less than one day
                $hours = intval (($now-$item->lastvisit_xml)/3600 );
                $xmlDate = JText::sprintf('Date_Hours_Minutes_Ago',$hours,($now-($hours*3600)-$item->lastvisit_xml)/60);
            } elseif ( $item->lastvisit_xml > ($now-259200)) { // Less than three days
                $days = intval(($now-$item->lastvisit_xml)/86400);
                $xmlDate = JText::sprintf('Date_Days_Hours_Ago',$days,intval(($now-($days*86400)-$item->lastvisit_xml)/3600));
            } else {
                $date = new JDate($item->lastvisit_xml);
                $xmlDate = $date->toFormat('%Y-%m-%d %H:%M');
            }

        ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td>
                    <a style="cursor: pointer;" onclick="if (window.parent) window.parent.<?php echo $function;?>('<?php echo $item->id; ?>', '<?php echo $this->escape($item->title); ?>');">
                        <?php echo $this->escape($item->title); ?></a>
                </td>
                <td align="center">
                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'sitemaps.'); ?>
                </td>
                <td align="center">
                    <?php echo $this->escape($item->access_level); ?>
                </td>
                <td class="center">
                    <?php echo $item->count_html .' / '.$item->views_html. ' / ' . $htmlDate; ?>
                </td>
                <td class="center">
                    <?php echo $item->count_xml .' / '.$item->views_xml. ' / ' . $xmlDate; ?>
                </td>
                <td align="center">
                    <?php echo (int) $item->id; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>