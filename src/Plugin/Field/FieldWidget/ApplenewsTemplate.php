<?php

namespace Drupal\applenews\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a default applenews template widget.
 *
 * @FieldWidget(
 *   id = "applenews_template_default",
 *   label = @Translation("Applenews template"),
 *   field_types = {
 *     "applenews_template_default"
 *   }
 * )
 */
class ApplenewsTemplate extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'status' => '',
        'template' => '',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $entity = $items->getEntity();

    $element['status'] = [
      '#type' => 'checkbox',
      '#title' => t('Enabled'),
      '#default_value' => $items->status,
    ];

    $element['template'] = [
      '#type' => 'select',
      '#title' => t('Template'),
      '#default_value' => $items->template,
      '#options' => $this->getTemplates($entity),
      '#description' => $this->t('Select template to use for Applenews'),
      '#states' => [
        'visible' => [
          ':input[name="' . $items->getName() . '[' . $delta . '][status]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // If the advanced settings tabs-set is available (normally rendered in the
    // second column on wide-resolutions), place the field as a details element
    // in this tab-set.
    if (isset($form['advanced'])) {
      // Override widget title to be helpful for end users.
      $element['#title'] = $this->t('Applenews settings');

      $element += [
        '#type' => 'details',
        '#group' => 'advanced',
        '#attributes' => [
          'class' => ['applenews-' . Html::getClass($entity->getEntityTypeId()) . '-settings-form'],
        ],
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $enabled = $this->getSetting('status');
    if ($enabled) {
      $summary[] = t('Template: !template', ['!template' => $this->getSetting('template')]);
    }

    return $summary;
  }

  /**
   * Generate template options.
   *
   * @param $entity
   *
   * @return array
   *   An array of templates indexed by id.
   */
  protected function getTemplates($entity) {
    $templates = [];

    try {
      $storage = \Drupal::entityTypeManager()->getStorage('applenews_template');
      $entity_ids = $storage->getQuery()
        ->condition('node_type', $entity->bundle())
        ->execute();
      $entities = $storage->loadMultiple($entity_ids);
      foreach ($entities as $entity) {
        $templates[$entity->id()] = $entity->label();
      }
    }
    catch (\Exception $e) {
      $this->logger()->error('Error loading templates: %code : %message', ['%code' => $e->getCode(), $e->getMessage()]);
    }


    return $templates;
  }

  /**
   * Logger.
   *
   * @return \Psr\Log\LoggerInterface
   */
  protected function logger() {
    return \Drupal::logger('applenews');
  }

}
