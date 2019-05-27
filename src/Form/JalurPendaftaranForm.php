<?php

namespace Drupal\pendaftaran\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class JalurPendaftaranForm.
 */
class JalurPendaftaranForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'pendaftaran.jalurpendaftaran',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'jalur_pendaftaran_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('pendaftaran.jalurpendaftaran');
    $form['jalur_pendaftaran'] = [
      '#type' => 'radios',
      '#title' => $this->t('Jalur Pendaftaran'),
      '#description' => $this->t('Model jalur pendaftaran yang dipakai dalam aplikasi, Model Terpadu ini menambahkan skor dari prestasi dan SKTM ke dalam skor, Model Terpisah menghitung masing masing jalur secara terpisah tdak saling terkait'),
      '#options' => ['0' => $this->t('Model Terpadu'), '1' => $this->t('Model Terpisah')],
      '#default_value' => $config->get('jalur_pendaftaran'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('pendaftaran.jalurpendaftaran')
      ->set('jalur_pendaftaran', $form_state->getValue('jalur_pendaftaran'))
      ->save();
	
	drupal_flush_all_caches();  
  }

}
