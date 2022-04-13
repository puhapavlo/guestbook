<?php

/**
 * @file
 * Contains \Drupal\pablo\Form\CatsForm.
 */

namespace Drupal\guestbook\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides form for the guestbook module.
 */
class GuestbookForm extends FormBase {

  /**
   * Return id Form.
   *
   * @return string
   *   return id form.
   */
  public function getFormId() {
    return 'guestbook_form';
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array|void
   *   Return renderable array for form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['system_messages'] = [
      '#markup' => '<div id="form-system-messages"></div>',
      '#weight' => -100,
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your name:'),
      '#description' => $this->t('The minimum length of the name is 2 characters, and the maximum is 100'),
      '#required' => TRUE,
      '#attributes' => [
        'class' => [
          'form-name',
        ],
      ],
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email:'),
      '#description' => $this->t('Example: example@gmail.com'),
      '#required' => TRUE,
      '#attributes' => [
        'class' => [
          'form-email',
        ],
      ],
    ];

    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Your phone number:'),
      '#description' => $this->t('Example: 380960000000'),
      '#required' => TRUE,
      '#attributes' => [
        'class' => [
          'form-phone',
        ],
      ],
    ];

    $form['feedback'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Your feedback:'),
      '#required' => TRUE,
      '#attributes' => [
        'class' => [
          'form-feedback',
        ],
      ],
    ];

    $form['avatar'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Your avatar:'),
      '#description' => 'The image format should be jpeg, jpg, png and the file size should not exceed 2 MB',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png'],
        'file_validate_size' => [2000000],
      ],
      '#attributes' => [
        'class' => [
          'form-avatar',
        ],
      ],
    ];

    $form['picture'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Feedback picture:'),
      '#description' => 'The image format should be jpeg, jpg, png and the file size should not exceed 5 MB',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png'],
        'file_validate_size' => [5000000],
      ],
      '#attributes' => [
        'class' => [
          'form-picture',
        ],
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#attributes' => [
        'class' => [
          'form-submit',
        ],
      ],
      '#ajax' => [
        'callback' => '::ajaxSubmitCallback',
        'event' => 'click',
        'progress' => [
          'type' => 'throbber',
        ],
      ],
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  public function ajaxSubmitCallback(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();

    if (strlen($form_state->getValue('name')) < 2 || strlen($form_state->getValue('name')) > 100) {
      $ajax_response->addCommand(new MessageCommand($this->t('The minimum length of the name is 2 characters, and the maximum is 100.'), '#form-system-messages', ['type' => 'error']));
    }

    elseif (!preg_match('/^.+@.+.\..+$/i', $form_state->getValue('email'))) {
      $ajax_response->addCommand(new MessageCommand($this->t('The email is not valid.'), '#form-system-messages', ['type' => 'error'], TRUE));
    }

    elseif (!preg_match('/^\d+$/', $form_state->getValue('phone')) || strlen($form_state->getValue('phone')) > 16) {
      $ajax_response->addCommand(new MessageCommand($this->t('The phone number should include only numbers and be 16 characters long.'), '#form-system-messages', ['type' => 'error'], TRUE));
    }

    elseif ($form_state->getValue('feedback') == NULL) {
      $ajax_response->addCommand(new MessageCommand($this->t('Feedback field is empty'), '#form-system-messages', ['type' => 'error'], TRUE));
    }

    return $ajax_response;
  }

}
