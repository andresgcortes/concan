<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

class AdministracionControllerVehiculo extends JControllerForm{	
		
    public function __construct($config = array()){

		parent::__construct($config);
		
		$this->registerTask('apply', 	'save');
		$this->registerTask('unpublish', 'publish');

	}
		
    function save($key = NULL, $urlVar = NULL){
				
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$app	= JFactory::getApplication();
		$model	= $this->getModel();
		$form	= $model->getForm();
		$data	= JRequest::getVar('concan', array(), 'post', 'array');
		$id		= JRequest::getInt('id');
		$option	= JRequest::getCmd('option');
		
		/*if (!JFactory::getUser()->authorise('core.create', $option)){
			JFactory::getApplication()->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}*/
				
		// Validate the posted data.
		$return = $model->validate($form, $data);
		
		if ($return === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_administracion.edit.vehiculo.global', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_administracion&view=vehiculos&layout=edit', false));
			
			return false;
		
		}
		
		$return = $model->save($data);
		
		if ($return === false){
			
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_administracion.edit.vehiculo.global', $data);

			// Save failed, go back to the screen and display a notice.
			$message = JText::sprintf('Error al Guardar la información', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=com_administracion&view=vehiculos&layout=edit', false));
			
			return false;
		
		}		
	
		// Set the redirect based on the task.
		switch ($this->getTask()){
			
			case 'apply':
				$message = JText::_('Vehiculo Guardado Correctamente');
				$this->setRedirect(JRoute::_('index.php?option=com_administracion&view=vehiculos&layout=edit&id='.$model->GetState('vehiculo.id')), $message);
				break;

			case 'save':
				$message = JText::_('Vehiculo Guardado Correctamente');
				$this->setRedirect(JRoute::_('index.php?option=com_administracion&view=vehiculos&layout=edit&id='.$model->GetState('vehiculo.id')), $message);
				break;
			
		}

		return true;
	}
	
    public function publish(){
		
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$ids    = $this->input->get('cid', array(), 'array');
		$values = array('publish' => 0, 'unpublish' => 1);
		$task   = $this->getTask();
		$value  = ArrayHelper::getValue($values, $task, 1, 'int');
		
		if (empty($ids)){
			JError::raiseWarning(500, JText::_('No se selección un Vehículo'));
		}else{
			// Get the model.
			/** @var BannersModelBanner $model */
			$model = $this->getModel();

			// Change the state of the records.
			if (!$model->stick($ids, $value)){
				JError::raiseWarning(500, $model->getError());
			}else{
				
				if ($value == 1){
					$ntext = 'Vehículo Deshabilitado';
				}else{
					$ntext = 'Vehículo Habilitado';
				}

				$this->setMessage(JText::plural($ntext, count($ids)));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_administracion&view=vehiculos'));
		
		return true; 
		
	}
	
	public function getPropietario(){
		
		$document = JFactory::getDocument();
	    $document->setMimeEncoding( 'text/html' );

		$model = $this->getModel('vehiculo'); // JModelForm
		$form = $model->getForm();
		
		$html = ''; 
				
		//$form->setFieldAttribute('ciudad', 'required', true);		
		$html.= $form->getInput('id_propiedad');
				
		print_r($html);
		
		die(); 		
		
	}
		
}