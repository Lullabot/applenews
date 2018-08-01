<?php

namespace Drupal\applenews;

/**
 * Applenews publisher manager.
 *
 * @package Drupal\applenews
 */
interface PublisherInterface {

  /**
   * @param string $channel_id
   *   Channel ID
   *
   * @see vendor/chapter-three/apple-news-api/examples/PublisherAPI/GetChannel.php
   *
   * @return mixed
   */
  public function getChannel($channel_id);

  /**
   * Retrieve article.
   *
   * @see vendor/chapter-three/apple-news-api/examples/PublisherAPI/GetArticle.php
   *
   * @return mixed
   */
  public function getArticle();

  /**
   *
   * @see vendor/chapter-three/apple-news-api/examples/PublisherAPI/GetSection.php
   *
   * @return mixed
   */
  public function getSection();

  /**
   *
   *  @see vendor/chapter-three/apple-news-api/examples/PublisherAPI/GetSections.php
   *
   * @param $channel_id
   *
   * @return mixed
   */
  public function getSections($channel_id);

  /**
   *
   * @see vendor/chapter-three/apple-news-api/examples/PublisherAPI/PostArticle.php
   *
   * @param $channel_id
   * @param $data
   *
   * @return mixed
   */
  public function postArticle($channel_id, $data);

  /**
   *
   *  @see vendor/chapter-three/apple-news-api/examples/PublisherAPI/UpdateArticle.php
   *
   * @return mixed
   */
  public function updateArticle();

}
