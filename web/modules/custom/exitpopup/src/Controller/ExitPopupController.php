<?php

namespace Drupal\exitpopup\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @file
 * Contains \Drupal\exitpopup\Controller\ExitPopupController.
 */

class ExitPopupController extends ControllerBase {
  
  /**
   * {@inheritdoc}
   */
  public function exit_popup() {
    $markup = exitpopup_exit_popup();
    return ['#markup' => $markup];
  }
  
}
