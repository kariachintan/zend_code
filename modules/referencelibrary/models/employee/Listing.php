<?php

class referencelibrary_models_employee_Listing extends Core_Models_Abstract_Listing
{
	function __construct()
	{
		parent::__construct();

		$this->setDtoClassName('phpnotes_services_referencelibrary_dto_EmployeeListingDTO');
	}

}