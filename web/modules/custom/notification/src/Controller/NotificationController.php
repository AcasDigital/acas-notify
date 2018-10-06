<?php
/**
 * @file
 * Contains \Drupal\notification\Controller\SolanaceaeController.
 */

namespace Drupal\notification\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;


class NotificationController extends ControllerBase {
  public function acas_webform_confirmation($webform, $webform_submission) {
    $markup = notification_confirmation($webform, $webform_submission);
    return array('#markup' => $markup);
  }
  
  public function company_house($employer) {
    return new JsonResponse(notification_company_house($employer));
  }
  
  public function ec_entry_switch() {
    return new JsonResponse(notification_ec_entry_switch());
  }
  public function ec_entry_switch_mobile() {
    return new JsonResponse(notification_ec_entry_switch(TRUE));
  }

  public function notify_test($webform_submission_id) {
    die(notification_send_dynamics($webform_submission_id));
  }
}
