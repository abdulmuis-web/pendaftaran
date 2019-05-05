<?php

namespace Drupal\pendaftaran\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Pendaftaran entity.
 *
 * @ingroup pendaftaran
 *
 * @ContentEntityType(
 *   id = "pendaftaran",
 *   label = @Translation("Pendaftaran"),
 *   handlers = {
 *     "storage" = "Drupal\pendaftaran\PendaftaranStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\pendaftaran\PendaftaranListBuilder",
 *     "views_data" = "Drupal\pendaftaran\Entity\PendaftaranViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\pendaftaran\Form\PendaftaranForm",
 *       "add" = "Drupal\pendaftaran\Form\PendaftaranForm",
 *       "edit" = "Drupal\pendaftaran\Form\PendaftaranForm",
 *       "delete" = "Drupal\pendaftaran\Form\PendaftaranDeleteForm",
 *     },
 *     "access" = "Drupal\pendaftaran\PendaftaranAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\pendaftaran\PendaftaranHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "pendaftaran",
 *   revision_table = "pendaftaran_revision",
 *   revision_data_table = "pendaftaran_field_revision",
 *   admin_permission = "administer pendaftaran entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/pendaftaran/{pendaftaran}",
 *     "add-form" = "/admin/content/pendaftaran/add",
 *     "edit-form" = "/admin/content/pendaftaran/{pendaftaran}/edit",
 *     "delete-form" = "/admin/content/pendaftaran/{pendaftaran}/delete",
 *     "version-history" = "/admin/content/pendaftaran/{pendaftaran}/revisions",
 *     "revision" = "/admin/content/pendaftaran/{pendaftaran}/revisions/{pendaftaran_revision}/view",
 *     "revision_revert" = "/admin/content/pendaftaran/{pendaftaran}/revisions/{pendaftaran_revision}/revert",
 *     "revision_delete" = "/admin/content/pendaftaran/{pendaftaran}/revisions/{pendaftaran_revision}/delete",
 *     "collection" = "/admin/content/pendaftaran",
 *   },
 *   field_ui_base_route = "pendaftaran.settings"
 * )
 */
class Pendaftaran extends RevisionableContentEntityBase implements PendaftaranInterface {

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

    // If no revision author has been set explicitly, make the pendaftaran owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
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
      ->setDescription(t('The user ID of author of the Pendaftaran entity.'))
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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('NISN'))
      ->setDescription(t('The name of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -41,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -41,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_lengkap'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama'))
      ->setDescription(t('The name of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -40,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -40,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['tempat_lahir'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Tempat lahir'))
      ->setDescription(t('The tempat_lahir of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -40,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -40,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['tgl_lahir'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Tanggal lahir'))
      ->setDescription(t('The tgl_lahir of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 20,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -40,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -40,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_ayah'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Ayah'))
      ->setDescription(t('The nama_ayah of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -40,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -40,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['matematika'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Matematika'))
      ->setDescription(t('The matematika of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -39,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -39,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['ipa'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('IPA'))
      ->setDescription(t('The ipa of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -39,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -39,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['ips'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('IPS'))
      ->setDescription(t('The ips of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -39,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -39,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Pendaftaran is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['english'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('B. Inggris'))
      ->setDescription(t('The english of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -39,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -39,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['indonesia'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('B. Indonesia'))
      ->setDescription(t('The indonesia of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -38,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -38,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Pendaftaran is published.'))
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

    return $fields;
  }

}
