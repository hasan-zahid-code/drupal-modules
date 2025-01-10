<?php

namespace Drupal\etss2_theme_settings\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides a JSON API for ETSS2 Theme Settings.
 */
class ThemeSettingsJsonController extends ControllerBase {
  private static $fontFields = [
    'body' => 'Body Font',
    'headings_primary' => 'Headings Primary',
    'headings_secondary' => 'Headings Secondary',
    'hero' => 'Hero Font',
    'navigation' => 'Navigation Font',
    'buttons' => 'Buttons Font',
    'subtle' => 'Subtle Text',
    'highlights' => 'Highlights Font',
  ];
  /**
   * Returns theme settings as JSON.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response with the theme settings.
   */
  public function getThemeSettings()
  {
    // Load the configuration for ETSS2 Theme Settings.
    $config = $this->config('etss2_theme_settings.settings');

    // Initialize font metadata array.
    $font_metadata = [];

    foreach (self::$fontFields as $field_name => $label) {
      $file = $config->get($field_name . '_file');

      // Only add the font if the URL is present
      if ($file) {
        $font_metadata[] = [
          'family' => $config->get($field_name . '_family'),
          'style' => $config->get($field_name . '_style'),
          'weight' => $config->get($field_name . '_weight'),
          'format' => $config->get($field_name . '_format'),
          'uri' => $config->get($field_name . '_uri'),
          'section' => $field_name,
        ];
      }
    }


    // Prepare the rest of the theme settings response.
    $data = [
      'font_metadata' => $font_metadata,  // Use the updated font metadata array
      'colors' => [
        'primary' => [
          'background' => $config->get('primary_background'),
          'accent' => $config->get('primary_accent'),
        ],
        'secondary' => [
          'background' => $config->get('secondary_background'),
          'accent' => $config->get('secondary_accent'),
        ],
        'tertiary' => [
          'background' => $config->get('tertiary_background'),
          'accent' => $config->get('tertiary_accent'),
        ],
        'alternate' => [
          'background' => $config->get('alternate_background'),
          'accent' => $config->get('alternate_accent'),
        ],
        'interactive' => [
          'hover' => $config->get('interactive_hover'),
          'active' => $config->get('interactive_active'),
          'disabled' => $config->get('interactive_disabled'),
        ],
        'text' => [
          'dark' => $config->get('text_dark'),
          'light' => $config->get('text_light'),
          'accent' => $config->get('text_accent'),
        ],
        'feedback' => [
          'error' => $config->get('feedback_error'),
          'error_light' => $config->get('feedback_error_light'),
          'warning' => $config->get('feedback_warning'),
          'warning_light' => $config->get('feedback_warning_light'),
          'success' => $config->get('feedback_success'),
          'success_light' => $config->get('feedback_success_light'),
          'info' => $config->get('feedback_info'),
          'info_light' => $config->get('feedback_info_light'),
        ],
      ],
      'layout' => [
        'spacing' => [
          'padding' => $config->get('padding'),
          'margin' => $config->get('margin'),
        ],
        'grid' => [
          'columns' => $config->get('grid_columns'),
        ],
      ],
      'custom' => [
        'css' => $config->get('custom_css'),
        'js' => $config->get('custom_js'),
      ],
    ];

    // Prepare the full response structure.
    $response = [
      'data' => [
        'type' => 'theme_settings--theme_settings',
        'attributes' => $data,
      ],
    ];

    // Return the response as JSON.
    return new JsonResponse($response);
  }

}
