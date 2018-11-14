<?php

namespace Drupal\notification\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @file
 * Contains \Drupal\notification\Controller\SolanaceaeController.
 */

class NotificationController extends ControllerBase {
  public function acas_webform_confirmation($webform, $webform_submission) {
    $markup = notification_confirmation($webform, $webform_submission);
    return ['#markup' => $markup];
  }
  
  public function acas_group_webform_confirmation($webform, $webform_submission) {
    $markup = notification_group_confirmation($webform, $webform_submission);
    return ['#markup' => $markup];
  }
  
  /**
   * {@inheritdoc}
   */
  public function company_house($employer) {
    return new JsonResponse(notification_company_house($employer));
  }

  /**
   * {@inheritdoc}
   */
  public function ec_entry_switch() {
    return new JsonResponse(notification_ec_entry_switch());
  }

  /**
   * {@inheritdoc}
   */
  public function ec_entry_switch_mobile() {
    return new JsonResponse(notification_ec_entry_switch(TRUE));
  }

  /**
   * {@inheritdoc}
   */
  public function notify_test($webform_submission_id) {
    die(notification_send_dynamics($webform_submission_id));
  }

  /**
   * {@inheritdoc}
   */
  public function retry_send_dynamics() {
    return new JsonResponse(notification_retry_send_dynamics());
  }
  
  /**
   * {@inheritdoc}
   */
  public function test_excel() {
    return ['#markup' => notification_test_excel()];
  }

}
