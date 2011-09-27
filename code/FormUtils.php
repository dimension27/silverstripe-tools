<?php

class FormUtils {

	/**
	 * Creates the standard FieldSet with a single Root.Main tab
	 * @param string $title Defines the title for the Main tab
	 * @return FieldSet
	 */
	static function createMain( $title = null ) {
		$fields = new FieldSet();
		$fields->push(new TabSet("Root", $mainTab = new Tab("Main")));
		$mainTab->setTitle($title ? $title : _t('SiteTree.TABMAIN', "Main"));
		return $fields;
	}

	static function createGroup( $title, FieldSet $fields, $fieldsToAdd, $fromTab = 'Root.Main' ) {
		return new FieldGroup($title, self::create_fieldset($fields, $fieldsToAdd, $fromTab));
	}

	/**
	 * Creates a FieldSet containing the $fieldsToAdd in $fields, removing each from $fields.
	 * @param FieldSet $fields
	 * @param array|FieldSet $fieldsToAdd
	 * @param string $fromTab
	 * @return FieldSet
	 */
	static function createFieldset( FieldSet $fields, $fieldsToAdd, $fromTab = 'Root.Main' ) {
		$rv = new FieldSet();
		$prefix = $fromTab ? "$fromTab." : '';
		foreach( $fieldsToAdd as $field ) {
			if( !is_object($field) ) {
				$field = $fields->fieldByName($prefix.$field);
			}
			$fields->removeFieldFromTab($fromTab, $field->Title());
			$rv->push($field);
		}
		return $rv;
	}

	static function flattenTabs( $fields ) {
		$flattened = new FieldSet();
		foreach( $fields as $field ) {
			if( $field instanceof TabSet ) {
				$tabSet = $field;
				foreach( self::flattenTabs($tabSet->Tabs()) as $field ) {
					$flattened->push($field);
				}
			}
			else if( $field instanceof Tab ) {
				$tab = $field;
				foreach( $tab->Fields() as $field ) {
					$flattened->push($field);
				}
			}
			else {
				$flattened->push($field);
			}
		}
		return $flattened;
	}

	static function moveToTab( FieldSet $fields, $tabName, $fieldsToAdd, $tabTitle = null, $fromTab = 'Root.Main' ) {
		$tab = $fields->findOrMakeTab($tabName, $tabTitle);
		$fieldsToAdd = self::create_fieldset($fields, $fieldsToAdd, $fromTab);
		foreach( $fieldsToAdd as $field ) {
			$tab->push($field);
		}
		return $tab;
	}

	/**
	 * Transforms a ComplexTableField or subclass into the corresponding DataObjectManager or subclass. 
	 * @param FieldSet $fields
	 * @param Controller $controller
	 * @param string $fieldName
	 * @param string $newClass The type of DataObjectManager to return 
	 * @param string $fromTab The tab in which the ComplexTableField resides
	 * @throws Exception
	 * @return DataObjectManager
	 */
	static function makeDOM( FieldSet $fields, $controller, $fieldName, $newClass = null, $fromTab = null ) {
		static $classes = array(
			'ComplexTableField' => 'DataObjectManager',
			'AssetTableField' => 'FileDataObjectManager',
			'HasManyComplexTableField' => 'HasManyDataObjectManager',
			'ManyManyComplexTableField' => 'ManyManyDataObjectManager'
		);
		$prefix = ($fromTab ? "$fromTab." : "Root.$fieldName.");
		if( $field = $fields->fieldByName($prefix.$fieldName) ) {
			$oldClass = get_class($field);
			if( $newClass || ($newClass = @$classes[$oldClass]) ) {
				$newField = new $newClass(
					$controller, // controller
					$field->Name(), // name
					$field->sourceClass(), // sourceClass
					$field->FieldList() // fieldList
					// detailFormFields
					// sourceFilter
					// sourceSort
					// sourceJoin
				);
				$fields->replaceField($fieldName, $newField);
				return $newField;
			}
			else if( !in_array($oldClass, $classes) ) {
				throw new Exception("No DataObjectManager has been defined as a replacement for the $oldClass class");
			}
		}
	}

	static function removeFields( FieldSet $fields, $fieldsToRemove ) {
		foreach( $fieldsToRemove as $fieldName ) {
			$fields->removeByName($fieldName);
		}
	}

