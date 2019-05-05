<?php

namespace Drupal\pendaftaran\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Pendaftaran entities.
 *
 * @ingroup pendaftaran
 */
interface PendaftaranInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Pendaftaran name.
   *
   * @return string
   *   Name of the Pendaftaran.
   */
  public function getName();

  /**
   * Sets the Pendaftaran name.
   *
   * @param string $name
   *   The Pendaftaran name.
   *
   * @return \Drupal\pendaftaran\Entity\PendaftaranInterface
   *   The called Pendaftaran entity.
   */
  public function setName($name);

  /**
   * Gets the Pendaftaran creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Pendaftaran.
   */
  public function getCreatedTime();

  /**
   * Sets the Pendaftaran creation timestamp.
   *
   * @param int $timestamp
   *   The Pendaftaran creation timestamp.
   *
   * @return \Drupal\pendaftaran\Entity\PendaftaranInterface
   *   The called Pendaftaran entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Pendaftaran published status indicator.
   *
   * Unpublished Pendaftaran are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Pendaftaran is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Pendaftaran.
   *
   * @param bool $published
   *   TRUE to set this Pendaftaran to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\pendaftaran\Entity\PendaftaranInterface
   *   The called Pendaftaran entity.
   */
  public function setPublished($published);

  /**
   * Gets the Pendaftaran revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Pendaftaran revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\pendaftaran\Entity\PendaftaranInterface
   *   The called Pendaftaran entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Pendaftaran revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Pendaftaran revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\pendaftaran\Entity\PendaftaranInterface
   *   The called Pendaftaran entity.
   */
  public function setRevisionUserId($uid);

}
