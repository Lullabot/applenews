<?php

namespace Drupal\applenews;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class ApplenewsTemplateSelection
 *
 * @package Drupal\applenews
 */
class ApplenewsTemplateSelection {
  use StringTranslationTrait;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs an ApplenewsTemplateSelection object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Get a list of fully-loaded applenews template objects that refer to a single
   * node type.
   *
   * @param string $node_type
   *
   * @return array
   *   An array indexed by entity id of all templates available for the node type.
   */
  public function getTemplatesForNodeType($node_type) {
    $templates = \Drupal::entityTypeManager()->getStorage('applenews_template')->loadMultiple();

    $return = [];
    foreach ($templates as $id => $template) {
      if ($template->node_type == $node_type) {
        $return[$id] = $template;
      }
    }

    return $return;
  }

  /**
   * Get a form selection element containing the available tempaltes for a given
   * node type.
   *
   * @param string $node_type
   *
   * @return array
   *   A form element for selecting an applenews_template.
   */
  public function getTemplateSelectionElement($node_type) {
    $templates = $this->getTemplatesForNodeType($node_type);
    $options = [];
    foreach ($templates as $id => $template) {
      $options[$id] = $template->label;
    }

    return [
      '#type' => 'select',
      '#title' => $this->t('Available templates'),
      '#options' => $options,
    ];
  }

}
