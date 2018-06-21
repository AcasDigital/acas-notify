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
      'cloudfront.settings'
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('acas.settings');
    $cloudfront_config = $this->config('cloudfront.settings');
    $form['feedback_email'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('contact_email') ?: 'john@johnburch.co.uk',
      '#title' => t('Feedback email'),
      '#description' => t('Emails will be sent to this address when a user completes the feedback form.'),
      '#size' => 100,
    );
    $form['search_placeholder'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('search_placeholder') ?: 'Search beta website',
      '#title' => t('Search placeholder'),
      '#description' => t('Place holder for the Search form'),
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
    $form['cloudfront'] = array(
      '#type' => 'fieldset',
      '#title' => t('CloudFront'),
      '#collapsible' => TRUE,
    );
    $form['cloudfront']['id'] = array(
      '#type' => 'textfield',
      '#default_value' => $cloudfront_config->get('id') ?: 'ER9FC5MXDG96X',
      '#title' => t('Distribution ID'),
      '#required' => TRUE,
    );
    $form['cloudfront']['key'] = array(
      '#type' => 'textfield',
      '#default_value' => $cloudfront_config->get('key') ?: 'AKIAIKOTK75DYAYK7MUA',
      '#title' => t('AWS Key'),
      '#required' => TRUE,
    );
    $form['cloudfront']['secret'] = array(
      '#type' => 'textfield',
      '#default_value' => $cloudfront_config->get('secret') ?: 'nxsvPvrGmJVyZIyl4BWpfSXPbTdhkZUfYjfkZ4ZU',
      '#title' => t('AWS Secret'),
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
    ->set('feedback_email', $form_state->getValue('feedback_email'))
    ->set('search_placeholder', $form_state->getValue('search_placeholder'))
    ->set('prod', $form_state->getValue('prod'))
    ->set('tables', $form_state->getValue('tables'))
    ->set('config', $form_state->getValue('config'))
    ->save();
    
    \Drupal::configFactory()->getEditable('cloudfront.settings')
    ->set('id', $form_state->getValue('id'))
    ->set('key', $form_state->getValue('key'))
    ->set('secret', $form_state->getValue('secret'))
    ->save();
    parent::submitForm($form, $form_state);
  }
}