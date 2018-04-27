<?php

namespace Drupal\applenews\Form;

use Drupal\applenews\Plugin\ApplenewsComponentTypeManager;
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

    $form['layout'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Layout'),
    ];


    $layout = $template->getLayout();

    $form['layout']['columns'] = [
      '#type' => 'number',
      '#title' => $this->t('Columns'),
      '#description' => $this->t('The number of columns this article was designed for. You must have at least one column.'),
      '#required' => TRUE,
      '#min' => 1,
    ];

    $component_types = $this->applenewsComponentTypeManager->getDefinitions();

    // You will need additional form elements for your custom properties.
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
    $entity = $this->entityTypeManager->getStorage('example')->getQuery()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }
}
