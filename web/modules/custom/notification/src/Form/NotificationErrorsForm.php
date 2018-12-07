<?php

namespace Drupal\notification\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the Dynamics form.
 */
class NotificationErrorsForm extends ConfigFormBase {
    
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'notification_errors_form';
  }
  
  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'notification.settings'
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('notification.settings');
    $header = [
      'date' => $this->t('Date'),
      'reference' => $this->t('Reference'),
      'status' => $this->t('Status'),
      'edit' => $this->t('View/Edit'),
    ];
    $query = \Drupal::database()->select('acas_notify_errors', 'ne');
    $query->fields('ne', ['id', 'timestamp', 'uri', 'webform_id', 'entity_id']);
    return parent::buildForm($form, $form_state);
  }
  
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::configFactory()->getEditable('notification.settings')
    ->set('key', $form_state->getValue('key'))
    ->save();
    parent::submitForm($form, $form_state);
  }
}