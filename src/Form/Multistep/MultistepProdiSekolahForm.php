<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\prodi_sekolah\Entity\ProdiSekolah;

class MultistepProdiSekolahForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_prodi_sekolah';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

	$form['prodi_sekolah'] = array(
	  '#title' => t('Program studi di @sekolah', 
	                array('@sekolah' => $this->store->get('nama_pilihan_sekolah'))),
	  '#default_value' => $this->store->get('prodi_sekolah'),
	  '#type' => 'radios',
	  '#required' => TRUE,
	  '#description' => 'Pilih prodi sekolah yang akan anda tuju.',
	  '#options' => $this->getProdiSekolahOptions(),
	);

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Lihat pilihan sekolah lainnya'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('pendaftaran.multistep_pilihan_sekolah'),
    );

    $form['actions']['submit']['#value'] = $this->t('Next');

    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getProdiSekolahOptions($index = FALSE){
	$query = \Drupal::entityQuery('prodi_sekolah')
	//->condition('jenis_sekolah', $this->store->get('pilihan_sekolah'), '=')
	->condition('pilihan_sekolah_id', $this->store->get('pilihan_sekolah'), '=')
	->execute();
	$records = ProdiSekolah::loadMultiple($query);
	$options = array();
	foreach($records as $record){
	  $options[$record->id()] = $record->kompetensi_keahlian_id->entity->label();
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
    $this->store->set('prodi_sekolah', $form_state->getValue('prodi_sekolah'));
    $this->store->set('nama_prodi_sekolah', $this->getProdiSekolahOptions($form_state->getValue('prodi_sekolah')));
    
	$redirect = 'pendaftaran.multistep_jalur_sktm';	
	if($this->store->get('provinsi') != '36'){
		$redirect = 'pendaftaran.multistep_selesai';
	}
    $form_state->setRedirect($redirect);
  }

}
