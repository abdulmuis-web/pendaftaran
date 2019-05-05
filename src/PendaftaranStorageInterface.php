<?php

namespace Drupal\pendaftaran;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface PendaftaranStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Pendaftaran revision IDs for a specific Pendaftaran.
   *
   * @param \Drupal\pendaftaran\Entity\PendaftaranInterface $entity
   *   The Pendaftaran entity.
   *
   * @return int[]
   *   Pendaftaran revision IDs (in ascending order).
   */
  public function revisionIds(PendaftaranInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Pendaftaran author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Pendaftaran revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\pendaftaran\Entity\PendaftaranInterface $entity
   *   The Pendaftaran entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(PendaftaranInterface $entity);

  /**
   * Unsets the language for all Pendaftaran with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
