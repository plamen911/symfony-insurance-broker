$(function () {
  $('#confirm-delete').on('show.bs.modal', function (e) {
    var confirmMessage = $(e.relatedTarget).data('message') || 'Are you sure? Do you want to proceed?'
    $('#confirm-message').html(confirmMessage)
    $(this).find('#delete-form').attr('action', $(e.relatedTarget).data('href'))
  })

  attachJsDatepicker()
})

function attachJsDatepicker () {
  var $elem = $('.js-datepicker')
  if ($elem.length > 0) {
    $elem.datepicker({
      language: 'bg',
      todayHighlight: true,
      format: 'dd.mm.yyyy',
      autoclose: true,
      weekStart: 1
    })
  }
}
