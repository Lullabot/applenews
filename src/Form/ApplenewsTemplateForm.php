<?php

namespace Drupal\applenews\Form;

use Drupal\applenews\Plugin\ApplenewsComponentTypeManager;
use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApplenewsTemplateForm extends EntityForm {

  /**
   * @var ApplenewsComponentTypeManager
   */
  protected $applenewsComponentTypeManager;

  /**
   * @var EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs an ApplenewsTemplateForm object.
   *
   * @param ApplenewsComponentTypeManager $component_type_manager
   */
  public function __construct(ApplenewsComponentTypeManager $component_type_manager, EntityTypeManager $entity_type_manager) {
    $this->applenewsComponentTypeManager = $component_type_manager;
    $this->entityTypeManager = $entity_type_manager;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.applenews_component_type'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $template = $this->entity;
    $node_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $template->label(),
      '#description' => $this->t("Label for this template."),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $template->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$template->isNew(),
    ];

    $node_type_options = [];
    foreach ($node_types as $id => $node_type) {
      $node_type_options[$id] = $node_type->label();
    }

    $form['node_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Node Type'),
      '#description' => $this->t('The node type to which this template should apply.'),
      '#options' => $node_type_options,
    ];



    $form['view_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('View Mode'),
      '#description' => $this->t('The view mode used to render content for this template.'),
      '#options' => $this->getViewModeOptions(),
    ];

    $form['layout'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Layout'),
      '#description' => $this->t('For more information: <a href="https://developer.apple.com/library/content/documentation/General/Conceptual/Apple_News_Format_Ref/Layout.html#//apple_ref/doc/uid/TP40015408-CH65-SW1">https://developer.apple.com/library/content/documentation/General/Conceptual/Apple_News_Format_Ref/Layout.html#//apple_ref/doc/uid/TP40015408-CH65-SW1</a>'),
    ];


    $layout = $template->getLayout();

    $form['layout']['columns'] = [
      '#type' => 'number',
      '#title' => $this->t('Columns'),
      '#description' => $this->t('The number of columns this article was designed for. You must have at least one column.'),
      '#required' => TRUE,
      '#min' => 1,
      '#default_value' => $layout['columns'] ? $layout['columns'] : 7,
    ];

    $form['layout']['width'] = [
      '#type' => 'number',
      '#title' => $this->t('Width'),
      '#description' => $this->t('The width (in points) this article was designed for. This property is used to calculate down-scaling scenarios for smaller devices.'),
      '#required' => TRUE,
      '#default_value' => $layout['width'] ? $layout['width'] : 1024,
      '#min' => 1,
    ];

    $form['layout']['gutter'] = [
      '#type' => 'number',
      '#title' => $this->t('Gutter'),
      '#description' => $this->t('The gutter size for the article (in points). The gutter provides spacing between columns.'),
      '#default_value' => $layout['gutter'] ? $layout['gutter'] : 20,
    ];

    $form['layout']['margin'] = [
      '#type' => 'number',
      '#title' => $this->t('Margin'),
      '#description' => $this->t('The outer (left and right) margins of the article, in points.'),
      '#default_value' => $layout['margin'] ? $layout['margin'] : 60,
    ];

    $component_num = $form_state->get('component_num');
    if (empty($component_num)) {
      $form_state->set('component_num', 0);
    }

    $form['components'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Components'),
      '#prefix' => '<div id="components-fieldset-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    ];

    $form['add_components'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Add Components'),
      '#prefix' => '<div id="add-components-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['add_components']['component_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Component types'),
      '#options' => $this->getComponentOptions(),
    ];

    $form['add_components']['add_component_form'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add new component'),
      '#ajax' => [
        'callback' => '::addComponentForm',
        'wrapper' => 'add-components-fieldset-wrapper',
      ],
    ];

    $input = $form_state->getUserInput();
    if (isset($input['component_type'])) {
      $component_type = $input['component_type'];
      $component_plugin = $this->applenewsComponentTypeManager->createInstance($component_type);
      $form['add_components'] += $component_plugin->settingsForm($form, $form_state);
      $form['add_components']['component_type']['#attributes'] = ['disabled' => TRUE];
      $form['add_components']['add_component_form']['#attributes'] = ['disabled' => TRUE];

      $form['add_components']['component_actions'] = [
        '#type' => 'actions',
      ];

      $form['add_components']['component_actions']['save_component'] = [
        '#type' => 'submit',
        '#value' => $this->t('Save Component'),
        '#submit' => ['::addComponent'],
        '#ajax' => [
          'callback' => '::saveComponent',
          'wrapper' => 'add-components-fieldset-wrapper',
        ],
      ];

      $form['add_components']['component_actions']['cancel'] = [
        '#type' => 'submit',
        '#value' => $this->t('Cancel'),
        '#button_type' => 'danger',
        '#ajax' => [
          'callback' => '::cancelComponentForm',
          'wrapper' => 'add-components-fieldset-wrapper',
        ],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $template = $this->entity;
    $status = $template->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label Template.', [
        '%label' => $template->label(),
      ]));
    }
    else {
      drupal_set_message($this->t('The %label Template was not saved.', [
        '%label' => $template->label(),
      ]));
    }

    $form_state->setRedirect('entity.applenews_template.collection');
  }

  /**
   * Helper function to check whether an Example configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityTypeManager->getStorage('applenews_template')->getQuery()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

  protected function getComponentOptions() {
    $component_options = [];
    foreach ($this->applenewsComponentTypeManager->getDefinitions() as $id => $component_type) {
      $component_options[$id] = $component_type['label'];
    }
    return $component_options;
  }

  protected function getViewModeOptions() {
    $view_mode_ids = $this->entityTypeManager->getStorage('entity_view_mode')->getQuery()
      ->condition('targetEntityType', 'node')
      ->execute();

    $view_modes = EntityViewMode::loadMultiple($view_mode_ids);
    $view_mode_options = [];
    foreach ($view_modes as $id => $view_mode) {
      $view_mode_options[$id] = $view_mode->label();
    }

    return $view_mode_options;
  }

  public function rebuildForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

  public function addComponentForm(array &$form, FormStateInterface $form_state) {
    return $form['add_components'];
  }

  public function saveComponent(array &$form, FormStateInterface $form_state) {
    return $form;
  }

  public function cancelComponentForm(array &$form, FormStateInterface $form_state) {
    return $form['add_components'];
  }
}
