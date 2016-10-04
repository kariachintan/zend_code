<?php

class referencelibrary_forms_employeeassoc_Details extends Core_Form {

    public $isNewRecord = false;
    public $reflibId = null;


    public $reflib = null;
    
    
    public function build() {
        $codes = new Core_Codes();

        $reflib = EntityUtils::get('phpnotes_services_referencelibrary_dto_LibraryDTO', $this->reflibId);

        $this->reflib = $reflib;

        $element = new Core_Form_Element_Text('Description');
        $element->setLabel('Document Description');
        $element->setAttribs(array('class' => 'longfield'));
        $element->setValue($reflib->getDescription());
        $element->setReadOnly(true);
        $this->addElement($element);

		
		$refModel = new referencelibrary_models_library_Details();
        $participantList = $refModel->getParticipantList($reflib->getId()) ? $refModel->getParticipantList($reflib->getId()) : "";
        $element = new Core_Form_Element_Textarea('EmployeeNumbers');
        $element->setLabel('Employee Numbers');
        $element->addValidators(array(
            array('regex', false, array(
                    'pattern' => '/[\d,]+/',
                    'messages' => 'Invalid Employee Number(s) Entered'))
        ));
        $element->setRequired(true);
        $element->setValue($participantList);
        $this->addElement($element);
				
		$element = new Core_Form_Element_Button('ParticipantListFromCSVButton');
		$element->setAttrib('content','Upload CSV');
		$element->setAttrib('href','javascript: void(0);');
		$element->setAttrib('onclick','window.open(\'/survey/participants/csv\',\'csvwindow\',config=\'height=600,width=700,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,directories=no,status=no\');');
		$element->setAttrib('class','button');
		$element->setAttrib('style', 'display: inline-block; margin-bottom: 10px; float: none; width: 120px; margin-left: 200px;');
		$this->addElement($element);
				
    }

    public function isValid($data) {

        $valid = parent::isValid($data);
        if ($valid) {
            $refModel = new referencelibrary_models_library_Details();
            $employeeNumbersString = $this->EmployeeNumbers->getValue();
            $employeeNumbersArray = array_unique(preg_split("/[\s,]+/", $employeeNumbersString));
						$_employeeNumbersArray = array();
						foreach($employeeNumbersArray as &$e) {
							$e = trim(preg_replace('/\s+/', ' ', $e));
							if ($e) {
								$_employeeNumbersArray[] = $e;
							}
						}
						$employeeNumbersArray = $_employeeNumbersArray;
            if (!empty($employeeNumbersArray)) {
                $existingEmployeeNumbers = $refModel->getEmployeesByNumber($employeeNumbersArray);
                $difference = array_diff($employeeNumbersArray, array_keys($existingEmployeeNumbers));
                if (count($difference)) {
										$this->EmployeeNumbers->setValue(implode(",",array_keys($existingEmployeeNumbers)));
                    $this->EmployeeNumbers->addError('Invalid Employee Number(s) ' . implode(', ', $difference));
										Core_State::set("referencelibrary","validation_invalid_employee_numbers",$difference);
                    $valid = false;
                }
            } else {
							$this->EmployeeNumbers->addError('Invalid Employee Number(s) ' . $employeeNumbersString);
							Core_State::set("referencelibrary","validation_invalid_employee_numbers",$employeeNumbersArray);
							$valid = false;
            }
            
        }
        
				
				
		// //Do we have enough memory.. workaround to avoid memory leaks..
		// //..and a large refactor
		// if ($valid) {
		// 	logl(ini_get("memory_limit"),"mem max");
		// 	$maxMem = toByteSize(ini_get("memory_limit"));
		// 	//Some memory calculation upfront..
		// 	if ($this->chapter->getTypeId() == $this->gate) {
		// 		$memNeeds = count($employeeNumbersArray) * 10000;
		// 		$maxEmpSupported = round($maxMem / 10000);
		// 	} else {
		// 		//other factors come into play.like how if a survey is attached.. but 
		// 		//this is a rough and rounded up estimate
		// 		$chapterModel = new certification_models_chapter_Details();
		// 		$chapCount = count($chapterModel->getSegmentChaptersAssoc($this->chapter->getId()));
		// 		$memNeeds = count($employeeNumbersArray) * $chapCount * 15000;
		// 		$maxEmpSupported = round($maxMem / (15000*$chapCount));
		// 	}
			
		// 	if ($memNeeds>$maxMem) {
		// 		logl("mem= $memNeeds : max=$maxEmpSupported","memory needs");
		// 		$this->EmployeeNumbers->addError("Too many employees assigned. You can only assign a maximum of $maxEmpSupported at a time to this particular chapter.");
		// 		$valid = false;
		// 	}
			
		// }
				
        return $valid;
    }

}
