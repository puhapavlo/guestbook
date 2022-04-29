<?php

namespace Drupal\guestbook\Plugin\Block;

/**
 * @file
 * Contains \Drupal\guestbook\Plugin\Block\cats_items.
 */
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
   * {@inheritdoc}
   */
  public function build() {
    // Getting data from the database.
    $db = \Drupal::database();
    $query = $db->select("guestbook", "g");
    $query->fields("g",
      [
        "id",
        "name",
        "email",
        "phone",
        "feedback",
        "avatar",
        "picture",
        "timestamp",
      ]);
    $query->orderBy("timestamp", "DESC");
    $result = $query->execute()->fetchAll();
    // Return renderable array.
    return [
      // Template name for current block.
      "#theme" => "feedbacks-template",
      "#items" => $result,
    ];
  }

}
