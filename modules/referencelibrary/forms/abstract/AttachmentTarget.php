<?php

abstract class referencelibrary_forms_abstract_AttachmentTarget extends Core_AbstractFormSmarty {

    public $attachmentModel = null;

    public function build() {
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        // attachments
        $element = new Core_Form_Element_Hidden('Attachments');
        $this->addElement($element);
        $element = new Core_Form_Element_Hidden('CoverAttachments');
        $this->addElement($element);
        $element = new Core_Form_Element_Hidden('AttachmentError');
        $element->setValue('');
        $this->addElement($element);

        $element = new Core_Form_Element_Text('AttachmentHidden');
        $element->setAttribs(array('style' => 'display:none;'));
        $this->addElement($element);
    }

    public function isValid($data) {
        $valid = parent::isValid($data);
        $getID = $this->getDto()->getId();

        if (!$this->getReadOnly()) {           

            $attachmentsValid = $this->validateAttachments();

            if (!$attachmentsValid)
                $valid = false;        }

      
        if(isset($getID) && (!$attachmentsValid))        
                  $valid = false;        
        else
         $this->storeAttachments();

        return $valid;
    }

    public function postPersist() {


        $coverUploads = isset($_FILES['SimpleAttachmentCover']) ? $_FILES['SimpleAttachmentCover'] : false;
        $newUploads = isset($_FILES['SimpleAttachment']) ? $_FILES['SimpleAttachment'] : false;

        if ($coverUploads['name'] != '') {
            $this->attachmentModel->saveCoverAttachments($this->getDto()->getId(), $this->CoverAttachments->getValue(), $totalFileSize);
        }
        if ($this->attachmentModel != null) {
            $this->attachmentModel->saveSimpleAttachments($this->getDto()->getId(), $this->Attachments->getValue(), $totalFileSize);
        }
       
        return parent::postPersist();
    }

    public function validateAttachments() {
        $valid = true;
        $newUploads = isset($_FILES['SimpleAttachment']) ? $_FILES['SimpleAttachment'] : false;
        $coverUploads = isset($_FILES['SimpleAttachmentCover']) ? $_FILES['SimpleAttachmentCover'] : false;
        $toDelete = isset($_POST['DeletedAttachment']) ? $_POST['DeletedAttachment'] : false;
        $getID = $this->getDto()->getId();



        

        if(isset($getID)){

        // If you are deleting normal pic and not adding a new one.
        if($newUploads['name'][0] == '' and $toDelete[1] <> 0){

            $this->AttachmentError->setValue('Normal and Cover pic attachments are mandatory.');
            $valid = false;

        }else{

             $allowedFiletypes = explode(",", Core_Config::get("referencelibrary", "attachable_file_types"));

                foreach ($newUploads["tmp_name"] as $k => $tmpname) {
                    $filename = $newUploads["name"][$k];
                    if (strlen(trim($filename)) > 0) {
                        $extension = strtolower(substr(strrchr($filename, '.'), 1));
                        if (!in_array($extension, $allowedFiletypes)) {
                            $this->AttachmentError->setValue('Uploaded files for Reference document must be in one the following formats: ' . implode(", ", $allowedFiletypes));
                            $valid = false;
                        }
                    }
                }

             }

        // If you are deleting cover pic and not adding a new one.
            if($coverUploads['name'][0] == '' and $toDelete[0] <> 0){

                $this->AttachmentError->setValue('Normal and Cover pic attachments are mandatory.');
                    $valid = false;

            }else{

                $allowedCoverFiletypes = explode(",", Core_Config::get("referencelibrary", "attachable_cover_file_types"));

                 $tmpname = $coverUploads["tmp_name"];
                 $filenameCover = $coverUploads["name"];                           
           
                 if (strlen(trim($filenameCover)) > 0) {
                    $extensionCover = strtolower(substr(strrchr($filenameCover, '.'), 1));
                    if (!in_array($extensionCover, $allowedCoverFiletypes)) {
                        $this->AttachmentError->setValue('Uploaded files for Cover Pic Image must be in one the following formats : ' . implode(", ", $allowedCoverFiletypes));
                        $valid = false;
                    }
                 }
                        

                 }

        }else{
                if (($newUploads['name'][0] != '' and $coverUploads['name'] != ''))  {

                    $allowedFiletypes = explode(",", Core_Config::get("referencelibrary", "attachable_file_types"));

                    foreach ($newUploads["tmp_name"] as $k => $tmpname) {
                        $filename = $newUploads["name"][$k];
                        if (strlen(trim($filename)) > 0) {
                            $extension = strtolower(substr(strrchr($filename, '.'), 1));
                            if (!in_array($extension, $allowedFiletypes)) {
                                $this->AttachmentError->setValue('Uploaded files for Reference document must be in one the following formats:' . implode(", ", $allowedFiletypes));
                                $valid = false;
                            }
                        }
                    }
                    $allowedCoverFiletypes = explode(",", Core_Config::get("referencelibrary", "attachable_cover_file_types"));
                    $tmpname = $coverUploads["tmp_name"];
                    $filenameCover = $coverUploads["name"];  
           
                     if (strlen(trim($filenameCover)) > 0) {
                        $extensionCover = strtolower(substr(strrchr($filenameCover, '.'), 1));
                        if (!in_array($extensionCover, $allowedCoverFiletypes)) {
                            $this->AttachmentError->setValue('Uploaded files for Cover Pic Image must be in one the following formats : ' . implode(", ", $allowedCoverFiletypes));
                            $valid = false;
                        }
                     }                    
                   
                } else if ($toDelete) {
                    
                } else {
                    $this->AttachmentError->setValue('Normal and Cover pic attachments are mandatory.');
                    $valid = false;
                }

        }

        return $valid;
    }

