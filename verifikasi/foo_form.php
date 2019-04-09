<?php

function foo_form() {
	
$form['submit_dialogue'] = array(
    '#type' => 'submit',
    '#value' => t("Click me, I'm a special AJAX confirm button"),
    '#ajax' => array(
      'callback' => 'your_callback',
      'wrapper' => 'your-wrapper-id',
      'method' => 'replace',
      'effect' => 'fade',
      'progress' => array(
        'type' => 'throbber',
        'message' => '',
      ),
      'event' => 'your_custom_click',
    ),
    '#attributes' => array(
      'class' => array(
        'btn-warning',
        'btn-special',
      ),
    ),
  );
  
  $form['modal'] = array(
    'modal' => array(
      'content' => array(
        'header' => array(
          'close' => array(
            '#theme' => 'button',
            '#value' => '&times;',
            '#button_type' => 'button',
            '#attributes' => array(
              'class' => array('close'),
              'data-dismiss' => 'modal',
            ),
          ),
          'title' => array(
            '#markup' => t('This is an example confirmation dialogue box.'),
            '#prefix' => '<h4 class="modal-title">',
            '#suffix' => '</h4>',
          ),
          '#prefix' => '<div class="modal-header">',
          '#suffix' => '</div>',
        ),
        'body' => array(
          'para' => array(
            '#markup' => t('Press Confirm to submit the form using AJAX, or click Cancel to close the dialogue box and do nothing.'),
            '#prefix' => '<p>',
            '#suffix' => '</p>',
          ),
          '#prefix' => '<div class="modal-body">',
          '#suffix' => '</div>',
        ),
        'footer' => array(
          'submit' => array(
            '#theme' => 'button',
            '#value' => t('Confirm'),
            '#button_type' => 'button',
            '#attributes' => array(
              'class' => array('btn', 'btn-success'),
              'data-dismiss' => 'modal',
            ),
          ),
          'close' => array(
            '#theme' => 'button',
            '#value' => t('Cancel'),
            '#button_type' => 'button',
            '#attributes' => array(
              'class' => array('btn', 'btn-warning'),
              'data-dismiss' => 'modal',
            ),
          ),
          '#prefix' => '<div class="modal-footer">',
          '#suffix' => '</div>',
        ),
        '#prefix' => '<div class="modal-content">',
        '#suffix' => '</div>',
      ),
      '#prefix' => '<div class="modal-dialog">',
      '#suffix' => '</div>',
    ),
    '#prefix' => '<div id="your-modal-id" class="modal fade" role="dialog">',
    '#suffix' => '</div>',
  );
	
	return $form;
			
}

function foo_form_submit($form, &$form_state) {

			
	drupal_goto();
}

/*
  (function ($) {


Drupal.behaviors.foo_form = {
  attach: function (context, settings) {
    var specialButton = $('.btn-special', context);
    var modalConfirm = $("#your-modal-id", context);
    specialButton.mousedown(function() {
      specialButton.prop('disabled', true);
      modalConfirm.modal('show');
    });
    modalConfirm.on('hidden.bs.modal', function () {
      specialButton.prop('disabled', false);
    });
    $('.btn-success', modalConfirm).on('click', function() {
      specialButton.prop('disabled', false);
      specialButton.trigger('foo_form_submit');
    });
  }
};


})(jQuery);
*/
?>