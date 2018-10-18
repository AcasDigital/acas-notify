<?php

namespace Drupal\notification\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the Dynamics form.
 */
class GovNotifyForm extends ConfigFormBase {
    
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gov_notify_form';
  }
  
  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'gov_notify.settings'
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('gov_notify.settings');
    $form['key'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('key') ?: 'notify-d58f997f-003d-45f1-9409-74c1f8b6dd96-31afc6c4-f5e2-433d-9e6f-3e8ca9f8122a',
      '#title' => t('API Key'),
      '#required' => TRUE,
      '#size' => 100,
    );
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
    \Drupal::configFactory()->getEditable('gov_notify.settings')
    ->set('key', $form_state->getValue('key'))
    ->save();
    parent::submitForm($form, $form_state);
  }
}