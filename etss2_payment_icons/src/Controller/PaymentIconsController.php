<?php

namespace Drupal\etss2_payment_icons\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\media\Entity\Media;

/**
 * Provides a JSON response for payment icons.
 */
class PaymentIconsController extends ControllerBase {

  /**
   * Fetch and return payment icons as a JSON response.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response containing payment icons data.
   */
  public function getPaymentIcons() {
    // Load the configuration for payment icons.
    $config = \Drupal::config('block.block.etss2_payment_icons');
    $icons = $config->get('settings.icons') ?? [];

    // Prepare the data for JSON output.
    $data = array_map(function ($icon) {
      $icon_url = '';
      if (isset($icon['media_id']) && $media = Media::load($icon['media_id'])) {
        // Get the media name and create a dynamic URL for the file.
        $file_name = $media->getName();
        $icon_url = '/icon/payment-icons/' . $file_name;
      }
      return [
        'merchant' => $icon['merchant'],
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
