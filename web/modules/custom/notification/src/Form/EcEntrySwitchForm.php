<?php

namespace Drupal\notification\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the EcEntrySwitch form.
 */
class EcEntrySwitchForm extends ConfigFormBase {
    
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ec_entry_switch_form';
  }
  
  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ec_entry_switch.settings'
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ec_entry_switch.settings');
    $form['enabled'] = array(
      '#type' => 'checkbox',
      '#default_value' => $config->get('enabled') ?: '',
      '#title' => t('Enabled'),
    );
    $form['count'] = array(
      '#type' => 'fieldset',
      '#title' => t('Counts'),
      '#collapsible' => TRUE,
    );
    $form['count']['in_count'] = array(
      '#type' => 'number',
      '#default_value' => $config->get('in_count') ?: '',
      '#title' => t('In'),
      '#description' => 'Current count: ' . $config->get('current_in_count') ?: 0,
      '#required' => TRUE,
    );
    $form['count']['out_count'] = array(
      '#type' => 'number',
      '#default_value' => $config->get('out_count') ?: '',
      '#title' => t('Out'),
      '#description' => 'Current count: ' . $config->get('current_out_count') ?: 0,
      '#required' => TRUE,
    );
    $form['count']['submit'] = [
      '#type' => 'submit',
      '#name' => 'reset',
      '#value' => t('Reset'),
      '#attributes' => ['class' => ['button, button--primary']],
    ];

    $form['urls'] = array(
      '#type' => 'fieldset',
      '#title' => t('URL'),
      '#collapsible' => TRUE,
    );
    $form['urls']['old_url'] = array(
      '#type' => 'url',
      '#default_value' => $config->get('old_url') ?: '',
      '#title' => t('Original'),
      '#required' => TRUE,
    );
    $form['urls']['new_url'] = array(
      '#type' => 'url',
      '#default_value' => $config->get('new_url') ?: '',
      '#title' => t('New'),
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
    if ($form_state->getTriggeringElement()['#name'] == 'reset') {
      \Drupal::configFactory()->getEditable('ec_entry_switch.settings')
      ->set('enabled', $form_state->getValue('enabled'))
      ->set('in_count', $form_state->getValue('in_count'))
      ->set('out_count', $form_state->getValue('out_count'))
      ->set('old_url', $form_state->getValue('old_url'))
      ->set('new_url', $form_state->getValue('new_url'))
      ->set('current_in_count', 0)
      ->set('current_out_count', 0)
      ->save();
    }else{
      \Drupal::configFactory()->getEditable('ec_entry_switch.settings')
      ->set('enabled', $form_state->getValue('enabled'))
      ->set('in_count', $form_state->getValue('in_count'))
      ->set('out_count', $form_state->getValue('out_count'))
      ->set('old_url', $form_state->getValue('old_url'))
      ->set('new_url', $form_state->getValue('new_url'))
      ->save();
    }
    parent::submitForm($form, $form_state);
  }
}