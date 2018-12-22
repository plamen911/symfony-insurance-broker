$(function () {
  $(document.body).on('click', '.modal-popup', function () {
    var _self = $(this)
    var target = _self.data('target')
    var policyType = _self.data('policy-type')
    var reminder = _self.data('reminder')
    var remindedAt = _self.data('reminded-at')
    var carMake = _self.data('car-make')
    var carModel = _self.data('car-model')
    var carOwner = _self.data('car-owner')
    var paymentOrder = _self.data('payment-order')
    var dueAt = _self.data('due-at')

    $('#reminderInfoLabel').html('<i class="far fa-bell"></i> Напомняне за плащане по ' + policyType)
    $('#reminderInfoBody').empty()
      .append($('<ul class="list-unstyled">')
        .append($('<li><h5>Дата: <small>' + remindedAt + '</small></h5></li>'))
        .append($('<li><h5>Напомняне от: <small>' + reminder + '</small></h5></li>'))
        .append($('<li><strong>Плащане No:</strong> ' + paymentOrder + '</li>'))
        .append($('<li><strong>Падеж:</strong> ' + dueAt + '</li>'))
        .append($('<li><strong>МПС:</strong> ' + carMake + ' ' + carModel + '</li>'))
        .append($('<li><strong>Собственик:</strong> ' + carOwner + '</li>'))
      )

    $(target).modal({focus: true})
  })

  $('.payment-reminder').on('change', function (e) {
    e.preventDefault()
    var _self = $(this)
    var paymentId = _self.data('payment-id')
    var isReminded = _self.val()

    $.ajax({
      type: 'post',
      url: '/report/car/payment/' + paymentId + '/remind',
      dataType: 'json',
      data: {
        isReminded: isReminded
      },
      beforeSend: function () {
        _self.attr('disabled', 'disabled')
      }, // End beforeSend
      success: function (res) {
        var isReminded = +res.isReminded || 0
        toastr.success('Напомнянето бе успешно ' + ((isReminded) ? 'добавено' : 'премахнато') + '.', 'Успех!')
        toggleReminder(res)
      }, // End success
      error: function (err) {
        toastr.error(parseAjaxError(err), 'Грешка!')
      }, // End error
      complete: function () {
        _self.removeAttr('disabled')
      }
    })
  })
})

function toggleReminder (data) {
  var paymentId = +data.paymentId || 0
  var isReminded = +data.isReminded || 0
  var reminder = data.reminder || ''
  var remindedAt = data.remindedAt || ''
  var carMake = data.carMake || ''
  var carModel = data.carModel || ''
  var carOwner = data.carOwner || ''
  var paymentOrder = data.paymentOrder || ''
  var dueAt = data.dueAt || ''
  var policyType = data.policyType || ''

  if (isReminded) {
    $('#reminder-status-' + paymentId).empty()
      .append(
        $('<button type="button">')
          .data('reminder', reminder)
          .data('reminded-at', remindedAt)
          .data('car-make', carMake)
          .data('car-model', carModel)
          .data('car-owner', carOwner)
          .data('payment-order', paymentOrder)
          .data('due-at', dueAt)
          .data('policy-type', policyType)
          .data('target', '#reminderInfoModal')
          .addClass('text-white btn btn-link btn-sm modal-popup')
          .append($('<i class="fas fa-bell"></i>'))
      )
  } else {
    $('#reminder-status-' + paymentId).empty()
      .append(
        $('<button type="button">')
          .addClass('text-white btn btn-link btn-sm')
          .append($('<i class="far fa-bell-slash"></i>'))
      )
  }
}
