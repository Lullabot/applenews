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
  public function getArticle($article_id) {
    $response = $this->publisher()->get('/articles/{article_id}', ['article_id' => $article_id]);
    return $this->handleResponse($response);
  }

  /**
   * {@inheritdoc}
   */
  public function getSection($section_id) {
    $response = $this->publisher()->get('/sections/{section_id}', ['section_id' => $section_id]);
    return $this->handleResponse($response);
  }

  /**
   * {@inheritdoc}
   */
  public function getSections($channel_id) {
    $response = $this->publisher()->get('/channels/{channel_id}/sections', ['channel_id' => $channel_id]);
    return $this->handleResponse($response);

  }

  /**
   * {@inheritdoc}
   */
  public function postArticle($channel_id, $data) {
    $response = $this->publisher()->post('/channels/{channel_id}/articles', ['channel_id' => $channel_id], $data);
    return $this->handleResponse($response);

  }

  /**
   * {@inheritdoc}
   */
  public function updateArticle($article_id, $data) {
    $response = $this->publisher()->post('/articles/{article_id}', ['article_id' => $article_id], $data);
    return $this->handleResponse($response);
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
      // Update to handle different error cases.
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
