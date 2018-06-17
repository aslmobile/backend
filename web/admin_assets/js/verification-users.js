$(document).ready(function () {
    verification.init();
});

var verification = {
    init: function () {
        $('.verify').on('click', verification.verify);
    },
    verify: function (e) {
        var item = $(e.target),
            row = item.closest('tr');

        $.ajax({
            url: item.attr('href'),
            data: {ajax: 1},
            beforeSend: function () {
                row.block();
            },
            complete: function (jqXHR, textStatus) {
                row.unblock();
                if(textStatus == 'success'){
                    row.removeAttr('class').addClass('success');
                    $('.verify[data-id = '+item.data('id')+']').addClass('disabled').removeAttr('href').text('Verified');
                }else{
                    console.log(textStatus);
                }
            }
        });

        return false;
    },
};