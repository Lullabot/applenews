<?php

namespace Drupal\applenews\Plugin\applenews\ComponentType;

use Drupal\applenews\Plugin\ApplenewsComponentTypeBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin class to generate all the default Component plugins.
 *
 * @ApplenewsComponentType(
 *  id = "default_text",
 *  label = @Translation("Default Component Type"),
 *  description = @Translation("Default component types based on AppleNewsAPI library."),
 *  component_type = "text",
 *  deriver = "Drupal\applenews\Derivative\ApplenewsDefaultComponentTextTypeDeriver"
 * )
 */
class ApplenewsDefaultTextComponentType extends ApplenewsComponentTypeBase {
  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $input = $form_state->getUserInput();
    $node_type = $input['node_type'];

    $element['component_settings']['component_data']['text_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Source field for main text'),
      '#options' => $this->getFieldOptions($node_type),
    ];

    return $element;
  }
}
