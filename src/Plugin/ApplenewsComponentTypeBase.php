<?php

namespace Drupal\applenews\Plugin;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ApplenewsComponentTypeBase extends PluginBase implements ApplenewsComponentTypeInterface {

  /**
   * @var EntityFieldManagerInterface
   */
  protected $fieldManager;

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $input = $form_state->getUserInput();

    $element = [];

    $element['component_form_title'] = [
      '#markup' => '<h3>' . $this->t('Adding @component_name component', ['@component_name' => $this->label()]) . '</h3>',
    ];

    $element['component_layout'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Component Layout'),
    ];

    $element['component_layout']['column_start'] = [
      '#type' => 'number',
      '#title' => $this->t('Column Start'),
      '#description' => $this->t("Indicates which column the component's start position is in, based on the number of columns in the document or parent container. By default, the component will start in the first column (note that the first column is 0, not 1)."),
    ];

    $element['component_layout']['column_span'] = [
      '#type' => 'number',
      '#title' => $this->t('Column Span'),
      '#description' => $this->t("Indicates how many columns the component spans, based on the number of columns in the document. By default, the component spans the entire width of the document or the width of its container component."),
    ];

    $node_type = $input['node_type'];
    $fields = $this->fieldManager->getFieldDefinitions('node', $node_type);
    $field_options = [];
    foreach ($fields as $field_name => $field) {
      $field_options[$field_name] = $field->getLabel();
      if (!$field->getFieldStorageDefinition()->isBaseField()) {
        $field_options[$field_name] .= ' (' . $field->getType() . ')';
      }
    }

    $element['component_field'] = [
      '#type' => 'select',
      '#options' => $field_options,
    ];

    // @todo add more component layout form elements

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

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityFieldManagerInterface $field_manager) {
    $this->fieldManager = $field_manager;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_field.manager')
    );
  }

}
