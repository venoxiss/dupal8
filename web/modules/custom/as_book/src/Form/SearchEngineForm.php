<?php

namespace Drupal\as_book\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Class SearchEngineForm.
 *
 * @package Drupal\as_book\Form
 */
class SearchEngineForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'search_engine_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['search'] = [
      '#type' => 'textfield',
      // '#title' => $this->t('Search'),
      '#maxlength' => 64,
      '#size' => 64,
      '#ajax' => array(
        'callback' => '\Drupal\as_book\Form\SearchEngineForm::ajaxSearchCallback',
        'event' => 'keyup',
        'progress' => array(
          'type' => 'progress',
          'message' => 'Chargement...',
        ),
      ),
    ];

    $form['submit'] = [
        '#type' => 'submit',
        '#value' => t('Search'),
    ];

    return $form;
  }

  public function ajaxSearchCallback(array $form, FormStateInterface $form_state) {

    $keyword = $form_state->getValue('search');

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

    $output = '';

    if (!empty($books)) {

      $renderable = [
        '#theme' => 'book_listing',
        'books' => $books,
      ];

      $output = render($renderable);
      $output = $output->__toString();
    }


    $response = new AjaxResponse();
    $htmlCommand = new HtmlCommand('div.region.region-content', $output);
    $response->addCommand($htmlCommand);

    return $response;
  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $route_name = 'as_book.default_controller_searchEngine';
    $route_parameters = [
      'keyword' => $form_state->getValue('search'),
    ];

    $form_state->setRedirect($route_name, $route_parameters);

  }

}