	/**
	 * Handles the different method names for setting the upload folder in UploadifyField and FileField.
	 * @param FormField $field
	 * @param string $folder
	 */
	static function setUploadFolder( $field, $folder ) {
		return (in_array('UploadifyField', class_parents($field)) 
				? $field->setUploadFolder($folder)
				: $field->setFolderName($folder)
		);
	}

	static function getFileCMSFields( $includeDescription = false, $titleLabel = 'Title' ) {
		$fields = new FieldSet(new TextField('Title', $titleLabel));
		if( $includeDescription ) {
			$fields->push(new SimpleTinyMCEField('Description'));
		}
		$fields->push(new ReadonlyField('Filename'));
		return $fields;
	}

	static function getLabel( $label, $name = null ) {
		return new LiteralField($name, '<div class="field"><label>'.$label.'</label></div>');
	}

	static function getSelectMap( DataObjectSet $set ) {
		if( $set && $set->count() ) {
			$rv = $set->map();
		}
		else {
			$rv = array('' => '-- Empty --');
		}
		return $rv;
	}

	static function getEnumDropdown( DataObject $dataObject, $fieldName, $title = null ) {
		return new DropdownField($fieldName, $title, $dataObject->dbObject($fieldName)->enumValues());
	}

	/**
	 * Requires the following fields for persistence:
	 *   static $db => array(
	 *   	'LinkType' => 'Enum("Internal, External, File")',
	 *   	'LinkLabel' => 'Varchar(255)',
	 * 	 	'LinkTargetURL' => 'Varchar(255)'
	 *   );
	 *   static $has_one => array(
	 *   	'LinkTarget' => 'SiteTree',
	 * 	 	'LinkFile' => 'File'
	 *   );
	 * If the openInLightbox option is used then also need:
	 *   static $db => array(
	 *   	'OpenInLightbox' => 'Boolean'
	 *   );
	 * You can use the LinkFields::getLink() method to render the HTML.
	 * @param Fieldset $fields
	 * @param array $options
	 * @param string $tabName
	 */
	static function addLinkFields( $fields, $options = null, $tabName = 'Root.Main' ) {
		return LinkFields::addLinkFields($fields, $options, $tabName);
	}

}

class LinkFields {

	/**
	 * Requires the following fields for persistence:
	 *   static $db => array(
	 *   	'LinkType' => 'Enum("Internal, External, File")',
	 *   	'LinkLabel' => 'Varchar(255)',
	 * 	 	'LinkTargetURL' => 'Varchar(255)'
	 *   );
	 *   static $has_one => array(
	 *   	'LinkTarget' => 'SiteTree',
	 * 	 	'LinkFile' => 'File'
	 *   );
	 * If the openInLightbox option is used then also need:
	 *   static $db => array(
	 *   	'OpenInLightbox' => 'Boolean'
	 *   );
	 * You can use the LinkFields::getLink() method to render the HTML.
	 * @param Fieldset $fields
	 * @param array $options
	 * @param string $tabName
	 */
	static function addLinkFields( $fields, $options = null, $tabName = 'Root.Main' ) {
		if( @$options['label'] ) {
			$fields->addFieldToTab($tabName, new HeaderField($options['label'], null, 3));
		}
		$fields->addFieldToTab($tabName, $field = new TextField('LinkLabel', 'Link label'));
		// Install the urlfield module for URL validation git://github.com/chillu/silverstripe-urlfield.git
		$urlClass = class_exists('URLField') ? 'URLField' : 'TextField';
		$fields->addFieldToTab($tabName, $group = new SelectionGroup('LinkType', array(
				'Internal//Link to a page on this website' => new TreeDropdownField('LinkTargetID', 'Link target', 'SiteTree'),
				'External//Link to an external website' => new $urlClass('LinkTargetURL', 'Link target URL'),
				'File//Download a file' => new TreeDropdownField('LinkFileID', 'Download file', 'File')
		)));
		
		if( @$options['openInLightbox'] ) {
			$fields->addFieldToTab($tabName, new CheckboxField('OpenInLightbox', 'Open the link in a lightbox'));
		}
	}

	static function getLinkURL( $obj ) {
		switch( $obj->LinkType ) {
			case 'External':
				return $obj->LinkTargetURL;
			case 'Internal':
				if( ($target = $obj->LinkTarget()) && $target->exists() ) {
					return $target->Link();
				}
				else {
					return $obj->LinkTargetURL;
				}
				break;
			case 'File':
				if( ($target = $obj->LinkFile()) && $target->exists() ) {
					return $target->Link();
				}
				break;
		}
		
	}

}

?>