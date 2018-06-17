$(document).ready(function () {
    usersAction.init();
});

var usersAction = {
    init: function () {
        $('.block').on('click', this.block);
    },
    block: function () {
        var ob = $(this),
            modal = $('#block-user'),
            name = ob.data('name'),
            id = ob.data('id');

        $('.user-name', modal).text(name);
        $('input[name = "user_id"]', modal).val(id);


        modal.modal('show');
        return false;
    }
};