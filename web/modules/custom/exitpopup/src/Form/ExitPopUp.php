<?php

namespace Drupal\exitpopup\Form;

/**
* @file
* Contains Drupal\exitpopup\Form\ExitPopUp.
*/

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\Role;

/**
 * Class ExitPopUp.
 */
class ExitPopUp extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'exitpopup.settings',
    ];
  }

  /**
   * Get form id.
   */
  public function getFormId() {
    return 'exitpopup_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('exitpopup.settings');

    $form['epu_email'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('epu_email') ?: '',
      '#title' => t('Email'),
      '#description' => t('Email address to send submissions'),
      '#required' => TRUE,
    );
    
    $form['epu_webform'] = array(
      '#type' => 'textfield',
      '#default_value' => $config->get('epu_webform') ?: 'exit_popup',
      '#title' => t('Webform ID'),
      '#description' => t('The Webform machine ID e.g. "exit_popup"'),
      '#required' => TRUE,
    );
    
    $form['epu_pages'] = [
      '#title' => $this->t('Pages to add the popup (leave blank for all pages)'),
      '#type' => 'text_format',
      '#format' => 'restricted_html',
      '#rows' => 5,
      '#default_value' => $config->get('epu_pages.value'),
      '#description' => $this->t('Specify pages by using their paths. Enter one path per line. An example path is /about-us or /node/123. &lt;front&gt; is the front page.'),
    ];

    $form['epu_css'] = [
      '#type' => 'text_format',
      '#format' => 'restricted_html',
      '#rows' => 5,
      '#title' => 'Custom CSS',
      '#description' => 'write custom css for the above html code ',
      '#default_value' => $config->get('epu_css.value'),
    ];

    $form['epu_delay'] = [
      '#type' => 'number',
      '#title' => ' Delay on Display POP UP',
      '#description' => 'The time, in seconds, until the popup activates and begins watching for exit intent. If showOnDelay is set to true, this will be the time until the popup shows. ',
      '#default_value' => $config->get('epu_delay'),
    ];

    $form['epu_cookie_exp'] = [
      '#type' => 'number',
      '#title' => 'Cookie Expire Time (in Days)',
      '#description' => 'The number of days to set the cookie for. A cookie is used to track if the popup has already been shown to a specific visitor. If the popup has been shown, it will not show again until the cookie expires. A value of 0 will always show the popup. ',
      '#default_value' => $config->get('epu_cookie_exp'),
    ];

    $form['epu_width'] = [
      '#type' => 'number',
      '#title' => ' Width For the POP UP',
      '#description' => 'The width of the popup. This can be overridden by adding your own CSS for the #bio_ep element. ',
      '#default_value' => $config->get('epu_width'),
    ];

    $form['epu_height'] = [
      '#type' => 'number',
      '#title' => ' Height For the POP UP',
      '#description' => 'The width of the popup. This can be overridden by adding your own CSS for the #bio_ep element. ',
      '#default_value' => $config->get('epu_height'),
    ];

    $defaultRoles = $config->get('roles');
    $roles = Role::loadMultiple();
    $options = [];
    foreach ($roles as $role) {
      $options[$role->id()] = $role->label();
    }

    $form['roles'] = [
      '#type' => 'checkboxes',
      '#title' => t('Select roles to show exit popup'),
      '#options' => $options,
      '#default_value' => isset($defaultRoles) ? $defaultRoles : FALSE,
    ];

    $form['cache'] = [
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    // Get the config object.
    $config = $this->config('exitpopup.settings');

    $epu_email = $form_state->getValue('epu_email');
    $epu_webform = $form_state->getValue('epu_webform');
    $epu_pages = $form_state->getValue('epu_pages')['value'];
    $epu_css = $form_state->getValue('epu_css')['value'];
    $epu_delay = $form_state->getValue('epu_delay');
    $epu_width = $form_state->getValue('epu_width');
    $epu_height = $form_state->getValue('epu_height');
    $epu_cookie_exp = $form_state->getValue('epu_cookie_exp');
    $roles = $form_state->getValue('roles');

    // Set the values the user submitted in the form.
    $config->set('epu_email', $epu_email)
      ->set('epu_webform', $epu_webform)
      ->set('epu_pages.value', $epu_pages)
      ->set('epu_css.value', $epu_css)
      ->set('epu_delay', $epu_delay)
      ->set('epu_width', $epu_width)
      ->set('epu_height', $epu_height)
      ->set('epu_cookie_exp', $epu_cookie_exp)
      ->set('roles', $roles)
      ->save();
  }

}
