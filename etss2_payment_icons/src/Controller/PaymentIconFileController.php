<?php
namespace Drupal\etss2_payment_icons\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Serves payment icon files dynamically.
 */
class PaymentIconFileController extends ControllerBase {

  /**
   * Serves a payment icon file.
   *
   * @param string $file_name
   *   The name of the file to be served.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The file response or a 404 exception if the file is not found.
   */
  public function serveFile($file_name) {
    $s3_config = \Drupal::config('s3fs.settings');
    $bucket_name = $s3_config->get('bucket');
    $s3_bucket_url = 'https://' . $bucket_name . '.s3.amazonaws.com/icon/';
    $file_url = $s3_bucket_url . $file_name;

    // Use file_get_contents() to fetch the file content from S3.
    $file_content = @file_get_contents($file_url);

    // Check if the file exists and was fetched correctly.
    if ($file_content === FALSE) {
      // If file not found or unable to fetch, throw a 404.
      throw new NotFoundHttpException('File not found on S3.');
    }

    // Determine the MIME type based on file extension.
    $mime_type = $this->getMimeType($file_name);

    // Create a new response with the file content.
    $response = new Response($file_content);
    $response->headers->set('Content-Type', $mime_type);
    $response->headers->set('Content-Disposition', ResponseHeaderBag::DISPOSITION_INLINE);
    $response->headers->set('Content-Length', strlen($file_content));

    return $response;
  }

  /**
   * Determines the MIME type based on the file extension.
   *
   * @param string $file_name
   *   The name of the file.
   *
   * @return string
   *   The MIME type.
   */
  private function getMimeType($file_name) {
    $extension = pathinfo($file_name, PATHINFO_EXTENSION);

    // Map file extensions to MIME types.
    $mime_types = [
      'png' => 'image/png',
      'svg' => 'image/svg+xml',
      'webp' => 'image/webp',
    ];

    return $mime_types[strtolower($extension)] ?? 'application/octet-stream';
  }
}