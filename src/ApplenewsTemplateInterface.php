<?php

namespace Drupal\applenews;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface ApplenewsTemplateInterface extends ConfigEntityInterface {

  public function getLayout();

  public function getComponents();
}
