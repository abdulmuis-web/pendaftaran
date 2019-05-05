<?php

/**
 * @file
 * Contains \Drupal\pendaftaran\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\pendaftaran\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;

class MultistepDataPribadiForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_data_pribadi';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['nama_lengkap'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Nama lengkap'),
      '#default_value' => $this->store->get('nama_lengkap') ? $this->store->get('nama_lengkap') : '',
    );
    $form['nama_ayah'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Nama ayah'),
      '#default_value' => $this->store->get('nama_ayah') ? $this->store->get('nama_ayah') : '',
    );
    $form['pekerjaan_ayah'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Pekerjaan ayah'),
      '#default_value' => $this->store->get('pekerjaan_ayah') ? $this->store->get('pekerjaan_ayah') : '',
    );

    $form['actions']['submit']['#value'] = $this->t('Lanjut');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('nama_lengkap', $form_state->getValue('nama_lengkap'));
    $this->store->set('nama_ayah', $form_state->getValue('nama_ayah'));
    $this->store->set('pekerjaan_ayah', $form_state->getValue('pekerjaan_ayah'));
	
    $elements = array('nama_lengkap','nama_ayah','pekerjaan_ayah');
	foreach ($elements as $key => $element) {
		$values[$element] = $this->store->get($element);
	}
	dpm($values);
    $form_state->setRedirect('pendaftaran.multistep_pilih_provinsi');
  }

}
