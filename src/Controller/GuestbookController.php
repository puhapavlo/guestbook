<?php

/**
 * @file
 * Contains \Drupal\guestbook\Controller\GuestbookController.
 *
 * @return
 */

namespace Drupal\guestbook\Controller;

use Drupal\Core\Controller\ControllerBase;

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

}
