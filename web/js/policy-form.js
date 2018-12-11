(function ($) {
  'use strict'
  var $collectionHolder

  // setup an "add a payment" link
  var $addPaymentButton = $('<button type="button" class="add_payment_link btn btn-success my-2"><i class="fas fa-plus-circle"></i> Добави плащане</button>')
  var $newLinkLi = $('<li class="form-inline"></li>').append($addPaymentButton)

  $(function () {
    // Get the ul that holds the collection of payments
    $collectionHolder = $('ul.payments')

    // add a delete link to all of the existing tag form li elements
    $collectionHolder.find('li').each(function (i) {
      if (i > 0) {
        addPaymentFormDeleteLink($(this))
      }
    })

    // add the "add a payment" anchor and li to the payments ul
    $collectionHolder.append($newLinkLi)

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find('li').length - 1)
    // $collectionHolder.data('index', $collectionHolder.find(':input').length)

    $addPaymentButton.on('click', function (e) {
      // add a new payment form (see next code block)
      addPaymentForm($collectionHolder, $newLinkLi)
    })

    $('#calc-payments').on('click', function (e) {
      e.preventDefault();
      alert('Coming soon')
    })
  })

  function addPaymentForm ($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype')

    // get the new index
    var index = $collectionHolder.data('index')

    var newForm = prototype
    // You need this only if you didn't set 'label' => false in your payments field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index)

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1)

    // Display the form in the page in an li, before the "Add a payment" link li
    var $newFormLi = $('<li class="form-inline mb-1"></li>').append(newForm)
    $newLinkLi.before($newFormLi)

    window.setTimeout(function () {
      attachJsDatepicker()
    }, 100)

    // add a delete link to the new form
    addPaymentFormDeleteLink($newFormLi)
  }

  function addPaymentFormDeleteLink ($paymentFormLi) {
    var $removeFormButton = $('<button type="button" class="btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i></button>')
    $paymentFormLi.append($removeFormButton)

    $removeFormButton.on('click', function (e) {
      // remove the li for the payment form
      $paymentFormLi.remove()
    })
  }
})(jQuery)
