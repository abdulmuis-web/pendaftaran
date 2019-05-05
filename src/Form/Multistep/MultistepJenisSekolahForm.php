<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\jenis_sekolah\Entity\JenisSekolah;

class MultistepJenisSekolahForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_jenis_sekolah';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

	$form['jenis_sekolah'] = array(
	  '#title' => t('Pilih Jenis Sekolah'),
      '#default_value' => $this->store->get('jenis_sekolah'),
	  '#type' => 'radios',
	  '#required' => TRUE,
	  '#description' => 'Pilih jenis sekolah yang akan anda tuju.',
	  '#options' => $this->getJenisSekolahOptions(),
	);

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Kembali ke pilihan desa'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('pendaftaran.multistep_pilih_desa'),
    );

    $form['actions']['submit']['#value'] = $this->t('Lanjut');

    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getJenisSekolahOptions($index = FALSE){
	$query = \Drupal::entityQuery('jenis_sekolah')
	->execute();
	$records = JenisSekolah::loadMultiple($query);
	$options = array();
	foreach($records as $record){
	  $options[$record->id()] = $record->label();
	}

    if($index !== FALSE){
      return $options[$index];
    }
	return $options;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state){
    $this->store->set('jenis_sekolah', $form_state->getValue('jenis_sekolah'));
    $this->store->set('nama_jenis_sekolah', $this->getJenisSekolahOptions($form_state->getValue('jenis_sekolah')));

	$elements = array('provinsi', 'nama_provinsi', 'kabupaten', 'nama_kabupaten', 'kecamatan', 'nama_kecamatan', 'jenis_sekolah', 'nama_jenis_sekolah');
	foreach ($elements as $key => $element) {
		$values[$element] = $this->store->get($element);		
	}
	dpm($values);
	
    $form_state->setRedirect('pendaftaran.multistep_zona_sekolah');
  }

}
