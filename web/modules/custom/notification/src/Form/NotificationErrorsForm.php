<?php

namespace Drupal\notification\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Render\Markup;

/**
 * Implements the Dynamics form.
 */
class NotificationErrorsForm extends FormBase {
    
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'notification_errors_form';
  }
  
  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'notification.settings'
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('notification.settings');
    $header = [
      'date' => $this->t('Date'),
      'form' => $this->t('Form'),
      'reference' => $this->t('Reference'),
      'status' => $this->t('Status'),
      'edit' => $this->t('Show'),
      'delete' => $this->t('Delete'),
    ];
    $query = \Drupal::database()->select('acas_notify_errors', 'ne');
    $query->fields('ne', ['id', 'timestamp', 'form', 'guid', 'data', 'error', 'reference_no', 'status']);
    $query->orderBy('ne.id', 'DESC');
    $result = $query->execute()->fetchAll();
    $rows = [];
    foreach ($result as $row) {
      $rows[$row->id] = [
        'date' => $row->timestamp,
        'form' => $row->form,
        'reference' => $row->reference_no,
        'status' => $row->status,
        'edit' => new FormattableMarkup('<a class="edit" href="#" id="' . $row->id . '" status="' . $row->status . '" date="' . $row->timestamp . '" guid="' . $row->guid . '" form="' . $row->form . '" reference="' . $row->reference_no . '">Show</a>', []),
        'delete' => new FormattableMarkup('<a class="delete" href="#" id="' . $row->id . '" reference="' . $row->reference_no . '">Delete</a>', []),
      ];
      $form['data_' . $row->id] = [
        '#type' => 'hidden',
        '#value' => $row->data,
      ];
      $form['error_' . $row->id] = [
        '#type' => 'hidden',
        '#value' => $row->error,
      ];
    }
    $limit = 10;
    pager_default_initialize(count($rows), $limit);
    $page = pager_find_page();
    if (count($rows) > $limit) {
      $rows = array_slice($rows, $page * $limit, $limit);
    }
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No errors found'),
    ];
    $form['pager'] = array(
      '#type' => 'pager'
    );
    $form['display'] = [
      '#type' => 'details',
      '#title' => t('Data'),
      '#open' => TRUE,
    ];
    $form['display']['info'] = [
      '#markup' => Markup::create('<div class="details"><label>Date: </label><span id="date"></span><label>Reference: </label><span id="reference"></span><label>Form: </label><span id="form"></span><label>Status: </label><span id="status"></span><label>GUID: </label><span id="guid"></span></div><hr>'),
    ];
    $form['display']['json'] = [
      '#type' => 'details',
      '#title' => t('JSON'),
      '#open' => TRUE,
    ];
    $form['display']['json']['data'] = [
      '#type' => 'textarea',
      '#rows' => 20,
    ];
    $form['display']['dynamics'] = [
      '#type' => 'details',
      '#title' => t('Dynamics error'),
      '#open' => FALSE,
    ];
    $form['display']['dynamics']['error'] = [
      '#markup' => Markup::create('<pre id="error"></pre>'),
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Retry Submission'),
      '#attributes' => ['class' => ['button, button--primary']],
      '#suffix' => '<span class="ajax-progress-throbber"><span class="ajax-progress"><span class="throbber"><span class="text">Submitting...</span></span></span></span>',
    ];
    $form['current_id'] = [
      '#type' => 'hidden',
      '#value' => 0,
    ];
    $form['#attached']['library'][] = "notification/notification_error_form";
    return $form;
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
    \Drupal::configFactory()->getEditable('notification.settings')
    ->set('key', $form_state->getValue('key'))
    ->save();
    parent::submitForm($form, $form_state);
  }
}