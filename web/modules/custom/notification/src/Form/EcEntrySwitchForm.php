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
    $form['enabled'] = [
      '#type' => 'checkbox',
      '#default_value' => $config->get('enabled') ?: '',
      '#title' => t('Enabled'),
    ];
    $form['status'] = [
      '#type' => 'fieldset',
      '#title' => t('Status'),
      '#collapsible' => TRUE,
    ];
    $enabled = TRUE;
    $now = time() - strtotime("today");
    $status = 'Enabled';
    $today = date('D');
    // Test if the user should be re-directed to the new form
    if (!$config->get('enabled')) {
      $enabled = FALSE;
      $status = 'Disabled';
    }else if ($config->get('current_in_count') >= $config->get('in_count') || $config->get('current_out_count') >= $config->get('out_count')) {
      $enabled = FALSE;
      if ($config->get('current_in_count') >= $config->get('in_count')) {
        $status = 'Disabled: In count limit reached';
      }else{
        $status = 'Disabled: Out count limit reached';
      }
    }else if ($config->get('start_time') && $config->get('end_time') && ($now < $config->get('start_time') || $now > $config->get('end_time'))) {
      $enabled = FALSE;
      if ($now < $config->get('start_time')) {
        $status = 'Disabled: Current time < start time';
      }else{
        $status = 'Disabled: Current time > end time';
      }
    }else if ($config->get('weekend') && ($today == 'Sat' || $today == 'Sun')) {
      $enabled = FALSE;
      $status = 'Disabled: Today is the weekend';
    }
    if (!$enabled) {
      $status = '<div class="red status">' . $status . '</dev>';
    }else{
      $status = '<div class="green status">' . $status . '</dev>';
    }
    $form['status']['message'] = [
      '#type' => 'markup',
      '#markup' => $status,
    ];
    $form['count'] = [
      '#type' => 'fieldset',
      '#title' => t('Counts'),
      '#collapsible' => TRUE,
      '#states' => array(
        'visible' => array(
          ':input[name="enabled"]' => array('checked' => TRUE),
        ),
      ),
    ];
    $form['count']['in_count'] = [
      '#type' => 'number',
      '#default_value' => $config->get('in_count') ?: '',
      '#title' => t('In'),
      '#description' => 'Current count: ' . $config->get('current_in_count') ?: 0,
      '#required' => TRUE,
    ];
    $form['count']['out_count'] = [
      '#type' => 'number',
      '#default_value' => $config->get('out_count') ?: '',
      '#title' => t('Out'),
      '#description' => 'Current count: ' . $config->get('current_out_count') ?: 0,
      '#required' => TRUE,
    ];
    $form['count']['submit'] = [
      '#type' => 'submit',
      '#name' => 'reset',
      '#value' => t('Reset current counts'),
      '#attributes' => ['class' => ['button, button--primary']],
    ];
    $form['times'] = [
      '#type' => 'fieldset',
      '#title' => t('Times'),
      '#collapsible' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="enabled"]' => array('checked' => TRUE),
        ],
      ],
    ];
    $form['times']['start_time'] = [
      '#type' => 'time',
      '#title' => $this->t('Start Time'),
      '#default_value' => $config->get('start_time') ?: '00:00',
    ];
    $form['times']['end_time'] = [
      '#type' => 'time',
      '#title' => $this->t('End Time'),
      '#default_value' => $config->get('end_time') ?: '00:00',
    ];
    $form['times']['weekend'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable on weekends'),
      '#default_value' => $config->get('weekend') ?: '',
    ];
    
    $form['urls'] = [
      '#type' => 'fieldset',
      '#title' => t('URL'),
      '#collapsible' => TRUE,
    ];
    $form['urls']['old_url'] = [
      '#type' => 'url',
      '#default_value' => $config->get('old_url') ?: '',
      '#title' => t('Original'),
      '#size' => 50,
      '#required' => TRUE,
    ];
    $form['urls']['new_url'] = [
      '#type' => 'url',
      '#default_value' => $config->get('new_url') ?: '',
      '#title' => t('New'),
      '#size' => 50,
      '#required' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="enabled"]' => array('checked' => TRUE),
        ],
      ],
    ];
    $form['#attached']['library'][] = 'notification/icheck';
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
      ->set('start_time', $form_state->getValue('start_time'))
      ->set('end_time', $form_state->getValue('end_time'))
      ->set('weekend', $form_state->getValue('weekend'))
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
      ->set('start_time', $form_state->getValue('start_time'))
      ->set('end_time', $form_state->getValue('end_time'))
      ->set('weekend', $form_state->getValue('weekend'))
      ->set('old_url', $form_state->getValue('old_url'))
      ->set('new_url', $form_state->getValue('new_url'))
      ->save();
    }
    parent::submitForm($form, $form_state);
  }
}