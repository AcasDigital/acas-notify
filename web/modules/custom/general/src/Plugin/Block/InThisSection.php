<?php

namespace Drupal\general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'InThisSection' block.
 * @Block(
 *   id = "in_this_section_block",
 *   admin_label = @Translation("In this section block"),
 * )
 */
class InThisSection extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    $output = '';
    if ($node->hasField('field_taxonomy')) {
      $query = \Drupal::database()->select('taxonomy_index', 'ti');
      $query->join('taxonomy_term_field_data', 'fd', 'fd.tid = ti.tid');
      $query->join('node_field_data', 'nfd', 'nfd.nid = ti.nid');
      $query->fields('ti', array('nid'));
      $query->condition('ti.tid', $node->get('field_taxonomy')->target_id, '=');
      $query->condition('nfd.type', 'secondary_page', '=');
      $query->orderBy('fd.weight', 'ASC');
      $result = $query->execute();
      if($nodeIds = $result->fetchCol()){
        $nodes = \Drupal\node\Entity\Node::loadMultiple($nodeIds);
        foreach($nodes as $n) {
          if ($node->id() == $n->id()) {
            $output .= '<li class="active">' . $n->getTitle() . '</li>';
          }else{
            $output .= '<li><a href="' . $n->toUrl()->toString() . '">' . $n->getTitle() . '</a></li>';
          }
        }
        /*
        $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
        foreach($nodes as $node) {
          $view = $view_builder->view($node, 'teaser');
          $output .= drupal_render($view);
        }
        */
      }
    }
    if ($output) {
      $output = '<h2>Content</h2><ul>' . $output . '</ul>';
    }
    return ['#markup' => $output];
  }

}
