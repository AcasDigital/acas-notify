<?php

namespace Drupal\general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BetaBanner' block.
 * @Block(
 *   id = "beta_banner",
 *   admin_label = @Translation("Beta banner block"),
 * )
 */
class BetaBanner extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $output = 'This is the <strong>';
    $a = explode('.', $_SERVER['HTTP_HOST']);
    if (strpos($a[0], 'dev') !== FALSE) {
      $output .= 'DEV ';
    }else if (strpos($a[0], 'uat') !== FALSE) {
      $output .= 'UAT ';
    }
    $output .= 'BETA</strong> site';
    return ['#markup' => $output];
  }
}
