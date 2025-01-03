<?php

namespace Drupal\business_details\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class BusinessDetailsController extends ControllerBase {
  public function getBusinessDetails() {
    $config = $this->config('business_details.settings');
    $data = [
      'business_name' => $config->get('business_name'),
      'phone_number' => $config->get('phone_number'),
      'address' => $config->get('address'),
    ];

    return new JsonResponse($data);
  }
}
