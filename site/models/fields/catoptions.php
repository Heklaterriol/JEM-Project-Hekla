<?php
/**
 * @version     2.3.16
 * @package     JEM
 * @copyright   Copyright (C) 2013-2023 joomlaeventmanager.net
 * @copyright   Copyright (C) 2005-2009 Christoph Lukes
 * @license     https://www.gnu.org/licenses/gpl-3.0 GNU/GPL
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;

JFormHelper::loadFieldClass('list');

require_once __DIR__ . '/../../helpers/helper.php';

/**
 * CatOptions Field class.
 */
class JFormFieldCatOptions extends JFormFieldList
{
	/**
	 * The category options field type.
	 */
	protected $type = 'CatOptions';


	/**
	 * Create Input
	 * @see JFormField::getInput()
	 */
	public function getInput()
	{
		$attr = '';

		// Initialize field attributes.
		if (version_compare(JVERSION, '3.0', 'lt')) {
			# within Joomla 2.5 we are having a "element"
			$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

			// To avoid user's confusion, readonly="true" should imply disabled="true".
			if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
			{
				$attr .= ' disabled="disabled"';
			}

			$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
			$attr .= $this->multiple ? ' multiple="multiple"' : '';

			// Initialize JavaScript field attributes.
			$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
		} else {
			# but within Joomla 3 the element part was removed
			$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
			$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
			$attr .= $this->multiple ? ' multiple' : '';
			$attr .= $this->required ? ' required aria-required="true"' : '';

			// To avoid user's confusion, readonly="true" should imply disabled="true".
			if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
			{
				$attr .= ' disabled="disabled"';
			}

			// Initialize JavaScript field attributes.
			$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
		}

		// Output
		$currentid = JFactory::getApplication()->input->getInt('a_id');
		if (!$currentid) { // special case: new event as copy of another one
			$currentid = JFactory::getApplication()->input->getInt('from_id');
		}

		// Get the field options.
		$options = (array) $this->getOptions();

        $db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('DISTINCT catid');
		$query->from('#__jem_cats_event_relations');
		$query->where('itemid = '. $db->quote($currentid));
		$db->setQuery($query);
		$selectedcats = $db->loadColumn();

		// On new event we may have a category preferred to select.
		if (empty($selectedcats) && !empty($this->element['prefer'])) {
			$selectedcats = (array)$this->element['prefer'];
		}

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $selectedcats,$this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($selectedcats, ENT_COMPAT, 'UTF-8') . '"/>';
		}
		else
		// Create a regular list.
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $selectedcats,$this->id);
		}

		return implode($html);
	}

	/**
	 * Retrieve Options
	 * @see JFormFieldList::getOptions()
	 */
	protected function getOptions()
	{
		$currentid = JFactory::getApplication()->input->getInt('a_id');
		$options = self::getCategories($currentid);
		$options = array_values($options);

		// Pad the option text with spaces using depth level as a multiplier
		# the level has to be decreased as we are having a (invisible) root
		# treename is generated by the function so let's use that one instead of the Joomla way

		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			/*
			if ($options[$i]->published == 1)
			{
				$options[$i]->text = str_repeat('- ', ($options[$i]->level - 1)) . $options[$i]->text;
			}
			else
			{
				$options[$i]->text = str_repeat('- ', ($options[$i]->level - 1)) . '[' . $options[$i]->text . ']';
			}
			*/

			$options[$i]->text = $options[$i]->treename;
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	/**
	 * logic to get the categories
	 *
	 * @access public
	 * @return void
	 */
	public function getCategories($id)
	{
        $db = Factory::getContainer()->get('DatabaseDriver');
		$user   = JemFactory::getUser();
		$userid = (int) $user->get('id');

		if (empty($id)) {
			// for new events also show all categories user is allowed to see, disable non-useable categories
			// (to show same list in both cases, and allow "unusable" parents for structuring)
			$mitems = $user->getJemCategories('add', 'event', array('use_disable' => true));
		} else {
			$query = $db->getQuery(true);
			$query = 'SELECT COUNT(*)'
			       . ' FROM #__jem_events AS e'
			       . ' WHERE e.id = ' . $db->quote($id)
			       . '   AND e.created_by = ' . $db->quote($userid);
			$db->setQuery($query);
			$owner = $db->loadResult();

			// on edit show all categories user is allowed to see, disable non-useable categories
			$mitems = $user->getJemCategories(array('add', 'edit'), 'event', array('use_disable' => true, 'owner' => $owner));
		}

		if (!$mitems)
		{
			$mitems = array();
			$children = array();

			$parentid = 0;
		}
		else
		{
			$children = array();
			// First pass - collect children
			foreach ($mitems as $v)
			{
				$v->value = $v->id;
				$v->text = $v->catname;
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}

			// list childs of "root" which has no parent and normally id 1
			$parentid = intval(@isset($children[0][0]->id) ? $children[0][0]->id : 1);
		}

		//get list of the items
		$list = JemCategories::treerecurse($parentid, '', array(), $children, 9999, 0, 0);

		// append orphaned categories
		if (count($mitems) > count($list)) {
			foreach ($children as $k => $v) {
				if (($k > 1) && !array_key_exists($k, $list)) {
					$list = JemCategories::treerecurse($k, '?&nbsp;', $list, $children, 999, 0, 0);
				}
			}
		}

		return $list;
	}
}
