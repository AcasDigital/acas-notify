<?php

namespace Drupal\general\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SearchForm.
 *
 */
class SearchForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('acas.settings');
    $form['#action'] = '/search';
    $form['#method'] = 'get';
    $form['keys'] = [
      '#type' => 'textfield',
      '#title' => $config->get('search_placeholder'),
      '#title_display' => 'invisible',
      '#prefix' => '<div class="form--inline form-inline clearfix">',
      '#attributes' => ['placeholder' => $config->get('search_placeholder')],
      '#required' => TRUE,
    ];

    $form['submit_search'] = [
      '#type' => 'submit',
      '#value' => '',
      '#name' => '',
      '#prefix' => '<section data-drupal-selector="edit-actions" class="form-actions form-group js-form-wrapper form-wrapper" id="edit-search-actions">',
      '#suffix' => '</section></div>',
    ];
    $form['#attributes']['onsubmit'] = 'return viewsExposedSubmitForm(this);';
    $form['#attached']['library'][] = 'general/views_exposed_form_submit';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Form is redirected no need for anything here.
  }
}