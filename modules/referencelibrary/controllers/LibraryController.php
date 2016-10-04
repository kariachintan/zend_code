<?php

class referencelibrary_LibraryController extends Core_Controller_Action
{

	public function listingAction()
	{
		$this->setModel(new referencelibrary_models_library_Listing());
		$this->setTable(new referencelibrary_tables_library_Listing());

		Core_State::set("referencelibrary", "validation_invalid_employee_numbers", '');
		Core_State::set("referencelibrary", "RemovedEmployees", '');

		parent::listingAction();
	}

	function detailsAction()
	{

		Core_State::set('referencelibrary.details', 'refID', $this->id);

		$this->setModel(new referencelibrary_models_library_Details());
		$this->setForm(new referencelibrary_forms_library_Details());


		$this->view->filetypes = explode(",", Core_Config::get("referencelibrary", "attachable_file_types"));
		$this->setDeleteEnabled(true);

		parent::detailsAction();
	}

	function prepareFormDisplay()
	{

		parent::prepareFormDisplay();
		$this->view->attachments = $this->getModel()->getAttachmentsInfo($this->getDto()->getId());

	}

	public function postPersist()
	{

		if ($this->id) {
			// Check if Type updated to Public then delete the mappings.            
			$codes = new Core_Codes();
			$docTypeCodePublic = $codes->getCodeIdByCodeName(Core_Config::get('referencelibrary',
				'codes_reference_doctypes_public'));
			$docTypeId = $this->getDto()->getDocTypeId();
			if ($docTypeCodePublic == $docTypeId) {
				$this->setModel(new referencelibrary_models_library_Details());
				$this->getModel()->deleteHeaderMappings($this->id);
			}

			$message = "Record updated successfully.";

			$this->_redirect('/' . $this->getModuleName() . '/' . $this->getControllerName() . '/' . $this->getListingActionName() . '?msg=' . urlencode($message));

		} else {
			$message = "Record added successfully.";

			$codes = new Core_Codes();
			$docTypeCode = $codes->getCodeIdByCodeName(Core_Config::get('referencelibrary',
				'codes_reference_doctypes_individual'));
			$docTypeId = $_POST['DocTypeId'];

			if (($docTypeId == $docTypeCode) && (Core_Security::isAllowed('referencelibrary', 'admin'))) {
				$this->_redirect('/referencelibrary/employeeassoc/assignEmployees/' . $this->getDto()->getId());
			} else {
				$this->_redirect('/' . $this->getModuleName() . '/' . $this->getControllerName() . '/' . $this->getListingActionName() . '?msg=' . urlencode($message));
			}
		}


	}

	function deleteAction()
	{

		$this->setModel(new referencelibrary_models_library_Details());

		$this->getModel()->deleteHeaderMappings($this->id);

		parent::deleteAction();
	}

	protected function prepareDetailsControls()
	{


		// Checking if the doctype is Individual, then show Employee Association button.
		$codes = new Core_Codes();
		$docTypeCode = $codes->getCodeIdByCodeName(Core_Config::get('referencelibrary',
			'codes_reference_doctypes_individual'));
		$docTypeId = $this->getDto()->getDocTypeId();

		if ($this->id) {

			if (($docTypeId == $docTypeCode) && (Core_Security::isAllowed('referencelibrary', 'admin'))) {
				$control = new Core_Control_Item_Custom('Employee Association',
					array('href' => '/referencelibrary/employeeassoc/assignEmployees/' . $this->id));
				$this->view->controls->addItem($control);

			}
		}
		parent::prepareDetailsControls();

	}

	function attachmentbyidAction()
	{
		$lStrResource = $_GET['resource'];

		if (is_numeric($lStrResource)) {
			error_reporting(E_ALL | ~E_STRICT);
			$lObjResource = EntityUtils::get('phpnotes_services_activity_dto_AttachmentDTO', $lStrResource);
			$lStrResource = $this->getAttachmentUrl($lObjResource);

			$delegate = $this->getDelegate();
			$document = new phpnotes_services_cms_dto_DocumentDto($lStrResource);
			header('Cache-Control: public');
			header('Content-type: ' . $lObjResource->getMimeType());
			header('Content-disposition: attachment; filename="' . $lObjResource->getName() . '"');

			$filepath = str_replace('workspace://Local/', Core_Config::get('global', 'documentstorage_base_path'),
				$lStrResource);

			ob_end_flush();

			echo file_get_contents($filepath);

			die;
		} else {
			header('Cache-Control: public');
			header("Location: " . Zend_Registry::get('public_url') . '/images/noimage.png');
		}
	}

	public function getAttachmentUrl($lObjResource)
	{
		$lStrResource = 'none';

		if ($lObjResource->getId() > 0) {
			$lStrResource = $lObjResource->getLocation();
			if (strlen($lStrResource) < 1) {
				$lStrResource = $lObjResource->getDescription();
			}
			if (strlen($lStrResource) < 1) {
				$lStrResource = 'none';
			}
		}


		return $lStrResource;
	}

	protected function getDelegate()
	{
		return phpnotes_tools_patterns_delegate_factory_DelegateFactory::create('phpnotes_services_cms_delegate_IDocumentDelegate');
	}

}

