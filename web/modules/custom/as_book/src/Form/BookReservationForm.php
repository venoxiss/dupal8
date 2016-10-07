<?php

namespace Drupal\as_book\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BookReservationForm.
 *
 * @package Drupal\as_book\Form
 */
class BookReservationForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'book_reservation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get the current user
    $user = \Drupal::currentUser();

    if ($user->hasPermission('access book reservation form')) {

      $form['book_id'] = [
        '#type' => 'hidden',
      ];

      $form['user_id'] = [
        '#type' => 'hidden',
      ];

      $form['submit'] = [
          '#type' => 'submit',
          '#value' => t('Book me !'),
      ];

      return $form;
    }

    return ['#markup' => 'Veuillez vous connecter pour réserver le livre.'];

  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $current_uri = \Drupal::request()->getRequestUri();
    $current_user = \Drupal::currentUser();
    $original_book_id = str_replace('/node/', '', $current_uri);
    $original_user_id = $current_user->id();

    $submitted_book_id = $form_state->getValue('book_id');
    $submitted_user_id = $form_state->getValue('user_id');

    $invalid_book_id = $original_book_id != $submitted_book_id;
    $invalid_user_id = $original_user_id != $submitted_user_id;

    if ($invalid_book_id || $invalid_user_id) {
      $form_state->setErrorByName('book_id', $this->t('Mauvais format de donnée'));
    }

    if ($current_user->isAnonymous()) {
      $form_state->setErrorByName('user_id', $this->t('Utilisateur non connecté'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $book_id = $form_state->getValue('book_id');
    $user_id = $form_state->getValue('user_id');

    $node = \Drupal\node\Entity\Node::create([
      'type' => 'reservation',
      'title' => 'Réservation N° ' . $book_id . '-' . $user_id,
      'status' => 1,
      'field_book' => $book_id,
      'field_subscriber' => $user_id,
    ]);

    if ($node->save()) {
      drupal_set_message('La réservation a bien été prise en compte.', 'status');
    }
    else {
      drupal_set_message('Problème de réservation.', 'error');
    }

  }

}
