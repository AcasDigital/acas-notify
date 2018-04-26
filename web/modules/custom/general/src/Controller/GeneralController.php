<?php
/**
 * @file
 * Contains \Drupal\general\Controller\SolanaceaeController.
 */

namespace Drupal\general\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\votingapi\Entity\Vote;
use Drupal\votingapi\Entity\VoteType;
use Symfony\Component\HttpFoundation\JsonResponse;
use ZipArchive;

class GeneralController extends ControllerBase {
  public function searchheader() {
    return array('#markup' => '');
  }
  public function health() {
    return array('#markup' => general_health());
  }
  public function feedback($entity_id, $value) {
    if ($value == 'Yes') {
      $vote_value = 1;
    }else{
      $vote_value = -1;
    }
    
    $entity = $this->entityTypeManager()
      ->getStorage('node')
      ->load($entity_id);
      
    $voteType = VoteType::load('vote');
    $this->entityTypeManager()
      ->getViewBuilder('node')
      ->resetCache([$entity]);
      
    $vote = Vote::create(['type' => 'vote']);
    $vote->setVotedEntityId($entity_id);
    $vote->setVotedEntityType('node');
    $vote->setValueType($voteType->getValueType());
    $vote->setValue($vote_value);
    $vote->save();

    $this->entityTypeManager()
      ->getViewBuilder('node')
      ->resetCache([$entity]);
      
    return new JsonResponse([
      'vote' => $vote_value,
      'message_type' => 'status',
      'operation' => 'voted',
      'message' => t('Your vote was added.'),
    ]);
  }
  
  public function notfound() {
    $connection = \Drupal::database();
    $current_uri = \Drupal::request()->getRequestUri();
    if (strpos($current_uri, 'helpline') !== FALSE) {
      $nid = $connection->query("SELECT nid FROM {node_field_data} WHERE title = 'Helpline' AND type = 'support_page'")->fetchField();
      $node = \Drupal\node\Entity\Node::load($nid);
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
      return $view_builder->view($node, 'full');
    }
    
    return array('#markup' => 'The requested page could not be found.');
  }
  
  public function getNotFoundTitle() {
    $current_uri = \Drupal::request()->getRequestUri();
    if (strpos($current_uri, 'helpline') !== FALSE) {
      return 'Helpline';
    }else{
      return 'Page not found';
    }
  }
  
  public function feedback_results() {
    
  }
  
  public function anything_wrong_results() {
    $return = [];
    $connection = \Drupal::database();
    $query = $connection->query("SELECT s.sid, s.created FROM {webform_submission} s WHERE s.webform_id = 'anything_wrong'");
    $result = $query->fetchAll();
    foreach($result as $sid) {
      $query2 = $connection->query("SELECT name, value FROM {webform_submission_data} WHERE sid = :sid", array('sid' => $sid->sid));
      $result2 = $query2->fetchAll();
      $data = [];
      $data['sid'] = $sid->sid;
      $data['date'] = date('Y-m-d', $sid->created);
      foreach($result2 as $s) {
        if ($s->name == 'email_optional_') {
          $s->name = 'email';
        }
        if ($s->name == 'how_should_we_improve_this_page_') {
          $s->name = 'message';
        }
        if ($s->name == 'name_optional_') {
          $s->name = 'name';
        }
        $data[$s->name] = $s->value;
      }
      $return[] = $data;
    }
    return new JsonResponse($return);
  }
  
  public function sync_prod() {
    general_sync_prod();
    return array('#markup' => '<h3>Finished</h3>');
  }
  
  public function sync_update() {
    $uuid = \Drupal::config('system.site')->get('uuid');
    if ($uuid == $_POST['UUID']) {
      file_put_contents('/tmp/sync.zip', base64_decode($_POST['data']));
      $zip = new ZipArchive();
      $zip->open('/tmp/sync.zip');
      $zip->extractTo('/tmp/');
      $zip->close();
      unlink('/tmp/sync.zip');
      $connection = \Drupal\Core\Database\Database::getConnection()->getConnectionOptions();
      $cmd = 'mysql -u ' . $connection['username'] . ' -p' . $connection['password'] . ' -h ' . $connection['host'] . ' ' . $connection['database'] . ' < /tmp/' . $_POST['file'];
      exec($cmd);
      unlink('/tmp/' . $_POST['file']);
      return new JsonResponse('ok');
    }
    return new JsonResponse('error');
  }
}