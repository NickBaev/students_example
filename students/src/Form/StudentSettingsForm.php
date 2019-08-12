<?php
namespace Drupal\students\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class StudentSettingsForm extends ConfigFormBase {

  protected $education_options = ['очная', 'очно-заочная', 'заочная'];
  protected $date_format = 'Y-m-d';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'student_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('students.settings');

    $form['students_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Заглавие студентов'),
      '#default_value' => $config->get('students.page_title'),
      '#description' => $this->t('Название страницы со списком студентов'),
    ];

    $form['student_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Заглавие студента'),
      '#default_value' => $config->get('student.page_title'),
      '#description' => $this->t('Название страницы информации о конкретном студенте'),
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Имя'),
      '#default_value' => $config->get('student.name'),
      '#description' => $this->t('Имя студента'),
    ];

    $form['surname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Фамилия'),
      '#default_value' => $config->get('student.surname'),
      '#description' => $this->t('Фамилия студента'),
    ];

    $form['middlename'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Отчество'),
      '#default_value' => $config->get('student.middlename'),
      '#description' => $this->t('Отчество студента'),
    ];

    $form['education_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Форма обучения'),
      '#options' => array_combine($this->education_options, $this->education_options),
      '#default_value' => $config->get('student.education_type'),
      '#description' => $this->t('Форма обучения студента'),
    ];

    $form['birthdate'] = [
      '#type' => 'date',
      '#title' => $this->t('Дата рождения'),
      '#default_value' => $config->get('student.birthdate'),
      '#description' => $this->t("Дата рождения студента в формате ({$this->date_format})"),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $students_title = $form_state->getValue('students_title');
    $student_title = $form_state->getValue('student_title');
    $name = $form_state->getValue('name');
    $surname = $form_state->getValue('surname');
    $middlename = $form_state->getValue('middlename');
    $education_type = $form_state->getValue('education_type');
    $birthdate = $form_state->getValue('birthdate');

    if (!preg_match("/^[a-zA-ZА-Яа-я\s:!]{3,256}$/u", $students_title)) {
      $form_state->setErrorByName('students_title', $this->t('Заглавие должно состоять только из букв и быть не менее 3-х и не более 256-х символов'));
    }

    if (!preg_match("/^[a-zA-ZА-Яа-я\s:!]{3,256}$/u", $student_title)) {
      $form_state->setErrorByName('student_title', $this->t('Заглавие должно состоять только из букв и быть не менее 3-х и не более 256-х символов'));
    }

    if (!preg_match("/^[a-zA-ZА-Яа-я]{3,64}$/u", $name)) {
      $form_state->setErrorByName('name', $this->t('Имя должно состоять только из букв и быть не менее 3-х и не более 64-х символов'));
    }

    if (!preg_match("/^[a-zA-ZА-Яа-я]{3,64}$/u", $surname)) {
      $form_state->setErrorByName('surname', $this->t('Фамилия должна состоять только из букв и быть не менее 3-х и не более 64-х символов'));
    }

    if (!preg_match("/^[a-zA-ZА-Яа-я]{3,64}$/u", $middlename)) {
      $form_state->setErrorByName('middlename', $this->t('Отчество должно состоять только из букв и быть не менее 3-х и не более 64-х символов'));
    }

    if (!in_array($education_type, $this->education_options)) {
      $form_state->setErrorByName('name', $this->t('Форма обучения может быть только: '.implode(',', $this->education_options)));
    }

    if (!($date = date_create_from_format($this->date_format, $birthdate))) {
      $form_state->setErrorByName('birthdate', $this->t("Дата рождения должа соответсвовать формату ({$this->date_format})"));
    }

    if ($date > new \DateTime()) {
      $form_state->setErrorByName('birthdate', $this->t("Дата рождения должа быть не позже текущего числа"));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('students.settings');
    $config->set('student.page_title', $form_state->getValue('student_title'));
    $config->set('students.page_title', $form_state->getValue('students_title'));
    $config->set('student.name', $form_state->getValue('name'));
    $config->set('student.surname', $form_state->getValue('surname'));
    $config->set('student.middlename', $form_state->getValue('middlename'));
    $config->set('student.education_type', $form_state->getValue('education_type'));
    $config->set('student.birthdate', $form_state->getValue('birthdate'));
    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'students.settings',
    ];
  }

}
