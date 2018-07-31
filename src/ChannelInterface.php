<?php

namespace Drupal\applenews;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining a channel entity.
 *
 * @code
*  [createdAt] => 2018-07-27T20:15:08Z
 * [modifiedAt] => 2018-07-27T20:15:34Z
 * [id] => aefc44a9-0c3a-4ca8-82ad-159b362b71d3
 * [type] => channel
 * [shareUrl] => https://apple.news/TrvxEqQw6TKiCrRWbNitx0w
 * [links] => stdClass Object (
 *  [defaultSection] => https://news-api.apple.com/sections/09ef4e89-87a7-4aaf-8184-3d67a5e1f4ac
 *  [self] => https://news-api.apple.com/channels/aefc44a9-0c3a-4ca8-82ad-159b362b71d3
 * )
 * [name] => Playground
 * [website] =>
 * @endcode
 * 
 */
interface ChannelInterface extends ContentEntityInterface {

  /**
   * @return string
   */
  public function getCreatedAt();

  /**
   * @return string
   */
  public function getModifiedAt();

  /**
   * @return string
   */
  public function getId();

  /**
   * @return string
   */
  public function getType();

  /**
   * @return string
   */
  public function getShareUrl();

  /**
   * @return string[]
   */
  public function getLinks();

  /**
   * @return string
   */
  public function getName();

  /**
   * @return string
   */
  public function getWebsite();

}
