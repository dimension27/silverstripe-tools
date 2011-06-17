<?php

class FormUtils {

	static function createGroup( $title, FieldSet $fields, $fieldsToAdd, $fromTab = 'Root.Main' ) {
		return new FieldGroup($title, self::create_fieldset($fields, $fieldsToAdd, $fromTab));
	}

	/**
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

	static function moveToTab( FieldSet $fields, $tabName, $fieldsToAdd, $tabTitle = null, $fromTab = 'Root.Main' ) {
		$tab = $fields->findOrMakeTab($tabName, $tabTitle);
		$fieldsToAdd = self::create_fieldset($fields, $fieldsToAdd, $fromTab);
		foreach( $fieldsToAdd as $field ) {
			$tab->push($field);
		}
		return $tab;
	}

	static function makeDOM( FieldSet $fields, $controller, $fieldName, $fromTab = null ) {
		static $classes = array(
			'ComplexTableField' => 'DataObjectManager',
			'AssetTableField' => 'FileDataObjectManager',
			'HasManyComplexTableField' => 'HasManyDataObjectManager',
			'ManyManyComplexTableField' => 'ManyManyDataObjectManager'
		);
		$prefix = ($fromTab ? "$fromTab." : "Root.$fieldName.");
		if( $field = $fields->fieldByName($prefix.$fieldName) ) {
			$oldClass = get_class($field);
			if( $newClass = @$classes[$oldClass] ) {
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
			}
			else if( !in_array($oldClass, $classes) ) {
				throw new Exception("No DataObjectManager has been defined as a replacement for the $oldClass class");
			}
		}
	}

	/**
	 * @return FieldSet
	 */
	static function createMain( $title = null ) {
		$fields = new FieldSet();
		$fields->push(new TabSet("Root", $mainTab = new Tab("Main")));
		$mainTab->setTitle($title ? $title : _t('SiteTree.TABMAIN', "Main"));
		return $fields;
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

	static function getFileCMSFields( $includeDescription = false ) {
		$fields = new FieldSet(new TextField('Title'));
		if( $includeDescription ) {
			$fields->push(new SimpleTinyMCEField('Description'));
		}
		$fields->push(new ReadonlyField('Filename'));
		return $fields;
	}

}

?>