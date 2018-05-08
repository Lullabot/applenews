<?php

namespace Drupal\applenews\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ApplenewsPreviewController extends ControllerBase {

  public function preview(NodeInterface $node, $template_id) {
    $uri = 'public://applenews_preview/';
    $filename = 'applenews-node-' . $node->id() . '-' . $template_id . '.json';

    $link = Link::fromTextAndUrl($this->t('Download files'), Url::fromUserInput(file_url_transform_relative(file_create_url($uri . $filename))));

    return [
      '#markup' => $link->toString(),
    ];
  }

}
