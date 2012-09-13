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

	static function getUploadFolder( DataObject $dataObject, FormField $field = null, $subDir = null ) {
		$folder = null;
		foreach( self::$providers as $provider ) {
			if( $folder = $provider->getUploadFolderForObject($dataObject, $field, $subDir) ) {
				break;
			}
		}
		if( !isset($folder) || !$folder ) {
			$folder = self::getDefaultProvider()->getUploadFolderForObject($dataObject, $field, $subDir);
		}
		return $folder;
	}

	static function setUploadFolder( DataObject $dataObject, FormField $field, $subDir = null ) {
		if( $folder = self::getUploadFolder($dataObject, $field, $subDir) ) {
			self::setFieldUploadFolder($field, $folder);
			return $folder;
		}
	}

	static function setDOMUploadFolder( DataObjectManager $field, $subDir = null, $dataObject = null ) {
		if( !$dataObject ) {
			$dataObject = singleton($field->sourceClass());
		}
		if( $folder = self::getUploadFolder($dataObject, $field, $subDir) ) {
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

	/**
	 * @see IUploadFolderManager::getUploadFolderForObject($dataObject, $field, $subDir)
	 * @param DataObject $dataObject
	 */
	function getUploadFolderForObject( $dataObject, FormField $field, $subDir = null ) {
		$folder = '';
		$class = is_object($dataObject) ? get_class($dataObject) : $dataObject;
		$options = isset(self::$options[$class])
				? self::$options[$class]
				: self::$defaultOptions;
		if( class_exists('Subsite') 
				&& $options['subsite'] 
				&& $site = Subsite::currentSubsite() ) {
			$folder .= Utils::slugify($site->Title, false);
		}
		$folder .= ($options['folder'] !== null
				? '/'.$options['folder']
				: ($dataObject instanceof SiteTree
						? '/Uploads'
						: '/Uploads/'.Utils::slugify($dataObject->plural_name())
				)
		);
		$folder .= $options['date']
				? '/'.date($options['date']) : '';
		$folder .= $options['ID']
				? '/'.$dataObject->ID : '';
		$folder .= $options['ID-Title']
				? '/'.$dataObject->ID.'-'.Utils::slugify($dataObject->getTitle()) : '';
		$folder .= $options['Title']
				? '/'.Utils::slugify($dataObject->getTitle()) : '';
		if( $subDir ) {
			$folder .= '/'.$subDir;
		}
		return $folder;
	}

	public static $options = array();
	public static $defaultOptions = array(
		'folder' => null,
		'date' => 'Y',
		'ID' => null,
		'Title' => null,
		'ID-Title' => null,
		'subsite' => true,
	);

	static function setOptions( $className, $options ) {
		self::$options[$className] = array_merge(self::$defaultOptions, $options);
	}

}

interface IUploadFolderManager {
	function getUploadFolderForObject( $dataObject, FormField $field, $subDir = null );
}

class UploadFolderManagerController extends CliController {

	function print_upload_folders() {
		UploadFolderManager::printUploadFolders();
	}

}
