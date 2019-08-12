<?php
namespace Drupal\students\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a Student block with which you can add students.
 *
 * @Block(
 *   id = "student_block",
 *   admin_label = @Translation("Student block"),
 * )
 */
class StudentBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        // Return the form @ Form/LoremIpsumBlockForm.php.
        return \Drupal::formBuilder()->getForm('Drupal\students\Form\StudentForm');
    }

    /**
     * {@inheritdoc}
     */
    protected function blockAccess(AccountInterface $account) {
        return AccessResult::allowedIfHasPermission($account, 'add student');
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {

        $form = parent::blockForm($form, $form_state);

        $config = $this->getConfiguration();

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $this->setConfigurationValue('student_block_settings', $form_state->getValue('student_block_settings'));
    }

}

