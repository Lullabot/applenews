<?php

namespace Drupal\Tests\applenews\Functional;

/**
 * Tests node administration page functionality.
 *
 * @group applenews
 */
class AppleNewsTemplateAdminTest extends AppleNewsTestBase {

  /**
   * Tests template pages.
   */
  public function testAppleNewsTemplateAdminPages() {
    $assert_session = $this->assertSession();
    $this->drupalLogin($this->admin_user);

    // Verify overview page has empty message by default.
    $this->drupalGet('admin/config/services/applenews');
    $assert_session->statusCodeEquals(200);
    $assert_session->pageTextNotContains('There are no applenews template entities yet.');

    $assert_session->linkExists('Add Apple News Template');
  }

}
