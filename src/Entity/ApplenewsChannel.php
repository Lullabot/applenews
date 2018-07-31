<?php

namespace Drupal\applenews\Entity;

use Drupal\applenews\ChannelInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the contact message entity.
 *
 * @ContentEntityType(
 *   id = "applenews_channel",
 *   label = @Translation("Applenews channel"),
 *   label_collection = @Translation("Applenews channels"),
 *   label_singular = @Translation("Applenews channel"),
 *   label_plural = @Translation("Applenews channels"),
 *   label_count = @PluralTranslation(
 *     singular = "@count Applenews channel",
 *     plural = "@count Applenews channels",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\applenews\ChannelListBuilder",
 *     "form" = {
 *       "default" = "Drupal\applenews\Form\ChannelForm",
 *       "delete" = "Drupal\applenews\Form\ChannelDeleteForm",
 *     }
 *   },
 *   base_table = "applenews_channel",
 *   admin_permission = "administer applenews channels",
 *   entity_keys = {
 *     "id" = "cid",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/services/applenews/settings/channel",
 *     "create" = "/admin/config/services/applenews/settings/channel/add",
 *     "edit-form" = "/admin/config/services/applenews/settings/channel/{applenews_channel}",
 *     "delete-form" = "/admin/config/services/applenews/settings/channel/{applenews_channel}/delete",
 *   }
 * )
 */
class ApplenewsChannel extends ContentEntityBase implements ChannelInterface {

  /**
   * Data.
   *
   * @var string
   */
  protected $createdAt;

  /**
   * Data.
   *
   * @var string
   */
  protected $modifiedAt;

  /**
   * Data.
   *
   * @var string
   */
  protected $id;

  /**
   * Data.
   *
   * @var string
   */
  protected $shareUrl;

  /**
   * Data.
   *
   * @var string
   */
  protected $type;

  /**
   * Data.
   *
   * @var string[]
   */
  protected $links;

  /**
   * Data.
   *
   * @var string
   */
  protected $name;


  /**
   * Data.
   *
   * @var string
   */
  protected $website;

  /**
   * {@inheritdoc}
   */
  public function getCreatedAt() {
    // Sample data: 2018-07-27T20:15:08Z
    return $this->get('createdAt')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getModifiedAt() {
    // Sample data: 2018-07-27T20:15:34Z
    return $this->get('modifiedAt')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getId(){
    return $this->get('id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getType(){
    return $this->get('type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getShareUrl() {
    return $this->get('shareUrl')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getLinks(){
    return $this->get('links')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getName(){
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getWebsite(){
    return $this->get('website')->value;
  }

  public function save() {
    $storage = $this->entityTypeManager()->getStorage($this->entityTypeId);
    return $storage->save($this);
  }

  /**
   * @param $response
   *
   * @return $this
   */
  public function updateFromResponse($response) {
    if (is_object($response) && isset($response->data)) {
      $this->createdAt = $response->data->createdAt;
      $this->modifiedAt = $response->data->modifiedAt;
      $this->id = $response->data->id;
      $this->type = $response->data->type;
      $this->shareUrl = $response->data->shareUrl;
      $this->links = [
       'defaultSection' => $response->data->links->defaultSection,
        'self' => $response->data->links->self,
      ];
      $this->name = $response->data->name;
      $this->website = $response->data->website;
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    /** @var \Drupal\Core\Field\BaseFieldDefinition[] $fields */
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uuid']->setDescription(new TranslatableMarkup('The channel UUID.'));

    $fields['id'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('ID'))
      ->setReadOnly(TRUE);
    
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Name'))
      ->setTranslatable(TRUE)
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['createdAt'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup("The channel created"))
      ->setSetting('max_length', 20)
      ->setDescription(new TranslatableMarkup('The created time of the channel. e.g. 2018-07-27T20:15:34Z'));
    
    $fields['modifiedAt'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup("The channel modified"))
      ->setSetting('max_length', 20)
      ->setDescription(new TranslatableMarkup('The modified time of the channel. e.g. 2018-07-27T20:15:34Z'));

    $fields['type'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup("The channel type"))
      ->setSetting('max_length', 10)
      ->setDescription(new TranslatableMarkup('The type of the channel.'));

    $fields['shareUrl'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup("The channel share URL"))
      ->setDescription(new TranslatableMarkup('The share URL of the channel. e.g. https://apple.news/DedSkwdsQrdSWbNitx0w'));

    $fields['links'] = BaseFieldDefinition::create('string_long')
      ->setLabel(new TranslatableMarkup("The channel links"))
      ->setDescription(new TranslatableMarkup('An array of links. Allowed index are self, defaultSection'));

    $fields['website'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup("The channel share URL"))
      ->setDescription(new TranslatableMarkup('The share URL of the channel. e.g. https://apple.news/DedSkwdsQrdSWbNitx0w'));

    return $fields;
  }

}