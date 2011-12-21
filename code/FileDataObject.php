<?php
/**
 * Provides common implementation of a DataObject that provides a wrapper around a File.
 * Handles updating the Title of the File onAfterWrite().
 * @author simonwade
 */
class FileDataObject extends DataObject {

	static $db = array(
		'Title' => 'Varchar(255)',
		'Content' => 'HTMLText',
	);

	static $file_relation = 'File';

	function getCMSFields() {
		$fields = self::createMain();
		$fields->addFieldToTab('Root.Main', $field = new TextField('Title'));
		$fields->addFieldToTab('Root.Main', $field = new SimpleTinyMCEField('Content'));
		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

	function getFile() {
		$method = $this->stat('file_relation');
		return $this->$method();
	}

	function onAfterWrite() {
		parent::onAfterWrite();
		$file = $this->getFile();
		if( $file->exists() ) {
			$file->Title = $this->Title;
			$file->write();
		}
	}

}
