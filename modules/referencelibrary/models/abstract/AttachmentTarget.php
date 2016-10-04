<?php

abstract class referencelibrary_models_abstract_AttachmentTarget extends Core_Models_Abstract_Details
{

	/**
	 *
	 * @param type $postData
	 * @param type $totalSize
	 * @return \phpnotes_tools_collections_proxy_ProxiedLazyArray|boolean
	 * @throws Core_Exception
	 */
	public function saveSimpleAttachments($id, $attachmentStr, &$totalSize)
	{
		//'Attachments' will contains a JSON encoded list of attachments
		$attachmentArr = unserialize($attachmentStr);

		$totalSize = 0;
		$attachments = $attachmentArr['new'];
		$deletes = $attachmentArr['delete'];
		if (!empty($attachments)) {
			foreach ($attachments as $key => $att) {

				$fileSize = $att['filesize'];

				//1.store attachment in system storage
				$totalSize += $fileSize;

				$fileUrl = $att['location'];

				if ($fileUrl != null) {

					// TODO : dictionary
					$mimetype = 'application/octet-stream';
					$extensions = explode('.', $att['name']);
					if (count($extensions) >= 1) {
						$extension = strtolower(array_pop($extensions));
					} else {
						$extension = '';
					}
					switch ($extension) {
						case 'txt':
							$mimetype = 'text/plain';
							break;
						case 'doc':
							$mimetype = 'application/msword';
							break;
						case 'docx':
							$mimetype = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
							break;
						case 'pdf':
							$mimetype = 'application/pdf';
							break;
						case 'csv':
							$mimetype = 'text/csv';
							break;
						case 'xls':
							$mimetype = 'application/excel';
							break;
						case 'xlsx':
							$mimetype = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
							break;
						case 'jpg':
							$mimetype = 'image/jpeg';
							break;
						case 'jpeg':
							$mimetype = 'image/jpeg';
							break;
						case 'tiff':
							$mimetype = 'image/tiff';
							break;
						case 'gif':
							$mimetype = 'image/gif';
							break;
						case 'bmp':
							$mimetype = 'image/bmp';
							break;
						case 'png':
							$mimetype = 'image/png';
							break;
						case 'avi':
							$mimetype = 'video/avi';
							break;
						case 'mov':
							$mimetype = 'video/quicktime';
							break;
						default:
							$mimetype = 'application/octet-stream';
							break;
					}

					//2. Store Attachment
					$attachDto = new phpnotes_services_quotation_dto_AttachmentDTO();
					$attachDto->setName($att['name']);
					$attachDto->setAttachmentTypeId(2);
					$attachDto->setDescription("");
					$attachDto->setLocation($fileUrl);
					$attachDto->setAttachmentSize($fileSize);
					$attachDto->setMimeType($mimetype);
					$attachmentId = EntityUtils::persist($attachDto);

					Amerch_Log::info("referencelibrary", "AttachmentDTO",
						"Store attachment " . print_r($attachDto, true));

					//3. Store Attachment Applicability
					$attAppDto = new phpnotes_services_quotation_dto_AttachmentApplicabilityDTO();
					$attAppDto->setApplicableType('phpnotes_services_ReferenceLibrary_dto_RefLibDTO');
					$attAppDto->setAttachmentId($attachmentId);
					$attAppDto->setApplicableId($id);

					Amerch_Log::info("referencelibrary", "AttachmentApplicabilityDTO",
						"Store Attachment Applicability " . print_r($attAppDto, true));

					EntityUtils::persist($attAppDto);
				} else {
					throw new Core_Exception("An error occured storing attachment $filename in document repository.",
						"An error occured storing attachment {$att['destination']} in document repository.");
				}
			}
		}

		//Any deletes?
		if (!empty($deletes)) {
			foreach ($deletes as $d) {

				self::deleteAttachment($id, "", $d['id']);
			}
		}
	}

