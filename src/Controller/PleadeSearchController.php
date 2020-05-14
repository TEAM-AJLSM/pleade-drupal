<?php

    namespace Drupal\pleade\Controller;

    use Drupal\search\SearchPageInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Drupal\search\Controller\SearchController;

    /**
     * Override the Route controller for search.
     */
    class PleadeSearchController extends SearchController {

      /**
       * {@inheritdoc}
       */
      public function view(Request $request, SearchPageInterface $entity) {
	// init
        $build = parent::view($request, $entity);

	// Print title and block resume
        $build['search_results_title'] = [
          '#markup' => '<h2 id="search-result-pleade-and-drupal-title">' . $this->t('Search results Pleade and Drupal') . '</h2>' .
                       '<div id="block-pleade-and-drupal-results-resume">' .
                         '<div id="block-ead2-results-resume" class="block-pleade-and-drupal-results-resume"><span class="block-pleade-and-drupal-results-resume-label">'.$this->t('Civil status: ').'</span><span id="block-ead2-results-resume-value" class="block-pleade-and-drupal-results-resume-value">0 '.$this->t('result').'</span></div>' .
                         '<div id="block-ead3-results-resume" class="block-pleade-and-drupal-results-resume"><span class="block-pleade-and-drupal-results-resume-label">'.$this->t('Cadastre: ').'</span><span id="block-ead3-results-resume-value" class="block-pleade-and-drupal-results-resume-value">0 '.$this->t('result').'</span></div>' .
                         '<div id="block-ead4-results-resume" class="block-pleade-and-drupal-results-resume"><span class="block-pleade-and-drupal-results-resume-label">'.$this->t('Census list: ').'</span><span id="block-ead4-results-resume-value" class="block-pleade-and-drupal-results-resume-value">0 '.$this->t('result').'</span></div>' .
                         '<div id="block-militaire-results-resume" class="block-pleade-and-drupal-results-resume"><span class="block-pleade-and-drupal-results-resume-label">'.$this->t('Conscript: ').'</span><span id="block-militaire-results-resume-value" class="block-pleade-and-drupal-results-resume-value">0 '.$this->t('result').'</span></div>' .
                         '<div id="block-mariage-results-resume" class="block-pleade-and-drupal-results-resume"><span class="block-pleade-and-drupal-results-resume-label">'.$this->t('Matrimony: ').'</span><span id="block-mariage-results-resume-value" class="block-pleade-and-drupal-results-resume-value">0 '.$this->t('result').'</span></div>' .
                         '<div id="block-recensement_nominatif-results-resume" class="block-pleade-and-drupal-results-resume"><span class="block-pleade-and-drupal-results-resume-label">'.$this->t('Nominatif census: ').'</span><span id="block-recensement_nominatif-results-resume-value" class="block-pleade-and-drupal-results-resume-value">0 '.$this->t('result').'</span></div>' .
                         '<div id="block-ecrous-results-resume" class="block-pleade-and-drupal-results-resume"><span class="block-pleade-and-drupal-results-resume-label">'.$this->t('Nuts: ').'</span><span id="block-ecrous-results-resume-value" class="block-pleade-and-drupal-results-resume-value">0 '.$this->t('result').'</span></div>' .
                         '<div id="block-toponyme-results-resume" class="block-pleade-and-drupal-results-resume"><span class="block-pleade-and-drupal-results-resume-label">'.$this->t('Place name: ').'</span><span id="block-toponyme-results-resume-value" class="block-pleade-and-drupal-results-resume-value">0 '.$this->t('result').'</span></div>' .
                         '<div id="block-ead-results-resume" class="block-pleade-and-drupal-results-resume"><span class="block-pleade-and-drupal-results-resume-label">'.$this->t('Inventory: ').'</span><span id="block-ead-results-resume-value" class="block-pleade-and-drupal-results-resume-value">0 '.$this->t('result').'</span></div>' .
                         '<div id="block-portal-results-resume" class="block-pleade-and-drupal-results-resume"><span class="block-pleade-and-drupal-results-resume-label">'.$this->t('Portal: ').'</span><span id="block-portal-results-resume-value" class="block-pleade-and-drupal-results-resume-value"> '.$this->t('view result(s)').'</span></div>' .
                       '</div>' .
                       '<div id="block-pleade-and-drupal-results">' .
                         '<div id="block-ead2-results" class="block-pleade-and-drupal-results"></div>' .
                         '<div id="block-ead3-results" class="block-pleade-and-drupal-results"></div>' .
                         '<div id="block-ead4-results" class="block-pleade-and-drupal-results"></div>' .
                         '<div id="block-militaire-results" class="block-pleade-and-drupal-results"></div>' .
                         '<div id="block-mariage-results" class="block-pleade-and-drupal-results"></div>' .
                         '<div id="block-recensement_nominatif-results" class="block-pleade-and-drupal-results"></div>' .
                         '<div id="block-ecrous-results" class="block-pleade-and-drupal-results"></div>' .
                         '<div id="block-toponyme-results" class="block-pleade-and-drupal-results"></div>' .
                         '<div id="block-ead-results" class="block-pleade-and-drupal-results"></div>' .
                         '<div id="block-portal-results" class="block-pleade-and-drupal-results"><p id="pl-list-results-header-portal" class="pl-list-results-header pl-list-results-header-portal">'.$this->t('Portal').'</p><p id="pl-list-results-details-portal" class="pl-list-results-details pl-list-results-details-portal"><a href="" title="'.$this->t('View all results on portal').'">'.$this->t('View all results on portal').'</a></p></div>' .
                       '</div>',
        ];


	// Print nothing in case of empty result
        $build['search_results']['#empty'] = '';

        return $build;
      }

    }
