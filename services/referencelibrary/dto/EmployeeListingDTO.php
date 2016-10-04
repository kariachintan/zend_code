<?php

require_once('phpnotes/services/entity/dto/AbstractServiceDTO.php');

class phpnotes_services_referencelibrary_dto_EmployeeListingDTO extends phpnotes_services_entity_dto_AbstractServiceDTO {

    private $mLngDocTypeId = null;
    private $mLngEmployeeNum = null;
    private $mLngDocPriorityId = null;
    private $mColAttachments;
    private $mColStart = null;
	private $mColEnd = null;
    private $mLocation = null;
    private $mStrFullName = null;
     private $lObjEmployee = null;

     public function getEmployeeNum() {
       
        return $this->mLngEmployeeNum;
    }

    public function setEmployeeNum($value) {        
        $this->mLngEmployeeNum = $value;
    }

    public function getDocTypeId() {
       
        return $this->mLngDocTypeId;
    }

    public function setDocTypeId($lCodStatus) {
        $this->mLngDocTypeId = $lCodStatus;
    }

    public function getDocPriorityId() {       
        return $this->mLngDocPriorityId;
    }

    public function setDocPriorityId($lCodDocPriorityId) {
        $this->mLngDocPriorityId = $lCodDocPriorityId;
    }

    public function getEndDateTime() {
		return $this->mColEnd;
	}
	
	public function setEndDateTime($value) {
		$this->mColEnd = $value;
	}

   public function getLocation() {
         return Zend_Registry::get('base_url').'/image.php?id='.$this->mLocation;

    }
    
    public function setLocation($mLocation) {
        $this->mLocation = $mLocation;
    }

	
	public function getStartDateTime() {
		return $this->mColStart;
	}
	
	public function setStartDateTime($value) {
		$this->mColStart = $value;
	}

    public function getFullName() {
        return $this->mStrFullName;
    }
    
    public function setFullName($value) {
        $this->mStrFullName = $value;
    }      

     public function getEmployee(){
        if ($this->lObjEmployee == null) $this->lObjEmployee = EntityUtils::get('phpnotes_services_employee_dto_EmployeeDTO',$this->getEmployeeId());
        return $this->lObjEmployee;
    } 
    

}