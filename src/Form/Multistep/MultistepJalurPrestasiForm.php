<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\jalur_prestasi\Entity\JalurPrestasi;

class MultistepJalurPrestasiForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_jalur_prestasi';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

	if($this->store->get('provinsi') == '36'){
	  $form['jalur_prestasi'] = array(
	    '#title' => t('Apakah Anda akan mengikuti jalur prestasi ?'),
	    '#type' => 'radios',
	    '#required' => TRUE,
	    '#description' => 'Keterangan prestasi akan diverifikasi oleh admin sekolah.',
	    '#default_value' => $this->store->get('jalur_prestasi'),
	    '#options' => $this->getJalurPrestasiOptions(),
	  );

      $form['actions']['previous'] = array(
        '#type' => 'link',
        '#title' => $this->t('Kembali ke piihan jalur keluarga tidak mampu'),
        '#attributes' => array(
          'class' => array('button'),
        ),
        '#weight' => 0,
        '#url' => Url::fromRoute('pendaftaran.multistep_jalur_sktm'),
      );
      $form['actions']['submit']['#value'] = $this->t('Next');
	}else{
	  $form['info'] = array(
       '#type' => 'item',
       '#title' => $this->t('Anda tidak dapat mengakses halaman ini.'),
       '#description' => $this->store->get('Jalur prestasi hanya untuk siswa yang berdomisili dalam peovinsi.'),
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
	
	}
	
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getJalurPrestasiOptions($index = FALSE){
	$query = \Drupal::entityQuery('jalur_prestasi')
	->execute();
	$records = JalurPrestasi::loadMultiple($query);
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
    $this->store->set('jalur_prestasi', $form_state->getValue('jalur_prestasi'));
    
	$this->store->set('nama_jalur_prestasi', $this->getJalurPrestasiOptions($form_state->getValue('jalur_prestasi')));

	$redirect = 'pendaftaran.multistep_data_prestasi';
	if($this->store->get('jalur_prestasi') == '10'){
		$redirect = 'pendaftaran.multistep_selesai';
	}

    $form_state->setRedirect($redirect);
  }

}
