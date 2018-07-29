<?php

namespace Drupal\Tests\applenews\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\user\RoleInterface;

/**
 * Setup users and configurations.
 */
abstract class AppleNewsTestBase extends BrowserTestBase {
  /**
   * A user with permission to bypass access content.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $admin_user;

  /**
   * A normal user with permission to bypass node access content.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $base_user;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['applenews', 'serialization'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->admin_user = $this->drupalCreateUser(['access administration pages', 'administer applenews templates']);
    $this->base_user = $this->drupalCreateUser([]);
  }

}
