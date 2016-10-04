<?php

require_once('phpnotes/services/entity/dto/AbstractServiceDTO.php');

class phpnotes_services_phonedb_dto_UserCodeDTO extends phpnotes_services_entity_dto_AbstractServiceDTO {
	
	 /**
     * This variable holds the description of the user code 
     * 
     * @property
     * @access private
     * @var string
     */
	private $mStrDescription = null;
	
	/**
     * This method returns the description of the user code
     * 
     * @access public
     * @return string
     */
	public function getDescription()
	{
		return $this->mStrDescription;
	}
	
	/**
     * Set the description of the user code
     * 
     * @access public
     * @param $lStrDescription
     * @return void
     */
	public function setDescription($lStrDescription)
	{
		$this->mStrDescription = $lStrDescription;
	}
}