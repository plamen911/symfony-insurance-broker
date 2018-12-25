(function ($, toastr) {
  'use strict'
  var $paymentsHolder
  var $greenCardsHolder

  // setup an "add a payment" link
  var $addPaymentButton = $('<button type="button" class="add_payment_link btn btn-warning my-2"><i class="fas fa-plus-circle"></i> Добави плащане</button>')
  var $newPaymentLinkLi = $('<li class="form-inline"></li>').append($addPaymentButton)

  // setup an "add a green card" link
  var $addGreenCardButton = $('<button type="button" class="add_green_card_link btn btn-warning my-2"><i class="fas fa-plus-circle"></i> Добави зелена карта</button>')
  var $newGreenCardLinkLi = $('<li class="form-inline"></li>').append($addGreenCardButton)

  $(function () {
    // Get the ul that holds the collection of payments
    $paymentsHolder = $('ul.payments')
    $greenCardsHolder = $('ul.green-cards')

    // add a delete link to all of the existing tag form li elements
    $paymentsHolder.find('li').each(function (i) {
      if (i > 0) {
        addPaymentFormDeleteLink($(this))
      }
    })

    $greenCardsHolder.find('li').each(function (i) {
      if (i > 0) {
        addGreenCardFormDeleteLink($(this))
      }
    })

    // add the "add a payment" anchor and li to the payments ul
    $paymentsHolder.append($newPaymentLinkLi)
    $greenCardsHolder.append($newGreenCardLinkLi)

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $paymentsHolder.data('index', $paymentsHolder.find('li').length - 1)
    $greenCardsHolder.data('index', $greenCardsHolder.find('li').length - 1)

    $addPaymentButton.on('click', function () {
      // add a new payment form (see next code block)
      addPaymentForm($paymentsHolder, $newPaymentLinkLi)
    })

    $addGreenCardButton.on('click', function () {
      addGreenCardForm($greenCardsHolder, $newGreenCardLinkLi)
    })

    $('#calc-payments').on('click', function (e) {
      e.preventDefault();
      distributePayments()
    })

    $('#policy_amount').on('keyup', function (e) {
      calcTotalAmount()
    })
  })

  function addPaymentForm ($paymentsHolder, $newPaymentLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $paymentsHolder.data('prototype')

    // get the new index
    var index = $paymentsHolder.data('index')

    var newForm = prototype
    // You need this only if you didn't set 'label' => false in your payments field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index)

    // increase the index with one for the next item
    $paymentsHolder.data('index', index + 1)

    // Display the form in the page in an li, before the "Add a payment" link li
    var $newFormLi = $('<li class="form-inline mb-1"></li>').append(newForm)
    $newPaymentLinkLi.before($newFormLi)

    window.setTimeout(function () {
      attachJsDatepicker()
    }, 100)

    // add a delete link to the new form
    addPaymentFormDeleteLink($newFormLi)
  }

  function addGreenCardForm ($greenCardsHolder, $newGreenCardLinkLi) {
    var prototype = $greenCardsHolder.data('prototype')
    var index = $greenCardsHolder.data('index')
    var newForm = prototype
    newForm = newForm.replace(/__name__/g, index)
    $greenCardsHolder.data('index', index + 1)
    var $newFormLi = $('<li class="form-inline mb-1"></li>').append(newForm)
    $newGreenCardLinkLi.before($newFormLi)
    addGreenCardFormDeleteLink($newFormLi)
  }

  function addPaymentFormDeleteLink ($paymentFormLi) {
    var $removeFormButton = $('<button type="button" class="btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i></button>')
    $paymentFormLi.append($removeFormButton)

    $removeFormButton.on('click', function (e) {
      // remove the li for the payment form
      $paymentFormLi.remove()
    })
  }

  function addGreenCardFormDeleteLink ($greenCardFormLi) {
    var $removeFormButton = $('<button type="button" class="btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i></button>')
    $greenCardFormLi.append($removeFormButton)
    $removeFormButton.on('click', function (e) {
      $greenCardFormLi.remove()
    })
  }

  function calcTotalAmount () {
    var policyAmount = $('#policy_amount').val()
    var policyTaxes = $('#policy_taxes').val()
    var amountGf = $('#policy_amountGf').val()

    policyAmount = (!policyAmount || isNaN(policyAmount)) ? 0 : parseFloat(policyAmount)
    policyTaxes = (!policyTaxes || isNaN(policyTaxes)) ? 0 : parseFloat(policyTaxes)
    amountGf = (!amountGf || isNaN(amountGf)) ? 0 : parseFloat(amountGf)

    var policyTotal = +policyAmount + (policyTaxes * policyAmount / 100) + +amountGf
    policyTotal = parseFloat(policyTotal.toFixed(2))

    $('#policy_total').val(policyTotal)
  }

  function calcClientPayments () {
    var officeCommission = $('#policy_officeCommission').val()
    var clientCommission = $('#policy_clientCommission').val()

    officeCommission = (!officeCommission || isNaN(officeCommission)) ? 0 : parseFloat(officeCommission)
    clientCommission = (!clientCommission || isNaN(clientCommission)) ? 0 : parseFloat(clientCommission)

    if (clientCommission > officeCommission) {
      toastr.error('Процентът на клиента (' + clientCommission + ') не може да е по-голям от процента на офиса (' + officeCommission + ')!', 'Грешка!')
      return
    }
    // todo
  }

  function distributePayments () {
    calcTotalAmount()
    var policyTotal = $('#policy_total').val()
    policyTotal = (!policyTotal || isNaN(policyTotal)) ? 0 : parseFloat(policyTotal)
    var paymentsCount = $('.payments').find('li').length - 1
    if (paymentsCount > 0) {
      var paymentAmount = parseFloat((policyTotal / paymentsCount).toFixed(2))
      var firstPaymentAmount = parseFloat((policyTotal - (paymentAmount * (paymentsCount - 1))).toFixed(2))
      $('input[id$="_amountDue"]').each(function (i) {
        if (i === 0) {
          $(this).val(firstPaymentAmount)
        } else {
          $(this).val(paymentAmount)
        }
      })
    }
  }

})(jQuery, toastr)
