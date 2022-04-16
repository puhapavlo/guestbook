<?php

namespace Drupal\guestbook\Form;

/**
 * @file
 * Contains \Drupal\pablo\Form\EditForm.
 */

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Provides form for the guestbook module.
 */
class EditForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'guestbook_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entry = NULL) {
    $form['system_messages'] = [
      '#markup' => '<div id="form-system-messages-edit"></div>',
      '#weight' => -100,
    ];

    $form['id'] = [
      '#type' => 'hidden',
      '#default_value' => (isset($entry['id'])) ? $entry['id'] : '',
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your name:'),
      '#description' => $this->t('The minimum length of the name is 2 characters, and the maximum is 100'),
      '#required' => TRUE,
      '#default_value' => (isset($entry['name'])) ? $entry['name'] : '',
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
      '#default_value' => (isset($entry['email'])) ? $entry['email'] : '',
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
      '#default_value' => (isset($entry['phone'])) ? $entry['phone'] : '',
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
      '#default_value' => (isset($entry['feedback'])) ? $entry['feedback'] : '',
      '#attributes' => [
        'class' => [
          'form-feedback',
        ],
      ],
    ];

    $form['avatar'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Change your avatar:'),
      '#description' => 'The image format should be jpeg, jpg, png and the file size should not exceed 2 MB',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png'],
        'file_validate_size' => [2000000],
      ],
      '#preview_image_style' => 'medium',
      '#upload_location' => 'public://images',
      '#attributes' => [
        'class' => [
          'form-avatar',
        ],
      ],
    ];

    $form['picture'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Change feedback picture:'),
      '#description' => 'The image format should be jpeg, jpg, png and the file size should not exceed 5 MB',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png'],
        'file_validate_size' => [5000000],
      ],
      '#preview_image_style' => 'medium',
      '#upload_location' => 'public://images',
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

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Ajax callback for submit form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   JSON response object for AJAX requests.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function ajaxSubmitCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    if (strlen($form_state->getValue('name')) < 2 || strlen($form_state->getValue('name')) > 100) {
      $response->addCommand(new MessageCommand($this->t('The minimum length of the name is 2 characters, and the maximum is 100.'), '#form-system-messages-edit', ['type' => 'error']));
    }

    elseif (!preg_match('/^.+@.+.\..+$/i', $form_state->getValue('email'))) {
      $response->addCommand(new MessageCommand($this->t('The email is not valid.'), '#form-system-messages-edit', ['type' => 'error'], TRUE));
    }

    elseif (!preg_match('/^\d+$/', $form_state->getValue('phone')) || strlen($form_state->getValue('phone')) > 16) {
      $response->addCommand(new MessageCommand($this->t('The phone number should include only numbers and be 16 characters long.'), '#form-system-messages-edit', ['type' => 'error'], TRUE));
    }

    elseif ($form_state->getValue('feedback') == NULL) {
      $response->addCommand(new MessageCommand($this->t('Feedback field is empty'), '#form-system-messages-edit', ['type' => 'error'], TRUE));
    }

    else {
      $avaId = $form_state->getValue('avatar');
      if ($avaId == NULL) {
        $avatar = '/modules/custom/guestbook/images/avatar-default.png';
      }
      else {
        $file = File::load($avaId[0]);
        $file->setPermanent();
        $file->save();
        $uri = $file->getFileUri();
        $avatar = file_create_url($uri);
      }

      $picId = $form_state->getValue('picture');
      if ($picId == NULL) {
        $picture = NULL;
      }
      else {
        $file = File::load($picId[0]);
        $file->setPermanent();
        $file->save();
        $uri = $file->getFileUri();
        $picture = file_create_url($uri);
      }

      $query = \Drupal::database()->update('guestbook');
      $query->fields([
        'name' => $form_state->getValue('name'),
        'email' => $form_state->getValue('email'),
        'phone' => $form_state->getValue('phone'),
        'feedback' => $form_state->getValue('feedback'),
        'avatar' => $avatar,
        'picture' => $picture,
      ]);

      $query->condition('id', $form_state->getValue('id'));
      $query->execute();

      $response->addCommand(new MessageCommand($this->t('Entry modified successfully.'), '#form-system-messages-edit', ['type' => 'status']));
    }

    return $response;
  }

}
