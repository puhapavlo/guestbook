<?php

namespace Drupal\guestbook\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Defines a confirmation form to confirm deletion of something by id.
 */
class ConfirmDeleteForm extends FormBase {

  /**
   * ID of the item to delete.
   *
   * @var int
   */
  protected $id;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    // Rendarable array for form using Form API.
    $form['id'] = [
      '#type' => 'hidden',
      '#default_value' => $id,
    ];

    $form['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $this->t('Do you want to delete this entry?'),
    ];

    $form['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#attributes' => [
        'class' => [
          'form-submit',
          'delete-cancel',
        ],
      ],
      // Used AJAX.
      '#ajax' => [
        'callback' => '::ajaxCancelCallback',
        'event' => 'click',
        'progress' => [
          'type' => 'throbber',
        ],
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('OK'),
      '#attributes' => [
        'class' => [
          'form-submit',
          'delete-submit',
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
    // Delete data from the database using hidden input.
    $query = \Drupal::database()->delete('guestbook');
    $id = $form_state->getValue('id');
    $query->condition('id', $id);
    $query->execute();
    \Drupal::messenger()->addStatus($this->t('Entry deleted successfully.'));
    // Reload page.
    $currentURL = Url::fromRoute('guestbook.content');
    $response->addCommand(new RedirectCommand($currentURL->toString()));
    return $response;
  }

  /**
   * Ajax callback for cancel button.
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
  public function ajaxCancelCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $currentURL = Url::fromRoute('guestbook.content');
    $response->addCommand(new RedirectCommand($currentURL->toString()));
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return "confirm_delete_form";
  }

}
