<?php

defined('_JEXEC') or die('=;)');

class AdministracionModelMaterial extends JModelAdmin{

	public function getTable($type = 'Materiales', $prefix = 'AdministracionTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);

	}
	
	public function getForm($data = array(), $loadData = true){

		// Get the form.
		$form = $this->loadForm('com_administracion.material', 'material', array('control' => 'concan', 'load_data' => $loadData));

		if(empty($form)){
			return false;
		}

		return $form;

	}

  	protected function loadFormData() {

        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_administracion.edit.material.data', array());

		if (empty($data)){
           $data = $this->getItem();
        }

        return $data;

    }
	
	public function Save($data){
		
		$user 	= JFactory::getUser();
		$table 	= $this->gettable();
		$key 	= $table->getKeyName();
		$pk 	= (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew 	= true;
		
		$datenow 	= JFactory::getDate();
		$datenow	= $datenow->format('Y-m-d H:s', true);
		
 		$data['modificated_by'] = $user->id;
 		$data['modificated'] 	= $datenow;			 				
		
		if ($pk > 0){
			
			$table->load($pk);
			$isNew = false;
			
		}else{
			
			$data['created_by'] 	= $user->id;
 			$data['created'] 		= $datenow;

		}

		// Bind the data.
		if (!$table->bind($data)){
			$this->setError($table->getError());
			return false;
		}

		// Prepare the row for saving
		$this->prepareTable($table);

		// Check the data.
		if (!$table->check()){
			$this->setError($table->getError());
			return false;
		}
		
		// Store the data.
		if (!$table->store()){
			$this->setError($table->getError());
			return false;
		}

		// Clean the cache.
		$this->cleanCache();

		// Trigger the onContentAfterSave event.		
		$pkName = $table->getKeyName();

		if(isset($table->$pkName)){
			$this->setState($this->getName() . '.id', $table->$pkName);
		}
				
		$this->setState($this->getName() . '.new', $isNew);

		return true;
		
	} 
		
	function stick(&$pks, $value = 1){
	
		// Initialise variables.
		$table		= $this->getTable();
		$pks		= (array) $pks;
		$user		= JFactory::getUser();		
		
		// Access checks.
		foreach ($pks as $i => $pk){
			
			if ($table->load($pk)){	
															
				// Skip changing of same state
				if ($table->disabled == $value) {
					unset($pks[$i]);
					continue;
				}
				
				$table->disabled = (int) $value;
				
				// Allow an exception to be thrown.
				try{
					
					if (!$table->check()) {
						$this->setError($table->getError());
						return false;
					}

					// Trigger the onUserBeforeSave event.
					if (in_array(false, $result, true)) {
						// Plugin will have to raise it's own error or throw an exception.
						return false;
					}

					// Store the table.
					if (!$table->store()) {
						$this->setError($table->getError());
						return false;
					}

				}

				catch (Exception $e){
					$this->setError($e->getMessage());

					return false;
				}		
				
			}
		
		}

		return true;
	}		
	
} ?>