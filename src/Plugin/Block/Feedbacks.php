<?php

/**
 * @file
 * Contains \Drupal\guestbook\Plugin\Block\cats_items.
 */
namespace Drupal\guestbook\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a feedbacks.
 *
 * @Block(
 *   id = "feedbacks",
 *   admin_label = @Translation("Feedbacks")
 * )
 */
class Feedbacks extends BlockBase {

  /**
   * @return array
   */
  public function build() {
    $db = \Drupal::database();
    $query = $db->select('guestbook', 'g');
    $query->fields('g',
      ['id', 'name', 'email', 'phone', 'feedback', 'avatar', 'picture', 'timestamp']);
    $query->orderBy('timestamp', 'DESC');
    $result = $query->execute()->fetchAll();
    return [
      '#theme' => 'feedbacks-template',
      '#items' => $result,
    ];
  }
}