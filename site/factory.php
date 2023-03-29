<?php
/**
 * @version 2.3.15
 * @package JEM
 * @copyright (C) 2013-2023 joomlaeventmanager.net
 * @license https://www.gnu.org/licenses/gpl-3.0 GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

// Can't use JPATH_COMPONENT_SITE because factory maybe used in module or plugin!
require_once (JPATH_SITE.'/components/com_jem/classes/user.class.php');
require_once (JPATH_SITE.'/components/com_jem/classes/config.class.php');


/**
 * JEM Factory class
 *
 * @package  JEM
 * @since    2.1.5
 */
abstract class JemFactory extends JFactory
{
	/**
	 * Get a JEM user object.
	 *
	 * Returns the global {@link JemUser} object, only creating it if it doesn't already exist.
	 *
	 * @param   integer  $id  The user to load - Must be an integer or null for current user.
	 *
	 * @return  JemUser object
	 *
	 * @see     JemUser
	 * @since   2.1.5
	 */
	public static function getUser($id = null)
	{
		if (is_null($id))
		{
			$instance = self::getSession()->get('user');
			$id = ($instance instanceof JUser) ? $instance->id : 0;
		}

		return JemUser::getInstance($id);
	}

	/**
	 * Get the JEM configuration object.
	 *
	 * Returns the global {@link JemConfig} object, only creating it if it doesn't already exist.
	 *
	 * @return  JemConfig object
	 *
	 * @note    Because parent's getConfig() is limited to php files we don't override this function.
	 *
	 * @see     JemConfig
	 * @since   2.1.6
	 */
	public static function getJemConfig()
	{
		return JemConfig::getInstance();
	}

	/**
	 * Get the dispatcher.
	 *
	 * Returns the static {@link JDispatcher} or {@link JEventDispatcher} object, depending on Joomla version.
	 *
	 * @return  JDispatcher or JEventDispatcher object
	 *
	 * @see     JDispatcher, JEventDispatcher
	 * @since   2.1.7
	 */
	public static function getDispatcher()
	{
		if (version_compare(JVERSION, '4.0', 'ge')) {
			return Factory::getApplication();
		} else
		if (version_compare(JVERSION, '3.0', 'ge')) {
			return JEventDispatcher::getInstance();
		} else {
			return JDispatcher::getInstance();
		}
	}
}
