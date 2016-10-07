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

    $current_user = \Drupal::currentUser();

    if ($current_user->hasPermission('acces book reservation form')) {
      $form['book_id'] = [
        '#type' => 'textfield',
      ];
      $form['user_id'] = [
        '#type' => 'hidden',
      ];

      $form['submit'] = [
          '#type' => 'submit',
          '#value' => t('book me'),
      ];

      return $form;
    }

    return ['#markup' => 'veuillez vous connecter pour reserver le livre' ];

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

    if ($invalid_user_id || $invalid_book_id) {
      $form_state->setError($form['book_id'], 'Mauvais format de donnée');
    }

    if ($current_user->isAnonymous()) {
      $form_state->setError($form['user_id'], 'tu ne fais pas partie du harem');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.

    $book_id = $form_state->getValue('book_id');
    $user_id = $form_state->getValue('user_id');

    $nodes = \Drupal\node\Entity\Node::create([
      'type' => 'reservation',
      'title' => 'Réservation num' . $book_id . '-' . $user_id,
      'status' => 1,
      'field_livre' => $book_id,
      'field_suscriber' => $user_id
    ]);

    if ($nodes->save()) {
      drupal_set_message('Participation reussis', 'status');
    }

    foreach ($form_state->getValues() as $key => $value) {
        drupal_set_message($key . ': ' . $value);
    }

  }

}
