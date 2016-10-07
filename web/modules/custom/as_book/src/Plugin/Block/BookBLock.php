<?php

namespace Drupal\as_book\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BookBLock' block.
 *
 * @Block(
 *  id = "book_block",
 *  admin_label = @Translation("Book block"),
 * )
 */
class BookBLock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $current_uri = \Drupal::request()->getRequestUri();
    $book_id = str_replace('/node/', '', $current_uri);
    $current_user = \Drupal::currentUser();

    $user_id = $current_user->id();

    $form = \Drupal::formBuilder()->getForm('\Drupal\as_book\Form\BookReservationForm');
    $form['book_id']['#value'] = $book_id;
    $form['user_id']['#value'] = $user_id;

    //$form['book_id']['#value'] = ;
    //$form['user_id']['#value'] = ;

    $build['reservation_form'] = $form;

    return $build;
  }


}
