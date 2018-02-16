<?php

namespace Drupal\general\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class HeaderSearch.
 *
 * @package Drupal\my_search\Form
 */
class HeaderSearch extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'header_search';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#action'] = '/search';
    $form['#method'] = 'get';
    $form['k'] = [
      '#type' => 'search',
      '#title' => $this->t('Search ACAS'),
      '#maxlength' => 64,
      '#size' => 15,
      '#title_display' => 'invisible',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      // Prevent op from showing up in the query string.
      '#name' => '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Form is redirected no need for anything here.
  }
}