<?php

require_once('phpnotes/services/entity/dto/AbstractServiceDTO.php');

class phpnotes_services_referencelibrary_dto_LibraryDTO extends phpnotes_services_entity_dto_AbstractServiceDTO
{

	private $mLngDocTypeId;
	private $mLngDocPriorityId;
	private $mColAttachments;
	private $mColStart = null;
	private $mColEnd = null;
	private $mLocation = null;

	public function getDocTypeId()
	{
		if ($this->mLngDocTypeId == null) {
			$this->mLngDocTypeId = new phpnotes_services_code_dto_CodeDTO();
		}
		return $this->mLngDocTypeId;
	}

	public function setDocTypeId($lCodStatus)
	{
		$this->mLngDocTypeId = $lCodStatus;
	}

	public function getDocPriorityId()
	{
		if ($this->mLngDocPriorityId == null) {
			$this->mLngDocPriorityId = new phpnotes_services_code_dto_CodeDTO();
		}
		return $this->mLngDocPriorityId;
	}

	public function setDocPriorityId($lCodDocPriorityId)
	{
		$this->mLngDocPriorityId = $lCodDocPriorityId;
	}

	public function getEndDateTime()
	{
		return $this->mColEnd;
	}

	public function setEndDateTime($value)
	{
		$this->mColEnd = $value;
	}

	public function getStartDateTime()
	{
		return $this->mColStart;
	}

	public function setStartDateTime($value)
	{
		$this->mColStart = $value;
	}

	public function getAttachments()
	{

		if ($this->mColAttachments == null) {

			$this->mColAttachments = new phpnotes_tools_collections_proxy_ProxiedLazyArray();

			if ($this->getId() > 0) {
				$var = array(
					"'phpnotes_services_ReferenceLibrary_dto_CoverPicDTO'",
					"'phpnotes_services_ReferenceLibrary_dto_RefLibDTO'"
				);


				$lObjCriteria = new phpnotes_services_entity_criteria_DefaultCriteria();
				$lObjCriteria->setCriterion(array(
						array('FIELD' => 'ApplicableId', 'OPERATOR' => '=', 'SEARCHSTRING' => $this->getId()),
						array(
							'FIELD' => 'ApplicableType',
							'OPERATOR' => 'IN',
							'SEARCHSTRING' => "(" . implode(",", $var) . ")",
							'LITERAL' => true
						),

					)
				);
				$attachmentApplicabilities = EntityUtils::getAll("phpnotes_services_billing_dto_AttachmentApplicabilityDTO",
					$lObjCriteria);
				if (!empty($attachmentApplicabilities)) {
					foreach ($attachmentApplicabilities as $applicability) {
						$this->mColAttachments->set(null, $applicability->getAttachment());
					}
				}
			}
		}

		return $this->mColAttachments;
	}


}