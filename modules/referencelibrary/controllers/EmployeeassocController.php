<?php

class referencelibrary_EmployeeassocController extends Core_Controller_Action
{


	function assignemployeesAction()
	{


		$lObjForm = new referencelibrary_forms_employeeassoc_Details();
		$lObjForm->reflibId = $this->id;
		$lObjForm->build();
		$this->view->form = $lObjForm;
		$redirectUrl = '/' . $this->getModuleName() . '/employeeassoc/employeesassigned/' . $this->id;

		if ($this->_request->isPost()) {

			$lBlnValid = $lObjForm->isValid($_POST);

			if ($lBlnValid == true) {


				$refModel = new referencelibrary_models_library_Details();
				$employeesAssignedList = array();
				$employeesNotAssignedList = array();
				$formattedEmployeeArr = array();
				$redundantEmployees = array();
				Amerch_Log::debug("referencelibrary_EmployeeassocController", "postAction",
					"Used Mem Before: " . memory_get_usage());
				$storedEmployeeNumbers = $refModel->assignEmployees($_POST['EmployeeNumbers'], $this->id,
					$formattedEmployeeArr, $redundantEmployees, false);
				if (!empty($storedEmployeeNumbers)) {
					$employeesAssignedList[$this->id] = $refModel->getEmployeesByNumber($storedEmployeeNumbers);
				}

				Core_State::set("referencelibrary", "EmployeeNumbersJustAssigned", $employeesAssignedList);

				$this->_redirect($redirectUrl);
			}
		}

		// Adding Controls
		$this->view->controls = new Core_Control();
		$redirectUrl = '/' . $this->getModuleName() . '/library/details/' . $this->id;
		$this->view->controls->addItem(new Core_Control_Item_Back(array('href' => $redirectUrl)));
		$this->view->controls->addItem(new Core_Control_Item_Custom("Assign / Remove", array(
			'onclick' => 'initLoader();javascript: document.forms[\'fm\'].Core_Form_Submit.click();',
			'href' => 'javascript:void(0)'
		)));


		$this->view->title = "Reference Library Database - Assign Employees";
		$this->render('assignemployees');

	}

	public function employeesassignedAction()
	{

		$refModel = new referencelibrary_models_library_Details();

		$redirectUrl = '/referencelibrary/library/listing';

		//Employees assigned 
		$assignedEmployees = Core_State::get("referencelibrary", "EmployeeNumbersJustAssigned");

		//Employees who were removed		
		$nonAssignedEmployees = Core_State::get("referencelibrary", "RemovedEmployees");

		//Invalid Employees..set at validation
		$invalidEmployees = Core_State::get("referencelibrary", "validation_invalid_employee_numbers");


		$this->view->reflib = EntityUtils::get('phpnotes_services_referencelibrary_dto_LibraryDTO', $this->id);

		$this->view->assigned = $assignedEmployees;
		$this->view->nonAssigned = $nonAssignedEmployees;

		$this->view->invalid = $invalidEmployees;

		Amerch_Log::info("referencelibrary_EmployeeassocController", "postAction",
			"Used Mem Feedback screen: " . memory_get_usage());

		$this->view->title = "Reference Library Database - Employee Assignment Result";
		$this->view->controls = new Core_Control();
		$this->view->controls->addItem(new Core_Control_Item_Next(array('href' => $redirectUrl)));

		$this->render('employeesassigned');
	}

	/**
	 * Export employees to csv
	 */
	public function exportemployeesAction()
	{
		$whichList = $_GET['which'];
		$reflib = isset($_GET['reflib']) ? $_GET['reflib'] : "";

		switch ($whichList) {
			case "assigned":
				$data = Core_State::get("referencelibrary", "EmployeeNumbersJustAssigned");
				if ($reflib) {
					$data = $data[$reflib];
				}
				break;
			case "nonassigned":
				$data = Core_State::get("referencelibrary", "RemovedEmployees");
				if ($reflib) {
					$data = $data[$reflib];
				}
				break;
			case "invalid":
				$data = Core_State::get("referencelibrary", "validation_invalid_employee_numbers");
				break;
		}


		if ($whichList == "invalid") {
			$columns = array("NUMBER");
			foreach ($data as $d) {
				$csvData[] = array($d);
			}
			$data = null;
			unset($data);
		} else {
			$columns = array();
			$csvData = array();
			foreach ($data as $i => $r) {
				unset($r['CHAINNUMBERS']);
				unset($r['EMPLOYEEID']);
				if (empty($columns)) {
					$columns = array_keys($r);
				}
				$row = array_values($r);
				$csvData[] = $row;
			}
			$data = null;
			unset($data);
		}

		$filename = ("{$whichList}_referencelibrary_{$reflib}_report_" . date("Ymd") . ".csv");

		$this->csvToBrowser($columns, $csvData, $filename);
	}
}