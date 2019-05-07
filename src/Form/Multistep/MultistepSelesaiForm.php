<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepTwoForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\pendaftaran\Entity\Pendaftaran;

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

    if($this->store->get('jalur_sktm') != '10'){
	  $form['sktm'] = array(
       '#type' => 'fieldset',
       '#title' => $this->t('Keluarga tidak mampu'),
      );
	  $form['sktm']['nama_jalur_sktm'] = array(
       '#type' => 'item',
       '#title' => $this->t('SKTM :'),
       '#description' => $this->store->get('nama_jalur_sktm'),
	  );
	  $form['sktm']['skor_sktm'] = array(
       '#type' => 'item',
       '#title' => $this->t('Nilai SMTM :'),
       '#description' => $this->store->get('skor_sktm'),
	  );
	}
    if($this->store->get('jalur_prestasi') != '10'){

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
	  $form['jalur_prestasi']['nilai'] = array(
       '#type' => 'item',
       '#title' => $this->t('Nilai :'),
       '#description' => $nilai += $this->store->get('skor_prestasi'),
	  );
	}

	$previous = 'pendaftaran.multistep_data_prestasi';
	if($this->store->get('jalur_prestasi') == '10'){
	  $previous = 'pendaftaran.multistep_jalur_prestasi';
	}
	
    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Periksa kemnali'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute($previous),
    );
    
	$form['#attributes']['class'] = ['pendaftaran'];
	
    return $form;
  }
  
  public function getDataAkademik($name){
	$query = \Drupal::database()->select( 'data_akademik','d')
	  ->fields('d')
	  ->range('0', '1')
	  ->condition('d.nisn', $name, '=');
	$ids = $query->execute()->fetchAll();
	
	return reset($ids);
  }

  public function getAllSkorAkademik(){
	$query = \Drupal::entityQuery('skor_akademik')
	  ->condition('status', '1', '=');
	$ids = $query->execute();
	
	$skor = [];
	
	foreach($ids as $id){
		$entity = SkorAkademik::load($id);
		$skor[$entity->name->value] = $entity->skor->value;
	}
	return $skor;
  }  
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
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
					  'jalur_sktm', 'nama_jalur_sktm', 'skor_sktm',
					  'jalur_prestasi', 'nama_jalur_prestasi',
					  'penyelenggara','nama_penyelenggara','skor_penyelenggara',
					  'tingkat','nama_tingkat','skor_tingkat',
					  'juara','nama_juara','skor_juara',
					  'prestasi');

	foreach ($elements as $key => $element) {
		$values[$element] = $this->store->get($element);
	}

	//$user = \Drupal::currentUser();
	
	//$data = $this->getDataAkademik($user->getUsername());
	//$data = (array) $data;
	//$skor_akademik = $this->getAllSkorAkademik();
	
	$entries = array(
	  'name' => $user->getUsername(),

      'provinsi' => $this->store->get('provinsi'),
      'nama_provinsi' => $this->store->get('nama_provinsi'),
      'kabupaten' => $this->store->get('kabupaten'),
      'nama_kabupaten' => $this->store->get('nama_kabupaten'),
      'kecamatan' => $this->store->get('kecamatan'),
      'nama_kecamatan' => $this->store->get('nama_kecamatan'),
      'desa' => $this->store->get('desa'),

      'nama_desa' => $this->store->get('nama_desa'),
      'jenis_sekolah' => $this->store->get('jenis_sekolah'),
      'nama_jenis_sekolah' => $this->store->get('nama_jenis_sekolah'),
      'zona_sekolah' => $this->store->get('zona_sekolah'),
      'nama_zona_sekolah' => $this->store->get('nama_zona_sekolah'),
      'pilihan_sekolah' => $this->store->get('pilihan_sekolah'),
      'nama_pilihan_sekolah' => $this->store->get('nama_pilihan_sekolah'),
      'desa_sekolah' => $this->store->get('desa_sekolah'),
      'kecamatan_sekolah' => $this->store->get('kecamatan_sekolah'),
      'kabupaten_sekolah' => $this->store->get('kabupaten_sekolah'),
      'provinsi_sekolah' => $this->store->get('provinsi_sekolah'),
      'zonasi' => $this->store->get('zonasi'),
      'nama_zonasi' => $this->store->get('nama_zonasi'),
      'nilai_zonasi' => $this->store->get('nilai_zonasi'),
      'prodi_sekolah' => $this->store->get('prodi_sekolah'),
      'nama_prodi_sekolah' => $this->store->get('nama_prodi_sekolah'),
      'jalur_sktm' => $this->store->get('jalur_sktm'),
      'nama_jalur_sktm' => $this->store->get('nama_jalur_sktm'),
	  'skor_sktm' => $this->store->get('skor_sktm'),
      'jalur_prestasi' => $this->store->get('jalur_prestasi'),
      'nama_jalur_prestasi' => $this->store->get('nama_jalur_prestasi'),
      'skor_prestasi' => $this->store->get('skor_prestasi'),
      'penyelenggara' => $this->store->get('penyelenggara'),
      'nama_penyelenggara' => $this->store->get('nama_penyelenggara'),
      'skor_penyelenggara' => $this->store->get('skor_penyelenggara'),
      'tingkat' => $this->store->get('tingkat'),
      'nama_tingkat' => $this->store->get('nama_tingkat'),
      'skor_tingkat' => $this->store->get('skor_tingkat'),
      'juara' => $this->store->get('juara'),
      'nama_juara' => $this->store->get('nama_juara'),
      'skor_juara' => $this->store->get('skor_juara'),
      'prestasi' => $this->store->get('prestasi'),
	);
	$pendaftaran = $this->createPendaftaran($entries);
    // Save the data
    parent::saveData();
    $form_state->setRedirect('pendaftaran.multistep_selesai');
  }

  /**
   * createBook.
   *
   * @return object
   *   Return pendaftaran_catalogue object.
   */
  public function createPendaftaran($entries){
        $database = \Drupal::database();
        $transaction = $database->startTransaction();
        try {
          $pendaftaran = Pendaftaran::create($entries);
          $pendaftaran->save();
		  $this->messenger()->addMessage($this->t('Selamat pendaftaran anda sudah disimpan.'));
        }
        catch (\Exception $e) {
          $transaction->rollback();
          $pendaftaran = NULL;
          watchdog_exception('pendaftaran', $e, $e->getMessage());
          throw new \Exception(  $e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    return $pendaftaran;
  }
  
}
