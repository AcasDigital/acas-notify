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
}