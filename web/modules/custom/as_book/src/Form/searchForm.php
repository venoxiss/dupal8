<?php

namespace Drupal\as_book\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class searchForm.
 *
 * @package Drupal\as_book\Form
 */
class searchForm extends FormBase {




  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['submit'] = [
        '#type' => 'submit',
        '#value' => t('Submit'),
    ];

      $form['search_block_form']['#title'] = t('Search'); // Change the text on the label element
      $form['search_block_form']['#title_display'] = 'invisible'; // Toggle label visibilty
      $form['search_block_form']['#size'] = 40;  // define size of the textfield
      $form['search_block_form']['#default_value'] = t('Search'); // Set a default value for the textfield
      $form['actions']['submit']['#value'] = t('GO!'); // Change the text on the submit button
      $form['actions']['submit'] = array('#type' => 'image_button', '#src' => base_path() . path_to_theme() . '/images/search-button.png');

      // Add extra attributes to the text box
      $form['search_block_form']['#attributes']['onblur'] = "if (this.value == '') {this.value = 'Search';}";
      $form['search_block_form']['#attributes']['onfocus'] = "if (this.value == 'Search') {this.value = '';}";
      // Prevent user from searching the default text
      $form['#attributes']['onsubmit'] = "if(this.search_block_form.value=='Search'){ alert('Please enter a search'); return false; }";

      // Alternative (HTML5) placeholder attribute instead of using the javascript
      $form['search_block_form']['#attributes']['placeholder'] = t('Search');


    return $form;
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
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
        drupal_set_message($key . ': ' . $value);
    }

  }

}
