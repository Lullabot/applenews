<?php

namespace Drupal\applenews\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 *
 */
abstract class ApplenewsNormalizerBase implements NormalizerInterface, SerializerAwareInterface {
  use SerializerAwareTrait;

  /**
   * Name of the format that this normalizer deals with.
   */
  protected $format = 'applenews';

}
