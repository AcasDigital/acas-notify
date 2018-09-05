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
    if (strpos(\Drupal::request()->getRequestUri(), 'notification') !== FALSE) {
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
    }else{
      if ($form = \Drupal\webform\Entity\Webform::load('no_feedback_guidance')) {
        $build['no_feedback_form'] = \Drupal::entityManager()
          ->getViewBuilder('webform')
          ->view($form);
        $build['no_feedback_form']['#weight'] = 999;
        $build['no_feedback_form']['#attributes']['class'][] = 'webform-submission-no-feedback-form webform-submission-no-feedback-add-form';        
      }
      if ($form = \Drupal\webform\Entity\Webform::load('yes_feedback_guidance')) {
        $build['yes_feedback_form'] = \Drupal::entityManager()
          ->getViewBuilder('webform')
          ->view($form);
        $build['yes_feedback_form']['#weight'] = 999;
        $build['yes_feedback_form']['#attributes']['class'][] = 'webform-submission-yes-feedback-form webform-submission-yes-feedback-add-form';
      }
    }
    return $build;
  }
}
