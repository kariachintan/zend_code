<?php

class referencelibrary_models_library_Details extends referencelibrary_models_abstract_AttachmentTarget {

	private $db = null;

	public function __construct($config = array()) {
		parent::__construct($config);

		$this->setDtoClassName('phpnotes_services_referencelibrary_dto_LibraryDTO');
	}

	protected function getDb() {
        if ($this->db == null)
            $this->db = new phpnotes_tools_patterns_dao_connection_MysqlDatabaseConnection();
        return $this->db;
    }
      
    public function assignEmployees($employeeNumbersStr,$reflibId,&$employeeNumbersArray,&$redundantEmployees){

    	$employeeNumbersArray = $this->strToUniquesList($employeeNumbersStr);
		$employees = $this->getEmployeesByNumber($employeeNumbersArray);

		$employeeIds = array();
			foreach ($employees as $num=>$emp) {
				$employeeIds[$emp['EMPLOYEEID']] = $num;
			}


		// Checking if mapping already exists.
		$sql = "SELECT DISTINCT EMPLOYEEID FROM ATTACHMENT.REFLIBEMPLOYEEASSOC WHERE REFLIBRARYHEADERID='$reflibId' ";					

		$rows = $this->getDb()->fetchAll($sql);

		//REDUNDANT MEANS DB
		foreach($rows as $r) {
			$redundantEmployees[] = $r['EMPLOYEEID'];			
		}
		

		$removedEmployees = array_diff($redundantEmployees, array_keys($employeeIds));

		foreach($rows as $r) {
			$redundantEmployees[] = $r['EMPLOYEEID'];
			unset($employeeIds[$r['EMPLOYEEID']]);
		}

	
		$insertTotal = count($employeeIds);
		$deleteTotal = count($removedEmployees);

		$result = true;

		if ($deleteTotal > 0) {

			$result = $this->deleteEmployeeAttachmentMapping($reflibId, $removedEmployees);
			Amerch_Log::info("referencelibrary", "assignemployees - Following Employees are removed ", print_r($removedEmployees,true));

			$nonAssigned = $this->getEmployeesByID($removedEmployees);

			foreach ($nonAssigned as $key => $value) {
				$newArr[$reflibId][$key] = $value;
			}

			Core_State::set("referencelibrary","RemovedEmployees",$newArr);
			
			}

		if ($insertTotal > 0) {

			$result = $this->assignEmployeeToAttachment($reflibId, array_keys($employeeIds),$redundantEmployees);
			Amerch_Log::debug("referencelibrary/employeeassoc/assignemployees", "Mapping already exists - Employees", print_r($redundantEmployees,true));
			
			foreach($redundantEmployees as $redundId) {
				unset($employeeIds[$redundId]);
			}
		}

		$error = $result!==true ? $result : false;

		 if ($error) {					
		 		Amerch_Log::debug("referencelibrary", "assign employees", "Info: $error");
		} else {
				Amerch_Log::debug("referencelibrary", "assign employeess", "Success");
		}

		return $insertTotal ? $employeeIds : false;

    }

     public function deleteEmployeeAttachmentMapping($refId, $employeeIds) {

     	$db = $this->getDb();

	  	if (!is_array($employeeIds)) $employeeIds = array($employeeIds);
	  	$result = true;
	  	foreach($employeeIds as $employeeid){

	  		$qresult =  $db->query("DELETE FROM ATTACHMENT.REFLIBEMPLOYEEASSOC WHERE `REFLIBRARYHEADERID` = '$refId' AND `EMPLOYEEID` = $employeeid ");
	  		if (!$qresult) {

				Amerch_Log::error('referencelibrary', 'employeeassoc', "Error deleting employees [" . implode(",",$employeeIds) . "] for REFLIBRARY HEADERID: $reflibid");
				$result = "A system error occured while adding the employee to the attachment.";

			}			
	  	}
	  	return result;

     }

