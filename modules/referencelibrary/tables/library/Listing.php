<?php

class referencelibrary_tables_library_Listing extends Core_Table
{

	public function build()
	{
		$codes = new Core_Codes();
		$column = new Core_Table_Column('Id');
		$column->setWidth(5);
		$column->addContent(new Core_Table_Column_Content_Text('Id'));
		$column->setFilter(new Core_Table_Column_Filter_Text());
		$this->addColumn($column);

		$column = new Core_Table_Column('Description');
		$column->setWidth(25);
		$column->addContent(new Core_Table_Column_Content_Text('Description'));
		$column->setFilter(new Core_Table_Column_Filter_Text());
		$this->addColumn($column);

		$column = new Core_Table_Column('Type');
		$column->setWidth(10);
		$options = $codes->getOptionsByCodeName(Core_Config::get('referencelibrary', 'codes_reference_doctypes'));
		$content = new Core_Table_Column_Content_Lookup('DocTypeId');
		$content->setOptions($options);
		$column->addContent($content);
		$filter = new Core_Table_Column_Filter_Select(array());
		$filter->setOptionsWithKeys($options);
		$column->setFilter($filter);
		$this->addColumn($column);

		$column = new Core_Table_Column('Priority');
		$column->setWidth(10);
		$options = $codes->getOptionsByCodeName(Core_Config::get('referencelibrary',
			'codes_reference_docprioritytypes'));
		$content = new Core_Table_Column_Content_Lookup('DocPriorityId');
		$content->setOptions($options);
		$column->addContent($content);
		$filter = new Core_Table_Column_Filter_Select(array());
		$filter->setOptionsWithKeys($options);
		$column->setFilter($filter);
		$this->addColumn($column);

		$column = new Core_Table_Column('Start');
		$column->setWidth(15);
		$column->addContent(new Core_Table_Column_Content_Text('StartDateTime'));
		$column->setFilter(new Core_Table_Column_Filter_Text());
		$this->addColumn($column);

		$column = new Core_Table_Column('End');
		$column->setWidth(15);
		$column->addContent(new Core_Table_Column_Content_Text('EndDateTime'));
		$column->setFilter(new Core_Table_Column_Filter_Text());
		$this->addColumn($column);

		$column = new Core_Table_Column('Created By');
		$column->setWidth(10);
		$column->addContent(new Core_Table_Column_Content_Text('Creator'));
		$column->setFilter(new Core_Table_Column_Filter_Text());
		$this->addColumn($column);

		$column = new Core_Table_Column('Cover Pic');
		$column->setWidth(10);
		$column->addContent(new Core_Table_Column_Content_Image('Location', array('width' => '70', 'height' => '40')));
		$column->setExportable(false);
		$column->setIsSortable(false);
		$this->addColumn($column);

		$this->setRowHeight(40);
		$this->setOnclickUrl('/referencelibrary/library/details/{Id}');
	}

}
