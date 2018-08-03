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
   * @todo make this a list of value objects
   *
   * @var array
   */
  protected $components;

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
    return $this->components;
  }

  /**
   * {@inheritdoc}
   */
  public function getComponent($id) {
    foreach ($this->components as $component_id => $component) {
      if ($id == $component_id) {
        return $component;
      }
      if ($found = $this->getNestedComponent($component['component_data']['components'], $id)) {
        return $found;
      }
    }

    return NULL;
  }

  protected function getNestedComponent($components, $id) {
    foreach ($components as $component_id => $component) {
      if ($id == $component_id) {
        return $component;
      }
      if ($found = $this->getNestedComponent($component['component_data']['components'], $id)) {
        return $found;
      }
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function addComponent(array $component) {
    $this->components[$component['uuid']] = $component;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteComponent($id) {
    foreach ($this->components as $component_id => &$component) {
      if ($id == $component_id) {
        unset($this->components[$id]);
        return TRUE;
      }
      if ($this->deleteNestedComponent($component['component_data']['components'], $id)) {
        return TRUE;
      }

    }
    uasort($components, [$this, 'sortHelper']);
  }

  protected function deleteNestedComponent(&$components, $id) {
    foreach ($components as $component_id => $component) {
      if ($id == $component_id) {
        unset($components[$id]);
        if (!$components) {
          $components = NULL;
        }
        return TRUE;
      }
      if ($this->deleteNestedComponent($component['component_data']['components'], $id)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setComponents(array $components) {
    uasort($components, [$this, 'sortHelper']);
    $this->components = $components;
  }

  /**
   * Callable function to be used in a uasort call for the template's components.
   *
   * @param $a
   * @param $b
   */
  public function sortHelper($a, $b) {
    return $a['weight'] - $b['weight'];
  }

}