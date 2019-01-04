<?php

namespace Drupal\todo_new\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Todo new entity entities.
 *
 * @ingroup todo_new
 */
interface TodoNewEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Todo new entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Todo new entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Todo new entity creation timestamp.
   *
   * @param int $timestamp
   *   The Todo new entity creation timestamp.
   *
   * @return \Drupal\todo_new\Entity\TodoNewEntityInterface
   *   The called Todo new entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Todo new entity published status indicator.
   *
   * Unpublished Todo new entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Todo new entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Todo new entity.
   *
   * @param bool $published
   *   TRUE to set this Todo new entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\todo_new\Entity\TodoNewEntityInterface
   *   The called Todo new entity entity.
   */
  public function setPublished($published);

  /**
   * Gets the Todo new entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Todo new entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\todo_new\Entity\TodoNewEntityInterface
   *   The called Todo new entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Todo new entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Todo new entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\todo_new\Entity\TodoNewEntityInterface
   *   The called Todo new entity entity.
   */
  public function setRevisionUserId($uid);

}
