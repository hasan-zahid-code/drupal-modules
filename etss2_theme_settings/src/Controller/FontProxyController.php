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
    // Define the path to the font file in the public:// directory
    $file_path = "public://font/$file_name";

    // Check if the file exists
    $real_path = $this->fileSystem->realpath($file_path);
    if (!$real_path || !file_exists($real_path)) {
      throw new NotFoundHttpException('Font file not found');
    }

    // Get the file contents
    $file_contents = file_get_contents($real_path);

    // Determine the file extension and set the correct content type
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $content_type = $this->getFontContentType($file_extension);

    // Return the file response
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

    return isset($content_types[$extension]) ? $content_types[$extension] : 'application/octet-stream';
  }
}
