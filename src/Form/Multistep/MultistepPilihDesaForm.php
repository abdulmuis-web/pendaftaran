<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\wilayah_indonesia_vilage\Entity\Vilage;

class MultistepPilihDesaForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_pilih_desa';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

	$form['desa'] = array(
	  '#title' => t('Pilihan Desa di @kecamatan:', array('@kecamatan' => $this->store->get('nama_kecamatan'))),
      '#default_value' => $this->store->get('desa'),
	  '#type' => 'radios',
	  '#required' => TRUE,
	  '#description' => 'Pilih nama desa sesuai dengan kartu keluarga.',
	  '#options' => $this->getVilageOptions(),
	);
    $form['alamat'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Alamat'),
      '#default_value' => $this->store->get('alamat'),
	  '#description' => 'Nama jalan nomor rumah RT / RW.',
    );
	
    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Kembali ke pi;ihan kecamatan'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('pendaftaran.multistep_pilih_kabupaten'),
    );

    $form['actions']['submit']['#value'] = $this->t('Lanjut');

    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getVilageOptions($index = FALSE){
	$query = \Drupal::entityQuery('vilage')
	->condition('district_id', $this->store->get('kecamatan'), '=' )
	->execute();
	$records = Vilage::loadMultiple($query);
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
    $this->store->set('desa', $form_state->getValue('desa'));
    $this->store->set('nama_desa', $this->getVilageOptions($form_state->getValue('desa')));
    $this->store->set('alamat', $form_state->getValue('alamat'));

	$elements = array('provinsi', 'nama_provinsi', 'kabupaten', 'nama_kabupaten', 'kecamatan', 'nama_kecamatan', 'desa', 'nama_desa', 'alamat');
	foreach ($elements as $key => $element) {
		$values[$element] = $this->store->get($element);		
	}
	dpm($values);
	
    $form_state->setRedirect('pendaftaran.multistep_jenis_sekolah');
  }

}
