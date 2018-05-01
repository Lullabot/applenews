<?php

namespace Drupal\applenews\Entity;

use Drupal\applenews\ApplenewsTemplateInterface;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the ApplenewsTemplate entity.
 *
 * @ConfigEntityType(
 *  id = "applenews_template",
 *  label = @Translation("Applenews Template"),
 *  handlers = {
 *    "list_builder" = "Drupal\applenews\Controller\ApplenewsTemplateListBuilder",
 *    "form" = {
 *      "add" = "Drupal\applenews\Form\ApplenewsTemplateForm",
 *      "edit" = "Drupal\applenews\Form\ApplenewsTemplateForm",
 *      "delete" = "Drupal\applenews\Form\ApplenewsTemplateDeleteForm",
 *    }
 *  },
 *  config_prefix = "applenews_template",
 *  admin_permission = "administer applenews templates",
 *  entity_keys = {
 *    "id" = "id",
 *    "label" = "label",
 *  },
 *  links = {
 *    "edit-form" = "/admin/config/services/applenews/{applenews_template}",
 *    "delete-form" = "/admin/config/services/applenews/{applenews_template}/delete"
 *  }
 * )
 */
class ApplenewsTemplate extends ConfigEntityBase implements ApplenewsTemplateInterface {
  /**
   * The Applenews Template ID.
   *
   * @var string
   */
  public $id;

  /**
   * The Applenews Template label.
   *
   * @var string
   */
  public $label;

  /**
   * {@inheritdoc}
   */
  public function getLayout() {
    return [
      'columns' => $this->get('columns'),
      'width' => $this->get('width'),
      'gutter' => $this->get('gutter'),
      'margin' => $this->get('margin'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getComponents() {
    return [];
  }
}
