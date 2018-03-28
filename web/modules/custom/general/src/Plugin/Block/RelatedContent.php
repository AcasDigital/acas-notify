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
    $parents = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadAllParents($node->get('field_taxonomy')->target_id);
    $parents = array_reverse($parents, TRUE);
    array_pop($parents);
    $url = '';
    foreach($parents as $parent) {
      $url .= '/' . general_taxonomy_path($parent->getName());
    }
    $parent = array_pop($parents);
    $output = '<nav class="section-nav js-section-nav" data-track-zone="section-nav">
    <h3 class="section-nav__heading"><a href="#!" aria-controls="section-nav-list" aria-expanded="false"><span class="screenreader">Show</span> In this section</a></h3>
    <ul id="section-nav-list" tabindex="-1"><li><a class="section-nav__parent" href="' . $url . '">' . $parent->getName() . '</a><ul>';
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($parent->getVocabularyId(), $parent->id());
    $path = $url;
    foreach($terms as $term) {
      if (!$term->depth) {
        $url = $path . '/' . general_taxonomy_path($term->name);
        if ($node->get('field_taxonomy')->target_id != $term->tid) {
          $output .= '<li><a href="' . $url . '">' . $term->name . '</a></li>';
        }else{
          $output .= '<li class="active">' . $term->name . '</li>';
        }
      }
    }
    $output .= '</ul></li></ul></nav>';
    return ['#markup' => $output];
  }

}