    public function storeAttachments() {

        $location = Zend_Registry::get('public_upload_location');


        $newUploads = isset($_FILES['SimpleAttachment']) ? $_FILES['SimpleAttachment'] : false;

        $coverUploads = isset($_FILES['SimpleAttachmentCover']) ? $_FILES['SimpleAttachmentCover'] : false;

        $toDelete = isset($_POST['DeletedAttachment']) ? $_POST['DeletedAttachment'] : false;

       

        if ($newUploads['name'][0] != '') {

            $urls = array("new" => array(), "delete" => array());

            $allowedFiletypes = explode(",", Core_Config::get("referencelibrary", "attachable_file_types"));

            foreach ($newUploads["tmp_name"] as $k => $tmpname) {

                $filename = $newUploads["name"][$k];

                if (strlen(trim($filename)) > 0) {
                    $extension = strtolower(substr(strrchr($filename, '.'), 1));
                    if (!in_array($extension, $allowedFiletypes)) {
                        $this->AttachmentError->setValue('Uploaded files for Reference document must be in one the following formats:' . implode(",", $allowedFiletypes));
                        $valid = false;
                    } else {

                        $lStrFilename = Core_User_Information::getUserName() . "_" . date('YmdHis') . "_" . $filename;

                        move_uploaded_file($tmpname, $location . '/' .  $lStrFilename);

                        $lObjDocumentService = new survey_models_DocumentService();

                        $lBinData = file_get_contents($location . '/' .  $lStrFilename);

                        $imgurl = $lObjDocumentService->storeImageDocumentService('uploads', 'referencelibrary', $lStrFilename, $lBinData, 0, false);

                        if ($imgurl != null) {
                            //$urls["new"][] = array("name" => $filename, "location" => $location . $lStrFilename, "filesize" => strlen($lBinData));
                            $urls["new"][] = array("name" => $filename, "location" => $imgurl, "filesize" => strlen($lBinData));
                        } else {
                            throw new Core_Exception("Attachment storage error", "Attachment storage error");
                            $this->AttachmentError->setValue('An error occured storing the file in the document repository. Please try again later.');
                            $valid = false;
                        }
                    }
                }
            }
        } 
       
        if ($toDelete) {
            foreach ($toDelete as $attId) {
                if ($attId) {
                    $urls["delete"][] = array("id" => $attId);
                }
            }
        }
        if (!empty($newUploads)) {
            //Any attachments to delete? (existing attachments... marked for deletion)
            $this->Attachments->setValue(serialize($urls));
        }


        if ($coverUploads['name'] != '') {

            $urlsCover = array("new" => array(), "delete" => array());

            $allowedCoverFiletypes = explode(",", Core_Config::get("referencelibrary", "attachable_cover_file_types"));

            $tmpname = $coverUploads["tmp_name"];
            $filenameCover = $coverUploads["name"];

            if (strlen(trim($filenameCover)) > 0) {
                $extension = strtolower(substr(strrchr($filenameCover, '.'), 1));
                if (!in_array($extension, $allowedCoverFiletypes)) {
                    $this->AttachmentError->setValue('Uploaded files for Cover Pic Image must be in one the following formats :' . implode(",", $allowedCoverFiletypes));
                    $valid = false;
                } else {

                    $lStrFilename = Core_User_Information::getUserName() . "_" . $filenameCover;

                    move_uploaded_file($tmpname, $location  . '/' . $lStrFilename);

                    $lObjDocumentService = new survey_models_DocumentService();

                    $lBinDataCover = file_get_contents($location  . '/' . $lStrFilename);
                   

                    $imgurlCover = $lObjDocumentService->storeImageDocumentService('uploads', 'referencelibrary', $lStrFilename, $lBinDataCover, 0, false);

                    if ($imgurlCover != null) {
                        $urlsCover["new"][] = array("name" => $filenameCover, "location" => $imgurlCover, "filesize" => strlen($lBinDataCover));
                    } else {
                        throw new Core_Exception("Attachment storage error", "Attachment storage error");
                        $this->AttachmentError->setValue('An error occured storing the file in the document repository. Please try again later.');
                        $valid = false;
                    }
                }
            }
        } 
      
        //Set it here so we can get in controller and store location back in DTO on save
        if ($coverUploads['name'] != '') {         

            $this->CoverAttachments->setValue(serialize($urlsCover));
        }

        return true;
    }

}
