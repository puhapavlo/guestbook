<?php

/**
 * @file
 * Contains \Drupal\guestbook\Controller\GuestbookController.
 *
 * @return
 */

namespace Drupal\guestbook\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

/**
 * Provides route responses for the guestbook module.
 */
class GuestbookController extends ControllerBase {

  /**
   * Returns a page.
   *
   * @return array
   *   A renderable array.
   */
  public function content() {
    $guestbookForm = \Drupal::formBuilder()->getForm('Drupal\guestbook\Form\GuestbookForm');

    $blockManager = \Drupal::service('plugin.manager.block');
    $config = [];
    $feedbacksBlock = $blockManager->createInstance('feedbacks', $config);
    return [
      '#theme' => 'guestbook_template',
      '#form' => $guestbookForm,
      '#feedbacks' => $feedbacksBlock->build(),
    ];
  }

  public function edit($id) {
    $conn = Database::getConnection();
    $query = $conn->select('guestbook', 'g');
    $query->condition('id', $id)->fields('g');
    $entry = $query->execute()->fetchAssoc();

    $editForm = \Drupal::formBuilder()->getForm('Drupal\guestbook\Form\EditForm', $entry);
    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand('Edit Form', $editForm, ['width' => '800']));

    return $response;
  }

}
