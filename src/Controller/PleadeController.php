<?php

namespace Drupal\pleade\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Controller routines for book routes.
 */
class PleadeController extends ControllerBase {

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

protected function startsWith($haystack, $needle) {
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

	/**
	 * Grab the content from Pleade and plug it into Drupal.
	 * The Pleade service must be respond on URL http://<base url>/pleade
	 * 
	 * @param  string $node page to fetch from Pleade
	 * @return array      XHTML content of the Pleade page
	 *
	 * @tag pleade
	 */
	public function pleadeWrap($node) {
		// process Pleade query
		$response = $this->processPleadeRequest($node, \Drupal::request()->getRequestUri(), '');
		
		// prepare return. Disable drupal cache on this page for avoiding problems with pleade's cookie
		$aPleadeWrap = array(
				'#children' => (string) $response,
				'#cache' => ['max-age' => 0],
		);
		
		return $aPleadeWrap;
	}
	
	/**
	 * Grab the content from Pleade and plug it into Drupal.
	 * The Pleade service must be respond on URL http://<base url>/pleade/mets
	 *
	 * @param  string $node page to fetch from Pleade
	 * @return array      XHTML content of the Pleade page
	 *
	 * @tag pleade
	 */
	public function pleadeMetsWrap($node) {
		// process Pleade query
		$response = $this->processPleadeRequest($node, \Drupal::request()->getRequestUri(), 'mets/');
	
		// prepare return. Disable drupal cache on this page for avoiding problems with pleade's cookie
		$aPleadeWrap = array(
				'#children' => (string) $response,
				'#cache' => ['max-age' => 0],
		);
	
		return $aPleadeWrap;
	}
	
	/**
	 * 
	 * @param string $node
	 * @param string $tlQueryString
	 * @param string $pleadePrefix
	 */
	public function processPleadeRequest(string $node, string $tlQueryString, string $pleadePrefix){
		// FIXME build manualy query string here; because, sometime, Request::getQueryString function return parameters in different order
		$lQueryString = '';
		if(strpos($tlQueryString, '?') !== false) $lQueryString = substr( $tlQueryString, strpos($tlQueryString, '?') );
		
		// Encode query params values for avoiding encoding problems
		$elQueryString = '';
		$ttlQueryString = explode('&', $lQueryString);
		for($i=0; $i<count($ttlQueryString); $i++){
			$param = $ttlQueryString[$i];
			if( $this->startsWith($param, 'query') ){
				$param1 = substr( $param, 0, strpos($param, '=') );
				$param2 = substr( $param, (strpos($param, '=')+1), strlen($param) );
		
				$elQueryString .= $param1 . '=' . $param2;
			} else {
				$elQueryString .= $param;
			}
				
			if( $i < (count($ttlQueryString)-1) ) $elQueryString .= '&';
		}
		
		// Avoid Drupal cache problems due to Pleade's sessions cookie
		\Drupal::service('page_cache_kill_switch')->trigger();
		
		// Build complete Pleade request and avoir drupal cache
		$pleade_url = \Drupal::request()->getSchemeAndHttpHost() . '/pleade/embed/' . $pleadePrefix. $node . $elQueryString;
		
		// process pleade request.
		$client = \Drupal::httpClient(); // main HTTP object
		$request; //request object for quering Pleade
		$response; // Contain the response after quering Pleade
		try {
			$request = $client->get($pleade_url, $this->getHeaders());
			$response = $request->getBody();
		} catch (RequestException $e) {
			\Drupal::logger('pleade')->error($e->getMessage());
		}
		
		// return 
		return $response;
	}

}
