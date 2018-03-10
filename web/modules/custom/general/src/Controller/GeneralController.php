<?php
/**
 * @file
 * Contains \Drupal\general\Controller\SolanaceaeController.
 */

namespace Drupal\general\Controller;
use Drupal\Core\Controller\ControllerBase;

class GeneralController extends ControllerBase {
  public function searchheader() {
    return array('#markup' => '');
  }
  public function health() {
    return array('#markup' => general_health());
  }
}