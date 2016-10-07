<?php

namespace Drupal\as_book\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DefaultController.
 *
 * @package Drupal\as_book\Controller
 */
class DefaultController extends ControllerBase {

  /**
   * Booklisting.
   *
   * @return string
   *   Return Hello string.
   */
  public function bookListing() {

    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'as_book');
    $query->condition('status', 1);
    $query->sort('created', 'DESC');
    $query->range(0, 10);
    $result = $query->execute();

    $nodes = \Drupal\node\Entity\Node::loadMultiple($result);

    $books = [];
    foreach ($nodes as $node) {
      $books[] = node_view($node, 'teaser');
    }

    return [
      '#theme' => 'book_listing',
      'books' => $books,
    ];
  }

    /**
     * SearchEngine.
     *
     * @return string
     *   Return Hello string.
     */
    public function searchEngine() {

      $keyword = \Drupal::request()->get('keyword');

      $query = \Drupal::entityQuery('node');
      $query->condition('type', 'as_book');
      $query->condition('status', 1);
      $query->condition('title', $keyword, 'CONTAINS');
      $query->sort('created', 'DESC');
      $query->range(0, 10);
      $result = $query->execute();

      $nodes = \Drupal\node\Entity\Node::loadMultiple($result);

      $books = [];
      foreach ($nodes as $node) {
        $books[] = node_view($node, 'teaser');
      }

      return [
        '#theme' => 'book_listing',
        'books' => $books,
      ];
    }














}
