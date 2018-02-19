<?php

namespace Drupal\general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'RelatedContent' block.
 * @Block(
 *   id = "related_content_block",
 *   admin_label = @Translation("Related Content block"),
 * )
 */
class RelatedContent extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    $parents = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($node->get('field_taxonomy')->target_id);
    foreach($parents as $parent) {
      break;
    }
    $parent_name = str_replace(' ', '-', strtolower($parent->getName()));
    $output = '<nav class="section-nav js-section-nav" data-track-zone="section-nav">
    <h2 class="section-nav__heading"><a href="#!" aria-controls="section-nav-list" aria-expanded="false"><span class="screenreader">Show</span> In this section</a></h2>
    <ul id="section-nav-list" tabindex="-1"><li><a class="section-nav__parent" href="/' . $parent_name . '">' . $parent->getName() . '</a><ul>';
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($parent->getVocabularyId(), $parent->id());
    foreach($terms as $term) {
      $url = '/' . $parent_name . '/' . str_replace(' ', '-', strtolower($term->name));
      if ($node->get('field_taxonomy')->target_id != $term->tid) {
        $output .= '<li><a href="' . $url . '">' . $term->name . '</a></li>';
      }else{
        $output .= '<li class="active">' . $term->name . '</li>';
      }
    }
    $output .= '</ul></li></ul></nav>';
    return ['#markup' => $output];
  }

}