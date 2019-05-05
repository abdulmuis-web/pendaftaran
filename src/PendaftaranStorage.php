<?php

namespace Drupal\pendaftaran;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\pendaftaran\Entity\PendaftaranInterface;

/**
 * Defines the storage handler class for Pendaftaran entities.
 *
 * This extends the base storage class, adding required special handling for
 * Pendaftaran entities.
 *
 * @ingroup pendaftaran
 */
class PendaftaranStorage extends SqlContentEntityStorage implements PendaftaranStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(PendaftaranInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {pendaftaran_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {pendaftaran_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(PendaftaranInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {pendaftaran_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('pendaftaran_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
