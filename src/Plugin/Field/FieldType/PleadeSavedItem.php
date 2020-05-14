<?php

namespace Drupal\pleade\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Provides a field type of PleadeSaved.
 *
 * @FieldType(
 *   id = "pleade_saved",
 *   label = @Translation("Pleade Saved field"),
 *   description = @Translation("A field which will permit to create a block linked to a Pleade saved."),
 *   category = @Translation("Pleade"),
 *   module = "pleade",
 *   default_widget = "pleade_saved_widget",
 *   default_formatter = "pleade_saved_formatter"
 * )
 */

class PleadeSavedItem extends FieldItemBase implements FieldItemInterface {

	/**
	 * {@inheritdoc}
	 */
	public static function schema(FieldStorageDefinitionInterface $field_definition) {
		return array(
				// columns contains the values that the field will store
				'columns' => array(
						'saved_id' => array(
								'type' => 'varchar',
								'length' => 120,
						),
						'drupal_title' => array(
								'type' => 'varchar',
								'length' => 120,
						),
						'items_number' => array(
								'type' => 'varchar',
								'length' => 2,
						),
				),
		);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
		$properties = [];
		//$properties = parent::propertyDefinitions($field_definition);
		
		$properties['saved_id'] = DataDefinition::create('string')
		->setLabel(t('Saved id'))
		->setDescription(t('Pleade id of saved to link. This field is hidden.'));
		
		$properties['drupal_title'] = DataDefinition::create('string')
		->setLabel(t('Title'))
		->setDescription(t('Title of this block. If empty, title of the linked saved in Pleade will be used.'));
		
		$properties['items_number'] = DataDefinition::create('string')
		->setLabel(t('Number of items'))
		->setDescription(t('Number of items to show. Default is 6.'));
	
		return $properties;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function isEmpty() {
		$saved_id = $this->get('saved_id')->getValue();
		return $saved_id === NULL || $saved_id === '';
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function mainPropertyName() {
		return 'saved_id';
	}

}
