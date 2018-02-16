<?php

namespace Drupal\general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'AcasSearchBlock' block.
 * @Block(
 *   id = "acas_search_block",
 *   admin_label = @Translation("Acas search block"),
 * )
 */
class AcasSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\general\Form\HeaderSearch');
  }

}