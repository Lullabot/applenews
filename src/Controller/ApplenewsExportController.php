<?php

namespace Drupal\applenews\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;

class ApplenewsExportController extends ControllerBase {

  public function export(NodeInterface $node) {
    return [
      '#markup' => 'test'
    ];
  }
}
