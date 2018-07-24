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
      'dynamics.settings'
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dynamics.settings');
    $form['organization_uri'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('organization_uri') ?: '',
      '#title' => t('Organization URI'),
      '#required' => TRUE,
      '#size' => 100,
    );
    $form['application_id'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('application_id') ?: '',
      '#title' => t('Application ID'),
      '#required' => TRUE,
      '#size' => 100,
    );
    $form['application_secret'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('application_secret') ?: '',
      '#title' => t('Application secret'),
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
    \Drupal::configFactory()->getEditable('dynamics.settings')
    ->set('organization_uri', $form_state->getValue('organization_uri'))
    ->set('application_id', $form_state->getValue('application_id'))
    ->set('application_secret', $form_state->getValue('application_secret'))
    ->save();
    parent::submitForm($form, $form_state);
  }
}