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
 * Plugin implementation of the 'Pleade saved' widget.
 *
 * @FieldWidget(
 *   id = "pleade_saved_widget",
 *   label = @Translation("Pleade Saved Widget"),
 *   field_types = {
 *     "pleade_saved"
 *   }
 * )
 * @see \Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsWidgetBase
 */
class PleadeSavedWidget extends OptionsWidgetBase {
	
	/**
	 * {@inheritdoc}
	 */
	public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
		$element = [];
		
		// get options for saved id
		$soptions = $this->getSavedIdValuesFromPleade();
		
		$element['saved_id'] = array(
				'#type' => 'select',
				'#title' => $this->t('Saved id'),
				'#description' => $this->t('Name of saved or form from Pleade to link to this block.'),
				'#options' => isset($soptions) ? $soptions : NULL,
				'#default_value' => isset($items[$delta]->saved_id) ? (string)$items[$delta]->saved_id : NULL,
		);
	
		$element['drupal_title'] = array(
				'#type' => 'textfield',
				'#title' => $this->t('Title'),
				'#description' => $this->t('Title of this block. If empty, title of the linked saved in Pleade will be used.'),
				'#placeholder' => $this->getSetting('placeholder_drupal_title'),
				'#default_value' => isset($items[$delta]->drupal_title) ? $items[$delta]->drupal_title : NULL,
		);
		
		$element['items_number'] = array(
				'#type' => 'textfield',
				'#size' => 3,
				'#maxlength' => 2,
				'#title' => $this->t('Number of items'),
				'#description' => $this->t('Number of items to show. Default is 6.'),
				'#placeholder' => $this->getSetting('placeholder_items_number'),
				'#default_value' => isset($items[$delta]->items_number) ? $items[$delta]->items_number : '6',
		);
	
		return $element;
	}
	
	/**
	 * Return saved values from Pleade
	 * Values are formatted for select fieldType
	 */
	public function getSavedIdValuesFromPleade(){
		// Initialisation
		$options = []; // contains options to return
		$client = \Drupal::httpClient(); // main HTTP object
		$pleade_url; // contain the url for quering Pleade
		$request; //request object for quering Pleade
		$response; // Contain the response after quering Pleade
		$jresponse = NULL; // contient la réponse au format json
		
		// Avoid Drupal cache problems due to Pleade's sessions cookie
		\Drupal::service('page_cache_kill_switch')->trigger();
		
		// Build complete Pleade request and avoir drupal cache
		$pleade_url = \Drupal::request()->getSchemeAndHttpHost() . '/pleade/functions/getSaved.json';
		
		// process pleade request.
		try {
			$request = $client->get($pleade_url, $this->getHeaders());
			$response = $request->getBody();
		} catch (RequestException $e) {
			\Drupal::logger('pleade')->error($e->getMessage());
		}
		
		// Transforme response to json
		$jresponse = Json::decode( (string)$response );
		
		// built select-options saved
		$options[ '' ] = '';
		if( $jresponse != NULL ){
			foreach( $jresponse as $i ){
				$options[ 'Pleade::'.$i['type'].'::'.$i['id'] ] = $i['name'];
			}
		}
		
		// finaly, add option for Pleade federated search
		// TODO, in the futur, posibility to add any Pleade form
		$options[ 'Pleade::FederateSearchForm::drupal-default' ] = $this->t('PLEADE FEDERATED SEARCH FORM');
		
		return $options;
	}
	
	/**
	 * get headers and cookies
	 * @return [type] [description]
	 */
	protected function getHeaders() {
		$headers = array();
		if(isset($_SERVER['HTTP_COOKIE'])){
			$headers['headers'] = array(
					'Cookie' => $_SERVER['HTTP_COOKIE'],
			);
		}
		return $headers;
	}
	
}