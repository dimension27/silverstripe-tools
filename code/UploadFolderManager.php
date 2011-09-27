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

	static function setUploadFolderForObject( DataObject $dataObject, FormField $field ) {
		foreach( self::$providers as $provider ) {
			if( $folder = $provider->getUploadFolderForObject($dataObject, $field) ) {
				self::setUploadFolder($field, $folder);
				return $folder;
			}
		}
		if( $folder = self::getDefaultProvider()->getUploadFolderForObject($dataObject, $field) ) {
			self::setUploadFolder($field, $folder);
			return $folder;
		}
	}

	static function setUploadFolder( FormField $field, string $folder ) {
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
		$folder = preg_replace('/[^[:alnum:]]/', '', $dataObject->plural_name());
		return 'Uploads/'.$folder.'/'.date('Y');
	}

}

interface IUploadFolderManager {
	function getUploadFolderForObject( DataObject $dataObject, FormField $field );
}

?>