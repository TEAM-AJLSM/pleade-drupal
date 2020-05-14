<?php

namespace Drupal\pleade\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Provides a field type of PleadeEmbed.
 *
 * @FieldType(
 *   id = "pleade_embed",
 *   label = @Translation("Pleade Embed field"),
 *   description = @Translation("A field which will permit to create a block for embedding a Pleade page"),
 *   category = @Translation("Pleade"),
 *   module = "pleade",
 *   default_widget = "pleade_embed_widget",
 *   default_formatter = "pleade_embed_formatter"
 * )
 */

class PleadeEmbedItem extends FieldItemBase implements FieldItemInterface {

	/**
	 * {@inheritdoc}
	 */
	public static function schema(FieldStorageDefinitionInterface $field_definition) {
		return array(
				// columns contains the values that the field will store
				'columns' => array(
						'pleade_relative_url' => array(
								'type' => 'varchar',
								'length' => 500,
						),
						'drupal_title' => array(
								'type' => 'varchar',
								'length' => 120,
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
		
		$properties['pleade_relative_url'] = DataDefinition::create('string')
		->setLabel(t('Pleade relative URL'))
		->setDescription(t('Relative URL representing the page from pleade to embed'));
		
		$properties['drupal_title'] = DataDefinition::create('string')
		->setLabel(t('Title'))
		->setDescription(t('Title of this block. If empty, title of the linked saved in Pleade will be used.'));
	
		return $properties;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function isEmpty() {
		$pleade_relative_url = $this->get('pleade_relative_url')->getValue();
		return $pleade_relative_url === NULL || $pleade_relative_url === '';
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function mainPropertyName() {
		return 'pleade_relative_url';
	}

}
