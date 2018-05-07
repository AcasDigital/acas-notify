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
    $form['sync'] = array(
      '#type' => 'fieldset',
      '#title' => t('Production content sync'),
      '#collapsible' => TRUE,
    );
    $form['sync']['prod'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('prod') ?: 'https://beta-acas.org.uk',
      '#title' => t('Production URL'),
    );
    $form['sync']['tables'] = array(
      '#type' => 'textarea',
      '#default_value' => $config->get('tables') ?: '',
      '#title' => t('Tables to exclude'),
      '#description' => t('Enter the tables to exclude, one per line'),
    );
    $form['sync']['config'] = array(
      '#type' => 'textarea',
      '#default_value' => $config->get('config') ?: '',
      '#title' => t('Configuration names to ignore'),
      '#description' => t('Enter the configuration names to ignore, one per line'),
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
    ->set('prod', $form_state->getValue('prod'))
    ->set('tables', $form_state->getValue('tables'))
    ->set('config', $form_state->getValue('config'))
    ->save();
    parent::submitForm($form, $form_state);
  }
}