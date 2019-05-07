<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\pilihan_sekolah\Entity\PilihanSekolah;
use Drupal\zonasi\Entity\Zonasi;

class MultistepPilihanSekolahForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_pilihan_sekolah';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

	$form['pilihan_sekolah'] = array(
	  '#title' => t('Pilihan @jenis_sekolah di @zona_sekolah', 
	                array('@jenis_sekolah' => $this->store->get('nama_jenis_sekolah'), '@zona_sekolah'=>$this->store->get('nama_zona_sekolah'))),
      '#default_value' => $this->store->get('pilihan_sekolah'),
	  '#type' => 'radios',
	  '#required' => TRUE,
	  '#description' => 'Pilih pilihan sekolah yang akan anda tuju.',
	  '#options' => $this->getPilihanSekolahOptions(),
	);
	
    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Lihat pilihan sekolah di zona lainnya'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('pendaftaran.multistep_zona_sekolah'),
    );

    $form['actions']['submit']['#value'] = $this->t('Next');

    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getPilihanSekolahOptions($index = FALSE){
	$query = \Drupal::entityQuery('pilihan_sekolah')
	->condition('jenis_sekolah', $this->store->get('jenis_sekolah'), '=')
	->condition('zona', $this->store->get('zona_sekolah'), '=')
	->execute();
	$records = PilihanSekolah::loadMultiple($query);
	$options = array();
	foreach($records as $record){
	  $options[$record->id()] = $record->label();
	}
	//$options['9999'] = t('Lihat pilihan sekolah di zona lainnya.');

    if($index !== FALSE){
      return $options[$index];
    }
	return $options;
  }
  
  public function getZonasiMachineName($lokasi, $var){
	if($lokasi['desa_sekolah'] == $var->get('desa')){
	  $zonasi = 'satu_desa';
	  return $zonasi;
	}
    elseif($lokasi['kecamatan_sekolah'] == $var->get('kecamatan')){
	  $zonasi = 'satu_kecamatan';
	  return $zonasi;
	}
    elseif($lokasi['kabupaten_sekolah'] == $var->get('kabupaten')){
	  $zonasi = 'satu_kabupaten';
	  return $zonasi;
	}
    elseif($lokasi['provinsi_sekolah'] == $var->get('provinsi')){
	  $zonasi = 'satu_provinsi';
	  return $zonasi;
	}
	else{
	  return 'luar_provinsi';
	}
  }
  
  /**
   * {@inheritdoc}
   */
  public function getZonasi($machine_name){
	$query = \Drupal::entityQuery('zonasi')
	->condition('jenis_sekolah', $this->store->get('jenis_sekolah'), '=')
	->condition('machine_name', $machine_name, '=')
	->range('0', '1')
	->execute();
	
	$zonasi = Zonasi::load(reset($query));
	return $zonasi;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state){
    $this->store->set('pilihan_sekolah', $form_state->getValue('pilihan_sekolah'));
    $this->store->set('nama_pilihan_sekolah', $this->getPilihanSekolahOptions($form_state->getValue('pilihan_sekolah')));
	
	$sekolah = PilihanSekolah::load($this->store->get('pilihan_sekolah'));

    $lokasi['desa_sekolah'] = $sekolah->vilage->entity->id();
    $lokasi['kecamatan_sekolah'] = $sekolah->vilage->entity->district_id->entity->id->value;
    $lokasi['kabupaten_sekolah'] = $sekolah->vilage->entity->district_id->entity->regency_id->entity->id->value;
    $lokasi['provinsi_sekolah'] = $sekolah->vilage->entity->district_id->entity->regency_id->entity->province_id->entity->id->value;
	
    $this->store->set('desa_sekolah', $lokasi['desa_sekolah']);
    $this->store->set('kecamatan_sekolah', $lokasi['kecamatan_sekolah']);
    $this->store->set('kabupaten_sekolah', $lokasi['kabupaten_sekolah']);
    $this->store->set('provinsi_sekolah', $lokasi['provinsi_sekolah']);
	
    $machine_name = $this->getZonasiMachineName($lokasi, $this->store);
	$zonasi = $this->getZonasi($machine_name);
    $this->store->set('zonasi',$zonasi->id());
    $this->store->set('nama_zonasi',$zonasi->label());
    $this->store->set('nilai_zonasi',$zonasi->score->value);

	$elements = array('provinsi', 'nama_provinsi', 'kabupaten', 'nama_kabupaten', 'kecamatan', 'nama_kecamatan', 'desa', 'nama_desa',
	                  'jenis_sekolah','nama_jenis_sekolah','zona_sekolah', 'nama_zona_sekolah', 'nama_jenis_sekolah', 'pilihan_sekolah', 'nama_pilihan_sekolah', 'desa_sekolah', 'kecamatan_sekolah',
					  'kabupaten_sekolah','provinsi_sekolah', 'zonasi', 'nama_zonasi', 'nilai_zonasi');
	foreach ($elements as $key => $element) {
		$values[$element] = $this->store->get($element);
	}
	dpm($values);
    
	$redirect = 'pendaftaran.multistep_prodi_sekolah';
	if($this->store->get('jenis_sekolah') != '10'){
		$redirect = 'pendaftaran.multistep_jalur_sktm';
	}

    $form_state->setRedirect($redirect);
  }

}
