<?php

namespace Drupal\notification\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Feedback' block.
 * @Block(
 *   id = "feedback",
 *   admin_label = @Translation("Feedback forms block"),
 * )
 */
class Feedback extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    if ($form = \Drupal\webform\Entity\Webform::load('no_feedback')) {
      $build['no_feedback_form'] = \Drupal::entityManager()
        ->getViewBuilder('webform')
        ->view($form);
      $build['no_feedback_form']['#weight'] = 999;
    }
    if ($form = \Drupal\webform\Entity\Webform::load('yes_feedback')) {
      $build['yes_feedback_form'] = \Drupal::entityManager()
        ->getViewBuilder('webform')
        ->view($form);
      $build['yes_feedback_form']['#weight'] = 999;
    }
    return $build;
  }
}
