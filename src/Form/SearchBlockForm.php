<?php

namespace Drupal\pleade\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\search\SearchPageRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds the search form for the search block.
 *
 * @internal
 */
class SearchBlockForm extends FormBase {

  /**
   * The search page repository.
   *
   * @var \Drupal\pleade\SearchPageRepositoryInterface
   */
  protected $searchPageRepository;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a new SearchBlockForm.
   *
   * @param \Drupal\pleade\SearchPageRepositoryInterface $search_page_repository
   *   The search page repository.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(SearchPageRepositoryInterface $search_page_repository, ConfigFactoryInterface $config_factory, RendererInterface $renderer) {
    $this->searchPageRepository = $search_page_repository;
    $this->configFactory = $config_factory;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('search.search_page_repository'),
      $container->get('config.factory'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pleade_search_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Set up the form to submit using GET to the correct search page.
    $entity_id = $this->searchPageRepository->getDefaultSearchPage();

    // SearchPageRepository::getDefaultSearchPage() depends on search.settings.
    // The dependency needs to be added before the conditional return, otherwise
    // the block would get cached without the necessary cacheablity metadata in
    // case there is no default search page and would not be invalidated if that
    // changes.
    $this->renderer->addCacheableDependency($form, $this->configFactory->get('pleade.settings'));

    if (!$entity_id) {
      $form['message'] = [
        '#markup' => $this->t('Search is currently disabled'),
      ];
      return $form;
    }

    $route = 'search.view_' . $entity_id;
    $form['#action'] = $this->url($route);
    $form['#method'] = 'get';
    $form['#attributes'] = array('class' => array('form-inline', 'header-block-search'));

    $form['keys']['format'] = [
      '#type' => 'hidden',
      '#value' => 'drupal',
    ];

        $form['container'] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => ['row', 'width100', 'block-jumbotron-search']
            ],
        ];

        $form['container']['keys'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Fulltext'),
            '#title_display' => 'invisible',
            '#size' => 15,
            '#default_value' => isset($_GET['keys']) ? $_GET['keys'] : '',
            '#attributes' => [
                'title' => $this->t('I am looking for a keyword'),
            ],
            '#placeholder' => $this->t('I am looking for a keyword'),
            '#prefix' => '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-offset-1 col-lg-4 search-pleade-container">',
            '#suffix' => '</div>',
        ];

        $form['container']['collections'] = [
      '#type' => 'select',
      '#title' => $this->t('Collection'),
      '#title_display' => 'invisible',
      '#default_value' => '',
      '#attributes' => ['title' => $this->t('Choose the collection to search for.')],
      '#options' => [
            '' => $this->t('All collections'),
            'ead2' => $this->t('Civil status'),
            'ead3' => $this->t('Cadastre'),
            'ead4' => $this->t('Census list'),
            'militaire' => $this->t('Conscript'),
            'mariage' => $this->t('Matrimony'),
            'recensement_nominatif' => $this->t('Nominatif census'),
            'ecrous' => $this->t('Nuts'),
            'toponyme' => $this->t('Place name'),
            'ead' => $this->t('Inventory'),
            'portal' => $this->t('Portal'),
	],
            '#prefix' => '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 select-pleade-container">',
            '#suffix' => '</div>',
    ];

        $form['container']['actions'] = [
            '#type' => 'actions',
            '#prefix' => '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-2 btn-search-pleade-container">',
            '#suffix' => '</div>',
    ];
        $form['container']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      // Prevent op from showing up in the query string.
      '#name' => '',
            '#attributes' => [
                'class' => ['btn', 'btn-primary', 'btn-fond-sombre', 'icon-before', 'btn-search-pleade']
            ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // This form submits to the search page, so processing happens there.
  }

}
