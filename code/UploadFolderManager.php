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

	function getUploadFolderForObject( DataObject $dataObject, FormField $field, $subDir = null ) {
		$folder = '';
		$options = isset(self::$options[get_class($dataObject)])
				? self::$options[get_class($dataObject)]
				: self::$defaultOptions;
		if( class_exists('Subsite') 
				&& $options['subsite'] 
				&& $site = Subsite::currentSubsite() ) {
			$folder .= Utils::slugify($site->Title, false);
		}
		$folder .= $options['folder']
				? '/'.$options['folder']
				: '/Uploads/'.preg_replace('/[^[:alnum:]]/', '', $dataObject->plural_name());
		$folder .= $options['date']
				? '/'.date($options['date']) : '';
		$folder .= $options['ID']
				? '/'.$dataObject->ID : '';
		$folder .= $options['Title']
				? '/'.preg_replace('/[^[:alnum:]]/', '', $dataObject->getTitle()) : '';
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
		'subsite' => true,
	);

	static function setOptions( $className, $options ) {
		self::$options[$className] = array_merge(self::$defaultOptions, $options);
	}

	static function printUploadFolders() {
		foreach( ClassInfo::allClasses() as $className ) {
			if( !in_array($className, array('SS_Benchmark_Timer'))
					&& class_exists($className)
					&& is_subclass_of($className, 'DataObject') ) {
				new $className;
			}
		}
		foreach( self::$options as $className => $options ) {
			echo "$className: ".self::getUploadFolder(new $className, new FileUploadField($className)).NL;
		}
	}

}

interface IUploadFolderManager {
	function getUploadFolderForObject( DataObject $dataObject, FormField $field, $subDir = null );
}

class UploadFolderManagerController extends CliController {

	function print_upload_folders() {
		UploadFolderManager::printUploadFolders();
	}

}
