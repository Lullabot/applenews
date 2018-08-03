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
   * @param $article_id
   *   Unique article UUID.
   *
   * @see vendor/chapter-three/apple-news-api/examples/PublisherAPI/GetArticle.php
   *
   * @return mixed
   */
  public function getArticle($article_id);

  /**
   * Retrieves section details.
   *
   * @param $section_id
   *   Unique section UUID.
   *
   * @see vendor/chapter-three/apple-news-api/examples/PublisherAPI/GetSection.php
   *
   * @return mixed
   */
  public function getSection($section_id);

  /**
   *
   *  @see vendor/chapter-three/apple-news-api/examples/PublisherAPI/GetSections.php
   *
   * @param $channel_id
   *   Unique channel UUID.
   *
   * @return mixed
   */
  public function getSections($channel_id);

  /**
   * Creates new article.
   *
   * @see vendor/chapter-three/apple-news-api/examples/PublisherAPI/PostArticle.php
   *
   * @param $channel_id
   *   Unique channel UUID.
   * @param $data
   *   An array of data to post.
   *
   * @return mixed
   */
  public function postArticle($channel_id, $data);

  /**
   * Update an existing article.
   *
   *  @see vendor/chapter-three/apple-news-api/examples/PublisherAPI/UpdateArticle.php
   *
   * @param $article_id
   *   Unique article UUID.
   * @param $data
   *   An array of article data.
   *
   * @return mixed
   */
  public function updateArticle($article_id, $data);

}
