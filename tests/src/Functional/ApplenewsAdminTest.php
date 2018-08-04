<?php

namespace Drupal\Tests\applenews\Functional;

/**
 * Tests node administration page functionality.
 *
 * @group applenews
 */
class AppleNewsAdminTest extends AppleNewsTestBase {

  /**
   * Tests admin pages.
   */
  public function testAppleNewsAdminPages() {
    $assert_session = $this->assertSession();
    $this->drupalLogin($this->admin_user);

    // Verify overview page.
    $this->drupalGet('admin/config');
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists('Apple News');
  }

}
