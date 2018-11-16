<?php

namespace Drupal\general\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the Admin form.
 */
class ServicesForm extends ConfigFormBase {
    
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'services_form';
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
    $form['notification_ref_no_ip'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('notification_ref_no_ip') ?: 'Number-service-load-balancer-324734204.eu-west-1.elb.amazonaws.com',
      '#title' => t('URL of the Notification reference number service'),
      '#required' => TRUE,
    );
    $form['notification_ref_userpass'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('notification_ref_userpass') ?: '',
      '#title' => t('Service user & password'),
      '#description' => t('Format user:password eg. fred;mypass'),
      '#required' => TRUE,
    );
    $this->get_service_data($form);

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
    ->set('notification_ref_no_ip', $form_state->getValue('notification_ref_no_ip'))
    ->set('notification_ref_userpass', $form_state->getValue('notification_ref_userpass'))
    ->save();
    parent::submitForm($form, $form_state);
    $config = $this->config('acas.settings');
    $url = $config->get('notification_ref_no_ip') . '?save=' . urlencode(serialize($form_state->getValue('services')));
    $process = curl_init($url);
    curl_setopt($process, CURLOPT_USERPWD, $config->get('notification_ref_userpass'));
    curl_setopt($process, CURLOPT_TIMEOUT, 30);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
    $data = curl_exec($process);
    curl_close($process);
  }
  
  private function get_service_data(&$form) {
    $form['services'] = array(
      '#type' => 'table',
      '#caption' => 'Notification refrence numbers',
      '#header' => array('Service', 'Individual', 'Group'),
    );
    $config = $this->config('acas.settings');
    $url = $config->get('notification_ref_no_ip'). '?data=1';
    $process = curl_init($url);
    curl_setopt($process, CURLOPT_USERPWD, $config->get('notification_ref_userpass'));
    curl_setopt($process, CURLOPT_TIMEOUT, 30);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
    $data = unserialize(curl_exec($process));
    curl_close($process);
    $rows = [];
    foreach($data as $key => $value) {
      $form['services'][$value['service']]['service'] = [
        '#markup' => $value['service'],
      ];
      $form['services'][$value['service']]['individual'] = [
        '#type' => 'textfield',
        '#title' => t('Name'),
        '#title_display' => 'invisible',
        '#default_value' => $value['individual_no'],
        '#size' => 20,
      ];
      $form['services'][$value['service']]['group'] = [
        '#type' => 'textfield',
        '#title' => t('Group'),
        '#title_display' => 'invisible',
        '#default_value' => $value['group_no'],
        '#size' => 20,
      ];
    }
  }

}