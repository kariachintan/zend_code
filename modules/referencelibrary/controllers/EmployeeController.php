<?php

class referencelibrary_EmployeeController extends Core_Controller_Action
{

	public function listingAction()
	{
		$this->setModel(new referencelibrary_models_employee_Listing());
		$this->setTable(new referencelibrary_tables_employee_Listing());

		parent::listingAction();
	}

	/**
	 *
	 */
	protected function prepareListingControls()
	{
		//hide new button...
	}
}