<?php

namespace Drupal\general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'FooterNavigation' block.
 * @Block(
 *   id = "footer_navigation_block",
 *   admin_label = @Translation("Footer Navigation block"),
 * )
 */
class FooterNavigation extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $children = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('acas', 0, 1);
    $termIds = [];
    foreach ($children as $value) {
      $termIds[$value->tid] = $value->tid;
    }
    $query = \Drupal::database()->select('taxonomy_index', 'ti');
    $query->join('taxonomy_term_field_data', 'fd', 'fd.tid = ti.tid');
    $query->fields('ti', array('nid'));
    $query->condition('ti.tid', $termIds, 'IN');
    $query->orderBy('fd.weight', 'ASC');
    $result = $query->execute();
    $output = '<ul class="menu menu--footer nav">';
    if($nodeIds = $result->fetchCol()){
      $nodes = \Drupal\node\Entity\Node::loadMultiple($nodeIds);
      foreach($nodes as $node) {
        $output .= '<li><a href="' . $node->toUrl()->toString() . '">' . $node->getTitle() . '</a></li>';
      }
    }
    $output .= '</ul>';
    return ['#markup' => $output];
  }
}
