<?php
namespace Drupal\students\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Student form
 */
class StudentForm extends FormBase
{
    protected $education_options = ['очная', 'очно-заочная', 'заочная'];
    protected $date_format = 'Y-m-d';

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'student_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = \Drupal::config('students.settings');
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

        // Submit.
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Отправить'),
        ];

        return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $name = $form_state->getValue('name');
        $surname = $form_state->getValue('surname');
        $middlename = $form_state->getValue('middlename');
        $education_type = $form_state->getValue('education_type');
        $birthdate = $form_state->getValue('birthdate');

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
        $student_data = [
            'name' => $form_state->getValue('name'),
            'surname' => $form_state->getValue('surname'),
            'middlename' => $form_state->getValue('middlename'),
            'education_type' => $form_state->getValue('education_type'),
            'birthdate' => $form_state->getValue('birthdate')
        ];
        //$form_state->setRedirect('students.add', $student_data);

        $config = \Drupal::config('students.settings');
        $table = $config->get('table.name');

        $student_data['id'] = $this->generateId($student_data);

        $query = \Drupal::database()->upsert($table);
        $query->fields($student_data);
        $query->key('id');
        $query->execute();
    }

    protected function generateId(array $student_data){
        return md5($student_data['name'].$student_data['surname'].$student_data['middlename'].$student_data['birthdate']);
    }
}
