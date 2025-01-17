<?php

namespace Drupal\etss2_payment_icons\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\media\Entity\Media;


/**
 * Provides a 'Payment Icons Block'.
 *
 * @Block(
 *   id = "etss2_payment_icons",
 *   admin_label = @Translation("ETSS2 Payment Icons Block"),
 *   category = @Translation("Custom")
 * )
 */
class PaymentIconsBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   *
   * Builds the block content for rendering.
   */

  public function build()
  {
    $config = $this->getConfiguration();
    $icons = $config['icons'] ?? [];

    $rendered_icons = array_map(function ($icon) {
      $file_url = '';
      if (isset($icon['media_id']) && $media = Media::load($icon['media_id'])) {
        $file_url = $media->get('thumbnail')->entity->createFileUrl();
      }
      return [
        'merchant' => $icon['merchant'],
        'url' => $file_url,
      ];
    }, $icons);

    return [
      'content' => [
        '#theme' => 'etss2_payment_icons',
        '#icons' => $rendered_icons,
        '#cache' => [
        'max-age' => 0,
      ],
      ],
    ];

  }

  /**
   * {@inheritdoc}
   *
   * Adds a configuration form for the block.
   */
  public function blockForm($form, FormStateInterface $form_state)
  {
    // Fetch the current icons from the form state or configuration.
    if (!$form_state->has('icons')) {
      $form_state->set('icons', $this->getConfiguration()['icons'] ?? []);
    }
    $icons = $form_state->get('icons');

    $form['icons'] = [
      '#type' => 'fieldset',
      '#title' => t('Payment Gateway Icons'),
      '#tree' => TRUE,
      '#description' => t('Add or edit payment gateway icons uploaded as files. Browse existing icons from the media library or upload a new one.'),
    ];

    // Loop over the predefined icons to create the form elements
    foreach ($icons as $index => $icon) {
      $form['icons'][$index] = [
        '#type' => 'details',
        '#title' => $icon['merchant'] == '' ? $this->t('New Payment Icon') : $icon['merchant'],
        '#open' => FALSE,
      ];

      // Icon Name Field (Merchant Name)
      $form['icons'][$index]['merchant'] = [
        '#type' => 'textfield',
        '#title' => t('Merchant Name'),
        '#default_value' => $icon['merchant'],
        '#description' => t('Enter the name of the payment gateway (e.g., Visa, MasterCard, AMEX). Only alphanumeric characters and spaces are allowed.'),
        '#required' => TRUE,
        '#maxlength' => 50,
      ];

      // Media Library Field (Icon Selection)
      $form['icons'][$index]['media_id'] = [
        '#type' => 'media_library',
        '#title' => t('Select Icon from Media Library'),
        '#allowed_bundles' => ['icon'],
        '#description' => t('Select or upload an icon from the media library.'),
        '#default_value' => $icon['media_id'] ?? NULL,
        '#required' => TRUE,
      ];

      // Remove Icon Button
      $form['icons'][$index]['remove'] = [
        '#type' => 'submit',
        '#value' => t('Remove'),
        '#name' => "remove_$index",
        '#submit' => [[$this, 'removeIconSubmit']],
        '#limit_validation_errors' => [],
        '#ajax' => [
          'callback' => [$this, 'ajaxUpdateCallback'],
          'wrapper' => 'block-form',
        ],
      ];
    }

    $form['add_icon'] = [
      '#type' => 'submit',
      '#value' => t('Add Icon'),
      '#submit' => [[$this, 'addIconSubmit']],
      '#limit_validation_errors' => [],
      '#ajax' => [
        'callback' => [$this, 'ajaxUpdateCallback'],
        'wrapper' => 'block-form',
      ],

    ];
    // \Drupal::logger('etss2_payment_icons')->debug('Icons Form: @icons', ['@icons' => print_r($form_state->get('icons'), TRUE)]);
    return $form;
  }


  public function addIconSubmit(array &$form, FormStateInterface $form_state)
  {
    $icons = $form_state->get('icons') ?? [];

    $icons[] = [
      'merchant' => '',
      'media_id' => NULL,
    ];
    $icons = array_values($icons);
    // \Drupal::logger('etss2_payment_icons')->debug('Icons array after add: @icons', ['@icons' => print_r($icons, TRUE)]);

    $form_state->set('icons', $icons);
    $form_state->setRebuild(TRUE);
  }


  public function removeIconSubmit(array &$form, FormStateInterface $form_state)
  {
    $triggering_element = $form_state->getTriggeringElement();
    $index = explode('_', $triggering_element['#name'])[1]; // Extract index.
    // \Drupal::logger('etss2_payment_icons')->debug('$index: @icons', ['@icons' => print_r($index, TRUE)]);
    $icons = $form_state->get('icons') ?? []; // Ensure $icons is always an array.

    unset($icons[$index]);

    $form_state->set('icons', array_values($icons));

    $form_state->setRebuild(TRUE);
  }

  public function ajaxUpdateCallback(array $form, FormStateInterface $form_state)
  {
    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * Handles form submission for the block configuration.
   */
  public function blockSubmit($form, FormStateInterface $form_state)
  {
    $this->setConfigurationValue('icons', $form_state->getValue('icons'));
  }

}


