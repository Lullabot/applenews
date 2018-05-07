<?php

namespace Drupal\applenews\Form;

use Drupal\applenews\ApplenewsTemplateSelection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApplenewsPreviewForm extends FormBase {

  /**
   * @var
   */
  protected $applenewsTemplateSelection;

  public function __construct(ApplenewsTemplateSelection $template_selection) {
    $this->applenewsTemplateSelection = $template_selection;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('applenews.template_selection')
    );
  }

  public function getFormId() {
    return 'applenew_preview_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {

    $form['applenews_template'] = $this->applenewsTemplateSelection->getTemplateSelectionElement($node->bundle());

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
