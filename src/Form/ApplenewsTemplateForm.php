<?php

namespace Drupal\applenews\Form;

use Drupal\applenews\Plugin\ApplenewsComponentTypeManager;
use Drupal\Component\Serialization\Json;
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
      '#default_value' => $template->node_type,
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
        $this->t('Data Mapping'),
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
        '#markup' => $component['id'],
      ];
      $rows[$id]['field'] = [
        '#markup' => $this->displayComponentData($component),
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
    $component_type = $form_state->get('sub_form_component_type');
    if ($component_type) {
      $component_plugin = $this->applenewsComponentTypeManager->createInstance($component_type);
      $form['add_components'] += $component_plugin->settingsForm($form, $form_state);
      unset($form['add_components']['add_component_form']);
      unset($form['add_components']['component_type']);
    }

    if ($component_type || isset($input['save_component'])) {

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

  /**
   * Get all the available Applenews component plugins to use in a select element.
   *
   * @return array
   *  An array of component options suitable for a select element.
   */
  protected function getComponentOptions() {
    $component_options = [];
    foreach ($this->applenewsComponentTypeManager->getDefinitions() as $id => $component_type) {
      $component_options[$id] = $component_type['label'];
    }
    return $component_options;
  }

  /**
   * Ajax submit handler when someone clicks "Add new component". Stores the
   * selected component type for later use.
   */
  public function setComponentFormStep(array &$form, FormStateInterface $form_state) {
    $input = $form_state->getUserInput();
    $form_state->set('sub_form_component_type', $input['component_type']);
    $form_state->setRebuild();
  }

  /**
   * Ajax submit handler used when a "Cancel" button is clicked.
   */
  public function resetTempFormValues(array &$form, FormStateInterface $form_state) {
    $form_state->set('sub_form_component_type', NULL);
    $form_state->set('delete_component', NULL);
    $form_state->setRebuild();
  }

  /**
   * Ajax callback responsible for displaying the component form. Triggered when
   * the "Add new component" button is clicked.
   */
  public function addComponentForm(array &$form, FormStateInterface $form_state) {
    return $form['add_components'];
  }

  /**
   * Ajax callback respsonsible for returning the updated components table.
   * Triggered when either the "delete" button or either of its confirmation
   * buttons are clicked.
   */
  public function refreshComponentTable(array &$form, FormStateInterface $form_state) {
    return $form['components_list']['components_table'];
  }

  /**
   * Ajax submit handler responsible for saving a new component. Triggered when
   * the "Save Component" button is clicked.
   */
  public function addComponent(array &$form, FormStateInterface $form_state) {
    $form_state->set('sub_form_component_type', NULL);
    if ($component = $this->getNewComponentValues($form_state)) {
      $this->entity->addComponent($component);
    }

    $this->entity->save();
    drupal_set_message('Component added successfully.');
    $form_state->setRebuild();
  }

  /**
   * Ajax callback that refreshed the whole form. Triggered when
   * the "Save Component" button is clicked.
   */
  public function saveComponent(array &$form, FormStateInterface $form_state) {
    // @todo return commands and replace both form and component table so we don't have to replace the whole form.
    return $form;
  }

  /**
   * Ajax callback that gets rid of the new component form. Triggered when the
   * "Cancel" button is clicked on the new component form.
   */
  public function cancelComponentForm(array &$form, FormStateInterface $form_state) {
    return $form['add_components'];
  }

  /**
   * Ajax submit handler that stores the row the "delete" button was clicked on.
   */
  public function setDeleteComponentForm(array &$form, FormStateInterface $form_state) {
    $this->saveComponentOrder($form_state);
    $id = $this->getTriggeringRowIndex($form_state->getTriggeringElement());
    $form_state->set('delete_component', $id);
    $form_state->setRebuild();
  }

  /**
   * Ajax submit handler responsible for deleting a component from the table
   * and entity. Triggered when the "Yes" button is clicked in confirmation.
   */
  public function deleteComponent(array &$form, FormStateInterface $form_state) {
    $form_state->set('delete_component', NULL);
    $id = $this->getTriggeringRowIndex($form_state->getTriggeringElement());
    $this->entity->deleteComponent($id);
    if (count($this->entity->getComponents()) == 0) {
      $this->entity->save();
    }
    else {
      $this->saveComponentOrder($form_state);
    }
    drupal_set_message('Component deleted.');
    $form_state->setRebuild();
  }

  /**
   * Format the values from a newly added component into an array usable for
   * an AppleTemplate.
   *
   * @param FormStateInterface $form_state
   * @return array
   *  An array in the proper format to pass to AppleTemplate::addComponent()
   */
  protected function getNewComponentValues(FormStateInterface $form_state) {
    $values = $form_state->getValues();
    if (isset($values['component_settings']['id'])) {
      $components = $this->entity->getComponents();
      $last_component = end($components);
      return [
        'uuid' => \Drupal::service('uuid')->generate(),
        'id' => $values['component_settings']['id'],
        'weight' => $last_component['weight'] + 1,
        'component_layout' => $values['component_settings']['component_layout'],
        'component_data' => $values['component_settings']['component_data'],
      ];
    }

    return [];
  }

  /**
   * Helper function to get the parent of a button that was pressed. Used for
   * component deletion and confirmation forms.
   *
   * @param array $triggering_element
   * @return string
   */
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

  /**
   * Sorts the components based on their new weights from the draggable table.
   *
   * @param FormStateInterface $form_state
   */
  protected function saveComponentOrder(FormStateInterface $form_state) {
    $component_weights = $form_state->getValue('components_table');
    $components = $this->entity->getComponents();
    if ($components) {
      foreach ($components as $id => $component) {
        $components[$id]['weight'] = $component_weights[$id]['weight'];
      }
      $this->entity->setComponents($components);
      $this->entity->save();
    }
  }

  /**
   * Return formatted component data as a summary to be used in the component
   * table.
   *
   * @param array $component
   * @return string
   */
  protected function displayComponentData($component) {
    $return = '';
    foreach ($component['component_data'] as $key => $data) {
      if (is_array($data)) {
        $data = Json::encode($data);
      }
      $return .= $key . ': ' . $data . '<br />';
    }
    return $return;
  }
}
