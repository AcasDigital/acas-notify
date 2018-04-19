<?php
namespace Drupal\general;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ResponseSubscriber implements EventSubscriberInterface {

  public function onRespond(FilterResponseEvent $event) {
    if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
      return;
    }
    $route_name = \Drupal::routeMatch()->getRouteName();
    $response = $event->getResponse();
    // Intercept 404
    if ($response->getStatusCode() == 404) {
      
/*
      $request_uri = $event->getRequest()->getRequestUri();

      if (strpos('/user', $request_uri) == 0) {
        $response = new RedirectResponse('/some-better-place');
        $response->send();
        return;
      }
*/
    }
  }

  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = array('onRespond');
    return $events;
  }

}