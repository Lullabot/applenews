<?php

namespace Drupal\applenews\Plugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;

abstract class ApplenewsComponentTypeBase extends PluginBase implements ApplenewsComponentTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $element = [];

    $element['layout'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Layout'),
    ];

    $element['layout']['column_start'] = [
      '#type' => 'number',
      '#title' => $this->t('Column Start'),
      '#description' => $this->t("Indicates which column the component's start position is in, based on the number of columns in the document or parent container. By default, the component will start in the first column (note that the first column is 0, not 1)."),
    ];

    $element['layout']['column_span'] = [
      '#type' => 'number',
      '#title' => $this->t('Column Span'),
      '#description' => $this->t("Indicates how many columns the component spans, based on the number of columns in the document. By default, the component spans the entire width of the document or the width of its container component."),
    ];

    // @todo form elements based on underlying component class (text vs photo)

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  public function getComponentClass() {
    return $this->pluginDefinition['component_class'];
  }



}
