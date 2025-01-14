<?php

namespace Drupal\etss2_social_icons\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 * Provides a 'Social Icons Block'.
 *
 * @Block(
 *   id = "etss2_social_icons_block",
 *   admin_label = @Translation("ETSS2 Social Icons Block"),
 *   category = @Translation("Custom")
 * )
 */
class SocialIconsBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   *
   * Builds the block content for rendering.
   */

  public function build()
  {
    // Load the block configuration.
    $config = \Drupal::config('block.block.olivero_etss2socialiconsblock');
    $icons = $config->get('settings.icons') ?? [];
    $this->getS3FileBaseUrl();
    // Prepare rendered icons for the block.
    $rendered_icons = array_map(function ($icon) {
      $file_url = '';

      // Debugging: Log the media_id and icon information.
      // \Drupal::messenger()->addMessage('Processing icon: ' . print_r($icon, TRUE), MessengerInterface::TYPE_STATUS);

      if (isset($icon['media_id'])) {
        // Use the entity type manager to load the media entity.
        $media_storage = \Drupal::entityTypeManager()->getStorage('media');
        $media = $media_storage->load($icon['media_id']);  // Load the media entity

        if ($media && $media->hasField('field_media_image')) {
          // Access the file entity associated with the media field.
          $image_field = $media->get('field_media_image')->first(); // Get the first image from the field
          if ($image_field) {
            $file = $image_field->entity;  // Get the file entity
            if ($file) {
              $file_url = preg_replace('/^public:\//', '', $file->getFileUri());
              $file_url = '/sites/default/files' . $file_url;

              $file_url = urldecode($file_url);
            }
          }
        }
      }

      // Debugging: Output the final URL and icon info.
      // \Drupal::messenger()->addMessage('Generated file URL: ' . $file_url, MessengerInterface::TYPE_STATUS);

      return [
        'icon' => htmlspecialchars($icon['icon'], ENT_QUOTES, 'UTF-8'),
        'link' => Url::fromUri($icon['link'])->toString(), // Ensure URL is properly handled
        'url' => 'huiu', // Full URL of the media image.
      ];

    }, $icons);

    return [
      '#theme' => 'etss2_social_icons',
      '#icons' => $rendered_icons,
      '#cache' => [
        'max-age' => 36000,
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
    $config = $this->getConfiguration();
    $icons = $form_state->get('icons') ?? $config['icons'] ?? [];

    $form['icons'] = [
      '#type' => 'details',
      '#title' => $this->t('Social Icons'),
      '#tree' => TRUE,
      '#description' => $this->t('Add or edit social icons with their links. You can upload a new icon or select one from the Media Library.'),
      '#prefix' => '<div id="social-icons-wrapper">',
      '#suffix' => '</div>',
    ];

    foreach ($icons as $index => $icon) {
      $form['icons'][$index] = [
        'icon' => [
          '#type' => 'textfield',
          '#title' => $this->t('Platform Name'),
          '#default_value' => $icon['icon'] ?? '',
          '#description' => $this->t('Enter the name of the social platform (e.g., Facebook, Twitter). Only alphanumeric characters and spaces are allowed.'),
          '#maxlength' => 50,
          '#required' => TRUE,
          '#element_validate' => [[$this, 'validateIconName']],
        ],
        'link' => [
          '#type' => 'url',
          '#title' => $this->t('Platform URL'),
          '#default_value' => $icon['link'] ?? '',
          '#description' => $this->t('Provide a valid URL (e.g., https://facebook.com).'),
          '#required' => TRUE,
          '#element_validate' => [[$this, 'validateUrl']],
        ],
        'media_id' => [
          '#type' => 'media_library',
          '#title' => $this->t('Select Icon from Media Library'),
          '#allowed_bundles' => ['social_icons'],
          '#description' => $this->t('Select an existing icon from the media library or upload a new one.'),
          '#default_value' => $icon['media_id'] ?? NULL,
          '#required' => TRUE,
        ],

      ];
    }

    // Add a button to dynamically add more icons.
    $form['add_icon'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Social Icon'),
      '#submit' => [[$this, 'addIconSubmit']],
      '#ajax' => [
        'callback' => '::ajaxCallback',
        'wrapper' => 'social-icons-wrapper',
      ],

    ];


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

  /**
   * Custom validation for the platform name field.
   */
  public function validateIconName($element, FormStateInterface $form_state)
  {
    if (!preg_match('/^[a-zA-Z0-9 ]+$/', $element['#value'])) {
      $form_state->setError($element, $this->t('The platform name must only contain alphanumeric characters and spaces.'));
    }
  }

  /**
   * Custom validation for the URL field.
   */
  public function validateUrl($element, FormStateInterface $form_state)
  {
    if (!filter_var($element['#value'], FILTER_VALIDATE_URL)) {
      $form_state->setError($element, $this->t('The URL provided is not valid. Please enter a valid URL.'));
    }
  }

  /**
   * Adds a new icon dynamically when the "Add Social Icon" button is clicked.
   */
  public function addIconSubmit(array &$form, FormStateInterface $form_state)
  {
    $icons = $form_state->get('icons') ?? [];
    $icons[] = [
      'icon' => '',
      'link' => '',
      'media_id' => NULL,
    ];
    $form_state->set('icons', $icons);
    $form_state->setRebuild();
    \Drupal::logger('etss2_social_icons')->debug('AJAX callback triggered.');
    \Drupal::logger('etss2_social_icons')->debug('Form state: @state', ['@state' => print_r($form_state->get('icons'), TRUE)]);

  }

  public function ajaxCallback(array &$form, FormStateInterface $form_state)
  {
    \Drupal::logger('etss2_social_icons')->debug('AJAX callback triggered.');
    \Drupal::logger('etss2_social_icons')->debug('Form state: @state', ['@state' => print_r($form_state->get('icons'), TRUE)]);

    // Return the full form element for the `icons` fieldset wrapped in the div with `id="social-icons-wrapper"`.
    return $form['icons'];
  }

  public function getS3FileBaseUrl()
  {
    $s3_settings = \Drupal::config('s3fs.settings');

    $s3_url = $s3_settings->get('bucket') ?? '';
    $s3_prefix = $s3_settings->get('prefix') ?? '';

    $s3_file_base_url = $s3_url ? 'https://' . $s3_url . '/' . $s3_prefix : '';

    return $s3_file_base_url;
  }

}


