<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepTwoForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class MultistepSelesaiForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_selesai';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['domisili'] = array(
     '#type' => 'fieldset',
     '#title' => $this->t('Domisili'),
    );
	$form['domisili']['nama_provinsi'] = array(
     '#type' => 'item',
     '#title' => $this->t('Provinsi :'),
     '#description' => $this->store->get('nama_provinsi'),
	 );
	$form['domisili']['nama_kabupaten'] = array(
     '#type' => 'item',
     '#title' => $this->t('Kabupaten :'),
     '#description' => $this->store->get('nama_kabupaten'),
	 );
	$form['domisili']['nama_kecamatan'] = array(
     '#type' => 'item',
     '#title' => $this->t('Kecamatan :'),
     '#description' => $this->store->get('nama_kecamatan'),
	 );
	$form['domisili']['nama_desa'] = array(
     '#type' => 'item',
     '#title' => $this->t('Desa :'),
     '#description' => $this->store->get('nama_desa'),
	 );
	 
	$form['sekolah'] = array(
     '#type' => 'fieldset',
     '#title' => $this->t('Sekolah'),
    );
	$form['sekolah']['nama_jenis_sekolah'] = array(
     '#type' => 'item',
     '#title' => $this->t('Jenis sekolah :'),
     '#description' => $this->store->get('nama_jenis_sekolah'),
	 );
	$form['sekolah']['nama_pilihan_sekolah'] = array(
     '#type' => 'item',
     '#title' => $this->t('Pilihan sekolah :'),
     '#description' => $this->store->get('nama_pilihan_sekolah'),
	 );
	$form['sekolah']['nama_prodi_sekolah'] = array(
     '#type' => 'item',
     '#title' => $this->t('Program studi :'),
     '#description' => $this->store->get('nama_prodi_sekolah'),
	 );

	$form['zonasi'] = array(
     '#type' => 'fieldset',
     '#title' => $this->t('Zonasi'),
    );
	$form['zonasi']['nama_zonasi'] = array(
     '#type' => 'item',
     '#title' => $this->t('Naa zonasi :'),
     '#description' => $this->store->get('nama_zonasi'),
	 );
	$form['zonasi']['nilai_zonasi'] = array(
     '#type' => 'item',
     '#title' => $this->t('Nilai zonasi :'),
     '#description' => $this->store->get('nilai_zonasi'),
	 );

	$form['sktm'] = array(
     '#type' => 'fieldset',
     '#title' => $this->t('Keluarga tidak mampu'),
    );
	$form['sktm']['nama_jalur_sktm'] = array(
     '#type' => 'item',
     '#title' => $this->t('SKTM :'),
     '#description' => $this->store->get('nama_jalur_sktm'),
	 );
	$form['sktm']['nilai_jalur_sktm'] = array(
     '#type' => 'item',
     '#title' => $this->t('Nilai SMTM :'),
     '#description' => $this->store->get('nilai_jalur_sktm'),
	 );

	$form['jalur_prestasi'] = array(
     '#type' => 'fieldset',
     '#title' => $this->t('Jalur prestasi'),
    );
	$form['jalur_prestasi']['nama_jalur_prestasi'] = array(
     '#type' => 'item',
     '#title' => $this->t('Jalur Prestasi :'),
     '#description' => $this->store->get('nama_jalur_prestasi'),
	 );
	$form['jalur_prestasi']['nama_penyelenggara'] = array(
     '#type' => 'item',
     '#title' => $this->t('Penyelenggara :'),
     '#description' => $this->store->get('nama_penyelenggara'),
	 );
	$form['jalur_prestasi']['nama_tingkat'] = array(
     '#type' => 'item',
     '#title' => $this->t('Tingkat :'),
     '#description' => $this->store->get('nama_tingkat'),
	 );
	$form['jalur_prestasi']['nama_juara'] = array(
     '#type' => 'item',
     '#title' => $this->t('Juara :'),
     '#description' => $this->store->get('nama_juara'),
	 );
	$form['jalur_prestasi']['prestasi'] = array(
     '#type' => 'item',
     '#title' => $this->t('Lomba :'),
     '#description' => $this->store->get('prestasi'),
	 );
    $nilai = $this->store->get('skor_penyelenggara');
	$nilai += $this->store->get('skor_tingkat');
	$nilai += $this->store->get('skor_juara');
	$this->store->get('skor_penyelenggara');
	$form['jalur_prestasi']['nilai'] = array(
     '#type' => 'item',
     '#title' => $this->t('Nilai :'),
     '#description' => $nilai ,
	 );
	 
    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('pendaftaran.multistep_data_prestasi'),
    );

    return $form;
  }
  
  public function getDataAkademik($name){
	$query = \Drupal::database()->select( 'data_akademik','d')
	  ->fields('d')
	  ->range('0', '1')
	  ->condition('d.nisn', $nama, '=');
	$ids = $query->execute()->fetchAll();
	
	return reset($ids);
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('jalur_prestasi', $form_state->getValue('jalur_prestasi'));
	
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
					  'jalur_sktm', 'nama_jalur_sktm',
					  'jalur_prestasi', 'nama_jalur_prestasi',
					  'penyelenggara','nama_penyelenggara','skor_penyelenggara',
					  'tingkat','nama_tingkat','skor_tingkat',
					  'juara','nama_juara','skor_juara',
					  'prestasi');

	foreach ($elements as $key => $element) {
		$values[$element] = $this->store->get($element);
	}
	
	dpm($values);
	$name = \Drupal::currentUser()->getUsername();
	dpm($name);
	
	$data = $this->getDataAkademik($name);
	dpm($data);
	
	
    // Save the data
    parent::saveData();
    $form_state->setRedirect('pendaftaran.multistep_selesai');
  }
}
