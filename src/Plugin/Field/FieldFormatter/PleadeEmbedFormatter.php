<?php

namespace Drupal\pleade\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Plugin implementation of the Pleade Embed formatter.
 *
 * @FieldFormatter(
 *   id = "pleade_embed_formatter",
 *   label = @Translation("Pleade Embed Formatter"),
 *   field_types = {
 *     "pleade_embed"
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class PleadeEmbedFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
	public function viewElements(FieldItemListInterface $items, $langcode) {
		$elements = [];
	
		foreach ($items as $delta => $item){
			// variables to send to Pleade
			$pleade_relative_url = $item->pleade_relative_url;
			$drupal_title = $item->drupal_title;
			
			// results markup from Pleade to print
			$markups = $this->getEmbedMarkupFromPleade($pleade_relative_url, $drupal_title);
			
			// results final
			$elements[$delta] = [
					'#theme' => 'pleade_embed_formatter',
					'#pleade_relative_url' => $pleade_relative_url,
					'#drupal_title' => $drupal_title,
					'#type' => 'markup',
					'#markups' => $markups,
			];
		}
	
		return $elements;
	}

	/**
	 * Return html value for a page from Pleade
	 */
	public function getEmbedMarkupFromPleade($pleade_relative_url, $drupal_title){
		// Initialisation
		$markup; // contains markup to return
		$client = \Drupal::httpClient(); // main HTTP object
		$pleade_url; // contain the url for quering Pleade
		$request; //request object for quering Pleade
		$response; // Contain the response after quering Pleade
		$hresponse = NULL; // contient la réponse au format html
	
		// Avoid Drupal cache problems due to Pleade's sessions cookie
		\Drupal::service('page_cache_kill_switch')->trigger();
	
		// build complete Pleade URL to embed
		$pleade_url = \Drupal::request()->getSchemeAndHttpHost();
		$pleade_url = $pleade_url . '/pleade/embed';
		if( !(substr($pleade_relative_url, 1) == '/') ) $pleade_url = $pleade_url . '/';
		$pleade_url = $pleade_url . $pleade_relative_url;
		
		// process pleade request.
		try {
			$request = $client->get($pleade_url, $this->getHeaders());
			$response = $request->getBody();
		} catch (RequestException $e) {
			\Drupal::logger('pleade')->error($e->getMessage());
		}
	
		// transfrom response to string and clean trailing xml tags
		$markup = (string) $response;
		
		return $markup;
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
