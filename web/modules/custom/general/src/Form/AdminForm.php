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
      'acas.settings'
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('acas.settings');
    $form['g10_fasttrack_phone'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('g10_fasttrack_phone') ?: '',
      '#title' => t('G10 fast track phone number'),
      '#size' => 20,
      '#required' => TRUE,
    );
    $form['g10_flagged_phone'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('g10_flagged_phone') ?: '',
      '#title' => t('G10 flagged phone number'),
      '#size' => 20,
      '#required' => TRUE,
    );
    $form['error_email'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('error_email') ?: '',
      '#title' => t('Send errors email address'),
      '#required' => TRUE,
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
    ->set('g10_fasttrack_phone', $form_state->getValue('g10_fasttrack_phone'))
    ->set('g10_flagged_phone', $form_state->getValue('g10_flagged_phone'))
    ->set('error_email', $form_state->getValue('error_email'))
    ->save();
    parent::submitForm($form, $form_state);
  }

}