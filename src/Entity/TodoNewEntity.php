<?php

namespace Drupal\todo_new\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Session\UserSession;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the Todo new entity entity.
 *
 * @ingroup todo_new
 *
 * @ContentEntityType(
 *   id = "todo_new_entity",
 *   label = @Translation("Todo new entity"),
 *   handlers = {
 *     "storage" = "Drupal\todo_new\TodoNewEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\todo_new\TodoNewEntityListBuilder",
 *     "views_data" = "Drupal\todo_new\Entity\TodoNewEntityViewsData",
 *     "translation" = "Drupal\todo_new\TodoNewEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\todo_new\Form\TodoNewEntityForm",
 *       "add" = "Drupal\todo_new\Form\TodoNewEntityForm",
 *       "edit" = "Drupal\todo_new\Form\TodoNewEntityForm",
 *       "delete" = "Drupal\todo_new\Form\TodoNewEntityDeleteForm",
 *     },
 *     "access" = "Drupal\todo_new\TodoNewEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\todo_new\TodoNewEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "todo_new_entity",
 *   data_table = "todo_new_entity_field_data",
 *   revision_table = "todo_new_entity_revision",
 *   revision_data_table = "todo_new_entity_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer todo new entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/todo_new_entity/{todo_new_entity}",
 *     "add-form" = "/admin/structure/todo_new_entity/add",
 *     "edit-form" = "/admin/structure/todo_new_entity/{todo_new_entity}/edit",
 *     "delete-form" = "/admin/structure/todo_new_entity/{todo_new_entity}/delete",
 *     "version-history" = "/admin/structure/todo_new_entity/{todo_new_entity}/revisions",
 *     "revision" = "/admin/structure/todo_new_entity/{todo_new_entity}/revisions/{todo_new_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/todo_new_entity/{todo_new_entity}/revisions/{todo_new_entity_revision}/revert",
 *     "revision_delete" = "/admin/structure/todo_new_entity/{todo_new_entity}/revisions/{todo_new_entity_revision}/delete",
 *     "translation_revert" = "/admin/structure/todo_new_entity/{todo_new_entity}/revisions/{todo_new_entity_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/todo_new_entity",
 *   },
 *   field_ui_base_route = "todo_new_entity.settings"
 * )
 */
class TodoNewEntity extends RevisionableContentEntityBase implements TodoNewEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the todo_new_entity owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Todo new entity entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

      $fields['text'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Todo Text'))
      ->setDescription(t('Todo Text'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'settings' => array(
          'display_label' => TRUE,
        ),
        'weight' => -4,
      ))
     ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

      $fields['date'] = BaseFieldDefinition::create('datetime')
       ->setLabel(t('Select Date'))
       ->setDescription(t('Task date'))
       ->setRevisionable(TRUE)
       ->setTranslatable(TRUE)
       ->setSettings([
        'datetime_type' => 'date'
       ])
       ->setDisplayOptions('form', array(
         'type' => 'datetime_default',
         'settings' => array(
          'display_label' => TRUE,
         ),
         'weight' => -4,
       ))
       ->setDisplayOptions('view', array(
         'label' => 'hidden',
         'type' => 'datetime',
         'weight' => -4,
       ))
       ->setDisplayConfigurable('form', TRUE)
       ->setDisplayConfigurable('view', TRUE)
       ->setRequired(TRUE);

      $fields['priority'] = BaseFieldDefinition::create('list_string')
       ->setLabel(t('Priority'))
       ->setDescription(t('Select Priority'))
       ->setRevisionable(TRUE)
       ->setTranslatable(TRUE)
       ->setDisplayOptions('form', array(
         'type' => 'options_select',
         'settings' => array(
            'display_label' => TRUE,
         ),
         'weight' => -4,
       ))
       ->setDisplayOptions('view', array(
         'label' => 'hidden',
         'type' => 'list_string',
         'weight' => -4,
       ))
       ->setDisplayConfigurable('form', TRUE)
       ->setDisplayConfigurable('view', TRUE)
       ->setRequired(TRUE)
       ->setSettings([
         'allowed_values' => [
           'low' => t('Low'),
         'normal' => t('Normal'),
         'high' => t('High'),]
       ])
       ->setDefaultValue(['value' => 'low']);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Todo new entity is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheTagsToInvalidate() {

    if ($this->isNew()) {
      return [];
    }

    if ($user = \Drupal::currentUser()) {
     return [$this->entityTypeId . ':' . $this->id()];
    }

  }

}
