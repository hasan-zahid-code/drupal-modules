<?php

namespace Drupal\etss2_social_icons\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\media\Entity\Media;

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
    $config = $this->getConfiguration();
    $icons = $config['icons'] ?? [];

    $rendered_icons = array_map(function ($icon) {
      $file_url = '';
      if (isset($icon['media_id']) && $media = Media::load($icon['media_id'])) {
        $file_url = $media->get('thumbnail')->entity->createFileUrl();
      }
      return [
        'icon' => htmlspecialchars($icon['icon'], ENT_QUOTES, 'UTF-8'),
        'link' => $icon['link'],
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
    // Fetch the current icons from the form state or configuration.
    if (!$form_state->has('icons')) {
      $form_state->set('icons', $this->getConfiguration()['icons'] ?? []);
    }
    $icons = $form_state->get('icons');

    $form['icons'] = [
      '#type' => 'fieldset',
      '#tree' => TRUE,
      '#title' => t('Social Media Icons'),
      '#description' => t('Add or edit social media icons uploaded as files. Browse existing icons from the media library or upload a new one.'),
    ];


    foreach ($icons as $index => $icon) {
      $form['icons'][$index] = [
        '#type' => 'details',
        '#title' => $icon['icon'] == '' ? $this->t('New Social Icon') : $icon['icon'],
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
        '#allowed_bundles' => ['icon'],
        '#default_value' => $icon['media_id'] ?? NULL,
        '#required' => TRUE,
      ];

      // Remove Icon Button
      $form['icons'][$index]['remove_icon'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove Icon'),
        '#name' => "remove_icon_$index",
        '#submit' => [[$this, 'removeIconSubmit']],
        '#ajax' => [
          'callback' => [$this, 'ajaxCallback'],
          'wrapper' => 'block-form',
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
        'wrapper' => 'block-form',
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
    $index = explode('_', $triggering_element['#name'])[1]; // Extract index.

    $icons = $form_state->get('icons') ?? [];
    unset($icons[$index]); // Remove the specific icon

    $icons = array_values($icons); // Reindex the array
    $form_state->set('icons', $icons);
    $form_state->setRebuild();

  }

  public function ajaxCallback(array &$form, FormStateInterface $form_state)
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

  /**
   * Custom validation for the URL field.
   */
  public function validateUrl($element, FormStateInterface $form_state)
  {
    if (!filter_var($element['#value'], FILTER_VALIDATE_URL)) {
      $form_state->setError($element, $this->t('The URL provided is not valid. Please enter a valid URL.'));
    }
  }


}


