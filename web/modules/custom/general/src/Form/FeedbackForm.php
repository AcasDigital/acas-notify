<?php

namespace Drupal\general\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\link\Plugin\Field\FieldWidget;

/**
 * Class FeedbackForm.
 *
 */
class FeedbackForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'feedback_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['filters'] = array(
      '#type' => 'fieldset',
      '#title' => t('Filters'),
      '#collapsible' => TRUE,
    );
    $form['filters']['from_date'] = [
      '#type' => 'date',
      '#title' => 'From date',
    ];
    $form['filters']['to_date'] = [
      '#type' => 'date',
      '#title' => 'To date',
    ];
    $form['filters']['url'] = [
      '#type' => 'url',
      '#title' => 'URL',
    ];
    $node = \Drupal::service('entity_type.manager')->getStorage('node')->create(array('type' => 'page'));
    $entity_form_display = \Drupal::service('entity_type.manager')->getStorage('entity_form_display')->load('node.page.default');
    if ($widget = $entity_form_display->getRenderer('field_test')) { //Returns the widget class
      $items = $node->get('field_test'); //Returns the FieldItemsList interface
      $items->filterEmptyItems();
      $form['#parents'] = [];
      $form['filters']['url'] = $widget->form($items, $form, $form_state); //Builds the widget form and attach it to your form
    }
    $form['filters']['type'] = [
      '#type' => 'radios',
      '#title' => 'Feedback',
      '#options' => [1 => 'Yes', 2 => 'No', 3 => 'Both'],
      '#default_value' => 3,
    ];
    $options = [1 => 'I do not understand the information', 2 => 'I cannot find the information I\'m looking for', 3 => 'I cannot work out what to do next', 4 => 'Other'];
    $form['filters']['issues'] = [
      '#type' => 'checkboxes',
      '#title' => 'Issues',
      '#options' => $options,
    ];
    $form['filters']['text'] = [
      '#type' => 'textfield',
      '#title' => 'Text',
    ];
    $manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $tree = $manager->loadTree('acas', 0, 2, TRUE);
    $result = [0 => '--Select--'];
    foreach ($tree as $term) {
      if (!empty($manager->loadParents($term->id()))) {
        $result[$term->id()] = $term->getName();
      }
    }
    $form['filters']['taxonomy'] = [
      '#type' => 'select',
      '#title' => 'Taxonomy',
      '#options' => $result,
    ];
    $form['export'] = [
      '#type' => 'checkbox',
      '#title' => 'Export as CSV file',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Apply'),
      '#attributes' => ['class' => ['button, button--primary']],
    ];
    $form['#suffix'] = '<br />' . $this->table($form_state, $options);
    return $form;
  }
  
  public function table(FormStateInterface $form_state, $options) {
    $header = [
      'created' => $this->t('Date'),
      'uri' => $this->t('Page'),
      'type' => $this->t('Type'),
      'issue' => $this->t('Issue'),
      'text' => $this->t('Text'),
      'taxonomy' => $this->t('Taxonomy'),
    ];

    
    $query = \Drupal::database()->select('webform_submission', 'ws');
    $query->fields('ws', ['sid', 'created', 'uri', 'webform_id', 'entity_id']);
    if ($values = $form_state->getValues()) {
      if ($values['from_date'] && $values['to_date']) {
        $query->condition('ws.created', [strtotime($values['from_date']), strtotime($values['to_date'])], 'BETWEEN');
      }else if ($values['from_date']) {
        $query->condition('ws.created', strtotime($values['from_date']), '>=');
      }else if ($values['to_date']) {
        $query->condition('ws.created', strtotime($values['to_date']), '<=');
      }
      if ($values['type'] == 1) {
        $query->condition('ws.webform_id', 'yes_feedback');
      }else if ($values['type'] == 2) {
        $query->condition('ws.webform_id', 'no_feedback');
      }
      if ($values['field_test'][0]['uri']) {
        $a = explode('node/', $values['field_test'][0]['uri']);
        $query->condition('ws.entity_id', $a[1]);
      }
    }
    $query->orderBy('ws.sid', 'DESC');
    $result = $query->execute()->fetchAll();

    $rows = [];
    foreach ($result as $row) {
      $entity = \Drupal\node\Entity\Node::load($row->entity_id);
      $parents = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($entity->get('field_taxonomy')->target_id);
      foreach($parents as $parent) {
        $taxonomy = $parent->getName();
        $tid = $parent->id();
      }
      $query = \Drupal::database()->select('webform_submission_data', 'wsd');
      $query->fields('wsd', ['name', 'value']);
      $query->condition('wsd.sid', $row->sid);
      if ($values) {
        foreach($values['issues'] as $issue) {
          if ($issue) {
            $query->condition('wsd.name', 'radios');
            $query->condition('wsd.value', $issue);
          }
        }
      }
      $result2 = $query->execute()->fetchAll();
      $issue = '';
      $issue_id = 0;
      $text = '';
      $add = TRUE;
      foreach ($result2 as $v) {
        if ($v->name == 'answer') {
          $text = $v->value;
        }
        if ($v->name == 'radios') {
          $issue = $options[$v->value];
          $iid = $v->value;
        }
      }
      $rows[$row->sid] = [
        'created' => format_date($row->created, 'short'),
        'uri' => new FormattableMarkup('<a href="' . $row->uri . '">' . $entity->getTitle() . '</a>', []),
        'type' => ($row->webform_id == 'yes_feedback' ? 'Yes' : 'No'),
        'issue' => $issue,
        'text' => $text,
        'taxonomy' => $taxonomy,
        'iid' => $iid,
        'tid' => $tid,
      ];
    }
    // Now remove if issues or text
    if ($values) {
      $found = FALSE;
      foreach($values['issues'] as $issue) {
        if ($issue) {
          $found = TRUE;
          break;
        }
      }
      if ($found) {
        foreach($rows as $key => $value) {
          
        }
      }
    }
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No feedback found'),
    ];

    return drupal_render($build);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }
}