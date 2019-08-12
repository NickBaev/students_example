<?php

/**
 * @file
 * Contains \Drupal\students\Controller\StudentsController.
 */
namespace Drupal\students\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Component\Utility\Html;
use Symfony\Component\HttpFoundation\Request;


class StudentsController extends ControllerBase{

    public function all()
    {
        $config = \Drupal::config('students.settings');
        $page_title = $config->get('students.page_title');
        $table = $config->get('table.name');

        $query = \Drupal::database()->select($table, 'studs');
        $query->fields('studs');
        $result = $query->execute()->fetchAll();

        $students=[];
        foreach ($result as $student){
            $students[] = [
                "name" => $student->name." ".$student->surname." ".$student->middlename." ",
                "education_type" => $student->education_type,
                "birthdate" => $student->birthdate,
                "read" => Url::fromRoute('students.show', ['student_id' => $student->id], [])->toString(),
                "delete" => Url::fromRoute('students.delete', ['student_id' => $student->id], [])->toString()
            ];
        }
        $element['#students'] = $students;
        $element['#title'] = Html::escape($page_title);
        $element['#theme'] = 'students';
        return $element;
    }

}
