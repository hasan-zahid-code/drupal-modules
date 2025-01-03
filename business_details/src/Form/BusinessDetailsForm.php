<?php

namespace Drupal\business_details\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class BusinessDetailsForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return ['business_details.settings'];
  }

  public function getFormId() {
    return 'business_details_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('business_details.settings');

    $form['business_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Business Name'),
      '#default_value' => $config->get('business_name'),
      '#required' => TRUE,
    ];

    $form['phone_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone Number'),
      '#default_value' => $config->get('phone_number'),
      '#required' => TRUE,
    ];

    $form['address'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Address'),
      '#default_value' => $config->get('address'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('business_details.settings')
      ->set('business_name', $form_state->getValue('business_name'))
      ->set('phone_number', $form_state->getValue('phone_number'))
      ->set('address', $form_state->getValue('address'))
      ->save();

    parent::submitForm($form, $form_state);
    \Drupal::messenger()->addMessage($this->t('Business details saved.'));
  }
}
