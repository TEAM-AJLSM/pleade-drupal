<?php

namespace Drupal\pleade\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Search form' block, which permit to trigger a research in Pleade and Drupal
 *
 * @Block(
 *   id = "pleade_search_form_block",
 *   admin_label = @Translation("Pleade Search form"),
 *   category = @Translation("Forms")
 * )
 */
class SearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  /*protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'search content');
  }*/

  /**
   * {@inheritdoc}
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\pleade\Form\SearchBlockForm');
  }

}
