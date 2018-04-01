<?php
/**
 * @file
 * Contains \Drupal\general\Controller\SolanaceaeController.
 */

namespace Drupal\general\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\votingapi\Entity\Vote;
use Drupal\votingapi\Entity\VoteType;

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
}