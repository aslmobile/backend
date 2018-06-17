$(document).ready(function () {
    usersAction.init();
});

var usersAction = {
    blockModal: '#block-user-modal',
    userPage: true,
    init: function () {
        this.blockInit();
        this.unblockInit();
        this.blockSettingsInit();
        $(usersAction.blockModal+' .submit').on('click', this.blocking);
    },
    blockInit: function () {
        if($('.user-page').length == 0){
            this.userPage = false;
        }
        $(document).on('click', '.block', this.block);
    },
    blockSettingsInit: function () {
        if($('.user-page').length == 0){
            this.userPage = false;
        }
        $(document).on('click', '.block-setting', this.blockSettings);
    },
    block: function (item) {
        var ob = $(this);
        if(typeof item.length != 'undefined'){
            ob = $(item);
        }
        var modal = $(usersAction.blockModal),
            action = ob.attr('href'),
            name = ob.data('name'),
            id = ob.data('id');

        if(typeof action != 'undefined'){
            $('form', modal).attr('action', action)
        }

        $('.user-name', modal).text(name);
        $('input[name = "user_id"]', modal).val(id);
        $('.alert', modal).remove();

        modal.modal('show');
        return false;
    },
    blockSettings: function () {
        var ob = $(this),
            modal = $(usersAction.blockModal),
            action = ob.attr('href'),
            name = ob.data('name'),
            id = ob.data('id'),
            reason = ob.data('reason'),
            duration = ob.data('duration');

        if(typeof action != 'undefined'){
            $('form', modal).attr('action', action)
        }

        $('.user-name', modal).text(name);
        $('input[name = "user_id"]', modal).val(id);
        $('.reason', modal).val(reason);
        $('.alert', modal).remove();

        modal.modal('show');
        $(usersAction.blockModal+' .submit').on('click', this.blocking);
        return false;
    },
    blocking: function () {
        var modal = $(usersAction.blockModal),
            form = $('form', modal),
            data = form.serializeArray();

        data.push({name: 'is_ajax', value: 1});

        $.ajax({
            url: form.attr('action'),
            data: data,
            type: 'post',
            beforeSend: function () {
                $('body').block();
            },
            complete: function(jqXHR, textStatus){
                $('body').unblock();
                if(textStatus != 'success'){
                    $('.modal-body', modal).prepend(
                        $('<div>').addClass('alert alert-danger alert-dismissible')
                            .append('<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>')
                            .append($('<h4>').html('<i class="icon fa fa-warning"></i> Alert!'))
                            .append('Sorry! Request error!<br>'+jqXHR.responseText)
                    )
                }
            },
            success: function (data) {
                if(typeof data.user_id != 'undefined'){
                    var items = $('.block[data-id = "'+data.user_id+'"]'),
                        rows = $('tr[data-key = "'+data.user_id+'"]');
                    $.each(items, function (key, item) {
                        var tr = $(item).closest('tr');
                        tr.closest('tr').removeClass().addClass('danger');
                        if(usersAction.userPage){
                            $(item).removeClass('btn-danger').removeClass('block').addClass('btn-success unblock').text('Unblock').attr('href','/admin/user/unblock');
                        }else{
                            $(item).remove();
                        }
                    });
                    $.each(rows, function (key, item) {
                        if(typeof data.blocked_at != 'undefined' && $('.blocked-at', item).length > 0){
                            $('.blocked-at', item).html(data.blocked_at)
                        }
                        if(typeof data.blocked_end != 'undefined' && $('.blocked-end', item).length > 0){
                            $('.blocked-end', item).html(data.blocked_end)
                        }
                        if(typeof data.blocked_reason != 'undefined' && $('.blocked-reason', item).length > 0){
                            $('.blocked-reason', item).html(data.blocked_reason)
                        }
                    });

                    modal.modal('hide');
                }
            }
        });
    },
    unblockInit: function () {
        $(document).on('click', '.unblock', function () {
            var ob = $(this),
                data = {user_id: ob.data('id'), is_ajax: 1};

            $.ajax({
                url: ob.attr('href'),
                data: data,
                type: 'post',
                beforeSend: function () {
                    $('body').block();
                },
                complete: function(jqXHR, textStatus){
                    $('body').unblock();
                    if(textStatus != 'success'){
                        //TODO if not status 200
                    }
                },
                success: function (data) {
                    if(typeof data.user_id != 'undefined'){
                        var items = $('.unblock[data-id = "'+data.user_id+'"]');
                        $.each(items, function (key, item) {
                            if($(item).hasClass('remove-item')){
                                $(item).closest('tr').remove();
                            }
                            $(item).closest('tr').removeClass().addClass('success');
                            $(item).removeClass('btn-success').removeClass('unblock').addClass('block btn-danger').text('Block').attr('href','/admin/user/block');
                        });
                    }
                }
            });

            return false;
        });
    }
};

// simple blocking

var changed = $('#user-status');
var toChange = $('.field-user-blocked_reason');

changed.on('change', function () {
    var val = changed.val();
    if (val == 9)
        toChange.show();
    else
        toChange.hide();
});