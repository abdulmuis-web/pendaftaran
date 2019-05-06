<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\penyelenggara\Entity\Penyelenggara;
use Drupal\tingkat\Entity\Tingkat;
use Drupal\juara\Entity\Juara;

class MultistepDataPrestasiForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_data_prestasi';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

	$form['penyelenggara'] = array(
	  '#title' => t('Penyelenggaran Kejuaraan / Perlombaan'),
	  '#type' => 'radios',
	  '#required' => TRUE,
	  '#description' => 'Pilih penyelenggara kejuaraan / perlombaan.',
	  '#default_value' => $this->store->get('penyelenggara'),
	  '#options' => $this->getPenyelenggaraOptions(),
	);

	$form['tingkat'] = array(
	  '#title' => t('Tingkat Kejuaraan / Perlombaan'),
	  '#type' => 'radios',
	  '#required' => TRUE,
	  '#description' => 'Pilih Tingkat kejuaraan / perlombaan yang diikuti.',
	  '#default_value' => $this->store->get('tingkat'),
	  '#options' => $this->getTingkatOptions(),
	);

	$form['juara'] = array(
	  '#title' => t('Juara'),
	  '#type' => 'radios',
	  '#required' => TRUE,
	  '#description' => 'Pilih juara kejuaraan / perlombaan yang diperoleh.',
	  '#default_value' => $this->store->get('juara'),
	  '#options' => $this->getJuaraOptions(),
	);

    $form['prestasi'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Perlombaan / kejuaraan'),
      '#default_value' => $this->store->get('prestasi') ? $this->store->get('prestasi') : '',
	  '#description' => 'Isi dengan nama perlomaan / kejuaraan yang diikuti.',
    );

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Kembali ke piihan jalur prestasi'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('pendaftaran.multistep_jalur_prestasi'),
    );

    $form['actions']['submit']['#value'] = $this->t('Next');

    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getPenyelenggaraOptions($index = FALSE){
	$query = \Drupal::entityQuery('penyelenggara')
	->execute();
	$records = Penyelenggara::loadMultiple($query);
	$options = array();
	$score = array();
	foreach($records as $record){
	  $options[$record->id()] = $record->label();
	  $score[$record->id()] = $record->score->value;
	}
    if($index !== FALSE){
	  return array('nama_penyelenggara' => $options[$index],'skor_penyelenggara' => $score[$index]); 
    }
	return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getTingkatOptions($index = FALSE){
	$query = \Drupal::entityQuery('tingkat')
	->execute();
	$records = Tingkat::loadMultiple($query);
	$options = array();
	$score = array();
	foreach($records as $record){
	  $options[$record->id()] = $record->label();
	  $score[$record->id()] = $record->score->value;
	}
    if($index !== FALSE){
	  return array('nama_tingkat' => $options[$index],'skor_tingkat' => $score[$index]); 
    }
	return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getJuaraOptions($index = FALSE){
	$query = \Drupal::entityQuery('juara')
	->execute();
	$records = Juara::loadMultiple($query);
	$options = array();
	$score = array();
	foreach($records as $record){
	  $options[$record->id()] = $record->label();
	  $score[$record->id()] = $record->score->value;
	}
    if($index !== FALSE){
	  return array('nama_juara' => $options[$index],'skor_juara' => $score[$index]); 
    }
	return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state){
    /* @var $entity \Drupal\wilayah_indonesia_province\Entity\Province */		
	parent::validateForm($form, $form_state);
	
	$values = $form_state->getValues();
	
	if($values['prestasi'] == ''){
	    $form_state->setErrorByName('prestasi',"Nama perlombaan / kejuaraan harus diisi jika mengikuti jalur prestasi");
	}
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state){

	$penyelenggara = Penyelenggara::load($form_state->getValue('penyelenggara'));
	$this->store->set('penyelenggara', $form_state->getValue('penyelenggara'));
    $this->store->set('nama_penyelenggara', $penyelenggara->label());
    $this->store->set('skor_penyelenggara', $penyelenggara->score->value);

	$tingkat = Tingkat::load($form_state->getValue('tingkat'));
	$this->store->set('tingkat', $form_state->getValue('tingkat'));
    $this->store->set('nama_tingkat', $tingkat->label());
    $this->store->set('skor_tingkat', $tingkat->score->value);
	
	$juara = Juara::load($form_state->getValue('juara'));
	$this->store->set('juara', $form_state->getValue('juara'));
    $this->store->set('nama_juara', $juara->label());
    $this->store->set('skor_juara', $juara->score->value);

    $this->store->set('prestasi', $form_state->getValue('prestasi'));
    $this->store->set('skor_prestasi', $penyelenggara->score->value + $tingkat->score->value + $juara->score->value);

	$elements = array('provinsi', 'nama_provinsi',
	                  'kabupaten', 'nama_kabupaten',
					  'kecamatan', 'nama_kecamatan',
					  'desa', 'nama_desa',
	                  'jenis_sekolah','nama_jenis_sekolah',
					  'zona_sekolah', 'nama_zona_sekolah',
					  'nama_jenis_sekolah',
					  'pilihan_sekolah', 'nama_pilihan_sekolah',
					  'desa_sekolah',
					  'kecamatan_sekolah',
					  'kabupaten_sekolah',
					  'provinsi_sekolah',
					  'zonasi', 'nama_zonasi', 'nilai_zonasi',
					  'prodi_sekolah', 'nama_prodi_sekolah',
					  'jalur_sktm', 'nama_jalur_sktm','skor_sktm',
					  'jalur_prestasi', 'nama_jalur_prestasi',
					  'penyelenggara','nama_penyelenggara','skor_penyelenggara',
					  'tingkat','nama_tingkat','skor_tingkat',
					  'juara','nama_juara','skor_juara',
					  'prestasi', 'skor_prestasi');

	foreach ($elements as $key => $element) {
		$values[$element] = $this->store->get($element);
	}
	dpm($values);

    $form_state->setRedirect('pendaftaran.multistep_selesai');
  }

}
