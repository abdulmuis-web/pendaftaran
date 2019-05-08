<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\wilayah_indonesia_district\Entity\District;

class MultistepPilihKecamatanForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_pilih_kecamatan';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

	$form['kecamatan'] = array(
	  '#title' => t('Pilihan Kecamatan di @kabupaten:', array('@kabupaten' => $this->store->get('nama_kabupaten'))),
	  //'#type' => 'select',
	  '#type' => 'radios',
	  '#required' => TRUE,
      '#default_value' => $this->store->get('kecamatan'),
	  '#description' => 'Pilih nama kecamatan sesuai dengan kartu keluarga.',
	  '#options' => $this->getDistrictOptions(),
	);

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Kembali ke pilihan kabupaten'),
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
  public function getDistrictOptions($index = FALSE){
	$query = \Drupal::entityQuery('district')
	->condition('regency_id', $this->store->get('kabupaten'), '=' )
	->execute();
	$records = District::loadMultiple($query);
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
    $this->store->set('kecamatan', $form_state->getValue('kecamatan'));
    $this->store->set('nama_kecamatan', $this->getDistrictOptions($form_state->getValue('kecamatan')));
	
    $form_state->setRedirect('pendaftaran.multistep_pilih_desa');
  }

}
