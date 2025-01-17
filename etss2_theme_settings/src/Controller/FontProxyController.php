<?php
namespace Drupal\etss2_theme_settings\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FontProxyController extends ControllerBase
{

  protected $fileSystem;

  public function __construct(FileSystemInterface $file_system)
  {
    $this->fileSystem = $file_system;
  }

  public static function create(ContainerInterface $container)
  {
    return new static($container->get('file_system'));
  }

  public function downloadFont($file_name)
{
    $file_path = 's3://font/' . $file_name;
    // \Drupal::logger('FontProxyController')->debug('S3 file path: @path', ['@path' => $file_path]);
    
    // Check if the file exists on S3.
    if (!file_exists($file_path)) {
      throw new NotFoundHttpException('Font file not found');
    }
    
    // Get the file contents from S3.
    $file_contents = file_get_contents($file_path);

    if ($file_contents === false) {
        throw new NotFoundHttpException('Unable to read the font file');
    }

    // Determine the file extension and set the correct content type.
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $content_type = $this->getFontContentType($file_extension);

    // Return the file response.
    return new Response($file_contents, 200, [
        'Content-Type' => $content_type,
        'Content-Disposition' => 'inline; filename="' . $file_name . '"',
    ]);
}

  /**
   * Helper function to determine the correct font content type based on file extension.
   */
  protected function getFontContentType($extension)
  {
    $content_types = [
      'woff' => 'font/woff',
      'woff2' => 'font/woff2',
      'otf' => 'font/otf',
      'ttf' => 'font/ttf',
      'eot' => 'application/vnd.ms-fontobject',
    ];

    return $content_types[$extension] ?? 'application/octet-stream';
  }
}
