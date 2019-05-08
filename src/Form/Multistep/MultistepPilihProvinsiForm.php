<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepTwoForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\wilayah_indonesia_province\Entity\Province;

class MultistepPilihProvinsiForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_pilih_provinsi';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);
	
	$form['provinsi'] = array(
	  '#title' => t('Pilih Provinsi'),
	  //'#type' => 'select',
	  '#type' => 'radios',
	  '#required' => TRUE,
	  '#description' => 'Pilih Provinsi sesuai dengan kartu keluarga.',
      '#default_value' => $this->store->get('provinsi') ? $this->store->get('provinsi') : '36',
	  '#options' => $this->getProvinceOptions(),
	);

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Sebelumnya'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('pendaftaran.multistep_data_pribadi'),
    );

    $form['actions']['submit']['#value'] = $this->t('Lanjut');

    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getProvinceOptions($index = FALSE){
	$query = \Drupal::entityQuery('province')
	->execute();
	$records = Province::loadMultiple($query);
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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('provinsi', $form_state->getValue('provinsi'));
    $this->store->set('nama_provinsi', $this->getProvinceOptions($form_state->getValue('provinsi')));

    $this->store->set('desa', FALSE);
    $this->store->set('kecamatan', FALSE);
    $this->store->set('kabupaten', FALSE);
	if($this->store->get('provinsi') != '36'){
      $this->store->set('sktm', '10');
      $this->store->set('jalur_prestasi', '10');
	}
    $form_state->setRedirect('pendaftaran.multistep_data_pribadi');
  }

}
