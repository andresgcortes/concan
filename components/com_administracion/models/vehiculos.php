<?php

//-- No direct access

defined('_JEXEC') or die('=;)');

class AdministracionModelVehiculos extends JModelList{

	public function __construct($config = array()){	

		if (empty($config['filter_fields'])) {

			$config['filter_fields'] = array(
				'nombre', 'a.nombre',
				'disabled', 'a.disabled',
			);
			
			if (JLanguageAssociations::isEnabled()){
				$config['filter_fields'][] = 'association';
			}

		}

		parent::__construct($config);

	}

	protected function populateState($ordering = null, $direction = null)	{
	
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
		
		// Load the parameters.
		$this->setState('params', JComponentHelper::getParams('com_administracion'));

		// List state information.
		parent::populateState($ordering, $direction);

	}

	protected function getStoreId($id = ''){
		
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		
		return parent::getStoreId($id);

	}
	
	function getListQuery(){
	
		// Create a new query object.
		$db 		= $this->getDbo();
		$my			= JFactory::getUser();
		
		$query = $db->getQuery(true);

		$query->select(
			$this->getState('list.select', 'a.*')
		);

		$query->from('#__core_vehiculos AS a');
		//$query->join('inner', '#__core_cargos AS b ON a.id_cargo = b.id_cargo');		
		
		$search = $this->getState('filter.search');
		
		if (!empty($search)){
			if (stripos($search, 'id:') === 0){
				$query->where('a.id = '.(int) substr($search, 3));
			}else {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.placa LIKE '.$search.')');
			}
		}
		
		$listOrdering 	= $this->getState('list.ordering', 'a.placa');
		$listDirn 		= $db->escape($this->getState('list.direction', 'ASC'));
		
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);
		
		return $query;

	}//function
	
}//class