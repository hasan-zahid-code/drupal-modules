<?php

namespace Drupal\etss2_theme_settings\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides a JSON API for ETSS2 Theme Settings.
 */
class ThemeSettingsJsonController extends ControllerBase
{

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

    // Prepare response data.
    $data = [
      'fonts' => [
        'large_text' => [
          'bold' => [
            'url' => $config->get('font_large_text_bold_url'),
          ],
          'light' => [
            'url' => $config->get('font_large_text_light_url'),
          ],
        ],
        'body' => [
          'bold' => [
            'url' => $config->get('font_body_bold_url'),
          ],
          'light' => [
            'url' => $config->get('font_body_light_url'),
          ],
        ],
        'heading' => [
          'bold' => [
            'url' => $config->get('font_heading_bold_url'),
          ],
          'light' => [
            'url' => $config->get('font_heading_light_url'),
          ],
        ],
      ],

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
          'error' => [
            'default' => $config->get('feedback_error'),
            'light' => $config->get('feedback_error_light'),
          ],
          'warning' => [
            'default' => $config->get('feedback_warning'),
            'light' => $config->get('feedback_warning_light'),
          ],
          'success' => [
            'default' => $config->get('feedback_success'),
            'light' => $config->get('feedback_success_light'),
          ],
          'info' => [
            'default' => $config->get('feedback_info'),
            'light' => $config->get('feedback_info_light'),
          ],
        ],
      ],

      'spacing' => [
        'padding' => $config->get('padding'),
        'margin' => $config->get('margin'),
        'grid_columns' => $config->get('grid_columns'),
      ],

      'custom' => [
        'css' => $config->get('custom_css'),
        'js' => $config->get('custom_js'),
      ],
    ];

    // Return the response as JSON.
    return new JsonResponse($data);
  }

}
