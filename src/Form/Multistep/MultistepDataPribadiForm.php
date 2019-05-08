<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\skor_akademik\Entity\SkorAkademik;
use Drupal\Core\Url;

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
       '#title' => $this->t('Data pribadi'),
      );
	  $form['data_pribadi']['nama_lengkap'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#title' => $this->t('Nama lengkap :'),
       '#default_value' => $this->store->get('nama_lengkap'),
	   );
	  $form['data_pribadi']['nama_ayah'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#title' => $this->t('Nama ayah :'),
       '#default_value' => $this->store->get('nama_ayah'),
	   );
	  $form['data_pribadi']['pekerjaan_ayah'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#title' => $this->t('Pekerjaan ayah :'),
       '#default_value' => $this->store->get('pekerjaan_ayah'),
	   );
	  $form['data_pribadi']['tempat_lahir'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#title' => $this->t('Tempat lahir :'),
       '#default_value' => $this->store->get('tempat_lahir'),
	   );
	  $form['data_pribadi']['tgl_lahir'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#title' => $this->t('Tgl lahir :'),
       '#default_value' => $this->store->get('tgl_lahir'),
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
       '#default_value' => $this->store->get('matematika'),
	   );
	  $form['data_akademik']['ipa'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#number_type' => 'decimal',
       '#size' => 6,
       '#maxlength' => 6,
       '#title' => $this->t('IPA :'),
       '#default_value' => $this->store->get('ipa'),
	  );
	  $form['data_akademik']['ips'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#number_type' => 'decimal',
       '#size' => 6,
       '#maxlength' => 6,
       '#title' => $this->t('IPS :'),
       '#default_value' => $this->store->get('ips'),
	  );
	  $form['data_akademik']['english'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#number_type' => 'decimal',
       '#size' => 6,
       '#maxlength' => 6,
       '#title' => $this->t('B. Inggris :'),
       '#default_value' => $this->store->get('english'),
	  );
	  $form['data_akademik']['indonesia'] = array(
       '#type' => 'textfield',
       '#required' => TRUE,
       '#number_type' => 'decimal',
       '#size' => 6,
       '#maxlength' => 6,
       '#title' => $this->t('B. Indonesia :'),
       '#default_value' => $this->store->get('indonesia'),
	  );
	}
	else{
	  $user = \Drupal::currentUser();
	  $data = $this->getDataAkademik($user->getUsername());
	  $skor_akademik = $this->getAllSkorAkademik();

	  $form['data_pribadi'] = array(
       '#type' => 'fieldset',
       '#title' => $this->t('Data pribadi'),
      );
	  $form['data_pribadi']['nama_lengkap'] = array(
       '#type' => 'item',
       '#title' => $this->t('Nama lengkap :'),
       '#description' => $this->store->get('nama_lengkap'),
	   );
	  $form['data_pribadi']['nama_ayah'] = array(
       '#type' => 'item',
       '#title' => $this->t('Nama ayah :'),
       '#description' => $this->store->get('nama_ayah'),
	   );
	  $form['data_pribadi']['pekerjaan_ayah'] = array(
       '#type' => 'item',
       '#title' => $this->t('Pekerjaan ayah :'),
       '#description' => $this->store->get('pekerjaan_ayah'),
	   );
	  $form['data_pribadi']['tempat_lahir'] = array(
       '#type' => 'item',
       '#title' => $this->t('Tempat lahir :'),
       '#description' => $this->store->get('tempat_lahir'),
	   );
	  $form['data_pribadi']['tgl_lahir'] = array(
       '#type' => 'item',
       '#title' => $this->t('Tgl lahir :'),
       '#description' => $this->store->get('tgl_lahir'),
	   );
	
	  $form['data_akademik'] = array(
       '#type' => 'fieldset',
       '#title' => $this->t('Data akademik'),
      );
	  $form['data_akademik']['matematika'] = array(
       '#type' => 'item',
       '#title' => $this->t('Matematika :'),
       '#description' => $this->store->get('matematika'),
	   );
	  $form['data_akademik']['ipa'] = array(
       '#type' => 'item',
       '#title' => $this->t('IPA :'),
       '#description' => $this->store->get('ipa'),
	  );
	  $form['data_akademik']['ips'] = array(
       '#type' => 'item',
       '#title' => $this->t('IPS :'),
       '#description' => $this->store->get('ips'),
	  );
	  $form['data_akademik']['english'] = array(
       '#type' => 'item',
       '#title' => $this->t('B. Inggris :'),
       '#description' => $this->store->get('english'),
	  );
	  $form['data_akademik']['indonesia'] = array(
       '#type' => 'item',
       '#title' => $this->t('B. Indonesia :'),
       '#description' => $this->store->get('indonesia'),
	  );

	}

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Kembali'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('pendaftaran.multistep_pilih_provinsi'),
    );
	
    $form['actions']['submit']['#value'] = $this->t('Lanjut');

    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	
   	$user = \Drupal::currentUser();
	$data = $this->getDataAkademik($user->getUsername());
	$skor_akademik = $this->getAllSkorAkademik();
	  
    if($this->store->get('provinsi') == '36'){
	  $elements = array(
        'nisn' => $data->nisn,
        'uid' => $user->Id(),
        'nama_lengkap' => $data->nama_lengkap,
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
	}else{
	  $elements = array(
        'nisn' => $user->getUsername(),
        'uid' => $user->Id(),
        'nama_lengkap' => $form_state->getValue('nama_lengkap'),
        'nama_ayah' => $form_state->getValue('nama_ayah'),
        'pekerjaan_ayah' => $form_state->getValue('pekerjaan_ayah'),
        'tempat_lahir' => $form_state->getValue('tempat_lahir'),
        'tgl_lahir' => $form_state->getValue('tgl_lahir'),
        'matematika' => $form_state->getValue('matematika'),
        'ipa' => $form_state->getValue('ipa'),
        'ips' => $form_state->getValue('ips'),
        'english' => $form_state->getValue('english'),
        'indonesia' => $form_state->getValue('indonesia'),
        'skor_matematika' => $skor_akademik['Matematika'],
        'skor_ipa' => $skor_akademik['IPA'],
        'skor_ips' => $skor_akademik['IPA'],
        'skor_english' => $skor_akademik['IPS'],
        'skor_indonesia' => $skor_akademik['Indonesia'],
	  );
	}

	$elements['skor_akademik'] = $this->getSkorAkademik($elements);
	foreach ($elements as $key => $element) {
	  $this->store->set($key, $element);
	}
    $elements = ['matematika', 'ipa', 'ips', 'english', 'indonesia', 'skor_matematika', 'skor_ipa', 'skor_ips', 'skor_english', 'skor_indonesia', 'skor_akademik'];
	foreach ($elements as $key => $element) {
	  $values[$element] = $this->store->get($element);
	}
    dpm($values);	
	$redirect = 'pendaftaran.multistep_jenis_sekolah';
	if($this->store->get('provinsi') == '36'){
	  $redirect = 'pendaftaran.multistep_pilih_kabupaten';
	}
    $redirect = 'pendaftaran.multistep_data_pribadi';
    $form_state->setRedirect($redirect);
  }
  public function getSkorAkademik($elements){
    $skor_akademik = '0';
	$skor_akademik = $elements['matematika'] * $elements['skor_matematika'];
    $skor_akademik += $elements['ipa'] * $elements['skor_ipa'];
    $skor_akademik += $elements['ips'] * $elements['skor_ips'];
    $skor_akademik += $elements['english'] * $elements['skor_english'];
    $skor_akademik += $elements['indonesia'] * $elements['skor_indonesia'];
	return $skor_akademik;
  }
}
