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

    // Prepare rendered icons for the block.
    $rendered_icons = array_map(function ($icon) {
      $file_url = '';

      if (isset($icon['media_id']) && !empty($icon['media_id'])) {
        // Load the media entity using the entity type manager.
        $media_storage = \Drupal::entityTypeManager()->getStorage('media');
        $media = $media_storage->load($icon['media_id']);

        if ($media && $media->hasField('thumbnail')) {
          // Get the file entity associated with the field.
          $file = $media->get('thumbnail')->entity;

          if ($file) {
            // Get the file URI.
            $file_uri = $file->getFileUri();

            // Generate the full S3 URL.
            $file_url = $this->getS3FileUrl($file_uri);
          }
        }
      }

      return [
        'icon' => htmlspecialchars($icon['icon'], ENT_QUOTES, 'UTF-8'),
        'link' => Url::fromUri($icon['link'])->toString(),
        'url' => $file_url,
      ];
    }, $icons);

    return [
      '#theme' => 'etss2_social_icons',
      '#icons' => $rendered_icons,
      '#cache' => [
        'max-age' => 0,
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


  protected function getS3FileUrl($file_uri)
  {
    $s3_config = \Drupal::config('s3fs.settings'); // For S3 configuration

    $bucket_name = $s3_config->get('bucket');

    $s3_base_url = 'https://' . $bucket_name . '.s3.amazonaws.com/';

    // Clean the file URI by removing the "s3://" or "public://" prefix.
    $clean_file_uri = preg_replace('/^(s3:|public:)\//', '', $file_uri);

    // Return the full S3 URL.
    return rtrim($s3_base_url, '/') . '/' . ltrim($clean_file_uri, '/');
  }

}


