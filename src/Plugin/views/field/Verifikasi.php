<?php

namespace Drupal\pendaftaran\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Random;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\Core\Url;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("verifikasi")
 */
class Verifikasi extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['hide_alter_empty'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    // Return a random text, here you can include your custom logic.
    // Include any namespace required to call the method required to generate
    // the desired output.
	$entity = $values->_entity;
	
	$prodi_sekolah_id = $entity->prodi_sekolah->entity->id();
    $link = [
     '#title' => t('Verifikasi'),
     '#type' => 'link',
     '#url' => Url::fromRoute('pendaftaran.verifikasi_admin_form',['pendaftaran' => $entity->id(), 'prodi_sekolah' => $prodi_sekolah_id],
	                                //['query' => \Drupal::service('redirect.destination')->getAsArray()]),
                             ['query' => '/prodi_sekolah/@id', array('@id'=> $prodi_sekolah_id) ]),

     '#attributes' => ['class' => ['button', 'button-action', 'button--primary', 'button--small']]
    ];
    return render($link);
  }

}
