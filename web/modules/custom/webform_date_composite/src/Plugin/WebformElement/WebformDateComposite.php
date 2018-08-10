<?php

namespace Drupal\webform_date_composite\Plugin\WebformElement;

use Drupal\webform\Plugin\WebformElement\WebformCompositeBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Provides a 'webform_date_composite' element.
 *
 * @WebformElement(
 *   id = "webform_date_composite",
 *   label = @Translation("GOV date composite"),
 *   description = @Translation("Provides a GOV element date."),
 *   category = @Translation("GOV elements"),
 *   multiline = TRUE,
 *   composite = TRUE,
 *   states_wrapper = TRUE,
 * )
 *
 * @see \Drupal\webform_date_composite\Element\WebformDateComposite
 * @see \Drupal\webform\Plugin\WebformElement\WebformCompositeBase
 * @see \Drupal\webform\Plugin\WebformElementBase
 * @see \Drupal\webform\Plugin\WebformElementInterface
 * @see \Drupal\webform\Annotation\WebformElement
 */
class WebformDateComposite extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  protected function formatHtmlItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    return $this->formatTextItemValue($element, $webform_submission, $options);
  }

  /**
   * {@inheritdoc}
   */
  protected function formatTextItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    $value = $this->getValue($element, $webform_submission, $options);

    $lines = [];
    $lines[] = ($value['day'] ? $value['day'] : '') .
      ($value['month'] ? '/' . $value['month'] : '') .
      ($value['year'] ? '/' . $value['year'] : '');
    return $lines;
  }

}
