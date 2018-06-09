<?php
namespace Drupal\general\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RemoveXGeneratorResponseHeader implements EventSubscriberInterface {
  public function RemoveXGeneratorOptions(FilterResponseEvent $event) {
    $response = $event->getResponse();
    $response->headers->remove('X-Generator');
    $response->headers->remove('X-Drupal-Cache-Tags');
    $response->headers->remove('X-Drupal-Dynamic-Cache');
    $response->headers->remove('X-Drupal-Cache-Contexts');
  }
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = array('RemoveXGeneratorOptions', -10);
    return $events;
  }
}