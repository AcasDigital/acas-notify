<?php

namespace Drupal\notification\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the Dynamics form.
 */
class DynamicsForm extends ConfigFormBase {
    
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dynamics_form';
  }
  
  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'notification.dynamics'
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('notification.dynamics');
    $form['environment'] = array(
      '#type' => 'select',
      '#default_value' => $config->get('environment') ?: '',
      '#title' => $this->t('Environment'),
      '#options' => ['Training' => 'Training', 'UAT' => 'UAT', 'TEST' => 'TEST', 'PreProd' => 'PreProd', 'Production' => 'Production'],
      '#required' => TRUE,
    );
    $form['form'] = [
      '#type' => 'fieldset',
      '#title' => t('Forms'),
      '#collapsible' => TRUE,
    ];
    $form['form']['url'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('url') ?: '',
      '#title' => $this->t('URL'),
      '#required' => TRUE,
      '#size' => 100,
    );
    $form['form']['token'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('token') ?: '',
      '#title' => $this->t('Token'),
      '#required' => TRUE,
      '#size' => 100,
    );
    $form['guid'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('GUID'),
      '#collapsible' => TRUE,
    ];
    $form['guid']['guid_url'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('guid_url') ?: '',
      '#title' => $this->t('URL'),
      '#required' => TRUE,
      '#size' => 100,
    );
    $form['guid']['guid_token'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('guid_token') ?: '',
      '#title' => $this->t('Token'),
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
    \Drupal::configFactory()->getEditable('notification.dynamics')
    ->set('url', $form_state->getValue('url'))
    ->set('token', $form_state->getValue('token'))
    ->set('environment', $form_state->getValue('environment'))
    ->set('guid_url', $form_state->getValue('guid_url'))
    ->set('guid_token', $form_state->getValue('guid_token'))
    ->save();
    parent::submitForm($form, $form_state);
  }
}