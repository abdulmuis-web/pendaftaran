<?php

namespace Drupal\pendaftaran;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Pendaftaran entity.
 *
 * @see \Drupal\pendaftaran\Entity\Pendaftaran.
 */
class PendaftaranAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\pendaftaran\Entity\PendaftaranInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished pendaftaran entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published pendaftaran entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit pendaftaran entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete pendaftaran entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add pendaftaran entities');
  }

}
