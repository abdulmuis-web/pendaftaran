<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\sktm\Entity\Sktm;

class MultistepJalurSktmForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_jalur_sktm';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

	$form['jalur_sktm'] = array(
	  '#title' => t('Apakah Anda akan mengikuti jalur keluarga tidak mampu ?'),
	  '#type' => 'radios',
	  '#default_value' => $this->store->get('jalur_sktm'),
	  '#required' => TRUE,
	  '#description' => 'Keterangan tidak mampu akan diverifikasi oleh admin sekolah.',
	  '#options' => $this->getSktmOptions(),
	);

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Kembali ke piihan Program studi'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('pendaftaran.multistep_prodi_sekolah'),
    );

    $form['actions']['submit']['#value'] = $this->t('Next');

    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getSktmOptions($index = FALSE){
	$query = \Drupal::entityQuery('sktm')
	->execute();
	$records = Sktm::loadMultiple($query);
	$options = array();
	$display_options = array();
	foreach($records as $record){
	  $options[$record->id()] = $record->label();
	  $display_options[$record->id()] = t('Ya, dengan menggunakan @sktm', array('@sktm' => $record->label()));
	}
    unset($display_options['10']);
	$display_options['10'] = t('Tidak, Saya tidak mengikuti jalur keluarga tidak mampu');
    if($index !== FALSE){
      return $options[$index];
    }
	return $display_options;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state){
    $this->store->set('jalur_sktm', $form_state->getValue('jalur_sktm'));
	$jalur_sktm = Sktm::load($form_state->getValue('jalur_sktm'));
	
    //$this->store->set('nama_jalur_sktm', $this->getSktmOptions($form_state->getValue('jalur_sktm')));
    $this->store->set('skor_sktm', $jalur_sktm->score->value);
    $this->store->set('nama_jalur_sktm', $jalur_sktm->label());

	$elements = array('provinsi', 'nama_provinsi', 'kabupaten', 'nama_kabupaten', 'kecamatan', 'nama_kecamatan', 'desa', 'nama_desa',
	                  'jenis_sekolah','nama_jenis_sekolah','zona_sekolah', 'nama_zona_sekolah', 'nama_jenis_sekolah', 'pilihan_sekolah', 'nama_pilihan_sekolah', 'desa_sekolah', 'kecamatan_sekolah',
					  'kabupaten_sekolah','provinsi_sekolah', 'zonasi', 'nama_zonasi', 'nilai_zonasi', 'prodi_sekolah', 'nama_prodi_sekolah', 'jalur_sktm', 'nama_jalur_sktm', 'skor_sktm');					  

	foreach ($elements as $key => $element) {
		$values[$element] = $this->store->get($element);
	}
	dpm($values);
    
	/*
	 * Persiapan jika path di kelola berdasarkan module tertentu 
	 *
	 * if (\Drupal::moduleHandler()->moduleExists('jalur_sktm')){
	 *
	 * }
	 */
    $form_state->setRedirect('pendaftaran.multistep_jalur_prestasi');
  }

}
