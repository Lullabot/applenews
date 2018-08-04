<?php

namespace Drupal\Tests\applenews\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\user\RoleInterface;

/**
 * Setup users and configurations.
 */
abstract class ApplenewsTestBase extends BrowserTestBase {
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
  public static $modules = ['applenews', 'serialization', 'block'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->admin_user = $this->drupalCreateUser([
      'access administration pages',
      'administer applenews configuration',
      'administer applenews templates',
      'administer applenews channels',
    ]);
    $this->base_user = $this->drupalCreateUser([]);

    $this->drupalPlaceBlock('local_actions_block');
    $this->drupalPlaceBlock('local_tasks_block');
  }

}
