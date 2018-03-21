<?php

namespace Drupal\general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'HomePage' block.
 * @Block(
 *   id = "home_page_block",
 *   admin_label = @Translation("Home Page block"),
 * )
 */
class HomePage extends BlockBase {

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
    $output = '';
    if($nodeIds = $result->fetchCol()){
      $nodes = \Drupal\node\Entity\Node::loadMultiple($nodeIds);
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
      foreach($nodes as $node) {
        $view = $view_builder->view($node, 'teaser');
        $output .= drupal_render($view);
      }
    }
    return ['#markup' => $output];
  }
  
  

}
