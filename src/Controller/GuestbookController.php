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
    return [
      '#theme' => 'guestbook_template',
    ];
  }

}
