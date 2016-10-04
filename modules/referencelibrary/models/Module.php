<?php

class referencelibrary_models_Module extends Core_Module_Model_Abstract
{

	public function setAcl()
	{
		//Init Module User Roles
		$this->acl->addRole(new Zend_Acl_Role ('admin'));
		$this->acl->addRole(new Zend_Acl_Role ('view'));

		//Assign Role/Resource Permissions
		$this->acl->allow('admin');
		$this->acl->allow('view', null, array('referencelibrary', 'listings'));

		// Fetch and apply username and user's groups to ACL
		$this->acl->setModuleRoles('referencelibrary');

	}

	function setNavigation()
	{

		$this->navigation->addItem(new Core_Navigation_Item('Reference Library', null, 'library',
			array('listing', 'details')));
		$this->navigation->addItem(new Core_Navigation_Item('Employee Lookup', null, 'employee', array('listing')));
	}

}