# students_example
An example of working with students drupal module.

*The presented functionality is not complete. 
Some functionality has been changed or cut out due to the Non-disclosure agreement


/students/all - The main page - show the list of registered students
![alt text](/screens/students.png)


/students/show/{student_id} - Student info page (click "подродно" on main page)
![alt text](/screens/student_info.png)


/student/delete/{student_id} - Student deletion page (click "удалить" on main page)
![alt text](/screens/deleted.png)


You can add new students using the form on the main page. 
This form is independent block that you can add on any page via /admin/structure/block.
![alt text](/screens/student_block.png)
![alt text](/screens/student_block_settings.png)


Form has its own validation rules
![alt text](/screens/validation_error.png)


/admin/config/development/students - Module has admin settings page where you can manage form and pages defaults
![alt text](/screens/students_settings_link.png)
![alt text](/screens/student_settings_apply.png)

Form has autocompletion by defaults
![alt text](/screens/students_after_settings.png)
