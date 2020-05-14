<?php

namespace Drupal\pleade\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsWidgetBase;

use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Drupal\Component\Serialization\Json;

/**
 * Plugin implementation of the 'Pleade embed' widget.
 *
 * @FieldWidget(
 *   id = "pleade_embed_widget",
 *   label = @Translation("Pleade Embed Widget"),
 *   field_types = {
 *     "pleade_embed"
 *   }
 * )
 * @see \Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsWidgetBase
 */
class PleadeEmbedWidget extends OptionsWidgetBase {
	
	/**
	 * {@inheritdoc}
	 */
	public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
		$element = [];
		
		$element['pleade_relative_url'] = array(
				'#type' => 'textfield',
				'#title' => $this->t('Pleade relative URL'),
				'#description' => $this->t('Relative URL representing the page from pleade to embed. On the complet URl, takes part afeter /pleade/ or /archives-en-ligne/. For exemple, http://pleade.com/pleade/search-form.html?name=default, take search-form.html?name=default.'),
				'#placeholder' => t('Pleade relative URL'),
				'#default_value' => isset($items[$delta]->pleade_relative_url) ? (string)$items[$delta]->pleade_relative_url : NULL,
		);
	
		$element['drupal_title'] = array(
				'#type' => 'textfield',
				'#title' => $this->t('Drupal title'),
				'#description' => $this->t('Title of this block. Warning : this title is different to Pleade\'s title.'),
				'#placeholder' => t('Drupal title'),
				'#default_value' => isset($items[$delta]->drupal_title) ? $items[$delta]->drupal_title : NULL,
		);
	
		return $element;
	}
	
}