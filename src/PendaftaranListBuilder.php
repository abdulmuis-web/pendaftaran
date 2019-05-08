<?php

namespace Drupal\pendaftaran;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Pendaftaran entities.
 *
 * @ingroup pendaftaran
 */
class PendaftaranListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['name'] = $this->t('NISN');
    $header['nama_lengkap'] = $this->t('Nama Siswa');
    $header['skor_zonasi'] = $this->t('Zonasi');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\pendaftaran\Entity\Pendaftaran */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.pendaftaran.canonical',
      ['pendaftaran' => $entity->id()]
    );
    $row['nama_lengkap'] = $entity->nama_lengkap->value;
    $row['skor_zonasi'] = $entity->skor_zonasi->value;
    return $row + parent::buildRow($entity);
  }

}
