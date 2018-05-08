<?php

namespace Drupal\applenews\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ApplenewsPreviewController extends ControllerBase {

  public function preview(NodeInterface $node, $template_id) {
    $uri = 'public://applenews_preview/';
    $filename = 'applenews-node-' . $node->id() . '-' . $template_id . '.json';

    $response = new BinaryFileResponse($uri . $filename);
    $response->setContentDisposition(
      ResponseHeaderBag::DISPOSITION_INLINE,
      $filename
    );

    return $response;
  }

}
