<?php

namespace Drupal\applenews\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a default applenews widget.
 *
 * @FieldWidget(
 *   id = "applenews_default",
 *   label = @Translation("Applenews"),
 *   field_types = {
 *     "applenews_default"
 *   }
 * )
 */
class Applenews extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'status' => FALSE,
        'template' => '',
        'channels' => '',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $entity = $items->getEntity();
    $element['#attached']['library'][] = 'applenews/drupal.applenews.admin';

    $element['status'] = [
      '#type' => 'checkbox',
      '#title' => t('Enabled'),
      '#default_value' => $items->status,
      '#attributes' => [
        'class' => ['applenews-publish-flag']
      ],
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
    $element['channel'] = [
      '#type' => 'container',
      '#prefix' => '<strong>' . t('Default channels and sections') . '</strong>',
    ];
    foreach ($this->getChannels() as $channel_id => $channel ) {
      $channel_key = 'channel-' . $channel_id;
      /** @var \Drupal\applenews\Entity\ApplenewsChannel $channel */
      $element['channel'][$channel_key] = [
        '#type' => 'checkbox',
        '#title' => $channel->getName(),
        '#default_value' => $items->{$channel_key},
        '#attributes' => [
          'data-channel-id' => $channel_key
        ],
      ];
      foreach ($channel->getSections() as $section_id => $section_label) {
        $section_key = 'section-' . $section_id;
        $element['channel'][$section_key] = [
          '#type' => 'checkbox',
          '#title' => $section_label,
          '#default_value' => $items->{$section_key},
          '#attributes' => [
            'data-section-of' => $channel_key,
            'class' => ['applenews-sections'],
          ],
        ];
      }

    }

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
   * Generate channel options.
   *
   *
   * @return array
   *   An array of channel indexed by id.
   */
  protected function getChannels() {
    $channels = [];

    try {
      $storage = \Drupal::entityTypeManager()->getStorage('applenews_channel');
      $entity_ids = $storage->getQuery()->execute();
      $channels = $storage->loadMultiple($entity_ids);
    }
    catch (\Exception $e) {
      $this->logger()->error('Error loading channel: %code : %message', ['%code' => $e->getCode(), $e->getMessage()]);
    }

    return $channels;
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
