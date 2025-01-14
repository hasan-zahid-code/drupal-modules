<?php

namespace Drupal\etss2_social_icons\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Drupal\media\Entity\Media;

/**
 * Serves social icon files dynamically.
 */
class SocialIconFileController extends ControllerBase {

  /**
   * Serves a social icon file.
   *
   * @param string $file_name
   *   The name of the file to be served.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   *   The file response or a 404 exception if the file is not found.
   */
  public function serveFile($file_name) {
    // Load the media entity by name.
    $media_storage = \Drupal::entityTypeManager()->getStorage('media');
    $media_entities = $media_storage->loadByProperties(['name' => $file_name]);

    // Ensure the media entity exists.
    if (empty($media_entities)) {
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('File not found.');
    }

    // Get the first matching media entity.
    $media = reset($media_entities);

    // Load the associated file entity.
    $file = $media->get('field_media_image')->entity;

    if ($file) {
      // Get the file's real path.
      $file_path = $file->getFileUri();
      $real_path = \Drupal::service('file_system')->realpath($file_path);

      // Serve the file using BinaryFileResponse.
      $response = new BinaryFileResponse($real_path);
      $response->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_INLINE,
        $file->getFilename()
      );

      return $response;
    }

    // File not found in the media entity.
    throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('File not found.');
  }
}
