<?php

namespace Drupal\etss2_social_icons\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\media\Entity\Media;

/**
 * Provides a JSON response for social icons.
 */
class SocialIconsController extends ControllerBase {

  /**
   * Fetch and return social icons as a JSON response.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response containing social icons data.
   */
  public function getSocialIcons() {
    // Load the configuration for social icons.
    $config = $this->config('etss2_social_icons.settings');
    $icons = $config->get('icons') ?? [];

    // Prepare the data for JSON output.
    $data = array_map(function ($icon) {
      $icon_url = '';
      if (isset($icon['media_id']) && $media = Media::load($icon['media_id'])) {
        // Get the media name and create a dynamic URL for the file.
        $file_name = $media->getName();
        $icon_url = '/social-icons/icon/' . $file_name;
      }
      return [
        'platform' => $icon['icon'],
        'url' => $icon['link'],
        'icon_url' => $icon_url,
      ];
    }, $icons);

    // Return the JSON response.
    return new JsonResponse([
      'status' => 'success',
      'data' => $data,
    ]);
  }
}
