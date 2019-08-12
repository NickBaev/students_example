<?php

namespace Drupal\Tests\students\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests for the Students module.
 *
 * @group students
 */
class StudentsTest extends BrowserTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  protected static $modules = ['students'];

  /**
   * A simple user.
   *
   * @var \Drupal\user\Entity\User
   */
  private $user;

  /**
   * Test student data
   */
  private $student_data;

  /**
   * Perform initial setup tasks that run before every test method.
   */
  public function setUp() {
    parent::setUp();
    // Create user
    $this->user = $this->drupalCreateUser([
      'administer site configuration',
      'access content'
    ]);

    //Insert 1 student into db
    $config = \Drupal::config('students.settings');
    $table = $config->get('table.name');
    $query = \Drupal::database()->upsert($table);
    $this->student_data = [
      'id' => "0aab2c32b6039246ed14803715ec612f",
      'name' => "Тест",
      'surname' => "Тестов",
      'middlename' => "Тестович",
      'education_type' => "очная",
      'birthdate' => "1995-01-01"
    ];
    $query->fields($this->student_data);
    $query->key('id');
    $query->execute();
  }

  /**
   * Tests that the students/all page can be reached.
   */
  public function testStudentsPageExists() {
    $this->drupalLogin($this->user);

    $this->drupalGet('students/all');
    $this->assertSession()->statusCodeEquals(200);
    $config = $this->config('students.settings');
    $this->assertSession()->pageTextContains($config->get('students.page_title'));

    //Test student data exists
    $this->assertSession()->pageTextContains("{$this->student_data['name']} {$this->student_data['surname']} {$this->student_data['middlename']}");
    $this->assertSession()->pageTextContains($this->student_data['education_type']);
    $this->assertSession()->pageTextContains($this->student_data['birthdate']);
  }

  /**
   * Tests that the students/show/{id} page can be reached.
   */
  public function testStudentPageExists() {
    $this->drupalLogin($this->user);

    $this->drupalGet('students/show/'.$this->student_data['id']);
    $this->assertSession()->statusCodeEquals(200);
    $config = $this->config('students.settings');
    $this->assertSession()->pageTextContains($config->get('student.page_title'));

    //Test student data exists
    $this->assertSession()->pageTextContains($this->student_data['name']);
    $this->assertSession()->pageTextContains($this->student_data['surname']);
    $this->assertSession()->pageTextContains($this->student_data['middlename']);
    $this->assertSession()->pageTextContains($this->student_data['education_type']);
    $this->assertSession()->pageTextContains($this->student_data['birthdate']);
  }

  /**
   * Tests that the students/show/{id} page can be reached.
   */
  public function testWrongStudentPageDoNotExists()
  {
    $this->drupalLogin($this->user);

    $this->drupalGet('students/show/'.md5("do not exist"));
    $this->assertSession()->statusCodeEquals(404);
  }
  /**
   * Tests the config form.
   */
  public function testConfigForm() {
    $this->drupalLogin($this->user);

    $this->drupalGet('admin/config/development/students');
    $this->assertSession()->statusCodeEquals(200);

    $config = $this->config('students.settings');

    //Test default form fields
    $this->assertSession()->fieldValueEquals(
      'students_title',
      $config->get('students.page_title')
    );
    $this->assertSession()->fieldValueEquals(
      'student_title',
      $config->get('student.page_title')
    );
    $this->assertSession()->fieldValueEquals(
      'name',
      $config->get('student.name')
    );
    $this->assertSession()->fieldValueEquals(
      'surname',
      $config->get('student.surname')
    );
    $this->assertSession()->fieldValueEquals(
      'middlename',
      $config->get('student.middlename')
    );
    $this->assertSession()->fieldValueEquals(
      'education_type',
      $config->get('student.education_type')
    );
    $this->assertSession()->fieldValueEquals(
      'birthdate',
      $config->get('student.birthdate')
    );

    // Test form submission.
    $this->drupalPostForm(NULL, [
      'students_title' => 'new students title',
      'student_title' => 'new student title',
      'name' => "John",
      'surname' => "Power",
      'middlename' => "Junior",
      'education_type' => "очная",
      'birthdate' => "1990-01-01"
    ], 'Save configuration');
    $this->assertSession()->pageTextContains('The configuration options have been saved.');

    // Test the new values are there.
    $this->drupalGet('admin/config/development/students');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->fieldValueEquals('students_title', 'new students title');
    $this->assertSession()->fieldValueEquals('student_title', 'new student title');
    $this->assertSession()->fieldValueEquals('name', "John");
    $this->assertSession()->fieldValueEquals('surname', "Power");
    $this->assertSession()->fieldValueEquals('middlename', "Junior");
    $this->assertSession()->fieldValueEquals('education_type', "очная");
    $this->assertSession()->fieldValueEquals('birthdate', "1990-01-01");

  }

}
