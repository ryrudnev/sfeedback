<?php

namespace Drupal\sfeedback\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Link;
use Drupal\Core\Form\FormStateInterface;
use Drupal\sfeedback\Storage\FeedbackStorage;
use Drupal\Component\Utility\Html;

class FeedbackForm extends FormBase
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
        return 'sfeedback_form';
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
        $feedback = [
            'fullname' => '',
            'email' => '',
            'text' => ''
        ];

        if (!is_null($this->id = $id) && empty($feedback = FeedbackStorage::getInstance()->get($this->id))) {
            drupal_set_message(t('Feedback with id = @id not found', ['@id' => $this->id]), 'error');

            $form['comeback-link'] = [
                '#prefix' => '<p>',
                '#markup' => Link::createFromRoute(t('To feedback list'), 'sfeedback.admin.read')->toString(),
                '#suffix' => '</p>'
            ];

            return $form;
        }

        $form['fullname'] = [
            '#type' => 'textfield',
            '#title' => t('FullName'),
            '#attributes' => ['placeholder' => t('Enter fullName')],
            '#default_value' => $feedback['fullname']
        ];
        $form['email'] = [
            '#type' => 'email',
            '#title' => t('Email'),
            '#attributes' => ['placeholder' => t('Enter e-mail')],
            '#default_value' => $feedback['email']
        ];
        $form['text'] = [
            '#type' => 'textarea',
            '#title' => t('Text'),
            '#attributes' => ['placeholder' => t('Enter text')],
            '#default_value' => $feedback['text']
        ];
        $form['actions'] = [
            '#type' => 'actions',
            'submit' => [
                '#type' => 'submit',
                '#value' => t('Add')
            ],
            'reset' => [
                '#type' => 'button',
                '#button_type' => 'reset',
                '#value' => t('Clear'),
                '#weight' => 9,
                '#attributes' => [
                    'onclick' => 'this.form.reset(); return false;',
                ],
            ]
        ];

        return $form;
    }

    /**
     * Form validation handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if (strlen($form_state->getValue('fullname')) === 0) {
            $form_state->setErrorByName('fullname', t('FullName is required'));
        }

        if (!\Drupal::service('email.validator')->isValid($form_state->getValue('email'))) {
            $form_state->setErrorByName('email', t('Invalid e-mail'));
        }

        if (strlen($form_state->getValue('text')) === 0) {
            $form_state->setErrorByName('text', t('Text is required'));
        }
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
        $new = is_null($id = $this->id);

        $values = $form_state->getValues();
        $values = [
            'fullname' => ($fullname = Html::escape($values['fullname'])),
            'email' => ($email = Html::escape($values['email'])),
            'text' => Html::escape($values['text'])
        ];

        $new ? $id = FeedbackStorage::getInstance()->add($values) : FeedbackStorage::getInstance()->update($id, $values);

        \Drupal::logger('sfeedback')->notice('@action feedback: id = @id, fullName = @fullname, email = @email', [
                '@action' => $new ? 'Added' : 'Updated',
                '@id' => $new ? "[new] $id" : $id,
                '@fullname' => $fullname,
                '@email' => $email
            ]
        );

        drupal_set_message(t('You feedback has been submitted'), 'status');

        if ($this->currentUser()->hasPermission('administer feedback')) {
            $form_state->setRedirect('sfeedback.admin.read');
        } else {
            $routeName = \Drupal::service('path.validator')->getUrlIfValid('')->getRouteName();
            $form_state->setRedirect($routeName);
        }
    }
}