<?php

namespace Drupal\notification\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;

/**
 * @file
 * Contains \Drupal\notification\Controller\NotificationController.
 */

class NotificationController extends ControllerBase {
  
  /**
   * {@inheritdoc}
   */
  public function acas_webform_confirmation($webform, $webform_submission) {
    $markup = notification_confirmation($webform, $webform_submission);
    return ['#markup' => $markup];
  }
  
  /**
   * {@inheritdoc}
   */
  public function acas_group_webform_confirmation($webform, $webform_submission) {
    $markup = notification_confirmation_group($webform, $webform_submission);
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
    return new JsonResponse(notification_ec_entry_switch());
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
  public function notification_errors_delete($id) {
    return new JsonResponse(notification_errors_delete($id));
  }
  
  /**
   * {@inheritdoc}
   */
  public function notification_errors_submit($id) {
    return new JsonResponse(notification_errors_submit($id));
  }
  
  /**
   * {@inheritdoc}
   */
  public function notification_currrent_number() {
    die(notification_get_current_reference_number());
  }
  
  /**
   * {@inheritdoc}
   */
  public function notification_download_pdf($sid1, $sid2) {
    $dompdf = new Dompdf(array('enable_remote' => true));
    $dompdf->loadHtml(notification_download_pdf_build_html($sid1, $sid2));
    $dompdf->render();
    $dompdf->stream('Notification.pdf');
    //die(notification_download_pdf_build_html($sid1, $sid2));
    return array(
      '#markup' => '',
    );
  }
  

}
