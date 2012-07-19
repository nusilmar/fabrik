<?php
/**
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005 Fabrik. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @since       1.6
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Connection Model
 * 
 * @package  Fabrik
 * @since    3.0
 */

class FabrikModelConnection extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_FABRIK_CONNECTION';

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 * 
	 * @return  JTable  A database object
	 * 
	 * @since	1.6
	 */

	public function getTable($type = 'Connection', $prefix = 'FabrikTable', $config = array())
	{
		/**
		 * not sure if we should be loading JTable or FabTable here
		 * issue with using Fabtable is that it will always load the cached verion of the cnn
		 * which might cause issues when migrating from test to live sites???
		 */
		$config['dbo'] = FabriKWorker::getDbo(true);
		return FabTable::getInstance($type, $prefix, $config);

	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array  $data      Data for the form.
	 * @param   bool   $loadData  True if the form is to load its own data (default case), false if not.
	 * 
	 * @return  mixed	A JForm object on success, false on failure
	 * 
	 * @since	1.6
	 */

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_fabrik.connection', 'connection', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed	The data for the form.
	 * 
	 * @since	1.6
	 */

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_fabrik.edit.connection.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Method to set the default item
	 *
	 * @param   int  $id  of connection to set as default
	 * 
	 * @return  boolean	 True on success.
	 * 
	 * @since	1.6
	 */

	public function setDefault($id)
	{
		$db = FabrikWorker::getDbo(true);
		$query = $db->getQuery(true);
		$query->update('#__fabrik_connections')->set($db->quoteName('default') . ' = 0');
		$db->setQuery($query);
		$db->query();
		$query->clear();
		$query->update('#__fabrik_connections')->set($db->quoteName('default') . ' = 1')->where('id = ' . (int) $id);
		$db->setQuery($query);
		$db->query();
		return true;
	}

	/**
	 * check if connection is the default and if so reset its values to those of the J db connection
	 * 
	 * @param   object  &$item  connection item
	 * 
	 * @return null
	 */

	public function checkDefault(&$item)
	{
		$table = $this->getTable();
		$app = JFactory::getApplication();
		if ($item->id == 1)
		{
			$app->enqueueMessage(JText::_('COM_FABRIK_ORIGINAL_CONNECTION'));
			if (!$this->matchesDefault($item))
			{
				$config = JFactory::getConfig();
				$item->host = $config->get('host');
				$item->user = $config->get('user');
				$item->password = $config->get('password');
				$item->database = $config->get('db');
				JError::raiseWarning(E_WARNING, JText::_('COM_FABRIK_YOU_MUST_SAVE_THIS_CNN'));
			}
		}
	}

	/**
	 * do the item details match the J db connection details
	 * 
	 * @param   object  $item  connection item
	 * 
	 * @return  bool  matches or not
	 */

	protected function matchesDefault($item)
	{
		$config = JFactory::getConfig();
		return $config->get('host') == $item->host && $config->get('user') == $item->user && $config->get('password') == $item->password
			&& $config->get('db') == $item->database;
	}

	/**
	 * save the connection- test first if its vald
	 * if it is remove the session instance of the connection then call parent save
	 * 
	 * @param   array  $data  connection data
	 * 
	 * @return  boolean  True on success, False on error.
	 */

	public function save($data)
	{
		$session = JFactory::getSession();
		$model = JModelLegacy::getInstance('Connection', 'FabrikFEModel');
		$model->setId($data['id']);
		$options = $model->getConnectionOptions(JArrayHelper::toObject($data));
		$db = JDatabase::getInstance($options);
		if (JError::isError($db))
		{
			$this->setError(JText::_('COM_FABRIK_UNABLE_TO_CONNECT'));
			return false;
		}
		$key = 'fabrik.connection.' . $data['id'];
		/**
		 * erm yeah will remove the session connection for the admin user, but not any other user whose already using the site
		 * would need to clear out the session table i think - but that would then log out all users.
		 */
		$session->clear($key);
		return parent::save($data);
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   11.1
	 */

	public function validate($form, $data, $group = null)
	{
		if ($data['password'] !== $data['passwordConf'])
		{
			$this->setError(JText::_('COM_FABRIK_PASSWORD_MISMATCH'));
			return false;
		}
		return parent::validate($form, $data);
	}
}
