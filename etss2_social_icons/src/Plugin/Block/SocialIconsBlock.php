<?php

namespace Drupal\etss2_social_icons\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;

/**
 * Provides a 'Social Icons Block'.
 *
 * @Block(
 *   id = "etss2_social_icons",
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
    $config = \Drupal::config('block.block.etss2_social_icons_block');
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
      '#type' => 'container',
      '#tree' => TRUE,
    ];


    foreach ($icons as $index => $icon) {
      $form['icons'][$index] = [
        '#type' => 'details',
        '#title' => $icon['icon'] ?? $this->t('Add new Social Icon'),
        '#open' => false,
      ];

      $form['icons'][$index]['icon'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Platform Name'),
        '#default_value' => $icon['icon'] ?? '',
        '#description' => $this->t('Enter the name of the social platform.'),
        '#maxlength' => 50,
        '#required' => TRUE,
      ];

      $form['icons'][$index]['link'] = [
        '#type' => 'url',
        '#title' => $this->t('Platform URL'),
        '#default_value' => $icon['link'] ?? '',
        '#description' => $this->t('Provide a valid URL.'),
        '#required' => TRUE,
      ];

      $form['icons'][$index]['media_id'] = [
        '#type' => 'media_library',
        '#title' => $this->t('Select Icon from Media Library'),
        '#allowed_bundles' => ['social_icons'],
        '#default_value' => $icon['media_id'] ?? NULL,
        '#required' => TRUE,
      ];

      // Remove Icon Button
      $form['icons'][$index]['remove_icon'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove Icon'),
        '#name' => 'remove_icon_' . $index,
        '#submit' => [[$this, 'removeIconSubmit']],
        '#ajax' => [
          'callback' => [$this, 'ajaxCallback'],
        ],
      ];
    }

    // Add Icon Button
    $form['add_icon'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Social Icon'),
      '#submit' => [[$this, 'addIconSubmit']],
      '#ajax' => [
        'callback' => [$this, 'ajaxCallback'],
      ],
    ];

    return $form;
  }

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
  }

  public function removeIconSubmit(array &$form, FormStateInterface $form_state)
  {
    $triggering_element = $form_state->getTriggeringElement();
    $button_name = $triggering_element['#name'];

    // Extract the index from the button name (e.g., "remove_icon_1")
    if (preg_match('/remove_icon_(\d+)/', $button_name, $matches)) {
      $index = $matches[1];
      $icons = $form_state->get('icons') ?? [];
      unset($icons[$index]); // Remove the specific icon
      $icons = array_values($icons); // Reindex the array
      $form_state->set('icons', $icons);
      $form_state->setRebuild();
    }
  }

  public function ajaxCallback(array &$form, FormStateInterface $form_state)
{
    // Log the form state to confirm icons exist
    // \Drupal::logger('etss2_social_icons')->debug('<pre>' . print_r($form_state->getValues(), TRUE) . '</pre>');

    // Ensure the icons container exists
    if (isset($form['icons'])) {
        return $form['icons'];
    }

    // Log an error when icons container is not found
    \Drupal::logger('etss2_social_icons')->error('Icons container not found in the form. Triggering page reload.');

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
  // public function validateIconName($element, FormStateInterface $form_state)
  // {
  //   if (!preg_match('/^[a-zA-Z0-9 ]+$/', $element['#value'])) {
  //     $form_state->setError($element, $this->t('The platform name must only contain alphanumeric characters and spaces.'));
  //   }
  // }

  // /**
  //  * Custom validation for the URL field.
  //  */
  // public function validateUrl($element, FormStateInterface $form_state)
  // {
  //   if (!filter_var($element['#value'], FILTER_VALIDATE_URL)) {
  //     $form_state->setError($element, $this->t('The URL provided is not valid. Please enter a valid URL.'));
  //   }
  // }

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