	public function saveCoverAttachments($id, $attachmentStr, &$totalSize)
	{
		$attachmentArr = unserialize($attachmentStr);
		$totalSize = 0;
		$attachments = $attachmentArr['new'];
		$deletes = $attachmentArr['delete'];

		if (!empty($attachments)) {
			foreach ($attachments as $key => $att) {

				$fileSize = $att['filesize'];

				//1.store attachment in system storage
				$totalSize += $fileSize;

				$fileUrl = $att['location'];

				if ($fileUrl != null) {

					// TODO : dictionary
					$mimetype = 'application/octet-stream';
					$extensions = explode('.', $att['name']);
					if (count($extensions) >= 1) {
						$extension = strtolower(array_pop($extensions));
					} else {
						$extension = '';
					}
					switch ($extension) {
						case 'txt':
							$mimetype = 'text/plain';
							break;
						case 'doc':
							$mimetype = 'application/msword';
							break;
						case 'docx':
							$mimetype = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
							break;
						case 'pdf':
							$mimetype = 'application/pdf';
							break;
						case 'csv':
							$mimetype = 'text/csv';
							break;
						case 'xls':
							$mimetype = 'application/excel';
							break;
						case 'xlsx':
							$mimetype = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
							break;
						case 'jpg':
							$mimetype = 'image/jpeg';
							break;
						case 'jpeg':
							$mimetype = 'image/jpeg';
							break;
						case 'tiff':
							$mimetype = 'image/tiff';
							break;
						case 'gif':
							$mimetype = 'image/gif';
							break;
						case 'bmp':
							$mimetype = 'image/bmp';
							break;
						case 'png':
							$mimetype = 'image/png';
							break;
						case 'avi':
							$mimetype = 'video/avi';
							break;
						case 'mov':
							$mimetype = 'video/quicktime';
							break;
						default:
							$mimetype = 'application/octet-stream';
							break;
					}

					//2. Store Attachment
					$attachDto = new phpnotes_services_quotation_dto_AttachmentDTO();
					$attachDto->setName($att['name']);
					$attachDto->setAttachmentTypeId(2);
					$attachDto->setDescription("");
					$attachDto->setLocation($fileUrl);
					$attachDto->setAttachmentSize($fileSize);
					$attachDto->setMimeType($mimetype);
					$attachmentId = EntityUtils::persist($attachDto);


					Amerch_Log::info("referencelibrary", "AttachmentDTO",
						"Store attachment " . print_r($attachDto, true));

					//3. Store Attachment Applicability
					$attAppDto = new phpnotes_services_quotation_dto_AttachmentApplicabilityDTO();
					$attAppDto->setApplicableType('phpnotes_services_ReferenceLibrary_dto_CoverPicDTO');
					$attAppDto->setAttachmentId($attachmentId);
					$attAppDto->setApplicableId($id);

					Amerch_Log::info("referencelibrary", "AttachmentApplicabilityDTO",
						"Store Attachment Applicability " . print_r($attAppDto, true));

					EntityUtils::persist($attAppDto);
				} else {
					throw new Core_Exception("An error occured storing attachment $filename in document repository.",
						"An error occured storing attachment {$att['destination']} in document repository.");
				}
			}
		}
	}

	/**
	 *
	 * @param type $location
	 * @param type $id
	 */
	public function deleteAttachment($applicableId, $location = "", $id = '')
	{

		if (!$location) {
			if ($id) {
				//get Location first
				$dto = EntityUtils::get("phpnotes_services_quotation_dto_AttachmentDTO", $id);
				$location = $dto->getLocation();

				//Delete
				$lObjCriteria = new phpnotes_services_entity_criteria_DefaultCriteria();
				$lObjCriteria->setCriterion(array(
						array('FIELD' => 'AttachmentId', 'OPERATOR' => '=', 'SEARCHSTRING' => $id),
						array('FIELD' => 'ApplicableId', 'OPERATOR' => '=', 'SEARCHSTRING' => $applicableId)
					)
				);
				$attachmentApplicabilities = EntityUtils::getAll("phpnotes_services_quotation_dto_AttachmentApplicabilityDTO",
					$lObjCriteria);

				EntityUtils::delete($attachmentApplicabilities[0]);
				EntityUtils::delete($dto);
			}
		}

		// if ($location) {
		//     //physically delete file
		//     $lObjDocumentService = new survey_models_DocumentService();
		//     $lObjDocumentService->deleteLocalDocument($location);
		// }
	}

	/**
	 *
	 * @param type $id
	 * @return type
	 */
	public function getAttachmentsInfo($id)
	{
		$dto = EntityUtils::get($this->getDtoClassName(), $id, false);

		$attachmentDtos = $dto->getAttachments()->getDecorated();

		$info = array();
		foreach ($attachmentDtos as $d) {

			$info[] = array(
				'id' => $d->getId(),
				'location' => urlencode($d->getLocation()),
				'filename' => ellipsis($d->getName(), 45)
			);
		}

		return $info;
	}

}