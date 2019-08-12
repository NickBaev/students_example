<?php

/**
 * @file
 * Contains \Drupal\students\Controller\StudentController.
 */
namespace Drupal\students\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Utility\Html;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class StudentController extends ControllerBase{

    public function show($student_id)
    {
        $config = \Drupal::config('students.settings');
        $page_title = $config->get('student.page_title');
        $table = $config->get('table.name');

        $query = \Drupal::database()->select($table, 'studs');
        $query->fields('studs');
        $query->condition('studs.id', $student_id);
        $result = $query->execute()->fetchAssoc();
        if (empty($result)){
            throw new NotFoundHttpException();
        }
        array_walk($result, function (&$value) {$value = Html::escape($value);});
        unset($result['id']);
        $element['#student_data'] = $result;
        $element['#title'] = Html::escape($page_title);
        $element['#theme'] = 'student';
        return $element;
    }

    public function delete($student_id)
    {
        $config = \Drupal::config('students.settings');
        $table = $config->get('table.name');

        $query = \Drupal::database()->delete($table);
        $query->condition('id', $student_id);
        $query->execute();
        return [
          '#type' => 'markup',
          '#markup' => $this->t('Студент успешно удален'),
        ];
    }
}
