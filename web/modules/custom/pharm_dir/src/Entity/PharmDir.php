<?php

namespace Drupal\pharm_dir\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Pharm dir entity.
 *
 * @ingroup pharm_dir
 *
 * @ContentEntityType(
 *   id = "pharm_dir",
 *   label = @Translation("Pharm dir"),
 *   handlers = {
 *     "storage" = "Drupal\pharm_dir\PharmDirStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\pharm_dir\PharmDirListBuilder",
 *     "views_data" = "Drupal\pharm_dir\Entity\PharmDirViewsData",
 *     "translation" = "Drupal\pharm_dir\PharmDirTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\pharm_dir\Form\PharmDirForm",
 *       "add" = "Drupal\pharm_dir\Form\PharmDirForm",
 *       "edit" = "Drupal\pharm_dir\Form\PharmDirForm",
 *       "delete" = "Drupal\pharm_dir\Form\PharmDirDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\pharm_dir\PharmDirHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\pharm_dir\PharmDirAccessControlHandler",
 *   },
 *   base_table = "pharm_dir",
 *   data_table = "pharm_dir_field_data",
 *   revision_table = "pharm_dir_revision",
 *   revision_data_table = "pharm_dir_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer pharm dir entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/e-pharm/pharm-dir/{pharm_dir}",
 *     "add-form" = "/admin/hkdu/pharm-dir/add",
 *     "edit-form" = "/admin/hkdu/pharm-dir/{pharm_dir}/edit",
 *     "delete-form" = "/admin/hkdu/pharm-dir/{pharm_dir}/delete",
 *     "version-history" = "/admin/hkdu/pharm-dir/{pharm_dir}/revisions",
 *     "revision" = "/admin/hkdu/pharm-dir/{pharm_dir}/revisions/{pharm_dir_revision}/view",
 *     "revision_revert" = "/admin/hkdu/pharm-dir/{pharm_dir}/revisions/{pharm_dir_revision}/revert",
 *     "revision_delete" = "/admin/hkdu/pharm-dir/{pharm_dir}/revisions/{pharm_dir_revision}/delete",
 *     "translation_revert" = "/admin/hkdu/pharm-dir/{pharm_dir}/revisions/{pharm_dir_revision}/revert/{langcode}",
 *     "collection" = "/admin/hkdu/pharm-dir",
 *   },
 *   field_ui_base_route = "pharm_dir.settings"
 * )
 */
class PharmDir extends EditorialContentEntityBase implements PharmDirInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

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

    // If no revision author has been set explicitly,
    // make the pharm_dir owner the revision author.
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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Pharm dir entity.'))
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
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Pharm dir entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Pharm dir is published.'))
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

}
