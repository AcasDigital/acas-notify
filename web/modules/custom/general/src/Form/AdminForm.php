<?php

namespace Drupal\general\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the Admin form.
 */
class AdminForm extends ConfigFormBase {
    
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_form';
  }
  
  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'acas.settings',
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('acas.settings');
    $form['feedback_email'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('contact_email') ?: 'john@johnburch.co.uk',
      '#title' => t('Feedback email'),
      '#description' => t('Alerts will be sent to this address when a user completes the feedback form or clicks "Was this page usefull?"'),
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
    \Drupal::configFactory()->getEditable('acas.settings')
    ->set('feedback_email', $form_state->getValue('feedback_email'))
    ->save();
    parent::submitForm($form, $form_state);
  }
}