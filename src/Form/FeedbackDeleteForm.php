<?php

namespace Drupal\sfeedback\Form;

use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\sfeedback\Storage\FeedbackStorage;

class FeedbackDeleteForm extends ConfirmFormBase
{
    protected $id;

    /**
     * Returns a unique string identifying the form.
     *
     * @return string
     *   The unique string identifying the form.
     */
    public function getFormId()
    {
        return 'sfeedback_delete';
    }

    function getQuestion()
    {
        return t('Are you sure you want to delete feedback id = @id?', ['@id' => $this->id]);
    }

    function getConfirmText()
    {
        return t('Delete');
    }

    /**
     * Returns the route to go to if the user cancels the action.
     *
     * @return \Drupal\Core\Url
     *   A URL object.
     */
    public function getCancelUrl()
    {
        return new Url('sfeedback.admin.read');
    }

    /**
     * Form constructor.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @return array
     *   The form structure.
     */
    public function buildForm(array $form, FormStateInterface $form_state, $id = null)
    {
        $this->id = $id;

        return parent::buildForm($form, $form_state);
    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        FeedbackStorage::getInstance()->delete($this->id);

        \Drupal::logger('sfeedback')->notice('Deleted feedback with id = @id', ['@id' => $this->id]);

        drupal_set_message(t('The feedback with id = @id has been deleted.', ['@id' => $this->id]), 'status');
        $form_state->setRedirect('sfeedback.admin.read');
    }
}