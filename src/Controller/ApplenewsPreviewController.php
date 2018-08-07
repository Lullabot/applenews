<?php

namespace Drupal\applenews\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Class ApplenewsPreviewController
 *
 * @package Drupal\applenews\Controller
 */
class ApplenewsPreviewController extends ControllerBase {

  /**
   * Generates article.json and assets for preview.
   *
   * @param \Drupal\node\NodeInterface $node
   * @param $template_id
   *
   * @return array
   */
  public function preview(NodeInterface $node, $template_id) {
    $uri = 'public://applenews_preview/';
    $filename = 'applenews-node-' . $node->id() . '-' . $template_id . '.json';

    $link = Link::fromTextAndUrl($this->t('Download files'), Url::fromUserInput(file_url_transform_relative(file_create_url($uri . $filename))));

    return [
      '#markup' => $link->toString(),
    ];
  }

}
