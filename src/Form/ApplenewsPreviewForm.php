<?php

namespace Drupal\applenews\Form;

use Drupal\applenews\ApplenewsTemplateSelection;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ApplenewsPreviewForm.
 *
 * @package Drupal\applenews\Form
 */
class ApplenewsPreviewForm extends FormBase {

  /**
   * @var \Drupal\applenews\ApplenewsTemplateSelection
   */
  protected $applenewsTemplateSelection;

  /**
   * @var \Symfony\Component\Serializer\Serializer
   */
  protected $serializer;

  /**
   * @param \Drupal\applenews\ApplenewsTemplateSelection $template_selection
   * @param \Symfony\Component\Serializer\Serializer $serializer
   */
  public function __construct(ApplenewsTemplateSelection $template_selection, Serializer $serializer) {
    $this->applenewsTemplateSelection = $template_selection;
    $this->serializer = $serializer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('applenews.template_selection'),
      $container->get('serializer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'applenews_preview_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    $form_state->set('node_to_preview', $node);

    $form['applenews_template'] = $this->applenewsTemplateSelection->getTemplateSelectionElement($node->bundle());

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['download'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $node = $form_state->get('node_to_preview');
    $context = [];
    $context['template_id'] = $form_state->getValue('applenews_template');

    /** @var \ChapterThree\AppleNewsAPI\Document $document */
    $document = $this->serializer->normalize($node, 'applenews', $context);

    $directory = 'public://applenews_preview';
    $filename = 'applenews-node-' . $node->id() . '-' . $context['template_id'] . '.json';
    $file = NULL;
    if (file_prepare_directory($directory, FILE_CREATE_DIRECTORY + FILE_MODIFY_PERMISSIONS)) {
      $file = file_unmanaged_save_data(Json::encode($document), $directory . '/' . $filename, FILE_EXISTS_REPLACE);
    }

    if (!$file) {
      $form_state->setErrorByName('applenews_template', $this->t('There was an error when saving the preview file. Please check the logs.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $node = $form_state->get('node_to_preview');
    $form_state->setRedirect('applenews.preview_download', ['node' => $node->id(), 'template_id' => $form_state->getValue('applenews_template')]);
  }

}
