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

	  $form['data_pribadi'] = array(
       '#type' => 'fieldset',
       '#title' => $this->t('Data pribadi'),
      );
	  $form['data_pribadi']['nama_lengkap'] = array(
       '#type' => 'item',
       '#title' => $this->t('Nama lengkap :'),
       '#description' => $this->store->get('nama_lengkap')?:$data->nama,
	   );
	  $form['data_pribadi']['nama_ayah'] = array(
       '#type' => 'item',
       '#title' => $this->t('Nama ayah :'),
       '#description' => $this->store->get('nama_ayah')?:$data->nama_ayah,
	   );
	  $form['data_pribadi']['pekerjaan_ayah'] = array(
       '#type' => 'item',
       '#title' => $this->t('Pekerjaan ayah :'),
       '#description' => $this->store->get('pekerjaan_ayah')?:$data->pekerjaan_ayah,
	   );
	  $form['data_pribadi']['tempat_lahir'] = array(
       '#type' => 'item',
       '#title' => $this->t('Tempat lahir :'),
       '#description' => $this->store->get('tempat_lahir')?:$data->tempat_lahir,
	   );
	  $form['data_pribadi']['tgl_lahir'] = array(
       '#type' => 'item',
       '#title' => $this->t('Tgl lahir :'),
       '#description' => $this->store->get('tgl_lahir')?:$data->tgl_lahir,
	   );
	
	  $form['data_akademik'] = array(
       '#type' => 'fieldset',
       '#title' => $this->t('Data akademik'),
      );
	  $form['data_akademik']['matematika'] = array(
       '#type' => 'item',
       '#title' => $this->t('Matematika :'),
       '#description' => $this->store->get('matematika')?:$data->matematika,
	   );
	  $form['data_akademik']['ipa'] = array(
       '#type' => 'item',
       '#title' => $this->t('IPA :'),
       '#description' => $this->store->get('ipa')?:$data->ipa,
	  );
	  $form['data_akademik']['ips'] = array(
       '#type' => 'item',
       '#title' => $this->t('IPS :'),
       '#description' => $this->store->get('ips')?:$data->ips,
	  );
	  $form['data_akademik']['english'] = array(
       '#type' => 'item',
       '#title' => $this->t('B. Inggris :'),
       '#description' => $this->store->get('english')?:$data->english,
	  );
	  $form['data_akademik']['indonesia'] = array(
       '#type' => 'item',
       '#title' => $this->t('B. Indonesia :'),
       '#description' => $this->store->get('indonesia')?:$data->indonesia,
	  );

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
	$form['zonasi']['skor_zonasi'] = array(
     '#type' => 'item',
     '#title' => $this->t('Skor zonasi :'),
     '#description' => $this->store->get('skor_zonasi'),
	 );

	if($this->store->get('provinsi') == '36'){
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
	}
	

	$previous = 'pendaftaran.multistep_data_prestasi';
	if($this->store->get('jalur_prestasi') == '10'){
	  $previous = 'pendaftaran.multistep_jalur_prestasi';
	}
	
	if($this->store->get('provinsi') != '36'){
		$previous = 'pendaftaran.multistep_prodi_sekolah';
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
	$user = \Drupal::currentUser();
	$entries =[];
	$entries = array(
	  'name' => $user->getUsername(),
      'nama_lengkap'=> $this->store->get('nama_lengkap'),
	  'nama_ayah'=> $this->store->get('nama_ayah'),
	  'pekerjaan_ayah'=> $this->store->get('pekerjaan_ayah'),
	  'tempat_lahir' => $this->store->get('tempat_lahir'),
	  'tgl_lahir' => $this->store->get('tgl_lahir'),
	  'matematika' => $this->store->get('matematika'),
	  'ipa' => $this->store->get('ipa'),
	  'ips' => $this->store->get('ips'),
	  'english' => $this->store->get('english'),
	  'indonesia' => $this->store->get('indonesia'),
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
      'skor_zonasi' => $this->store->get('skor_zonasi'),
      'prodi_sekolah' => $this->store->get('prodi_sekolah'),
      'nama_prodi_sekolah' => $this->store->get('nama_prodi_sekolah'),
	);
	$jalur_sktm = array(
      'jalur_sktm' => FALSE,
      'nama_jalur_sktm' => FALSE,
	  'skor_sktm' => FALSE,
	);  
    if($this->store->get('jalur_sktm') != '10' && $this->store->get('provinsi') == '36'){
	  $jalur_sktm = array(
        'jalur_sktm' => $this->store->get('jalur_sktm'),
        'nama_jalur_sktm' => $this->store->get('nama_jalur_sktm'),
	    'skor_sktm' => $this->store->get('skor_sktm'),
	  );
	}
    $entries = array_merge($entries, $jalur_sktm);

    $jalur_prestasi = array(
      'jalur_prestasi' => FALSE,
      'nama_jalur_prestasi' => FALSE,
      'skor_prestasi' => FALSE,
      'penyelenggara' => FALSE,
      'nama_penyelenggara' => FALSE,
      'skor_penyelenggara' => FALSE,
      'tingkat' => FALSE,
      'nama_tingkat' => FALSE,
      'skor_tingkat' => FALSE,
      'juara' => FALSE,
      'nama_juara' => FALSE,
      'skor_juara' => FALSE,
      'prestasi' => FALSE,
    );
    if($this->store->get('jalur_prestasi') != '10' && $this->store->get('provinsi') == '36'){
	  $jalur_prestasi = array(
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
	}
    $entries = array_merge($entries, $jalur_prestasi);
	
	$id = $this->getPendaftaran($entries['name']);
	if($id){
		$pendaftaran = $this->updatePendaftaran($entries, $id);
	}
	else{
		$pendaftaran = $this->createPendaftaran($entries);
	}
    // Save the data
    parent::saveData();
    $form_state->setRedirect('pendaftaran.multistep_selesai');
  }

  public function getPendaftaran($name){
	$query = \Drupal::entityQuery('pendaftaran')
	  ->condition('name', $name, '=')
	  ->range('0', '1')
	  ->condition('status', '1', '=');
	$id = $query->execute();

	return $id;
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
  /**
   * createBook.
   *
   * @return object
   *   Return pendaftaran_catalogue object.
   */
  public function updatePendaftaran($entries, $id){
	    $pendaftaran = Pendaftaran::load(reset($id));
        
		foreach($entries as $key=>$value){
			$pendaftaran->set($key, $value);
		}
		
		$database = \Drupal::database();
        $transaction = $database->startTransaction();
        try {
		  $pendaftaran->setNewRevision(TRUE); // enabling revision for the entity save.
	      $pendaftaran->setRevisionCreationTime(REQUEST_TIME);
		  //$pendaftaran->setRevisionLogMessage('Our custom message for entity save.'); // Setting the log message for the revision
          $pendaftaran->save();
		  $this->messenger()->addMessage($this->t('Selamat pendaftaran anda sudah diupdate.'));
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
