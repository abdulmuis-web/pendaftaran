<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\skor_akademik\Entity\SkorAkademik;

class MultistepDataPribadiForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_data_pribadi';
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
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);
    if($this->store->get('provinsi') != '36'){


	  $form['data_pribadi'] = array(
       '#type' => 'fieldset',
       '#title' => $this->t('Data akademik'),
      );
	  $form['data_pribadi']['nama_lengkap'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#title' => $this->t('Nama lengkap :'),
       '#default_value' => $this->store->get('nama_lengkap')?:$data->nama,
	   );
	  $form['data_pribadi']['nama_ayah'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#title' => $this->t('Nama ayah :'),
       '#default_value' => $this->store->get('nama_ayah')?:$data->nama_ayah,
	   );
	  $form['data_pribadi']['pekerjaan_ayah'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#title' => $this->t('Pekerjaan ayah :'),
       '#default_value' => $this->store->get('pekerjaan_ayah')?:$data->pekerjaan_ayah,
	   );
	  $form['data_pribadi']['tempat_lahir'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#title' => $this->t('Tempat lahir :'),
       '#default_value' => $this->store->get('tempat_lahir')?:$data->tempat_lahir,
	   );
	  $form['data_pribadi']['tgl_lahir'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#title' => $this->t('Tgl lahir :'),
       '#default_value' => $this->store->get('tgl_lahir')?:$data->tgl_lahir,
	   );
	
	  $form['data_akademik'] = array(
       '#type' => 'fieldset',
       '#title' => $this->t('Data akademik'),
      );
	  $form['data_akademik']['matematika'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#number_type' => 'decimal',
       '#size' => 6,
       '#maxlength' => 6,
       '#title' => $this->t('Matematika :'),
       '#default_value' => $this->store->get('matematika')?:$data->matematika,
	   );
	  $form['data_akademik']['ipa'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#number_type' => 'decimal',
       '#size' => 6,
       '#maxlength' => 6,
       '#title' => $this->t('IPA :'),
       '#default_value' => $this->store->get('ipa')?:$data->ipa,
	  );
	  $form['data_akademik']['ips'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#number_type' => 'decimal',
       '#size' => 6,
       '#maxlength' => 6,
       '#title' => $this->t('IPS :'),
       '#default_value' => $this->store->get('ips')?:$data->ips,
	  );
	  $form['data_akademik']['english'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#number_type' => 'decimal',
       '#size' => 6,
       '#maxlength' => 6,
       '#title' => $this->t('B. Inggris :'),
       '#default_value' => $this->store->get('english')?:$data->english,
	  );
	  $form['data_akademik']['indonesia'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#number_type' => 'decimal',
       '#size' => 6,
       '#maxlength' => 6,
       '#title' => $this->t('B. Indonesia :'),
       '#default_value' => $this->store->get('indonesia')?:$data->indonesia,
	  );
	}
	else{
	  $user = \Drupal::currentUser();
	  $data = $this->getDataAkademik($user->getUsername());
	  $skor_akademik = $this->getAllSkorAkademik();

	  $form['data_pribadi'] = array(
       '#type' => 'fieldset',
       '#title' => $this->t('Data akademik'),
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

	}

    $form['actions']['submit']['#value'] = $this->t('Lanjut');

    return $form;
  }
  public function number_field_widget_validate(FormStateInterface $form_state){
	  dpm($form_state->getValue('num_seats'));
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	
    $user = \Drupal::currentUser();
	  
    $this->store->set('nama_lengkap', $form_state->getValue('nama_lengkap'));
    $this->store->set('nama_ayah', $form_state->getValue('nama_ayah'));
    $this->store->set('pekerjaan_ayah', $form_state->getValue('pekerjaan_ayah'));
    $this->store->set('tempat_lahir', $form_state->getValue('tempat_lahir'));
    $this->store->set('tgl_lahir', $form_state->getValue('tgl_lahir'));
    $this->store->set('matematika', $form_state->getValue('matematika'));
    $this->store->set('ipa', $form_state->getValue('ipa'));
    $this->store->set('ips', $form_state->getValue('ips'));
    $this->store->set('english', $form_state->getValue('english'));
    $this->store->set('indonesia', $form_state->getValue('indonesia'));
	
/*	
	$data = array(
      'nisn' => $data->nisn,
      'uid' => $user->Id(),
      'nama_lengkap' => $data->nama,
      'nama_ayah' => $data->nama_ayah,
      'pekerjaan_ayah' => $data->pekerjaan_ayah,
      'tempat_lahir' => $data->tempat_lahir,
      'tgl_lahir' => $data->tgl_lahir,
      'matematika' => $data->matematika,
      'ipa' => $data->ipa,
      'ips' => $data->ips,
      'english' => $data->english,
      'indonesia' => $data->english,
      'skor_matematika' => $skor_akademik['Matematika'],
      'skor_ipa' => $skor_akademik['IPA'],
      'skor_ips' => $skor_akademik['IPA'],
      'skor_english' => $skor_akademik['IPS'],
      'skor_indonesia' => $skor_akademik['Indonesia'],	
	);
*/
	
    $elements = array('nama_lengkap','nama_ayah', 'pekerjaan_ayah','tempat_lahir', 'tgl_lahir', 'matematika','ipa','ips','english','indonesia');
	foreach ($elements as $key => $element) {
	  $values[$element] = $this->store->get($element);
	}
	$redirect = 'pendaftaran.multistep_jenis_sekolah';
	if($this->store->get('provinsi') == '36'){
	  $redirect = 'pendaftaran.multistep_pilih_kabupaten';
	}
	dpm($values);
    $form_state->setRedirect($redirect);
   
  }

}
