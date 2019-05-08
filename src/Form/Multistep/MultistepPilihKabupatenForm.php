<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\wilayah_indonesia_regency\Entity\Regency;

class MultistepPilihKabupatenForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_pilih_kabupaten';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

	$form['kabupaten'] = array(
	  '#title' => t('Pilihan Kabupaten / Kota di Provinsi @provinsi:', array('@provinsi' => $this->store->get('nama_provinsi'))),
	  //'#type' => 'select',
	  '#type' => 'radios',
	  '#required' => TRUE,
      '#default_value' => $this->store->get('kabupaten'),
	  '#description' => 'Pilih Kabupaten / Kota sesuai dengan kartu keluarga.',
	  '#options' => $this->getResidenceOptions(),
	);

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Kembali ke piihan provinsi'),
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
  public function getResidenceOptions($index = FALSE){
	$query = \Drupal::entityQuery('regency')
	->condition('province_id', $this->store->get('provinsi'), '=')
	->execute();
	$records = Regency::loadMultiple($query);
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
    $this->store->set('kabupaten', $form_state->getValue('kabupaten'));
    $this->store->set('nama_kabupaten', $this->getResidenceOptions($form_state->getValue('kabupaten')));
	
    $form_state->setRedirect('pendaftaran.multistep_pilih_kecamatan');
  }

}
