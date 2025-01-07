<?php

namespace Drupal\etss2_theme_settings\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides a JSON API for ETSS2 Theme Settings.
 */
class ThemeSettingsJsonController extends ControllerBase {

  /**
   * Returns theme settings as JSON.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response with the theme settings.
   */
  public function getThemeSettings() {
    // Load the configuration for ETSS2 Theme Settings.
    $config = $this->config('etss2_theme_settings.settings');

    // Prepare response data with nested categories.
    $data = [
      'fonts' => [
        'font_large_text_bold' => [
          'url' => $config->get('font_large_text_bold_url'),
        ],
        'font_large_text_light' => [
          'url' => $config->get('font_large_text_light_url'),
        ],
        'font_body_bold' => [
          'url' => $config->get('font_body_bold_url'),
        ],
        'font_body_light' => [
          'url' => $config->get('font_body_light_url'),
        ],
        'font_heading_bold' => [
          'url' => $config->get('font_heading_bold_url'),
        ],
        'font_heading_light' => [
          'url' => $config->get('font_heading_light_url'),
        ],
      ],
      'colors' => [
        'primary_background' => $config->get('primary_background'),
        'primary_accent' => $config->get('primary_accent'),
        'secondary_background' => $config->get('secondary_background'),
        'secondary_accent' => $config->get('secondary_accent'),
        'tertiary_background' => $config->get('tertiary_background'),
        'tertiary_accent' => $config->get('tertiary_accent'),
        'alternate_background' => $config->get('alternate_background'),
        'alternate_accent' => $config->get('alternate_accent'),
        'interactive_hover' => $config->get('interactive_hover'),
        'interactive_active' => $config->get('interactive_active'),
        'interactive_disabled' => $config->get('interactive_disabled'),
        'text_dark' => $config->get('text_dark'),
        'text_light' => $config->get('text_light'),
        'text_accent' => $config->get('text_accent'),
        'feedback_error' => $config->get('feedback_error'),
        'feedback_error_light' => $config->get('feedback_error_light'),
        'feedback_warning' => $config->get('feedback_warning'),
        'feedback_warning_light' => $config->get('feedback_warning_light'),
        'feedback_success' => $config->get('feedback_success'),
        'feedback_success_light' => $config->get('feedback_success_light'),
        'feedback_info' => $config->get('feedback_info'),
        'feedback_info_light' => $config->get('feedback_info_light'),
      ],
      'spacing' => [
        'padding' => $config->get('padding'),
        'margin' => $config->get('margin'),
        'grid_columns' => $config->get('grid_columns'),
      ],
      'custom' => [
        'custom_css' => $config->get('custom_css'),
        'custom_js' => $config->get('custom_js'),
      ],
    ];

    // Return the response as JSON.
    return new JsonResponse($data);
  }

}
