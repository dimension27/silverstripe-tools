<?php

class UploadFolderManager implements IUploadFolderManager {

	/**
	 * @var array of IUploadFolderManager objects 
	 */
	protected static $providers = array();

	/**
	 * @var IUploadFolderManager The provider that will be used if no others return a folder,
	 * defaults to an instance of this class.
	 */
	protected static $defaultProvider;

	static function addProvider( IUploadFolderManager $provider ) {
		self::$providers[] = $provider;
	}

	static function getUploadFolderForObject( DataObject $dataObject, FormField $field ) {
		foreach( self::$providers as $provider ) {
			if( $folder = $provider->getUploadFolderForObject($dataObject, $field) ) {
				return $folder;
			}
		}
		if( $folder = self::getDefaultProvider()->getUploadFolderForObject($dataObject, $field) ) {
			return $folder;
		}
	}

	static function setUploadFolderForObject( DataObject $dataObject, FormField $field ) {
		if( $folder = self::getUploadFolderForObject($dataObject, $field) ) {
			self::setFieldUploadFolder($field, $folder);
			return $folder;
		}
	}

	static function setFieldUploadFolder( FormField $field, string $folder ) {
		if( in_array('UploadifyField', $superclasses = class_parents($field))
				|| in_array('FileDataObjectManager', $superclasses) ) {
			$field->setUploadFolder($folder);
		}
		else {
			$field->setFolderName($folder);
		}
	}

	static function getDefaultProvider() {
		if( !isset(self::$defaultProvider) ) {
			self::$defaultProvider = new UploadFolderManager();
		}
		return self::$defaultProvider;
	}

	function getUploadFolderForObject( DataObject $dataObject, FormField $field ) {
		$options = isset(self::$options[get_class($dataObject)])
				? self::$options[get_class($dataObject)]
				: self::$defaultOptions;
		$folder = $options['folder']
				? $options['folder']
				: 'Uploads/'.preg_replace('/[^[:alnum:]]/', '', $dataObject->plural_name());
		$folder .= $options['date']
				? '/'.date($options['date']) : '';
		$folder .= $options['ID']
				? '/'.$dataObject->ID : '';
		$folder .= $options['Title']
				? '/'.preg_replace('/[^[:alnum:]]/', '', $dataObject->getTitle()) : '';
		
		return $folder;
	}

	public static $options = array();
	public static $defaultOptions = array(
		'folder' => null,
		'date' => 'Y',
		'ID' => null,
		'Title' => null,
	);

	static function setOptions( $className, $options ) {
		self::$options[$className] = array_merge(self::$defaultOptions, $options);
	}

}

interface IUploadFolderManager {
	function getUploadFolderForObject( DataObject $dataObject, FormField $field );
}

?>