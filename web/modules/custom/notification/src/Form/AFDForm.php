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
    $form['url'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('url') ?: '',
      '#title' => t('URL'),
      '#required' => TRUE,
      '#size' => 100,
    );
    $form['token'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('token') ?: '',
      '#title' => t('Token'),
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
    ->set('url', $form_state->getValue('url'))
    ->set('token', $form_state->getValue('token'))
    ->save();
    parent::submitForm($form, $form_state);
  }
}