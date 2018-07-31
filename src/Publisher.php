<?php

namespace Drupal\applenews;

use ChapterThree\AppleNewsAPI\PublisherAPI;
use Drupal\applenews\Exception\ApplenewsInvalidResponseException;
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
   * Construct the PublisherManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('applenews.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getChannel($channel_id) {
    $response = $this->publisher()->get('/channels/{channel_id}', ['channel_id' => $channel_id]);
    return $this->handleResponse($response);
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
  public function GetSections($channel_id) {
    $response = $this->publisher()->get('/channels/{channel_id}/sections', ['channel_id' => $channel_id]);
    return $this->handleResponse($response);

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

  /**
   * @param $response
   *
   * @return mixed
   * @throws \Drupal\applenews\Exception\ApplenewsInvalidResponseException
   */
  protected function handleResponse($response) {
    if (isset($response->errors) && is_array($response->errors)) {
      $error = current($response->errors);
      throw new ApplenewsInvalidResponseException($error->code, '500');
    }
    return $response;
  }

  /**
   * @return \ChapterThree\AppleNewsAPI\PublisherAPI
   */
  protected function publisher() {
    return new PublisherAPI($this->config->get('api_key'), $this->config->get('api_secret'), $this->config->get('endpoint'));
  }

}
