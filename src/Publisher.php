<?php

namespace Drupal\applenews;

use ChapterThree\AppleNewsAPI\PublisherAPI;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Applenews publisher manager.
 */
class Publisher implements PublisherInterface {
  use StringTranslationTrait;

  /**
   * The applenews settings config object.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The applenews publisher API object.
   *
   * @var \ChapterThree\AppleNewsAPI\PublisherAPI
   */
  protected $publisher;

  /**
   * Construct the PublisherManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $config = $config_factory->get('applenews.settings');
    $this->publisher = new PublisherAPI($config->get('api_key'), $config->get('api_secret'), $config->get('endpoint'));
  }

  /**
   * {@inheritdoc}
   */
  public function getChannel($channel_id) {
    return $this->publisher->get('/channels/{channel_id}', ['channel_id' => $channel_id]);
  }

  /**
   * {@inheritdoc}
   */
  public function getArticle() {

  }

  /**
   * {@inheritdoc}
   */
  public function GetSection() {

  }

  /**
   * {@inheritdoc}
   */
  public function GetSections() {

  }

  /**
   * {@inheritdoc}
   */
  public function postArticle() {

  }

  /**
   * {@inheritdoc}
   */
  public function updateArticle() {

  }

}
