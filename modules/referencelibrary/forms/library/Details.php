<?php

require_once('phpnotes/services/entity/dto/property/TimeAdapter.php');
require_once('phpnotes/services/entity/dto/property/DateAdapter.php');

class referencelibrary_forms_library_Details extends referencelibrary_forms_abstract_AttachmentTarget
{

	public $isNewRecord = false;

	public function build()
	{
		$this->attachmentModel = new referencelibrary_models_library_Details();
		$refID = Core_State::get('referencelibrary.details', 'refID');

		parent::build();
		$codes = new Core_Codes();

		$element = new Core_Form_Element_Text('Description');
		$element->setLabel('Document Description');
		$element->setRequired(true);
		$element->setAttribs(array('class' => 'longfield', 'maxlength' => 96));
		$this->addElement($element);

		$element = new Core_Form_Element_Select('DocTypeId');
		$element->setLabel('Document Type');
		$element->setRequired(true);
		$element->setMultiOptions($codes->getOptionsByCodeName(Core_Config::get('referencelibrary',
			'codes_reference_doctypes')));
		$this->addElement($element);

		$element = new Core_Form_Element_Select('DocPriorityId');
		$element->setLabel('Document Priority Type');
		$element->setRequired(true);
		$element->setMultiOptions($codes->getOptionsByCodeName(Core_Config::get('referencelibrary',
			'codes_reference_docprioritytypes')));
		$this->addElement($element);

		$dateAdapter = new phpnotes_tools_patterns_dto_property_DateAdapter();

		$timeAdapter = new phpnotes_tools_patterns_dto_property_TimeAdapter();
		$timeAdapter->setDefaultTime('');

		$timeValueValidator = new Core_Validate_Time();
		$timeValueValidator->setSeconds(false);
		$timeValueValidator->setMessage("Time value is invalid");

		$element = new Core_Form_Element_Date('StartDateTime');
		$element->setLabel('Start Date');
		$element->setRequired(true);
		$element->setPropertyAdapter($dateAdapter);
		$element->setValue(date('Y-m-d', strtotime('tomorrow')));
		$this->addElement($element);

		$element = new Core_Form_Element_Date('EndDateTime');
		$element->setLabel('End Date');
		$element->setValue(date('Y-m-d', strtotime('tomorrow')));
		$element->setRequired(true);
		$element->setPropertyAdapter($dateAdapter);
		$this->addElement($element);

		$element = new Core_Form_Element_Text('StartTime');
		$element->setLabel('Start Time');
		$element->setDescription('24hr, HH:MM');
		$element->setAttribs(array('class' => 'shortfield'));
		$element->addValidator($timeValueValidator);
		$element->setPropertyName('StartDateTime');
		$element->setPropertyAdapter($timeAdapter);
		$element->setRequired(true);
		$element->setValue('00:00');
		$this->addElement($element);

		$element = new Core_Form_Element_Text('EndTime');
		$element->setLabel('End Time');
		$element->setDescription('24hr, HH:MM');
		$element->setAttribs(array('class' => 'shortfield'));
		$element->addValidator($timeValueValidator);
		$element->setPropertyName('EndDateTime');
		$element->setPropertyAdapter($timeAdapter);
		$element->setRequired(true);
		$element->setValue('23:59');
		$this->addElement($element);

		if ($refID) {
			$element = new Core_Form_Element_Text('ModificationDate');
			$element->setLabel('Modified Date');
			$element->setReadOnly(true);
			$this->addElement($element);

			$element = new Core_Form_Element_Text('Modifier');
			$element->setLabel('Modified By');
			$element->setReadOnly(true);
			$this->addElement($element);

			$element = new Core_Form_Element_Text('CreationDate');
			$element->setLabel('Created Date');
			$element->setReadOnly(true);
			$this->addElement($element);

			$element = new Core_Form_Element_Text('Creator');
			$element->setLabel('Created By');
			$element->setReadOnly(true);
			$this->addElement($element);
		}
	}

	public function isValid($data)
	{

		$refID = Core_State::get('referencelibrary.details', 'refID');

		$valid = parent::isValid($data);

		$description = $this->Description->getValue();

		if ($description) {
			//if (!preg_match('/^[a-zA-Z0-9 ,-]+$/',$description)) {
			if (!preg_match('/^[a-zA-Z0-9 ]+$/', $description)) {
				$this->Description->addError('Description entered is not alphanumeric - Only numbers and letters are allowed.');
				$valid = false;

			}
		}

		// Check dates
		$fromDate = $this->StartDateTime->getValue();
		$fromTimeHM = $this->StartTime->getValue();
		$toDate = $this->EndDateTime->getValue();
		$toTimeHM = $this->EndTime->getValue();


		if (!empty($fromDate)) {
			if (!empty($toDate)) {

				$fromDate = $fromDate . ' ' . $fromTimeHM . ':00';
				$toDate = $toDate . ' ' . $toTimeHM . ':00';
				$today_date = date("Y-m-d H:i:00");

				$fromTime = strtotime($fromDate);
				$toTime = strtotime($toDate);
				$datediff = $toTime - $fromTime;
				$differene = floor($datediff / (60 * 60 * 24));
				$today_date_ts = strtotime($today_date);

				//  // if(($fromTime < $today_date_ts) && (!$refID)) {
				// if($fromTime < $today_date_ts) {
				//       $this->StartTime->addError("Start date/time should be in the future.");
				//         $valid = false;  
				// }

				if ($toTime <= $today_date_ts) {
					$this->EndTime->addError("End date/time should fall after Start date/time.");
					$valid = false;
				}

				if ($fromDate > $toDate) {
					$this->StartTime->addError("Start date/time should be before End date/time.");
					$valid = false;
				}
			}

		}

		return $valid;
	}

}
