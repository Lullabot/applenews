<?php

namespace Drupal\applenews\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

class ApplenewsDefaultComponentTypeDeriver extends DeriverBase {
  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [];
    foreach ($this->getComponentClasses() as $id => $info) {
      $this->derivatives[$id] = $info + $base_plugin_definition;
    }

    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

  protected function getComponentClasses() {
    return array(
      'author' => [
        'component_class' => 'ChapterThree\AppleNewsAPI\Document\Components\Author',
        'label' => 'Author',
        'description' => 'The name of one of the authors of the article.',
      ],
      'body' => [
        'component_class' => 'ChapterThree\AppleNewsAPI\Document\Components\Body',
        'label' => 'Body',
        'description' => 'A chunk of text.',
      ],
      'byline' => [
        'component_class' => 'ChapterThree\AppleNewsAPI\Document\Components\Byline',
        'label' => 'Byline',
        'description' => 'A byline describes one or more contributors to the article, and usually includes the word "by" or "from" as well as the contributors\' names.',
      ],
    );
  }
}
