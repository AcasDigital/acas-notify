<?php

namespace Drupal\webform_date_composite\Element;

use Drupal\Component\Utility\Html;
use Drupal\webform\Element\WebformCompositeBase;

/**
 * Provides a 'webform_date_composite'.
 *
 * Webform composites contain a group of sub-elements.
 *
 *
 * IMPORTANT:
 * Webform composite can not contain multiple value elements (i.e. checkboxes)
 * or composites (i.e. webform_address)
 *
 * @FormElement("webform_date_composite")
 *
 * @see \Drupal\webform\Element\WebformCompositeBase
 * @see \Drupal\webform_date_composite\Element\WebformDateComposite
 */
class WebformDateComposite extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return parent::getInfo() + ['#theme' => 'webform_date_composite'];
  }

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements(array $element) {
    // Generate an unique ID that can be used by #states.
    $html_id = Html::getUniqueId('webform_date_composite');

    $elements = [];
    $elements['day'] = [
      '#type' => 'number',
      '#title' => t('Day'),
      '#attributes' => ['data-webform-composite-id' => $html_id . '--day'],
      '#min' => 1,
      '#max' => 31,
      '#pattern' => '[0-9]*',
    ];
    $elements['month'] = [
      '#type' => 'number',
      '#title' => t('Month'),
      '#attributes' => ['data-webform-composite-id' => $html_id . '--month'],
      '#min' => 1,
      '#max' => 12,
      '#pattern' => '[0-9]*',
    ];
    $elements['year'] = [
      '#type' => 'number',
      '#title' => t('Year'),
      '#attributes' => ['data-webform-composite-id' => $html_id . '--year'],
      '#min' => 1950,
      '#max' => date('Y'),
      '#pattern' => '[0-9]*',
    ];
    return $elements;
  }

}