    /**
		 * 
		 * @param type $reflibid
		 * @param type $employeeIds
		 */
  public function assignEmployeeToAttachment($reflibid, $employeeIds,&$redundantEmployees) {

	  	$db = $this->getDb();

	  	if (!is_array($employeeIds)) $employeeIds = array($employeeIds);
			
		$employeeIds =  array_unique($employeeIds);
		
		$employeeIds = !empty($employeeIds) ?  array_combine($employeeIds,$employeeIds) : array();

		$result = true;

		$valuesArr = array();

		if (!empty($employeeIds)) {


			foreach($employeeIds as $employeeid) {

				$valuesArr[] = "(
					$reflibid,
					$employeeid
					)";
			}

			$values = implode(",",$valuesArr);


			$qresult = $db->query("

				INSERT INTO 
							ATTACHMENT.REFLIBEMPLOYEEASSOC
						(
							`REFLIBRARYHEADERID`,
							`EMPLOYEEID`
						) 
						VALUES
						$values
				");

			if (!$qresult) {

				Amerch_Log::error('referencelibrary', 'employeeassoc', "Error adding employees [" . implode(",",$employeeIds) . "] to REFLIBRARY HEADERID: $reflibid");
				$result = "A system error occured while adding the employee to the attachment.";

			}

		}

		return $result;
  }

    public function getParticipantList($refId) {
			$results = array();
			
			if ($refId) {				

				
				$sql = "SELECT E.EMPLOYEENUM "
					. "	FROM ATTACHMENT.REFLIBEMPLOYEEASSOC CA INNER JOIN EMPLOYEE.EMPLOYEES E "
					. " ON CA.REFLIBRARYHEADERID='{$refId}' AND E.EMPLOYEEID=CA.EMPLOYEEID";


				$db = new phpnotes_tools_patterns_dao_connection_MysqlDatabaseConnection();
				$rows = $db->fetchAll($sql);
				foreach($rows as $r) {
					$results[] = $r['EMPLOYEENUM'];
				}
			}
			
			return implode(", ",$results);
		}


    /* @param type $listStr
	 * @return type
	 */
	protected function strToUniquesList($listStr) {
		$arr = preg_split("/[\s,]+/",$listStr);
		$_arr = array();
		foreach($arr as $e) {
			$e = trim(preg_replace('/\s+/', ' ', $e));
			if ($e) {
				$_arr[] = $e;
			}
		}
		$arr = array_unique($_arr);

		return $arr;
	}

			/**
		 * 
		 * @param type $employeeNumbersArray
		 * @return type
		 */
    public function getEmployeesByNumber($employeeNumbersArray){
        $db = $this->getDb();
        $employeeNumbersString = "'" . implode("','",$employeeNumbersArray) . "'";
        $query = "SELECT EMPLOYEEID,EMPLOYEENUM,VOICEMAIL,FULLNAME,CHAINNUMBERS FROM EMPLOYEE.EMPLOYEES WHERE EMPLOYEENUM IN ($employeeNumbersString)  ";
				
		logl($query);
				
        $rows = $db->query($query);
        $employeeNumbers = array();
        foreach($rows as $row){
            $employeeNumbers [trim($row['EMPLOYEENUM'])] = $row;
        }
       
        return $employeeNumbers;   
        
    }

	/**
	 * 
	 * @param type $employeeNumbersArray
	 * @return type
	 */
    public function getEmployeesByID($employeeNumbersArray){
        $db = $this->getDb();
        $employeeNumbersString = "'" . implode("','",$employeeNumbersArray) . "'";
        $query = "SELECT EMPLOYEEID,EMPLOYEENUM,VOICEMAIL,FULLNAME,CHAINNUMBERS FROM EMPLOYEE.EMPLOYEES WHERE EMPLOYEEID IN ($employeeNumbersString)  ";
				
		//logl($query);
				
        $rows = $db->query($query);
        $employeeNumbers = array();
        foreach($rows as $row){
            $employeeNumbers [trim($row['EMPLOYEENUM'])] = $row;
        }
       
        return $employeeNumbers;   
        
    }

    public function deleteHeaderMappings($refId) {

     	$db = $this->getDb();
     	$result = true;

	  		$qresult =  $db->query("DELETE FROM ATTACHMENT.REFLIBEMPLOYEEASSOC WHERE `REFLIBRARYHEADERID` = '$refId' ");
	  		if (!$qresult) {

				Amerch_Log::error('referencelibrary', 'deleteAction', "Error deleting employees for REFLIBRARY HEADERID: $refId");
				$result = "A system error occured while adding the employee to the attachment.";

			}else{
				Amerch_Log::info('referencelibrary', 'deleteAction', "Suceessfully deleted all mappings for REFLIBRARY HEADERID: $refId");
			}

			return $result;
	  	}
	  	

     

}