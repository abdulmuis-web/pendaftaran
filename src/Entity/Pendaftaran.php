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
        'weight' => 100,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 100,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

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
        'weight' => -52,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -52,
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
        'weight' => -51,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -51,
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
        'weight' => -50,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -50,
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
        'weight' => -49,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -49,
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
        'weight' => -48,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -48,
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
          'weight' => -47,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -47,
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
          'weight' => -46,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -46,
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
          'weight' => -45,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -45,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['english'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('B. Inggris'))
      ->setDescription(t('The english of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -44,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -44,
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
          'weight' => -43,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -43,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);


    $fields['skor_matematika'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Skor Matematika'))
      ->setDescription(t('The matematika of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -47,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'hidden',
          'weight' => -47,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['skor_ipa'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Skor IPA'))
      ->setDescription(t('The ipa of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -46,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'hidden',
          'weight' => -46,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['skor_ips'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Skor IPS'))
      ->setDescription(t('The ips of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -45,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'hidden',
          'weight' => -45,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['skor_english'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Skor B. Inggris'))
      ->setDescription(t('The english of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -44,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'hidden',
          'weight' => -44,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['skor_indonesia'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Skor B. Indonesia'))
      ->setDescription(t('The indonesia of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -43,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'hidden',
          'weight' => -43,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['provinsi'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Provinsi'))
      ->setDescription(t('The provinsi ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'province')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -42,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -42,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_provinsi'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Provinsi'))
      ->setDescription(t('The nama_provinsi of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -41,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -41,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['kabupaten'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Kabupaten'))
      ->setDescription(t('The kabupaten ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'regency')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -40,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -40,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_kabupaten'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Kabupaten'))
      ->setDescription(t('The nama_kabupaten of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -39,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -39,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['kecamatan'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Kecamatan'))
      ->setDescription(t('The kecamatan ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'district')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -38,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -38,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_kecamatan'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Kecamatan'))
      ->setDescription(t('The nama_kecamatan of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -37,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -37,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['desa'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Desa'))
      ->setDescription(t('The desa ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'vilage')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -36,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -36,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_desa'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Desa'))
      ->setDescription(t('The nama_desa of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -35,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -35,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['jenis_sekolah'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Jenis Sekolah'))
      ->setDescription(t('The jenis_sekolah ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'jenis_sekolah')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -34,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -34,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_jenis_sekolah'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Jenis Sekolah'))
      ->setDescription(t('The nama_jenis_sekolah of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -33,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -33,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['zona_sekolah'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Zona sekolah'))
      ->setDescription(t('The zona_sekolah ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'zona')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -32,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -32,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_zona_sekolah'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Zona Sekolah'))
      ->setDescription(t('The nama_zona_sekolah of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -31,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -31,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['pilihan_sekolah'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Pilihan sekolah'))
      ->setDescription(t('The zona_sekolah ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'pilihan_sekolah')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -30,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -30,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_pilihan_sekolah'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Pilihan Sekolah'))
      ->setDescription(t('The nama_pilihan_sekolah of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -29,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -29,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['desa_sekolah'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Desa sekolah'))
      ->setDescription(t('The desa_sekolah ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'vilage')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -28,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -28,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['kecamatan_sekolah'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Kecamatan sekolah'))
      ->setDescription(t('The kecamatan_sekolah ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'district')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -27,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -27,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['kabupaten_sekolah'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Kabupaten sekolah'))
      ->setDescription(t('The kabupaten_sekolah ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'regency')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -26,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -26,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['provinsi_sekolah'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Provinsi sekolah'))
      ->setDescription(t('The provinsi_sekolah ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'province')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -25,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -25,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['zonasi'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Zonasi'))
      ->setDescription(t('The zonasi ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'zonasi')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -24,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -24,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_zonasi'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Zonasi'))
      ->setDescription(t('The nama_zonasi of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -23,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -23,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nilai_zonasi'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Nilai zonasi'))
      ->setDescription(t('The nilai_zonasi of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -22,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -22,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['prodi_sekolah'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Prodi Sekolah'))
      ->setDescription(t('The prodi_sekolah ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'prodi_sekolah')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -21,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -21,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['nama_prodi_sekolah'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Prodi Sekolah'))
      ->setDescription(t('The nama_prodi_sekolah of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['jalur_sktm'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Jalur SKTM'))
      ->setDescription(t('The jalur_sktm ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'sktm')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -19,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -19,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_jalur_sktm'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Jalur SKTM'))
      ->setDescription(t('The nama_jalur_sktm of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -18,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -18,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['skor_sktm'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Skor SKTM'))
      ->setDescription(t('The skor_sktm of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -17,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -17,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['jalur_prestasi'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Jalur Prestasi'))
      ->setDescription(t('The jalur_prestasi ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'jalur_prestasi')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -16,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -16,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_jalur_prestasi'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Jalur Prestasi'))
      ->setDescription(t('The nama_jalur_prestasi of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -15,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['skor_prestasi'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Skor Prestasi'))
      ->setDescription(t('The skor_prestasi of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -14,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -14,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['penyelenggara'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Penyelenggara'))
      ->setDescription(t('The penyelenggara ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'penyelenggara')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -13,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -13,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_penyelenggara'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Penyelenggara'))
      ->setDescription(t('The nama_penyelenggara of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -12,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -12,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['skor_penyelenggara'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Skor penyelenggara'))
      ->setDescription(t('The skor_penyelenggara of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -11,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -11,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['tingkat'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Tingkat'))
      ->setDescription(t('The tingkat ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'tingkat')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -10,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_tingkat'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Tingkat'))
      ->setDescription(t('The nama_tingkat of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -9,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -9,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['skor_tingkat'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Skor Tingkat'))
      ->setDescription(t('The skor_tingkat of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -8,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -8,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);


    $fields['juara'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Juara'))
      ->setDescription(t('The juara ID of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'juara')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -7,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -7,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['nama_juara'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nama Juara'))
      ->setDescription(t('The nama_juara of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'hidden',
        'weight' => -6,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -6,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['skor_juara'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Skor juara'))
      ->setDescription(t('The skor_juara of the Pendaftaran entity'))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'decimal',
          'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['prestasi'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Prestasi'))
      ->setDescription(t('The prestasi of the Pendaftaran entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 191,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => -4,
      ])
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
