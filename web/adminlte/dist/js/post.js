$(document).ready(function () {
    postsAction.init();
});

var postsAction = {
    blockModal: '#block-user-modal',
    init: function () {
        this.deleteInit();
    },
    deleteInit: function () {
        $(document).on('click', '.delete-post', this.delete);
        $(document).on('click', '.delete-block', this.delete);
    },
    delete: function () {
        var ob = $(this),
            id = ob.data('post_id');

        postsAction.deleting(id, ob);

        return false;
    },
    deleting: function (id, ob) {
        var data = [{name: 'is_ajax', value: 1},{name: 'id', value: id}],
            tr = $(ob).closest('tr'),
            modal = $('#action-result-modal');

        $.ajax({
            url: ob.attr('href'),
            data: data,
            type: 'post',
            beforeSend: function () {
                tr.block();
            },
            complete: function(jqXHR, textStatus){
                tr.unblock();
                if(textStatus != 'success'){
                    $('.modal-body', modal).html(
                        $('<div>').addClass('alert alert-danger alert-dismissible')
                            .append($('<h4>').html('<i class="icon fa fa-warning"></i> Alert!'))
                            .append('Sorry! Request error!<br>'+jqXHR.responseText)
                    );
                    modal.modal('show');
                }
            },
            success: function (data) {
                if(typeof data.id != 'undefined'){
                    var items = $('tr[data-key = "'+data.id+'"]');
                    $.each(items, function (key, item) {
                        var tr = $(item).closest('tr');
                        tr.closest('tr').removeClass().addClass('danger');
                    });
                    ob.attr('href', ob.data('action'));
                    if(ob.hasClass('delete-block') && typeof usersAction != 'undefined'){
                        usersAction.block(ob);
                    }
                    $('.action-block', tr).html('');
                }
            }
        });
    }
};