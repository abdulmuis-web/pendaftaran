<?php

namespace Drupal\pendaftaran\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class GenerateDataAkademikForm.
 */
class GenerateDataAkademikForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'generate_data_akademik_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['jumlah_data'] = [
      '#type' => 'number',
      '#title' => $this->t('Jumlah data'),
      '#description' => $this->t('umlah data y diubuat'),
      '#default_value' => 50,
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }

  }

}
