<?php

namespace Drupal\applenews\Form;

use Drupal\applenews\ApplenewsTemplateInterface;
use Drupal\applenews\Plugin\ApplenewsComponentTypeInterface;
use Drupal\applenews\Plugin\ApplenewsComponentTypeManager;
use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityInterface;
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

    $form['#prefix'] = '<div id="applenews-template-form-wrapper">';
    $form['#suffix'] = '<div>';

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

    $components = $template->getComponents();

    $form['components_list'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Components'),
    ];

    $form['components_list']['components_table'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Type'),
        $this->t('Field'),
        $this->t('Operations'),
        $this->t('Weight'),
      ],
      '#empty' => $this->t('This template has no components yet.'),
      '#prefix' => '<div id="components-fieldset-wrapper">',
      '#suffix' => '</div>',
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'component-weight',
        ]
      ],
    ];

    $rows = [];
    $delete_form = $form_state->get('delete_component');
    foreach($components as $id => $component) {
      $rows[$id]['#attributes']['class'][] = 'draggable';
      $rows[$id]['type'] = [
        '#markup' => $component['component_type'],
      ];
      $rows[$id]['field'] = [
        '#markup' => $component['field_name'],
      ];
      $rows[$id]['operations'] = [
        '#type' => 'actions',
      ];

      if ($delete_form === $id) {
        $rows[$id]['operations'] = $this->getComponentRowDeleteConfirmation();
      }
      else {
        $rows[$id]['operations']['delete'] = [
          '#type' => 'submit',
          '#value' => $this->t('delete'),
          '#name' => 'component_delete_' . $id,
          '#submit' => ['::setDeleteComponentForm'],
          '#ajax' => [
            'callback' => '::refreshComponentTable',
            'wrapper' => 'components-fieldset-wrapper',
          ],
        ];
      }
      $rows[$id]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => $component['weight'],
        '#attributes' => array('class' => array('component-weight')),
      ];
    }

    $form['components_list']['components_table'] += $rows;

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
      '#submit' => ['::setComponentFormStep'],
      '#name' => 'add_component_form',
      '#ajax' => [
        'callback' => '::addComponentForm',
        'wrapper' => 'add-components-fieldset-wrapper',
      ],
    ];

    $input = $form_state->getUserInput();
    $form_step = $form_state->get('form_step');
    if ($form_step == 'component_form') {
      $component_type = $input['component_type'];
      $component_plugin = $this->applenewsComponentTypeManager->createInstance($component_type);
      $form['add_components'] += $component_plugin->settingsForm($form, $form_state);
      unset($form['add_components']['add_component_form']);
      unset($form['add_components']['component_type']);
    }

    if ($form_step == 'component_form' || isset($input['save_component'])) {

      $form['add_components']['component_actions'] = [
        '#type' => 'actions',
      ];

      $form['add_components']['component_actions']['save_component'] = [
        '#type' => 'submit',
        '#value' => $this->t('Save Component'),
        '#name' => 'save_component',
        '#submit' => ['::addComponent'],
        '#ajax' => [
          'callback' => '::saveComponent',
          'wrapper' => 'applenews-template-form-wrapper',
        ],
      ];

      $form['add_components']['component_actions']['cancel'] = [
        '#type' => 'submit',
        '#value' => $this->t('Cancel'),
        '#button_type' => 'danger',
        '#submit' => ['::resetTempFormValues'],
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
    $this->saveComponentOrder($form_state);
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

  public function setComponentFormStep(array &$form, FormStateInterface $form_state) {
    $form_state->set('form_step', 'component_form');
    $form_state->setRebuild();
  }

  public function resetTempFormValues(array &$form, FormStateInterface $form_state) {
    $form_state->set('form_step', NULL);
    $form_state->set('delete_component', NULL);
    $form_state->setRebuild();
  }

  public function addComponentForm(array &$form, FormStateInterface $form_state) {
    return $form['add_components'];
  }

  public function refreshComponentTable(array &$form, FormStateInterface $form_state) {
    return $form['components_list']['components_table'];
  }

  public function addComponent(array &$form, FormStateInterface $form_state) {
    $form_state->set('form_step', NULL);
    if ($component = $this->getNewComponentValues($form_state)) {
      $this->entity->addComponent($component);
    }

    $this->entity->save();
    drupal_set_message('Component added successfully.');
    $form_state->setRebuild();
  }

  public function setDeleteComponentForm(array &$form, FormStateInterface $form_state) {
    $id = $this->getTriggeringRowIndex($form_state->getTriggeringElement());
    $form_state->set('delete_component', $id);
    $form_state->setRebuild();
  }

  public function deleteComponent(array &$form, FormStateInterface $form_state) {
    $form_state->set('delete_component', NULL);
    $id = $this->getTriggeringRowIndex($form_state->getTriggeringElement());
    $this->entity->deleteComponent($id);
    $this->saveComponentOrder($form_state);
    drupal_set_message('Component deleted.');
    $form_state->setRebuild();
  }

  public function saveComponent(array &$form, FormStateInterface $form_state) {
    // @todo return commands and replace both form and component table so we don't have to replace the whole form.
    return $form;
  }

  public function cancelComponentForm(array &$form, FormStateInterface $form_state) {
    return $form['add_components'];
  }

  protected function getNewComponentValues(FormStateInterface $form_state) {
    $values = $form_state->getValues();
    if (isset($values['new_component_type'])) {
      return [
        'component_type' => $values['new_component_type'],
        'weight' => 0,
        'field_name' => $values['component_field'],
        'component_layout' => [
          'column_start' => $values['column_start'],
          'column_span' => $values['column_span'],
        ],
      ];
    }

    return [];
  }

  protected function getTriggeringRowIndex(array $triggering_element) {
    return $triggering_element['#parents'][1];
  }

  /**
   * Get form elements to display to confirm a component will be deleted. Used
   * in the components list table.
   *
   * @return array
   *  The form array containing a Yes and Cancel button.
   */
  protected function getComponentRowDeleteConfirmation() {
    $operations = [];

    $operations['yes'] = [
      '#type' => 'submit',
      '#value' => $this->t('Yes'),
      '#submit' => ['::deleteComponent'],
      '#button_type' => 'primary',
      '#prefix' => '<span>' . $this->t('Are you sure?') . '</span>',
      '#ajax' => [
        'callback' => '::refreshComponentTable',
        'wrapper' => 'components-fieldset-wrapper',
      ],
    ];

    $operations['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#submit' => ['::resetTempFormValues'],
      '#ajax' => [
        'callback' => '::refreshComponentTable',
        'wrapper' => 'components-fieldset-wrapper',
      ],
    ];

    return $operations;
  }

  protected function saveComponentOrder(FormStateInterface $form_state) {
    $component_weights = $form_state->getValue('components_table');
    $components = $this->entity->getComponents();
    foreach ($components as $id => $component) {
      $components[$id]['weight'] = $component_weights[$id]['weight'];
    }
    $this->entity->setComponents($components);
    $this->entity->save();
  }
}
