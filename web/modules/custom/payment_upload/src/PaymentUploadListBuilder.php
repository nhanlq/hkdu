<?php

namespace Drupal\payment_upload;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Payment upload entities.
 *
 * @ingroup payment_upload
 */
class PaymentUploadListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Payment upload ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\payment_upload\Entity\PaymentUpload $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.payment_upload.edit_form',
      ['payment_upload' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
