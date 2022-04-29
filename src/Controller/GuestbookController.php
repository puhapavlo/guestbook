<?php

namespace Drupal\guestbook\Controller;

/**
 * @file
 * Contains \Drupal\guestbook\Controller\GuestbookController.
 *
 * @return
 */

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

    // Get a renderable GuestbookForm array.
    $guestbookForm = \Drupal::formBuilder()->getForm("Drupal\guestbook\Form\GuestbookForm");

    // Used service plugin.manager.block to get the block.
    $blockManager = \Drupal::service("plugin.manager.block");
    $config = [];
    $feedbacksBlock = $blockManager->createInstance("feedbacks", $config);
    // Return renderable array.
    return [
      // Template name for current controller.
      "#theme" => "guestbook_template",
      "#form" => $guestbookForm,
      "#feedbacks" => $feedbacksBlock->build(),
    ];
  }

  /**
   * Function for outputting the deletion form.
   *
   * @param int $id
   *   Id a entry from the database.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   return ajax response
   */
  public function delete($id) {

    $confirmDeleteForm = \Drupal::formBuilder()->getForm("Drupal\guestbook\Form\ConfirmDeleteForm", $id);
    // Used AJAX.
    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand("Delete", $confirmDeleteForm, ["width" => "800"]));

    return $response;
  }

  /**
   * Function for outputting the edition form.
   *
   * @param int $id
   *   Id a entry from the database.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Return ajax response.
   */
  public function edit($id) {
    // Getting data from the database using the route parameter.
    $conn = Database::getConnection();
    $query = $conn->select("guestbook", "g");
    $query->condition("id", $id)->fields("g");
    $entry = $query->execute()->fetchAssoc();

    $editForm = \Drupal::formBuilder()->getForm("Drupal\guestbook\Form\GuestbookForm", $entry);
    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand("Edit Form", $editForm, ["width" => "800"]));

    return $response;
  }

}
