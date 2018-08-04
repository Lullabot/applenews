<?php

namespace Drupal\applenews;

/**
 * Class ApplenewsResponse
 *
 * @package Drupal\applenews
 */
class ApplenewsResponse {

  /**
   * @var string
   */
  protected $id;

  /**
   * @var
   */
  protected $created;

  /**
   * @var
   */
  protected $modified;

  /**
   * @var
   */
  protected $type;

  /**
   * @var
   */
  protected $shareUrl;

  /**
   * @var
   */
  protected $links;

  /**
   * @var
   */
  protected $sections;

  /**
   * ApplenewsResponse constructor.
   *
   * @param $id
   */
  public function __construct($id) {
    $this->id = $id;
  }

  /**
   * @param $response
   *
   * @code
   * stdClass::__set_state(array(
   * 'createdAt' => '2018-08-03T13:35:43Z',
   * 'modifiedAt' => '2018-08-03T13:35:43Z',
   * 'id' => 'c179e9c3-be60-434e-9e1d-d0de9276ad38',
   * 'type' => 'article',
   * 'shareUrl' => 'https://apple.news/AwXnpw75gQ06eHdDeknatOA',
   * 'links' =>
   * stdClass::__set_state(array(
   * 'channel' =>
   *   'https://news-api.apple.com/channels/aefc44a9-0c3a-4ca8-82ad-159b362b71d3',
   * 'self' =>
   *   'https://news-api.apple.com/articles/c179e9c3-be60-434e-9e1d-d0de9276ad38',
   * 'sections' =>
   * array (
   * 0 =>
   *   'https://news-api.apple.com/sections/09ef4e89-87a7-4aaf-8184-3d67a5e1f4ac',
   * ),
   * ))
   * @endcode
   *
   * @return \Drupal\applenews\ApplenewsResponse
   */
  public static function createFromResponse($response) {
    $object = new static(
      $response->id
    );
    $object->setCreated($response->createAt)
      ->setType($response->type)
      ->setModified($response->updatedAt)
      ->setShareUrl($response->shareUrl)
      ->setSections($response->links)
      ->setSections($response->sections);
    return $object;
  }


  /**
   * @return mixed
   */
  public function getCreated() {
    return $this->created;
  }

  /**
   * @param mixed $created
   *
   * @return $this
   */
  public function setCreated($created) {
    $this->created = $created;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getModified() {
    return $this->modified;
  }

  /**
   * @param mixed $modified
   *
   * @return $this
   */
  public function setModified($modified) {
    $this->modified = $modified;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @param mixed $type
   *
   * @return $this
   */
  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getShareUrl() {
    return $this->shareUrl;
  }

  /**
   * @param mixed $shareUrl
   *
   * @return $this
   */
  public function setShareUrl($shareUrl) {
    $this->shareUrl = $shareUrl;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getLinks() {
    return $this->links;
  }

  /**
   * @param mixed $links
   *
   * @return $this
   */
  public function setLinks($links) {
    $this->links = $links;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getSections() {
    return $this->sections;
  }

  /**
   * @param mixed $sections
   *
   * @return $this
   */
  public function setSections($sections) {
    $this->sections = $sections;
    return $this;
  }

  /**
   * @return string
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return array
   */
  public function toArray() {
    return [

    ];
  }

  /**
   * @return string
   */
  public function __toString() {
    return serialize($this->toArray());
  }

}