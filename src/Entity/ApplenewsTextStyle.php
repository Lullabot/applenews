<?php

namespace Drupal\applenews\Entity;

use ChapterThree\AppleNewsAPI\Document\Styles\TextStyle;
use Drupal\applenews\ApplenewsTextStyleInterface;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines an Apple News text style configuration entity.
 *
 * @ConfigEntityType(
 *   id = "applenews_text_style",
 *   label = @Translation("Apple News text style"),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\applenews\Form\TextStyleForm",
 *       "edit" = "Drupal\applenews\Form\TextStyleForm",
 *       "delete" = "Drupal\applenews\Form\TextStyleDeleteForm",
 *     },
 *     "list_builder" = "Drupal\applenews\TextStyleListBuilder",
 *     "storage" = "Drupal\applenews\TextStyleStorage",
 *   },
 *   config_prefix = "text_style",
 *   admin_permission = "administer applenews text styles",
 *   entity_keys = {
 *     "id" = "name",
 *     "label" = "label"
 *   },
 *   links = {
 *     "collection" = "/admin/config/services/applenews/text-style",
 *     "add-form" = "/admin/config/services/applenews/text-style/add",
 *     "edit-form" = "/admin/config/services/applenews/text-style/{applenews_text_style}",
 *     "delete-form" = "/admin/config/services/applenews/text-style/{applenews_text_style}/delete",
 *   }
 * )
 */
class ApplenewsTextStyle extends ConfigEntityBase implements ApplenewsTextStyleInterface {

  /**
   * The name of the text style.
   *
   * @var string
   */
  protected $name;

  /**
   * The image style label.
   *
   * @var string
   */
  protected $label;

  /**
   * @var
   */
  protected $fontName;

  /**
   * @var
   */
  protected $fontSize;

  /**
   * @var
   */
  protected $textColor;

  /**
   * @var
   */
  protected $textShadow;

  /**
   * @var
   */
  protected $textTransform;

  /**
   * @var
   */
  protected $underline;

  /**
   * @var
   */
  protected $strikethrough;

  /**
   * @var
   */
  protected $stroke;

  /**
   * @var
   */
  protected $backgroundColor;

  /**
   * @var
   */
  protected $verticalAlignment;

  /**
   * @var
   */
  protected $tracking;

  /**
   * @var
   */
  protected $textAlignment;

  /**
   * @var
   */
  protected $lineHeight;

  /**
   * @var
   */
  protected $dropCapStyle;

  /**
   * @var
   */
  protected $hyphenation;

  /**
   * @var
   */
  protected $linkStyle;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function toObject() {
    $object = new TextStyle();
    foreach (get_object_vars($this) as $field => $value) {
      $method = 'set' . ucfirst($field);
      if ($value && method_exists($object, $method)) {
        $object->{$method}($value);
      }
    }
    return $object;
  }

}
