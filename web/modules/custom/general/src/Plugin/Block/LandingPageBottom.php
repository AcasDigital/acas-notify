<?php

namespace Drupal\general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Landing Page Bottom' block.
 * @Block(
 *   id = "landing_page_bottom",
 *   admin_label = @Translation("Landing Page Bottom block"),
 * )
 */
class LandingPageBottom extends BlockBase {
    /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    $output = '';
    if ($node->get('field_bottom_text')->value) {
      $output = $node->get('field_bottom_text')->value;
    }
    return ['#markup' => $output];
  }
}