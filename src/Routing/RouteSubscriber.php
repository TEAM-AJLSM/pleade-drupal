<?php


    /**
     * @file
     * Contains \Drupal\pleade\Routing\RouteSubscriber.
     */

    namespace Drupal\pleade\Routing;

    use Drupal\Core\Routing\RouteSubscriberBase;
    use Symfony\Component\Routing\RouteCollection;

    /**
     * Listens to the dynamic route events.
     */
    class RouteSubscriber extends RouteSubscriberBase {

      /**
       * {@inheritdoc}
       */
      public function alterRoutes(RouteCollection $collection) {
        // Replace dynamically created "search.view_node_search" route's Controller
        // with our own.
        if ($route = $collection->get('search.view_node_search')) {
          $route->setDefault('_controller', '\Drupal\pleade\Controller\PleadeSearchController::view');
        }
      }
    }

