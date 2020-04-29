<?php

namespace Drupal\clinical_focus\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Clinical focus entity.
 *
 * @ingroup clinical_focus
 *
 * @ContentEntityType(
 *   id = "clinical_focus",
 *   label = @Translation("Clinical focus"),
 *   handlers = {
 *     "storage" = "Drupal\clinical_focus\ClinicalFocusStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\clinical_focus\ClinicalFocusListBuilder",
 *     "views_data" = "Drupal\clinical_focus\Entity\ClinicalFocusViewsData",
 *     "translation" = "Drupal\clinical_focus\ClinicalFocusTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\clinical_focus\Form\ClinicalFocusForm",
 *       "add" = "Drupal\clinical_focus\Form\ClinicalFocusForm",
 *       "edit" = "Drupal\clinical_focus\Form\ClinicalFocusForm",
 *       "delete" = "Drupal\clinical_focus\Form\ClinicalFocusDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\clinical_focus\ClinicalFocusHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\clinical_focus\ClinicalFocusAccessControlHandler",
 *   },
 *   base_table = "clinical_focus",
 *   data_table = "clinical_focus_field_data",
 *   revision_table = "clinical_focus_revision",
 *   revision_data_table = "clinical_focus_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer clinical focus entities",
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
 *     "canonical" = "/e-pharm/clinical-focus/{clinical_focus}",
 *     "add-form" = "/admin/hkdu/clinical-focus/add",
 *     "edit-form" = "/admin/hkdu/clinical-focus/{clinical_focus}/edit",
 *     "delete-form" = "/admin/hkdu/clinical-focus/{clinical_focus}/delete",
 *     "version-history" = "/admin/hkdu/clinical-focus/{clinical_focus}/revisions",
 *     "revision" = "/admin/hkdu/clinical-focus/{clinical_focus}/revisions/{clinical_focus_revision}/view",
 *     "revision_revert" = "/admin/hkdu/clinical-focus/{clinical_focus}/revisions/{clinical_focus_revision}/revert",
 *     "revision_delete" = "/admin/hkdu/clinical-focus/{clinical_focus}/revisions/{clinical_focus_revision}/delete",
 *     "translation_revert" = "/admin/hkdu/clinical-focus/{clinical_focus}/revisions/{clinical_focus_revision}/revert/{langcode}",
 *     "collection" = "/admin/hkdu/clinical-focus",
 *   },
 *   field_ui_base_route = "clinical_focus.settings"
 * )
 */
class ClinicalFocus extends EditorialContentEntityBase implements ClinicalFocusInterface {

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
    // make the clinical_focus owner the revision author.
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
      ->setDescription(t('The user ID of author of the Clinical focus entity.'))
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
      ->setDescription(t('The name of the Clinical focus entity.'))
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

    $fields['status']->setDescription(t('A boolean indicating whether the Clinical focus is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'default_value' => 0,
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
