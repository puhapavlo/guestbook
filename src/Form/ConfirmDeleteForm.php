<?php

namespace Drupal\guestbook\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Defines a confirmation form to confirm deletion of something by id.
 */
class ConfirmDeleteForm extends ConfirmFormBase {

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
    $form['id'] = [
      '#type' => 'hidden',
      '#default_value' => $id,
    ];

    $form['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $this->t('Do you want to delete this entry?'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $query = \Drupal::database()->delete('guestbook');
    $id = $form_state->getValue('id');
    $query->condition('id', $id);
    $query->execute();
    \Drupal::messenger()->addStatus($this->t('Entry deleted successfully.'));
    $form_state->setRebuild();
    return Url::fromRoute('<front>');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return "confirm_delete_form";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('guestbook.content');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {

  }

}