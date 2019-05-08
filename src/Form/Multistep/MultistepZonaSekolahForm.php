<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\zona\Entity\Zona;

class MultistepZonaSekolahForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_zona_sekolah';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

	$form['zona_sekolah'] = array(
	  '#title' => t('Pilih Zona Sekolah'),
	  '#default_value' => $this->store->get('zona_sekolah'),
	  '#type' => 'radios',
	  '#required' => TRUE,
	  '#description' => 'Pilih zona sekolah yang akan anda tuju.',
	  '#options' => $this->getZonaSekolahOptions(),
	);

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Kembali ke jenis sekolah'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('pendaftaran.multistep_jenis_sekolah'),
    );

    $form['actions']['submit']['#value'] = $this->t('Next');

    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getZonaSekolahOptions($index = FALSE){
	$query = \Drupal::entityQuery('zona')
	->execute();
	$records = Zona::loadMultiple($query);
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
    $this->store->set('zona_sekolah', $form_state->getValue('zona_sekolah'));
    $this->store->set('nama_zona_sekolah', $this->getZonaSekolahOptions($form_state->getValue('zona_sekolah')));

    $form_state->setRedirect('pendaftaran.multistep_pilihan_sekolah');
  }

}
