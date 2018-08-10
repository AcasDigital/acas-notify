<?php

namespace Drupal\webform_date_composite\Tests;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\webform\Tests\WebformTestBase;

/**
 * Tests for webform date composite.
 *
 * @group Webform
 */
class WebformDateCompositeTest extends WebformTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['webform_date_composite'];

  /**
   * Tests webform date element.
   */
  public function testWebformDateComposite() {
    $webform = Webform::load('webform_date_composite');

    // Check form element rendering.
    $this->drupalGet('webform/webform_date_composite');
    // NOTE:
    // This is a very lazy but easy way to check that the element is rendering
    // as expected.
    $this->assertRaw('<label for="edit-webform-date-composite-first-name">First name</label>');
    $this->assertFieldById('edit-webform-date-composite-first-name');
    $this->assertRaw('<label for="edit-webform-date-composite-last-name">Last name</label>');
    $this->assertFieldById('edit-webform-date-composite-last-name');
    $this->assertRaw('<label for="edit-webform-date-composite-date-of-birth">Date of birth</label>');
    $this->assertFieldById('edit-webform-date-composite-date-of-birth');
    $this->assertRaw('<label for="edit-webform-date-composite-gender">Gender</label>');
    $this->assertFieldById('edit-webform-date-composite-gender');

    // Check webform element submission.
    $edit = [
      'webform_date_composite[first_name]' => 'John',
      'webform_date_composite[last_name]' => 'Smith',
      'webform_date_composite[gender]' => 'Male',
      'webform_date_composite[date_of_birth]' => '1910-01-01',
      'webform_date_composite_multiple[items][0][first_name]' => 'Jane',
      'webform_date_composite_multiple[items][0][last_name]' => 'Doe',
      'webform_date_composite_multiple[items][0][gender]' => 'Female',
      'webform_date_composite_multiple[items][0][date_of_birth]' => '1920-12-01',
    ];
    $sid = $this->postSubmission($webform, $edit);
    $webform_submission = WebformSubmission::load($sid);
    $this->assertEqual($webform_submission->getElementData('webform_date_composite'), [
      'first_name' => 'John',
      'last_name' => 'Smith',
      'gender' => 'Male',
      'date_of_birth' => '1910-01-01',
    ]);
    $this->assertEqual($webform_submission->getElementData('webform_date_composite_multiple'), [
      [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'gender' => 'Female',
        'date_of_birth' => '1920-12-01',
      ],
    ]);
  }

}
