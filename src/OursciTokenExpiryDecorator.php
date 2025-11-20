<?php

namespace Drupal\farm_oursci;

use Drupal\simple_oauth\TokenExpiryTriggerHandler;
use Drupal\simple_oauth\TokenExpiryTriggerHandlerInterface;

class OursciTokenExpiryDecorator extends TokenExpiryTriggerHandler {

  /**
   * The inner service being decorated.
   *
   * @var \Drupal\simple_oauth\TokenExpiryTriggerHandlerInterface
   */
  protected $innerService;

  public function __construct(TokenExpiryTriggerHandlerInterface $inner_service) {
    $this->innerService = $inner_service;

    parent::__construct(
      $inner_service->configFactory,
      $inner_service->collector,
      $inner_service->logger
    );
  }

  /**
   * {@inheritdoc}
   */
  public function handleUserUpdate($user): void {
    $delete_tokens = FALSE;

    /** Only delete oauth tokens if one of the following happened:
     * - password was changed.
     * - account was deactivated.
     * - roles changed.
     */
    if ($user->pass->value !== $user->original->pass->value) {
      $delete_tokens = TRUE;
    }
    elseif (!$user->isActive() && $user->original->isActive()) {
      $delete_tokens = TRUE;
    }
    else {
      $roles_new = $user->getRoles();
      $roles_old = $user->original->getRoles();
      $roles_changed = !empty(array_merge(
        array_diff($roles_new, $roles_old),
        array_diff($roles_old, $roles_new)
      ));
      if ($roles_changed) {
        $delete_tokens = TRUE;
      }
    }

    if ($delete_tokens) {
      $this->collector->deleteMultipleTokens($this->collector->collectForAccount($user));
    }
  }

  /**
   * Handle all other methods that are not overridden.
   */
  public function __call($method, $args) {
    return call_user_func_array([$this->innerService, $method], $args);
  }
}
