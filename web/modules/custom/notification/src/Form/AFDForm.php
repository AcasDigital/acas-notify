<?php

namespace Drupal\notification\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the Dynamics form.
 */
class AFDForm extends ConfigFormBase {
    
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'afd_form';
  }
  
  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'afd.settings'
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('afd.settings');
    $form['serial'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('serial') ?: '',
      '#title' => t('Serial No'),
      '#required' => TRUE,
      '#size' => 100,
    );
    $form['password'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('password') ?: '',
      '#title' => t('Password'),
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
    \Drupal::configFactory()->getEditable('afd.settings')
    ->set('serial', $form_state->getValue('serial'))
    ->set('password', $form_state->getValue('password'))
    ->save();
    parent::submitForm($form, $form_state);
  }
}