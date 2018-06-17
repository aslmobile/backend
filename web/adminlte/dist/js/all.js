$(document).ready(function(){
    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '0%' // optional
    });
    $('.select-on-check-all').on('ifChecked', function(event){
        $(this).closest('table').find('tbody').find('input[type="checkbox"]').iCheck('check');;
    });
    $('.select-on-check-all').on('ifUnchecked', function(event){
        $(this).closest('table').find('tbody').find('input[type="checkbox"]').iCheck('uncheck');;
    });
    $( ".nav-tabs-custom" ).tabs();

    if(typeof $.blockUI != 'undefined'){
        $.blockUI.defaults.message = '<img style="height: 50%; max-height: 50px;" src="/images/preloader.gif">';
        $.blockUI.defaults.css = { top: '40%', cursor: 'wait', textAlign: 'center'};
        $.blockUI.defaults.baseZ = 2000;
    }

    if(typeof $.fn.editable != 'undefined'){
        $.fn.editable.defaults.mode = 'inline';
        $('.editable').editable({
            success: function(response, newValue) {
                if(response.status == 'error') return response.msg; //msg will be shown in editable form
            }
        });
    }
});



function select2html(targ, html){
    targ
        .html(html)
        .select2('destroy')
        .show().css('display','block')
        .select2();
}





function intval( mixed_var, base ) {	// Get the integer value of a variable
    //
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

    var tmp;

    if( typeof( mixed_var ) == 'string' ){
        tmp = parseInt(mixed_var);
        if(isNaN(tmp)){
            return 0;
        } else{
            return tmp.toString(base || 10);
        }
    } else if( typeof( mixed_var ) == 'number' ){
        return Math.floor(mixed_var);
    } else{
        return 0;
    }
}

function masks(){
    $(".phone-mask").inputmask("+99 (999) 999 99 99");
    $("input[type='tel']").inputmask("+99 (999) 999 99 99");
}


function update_search(key, el)
{
    var value = $(el).val();

    key = encodeURI(key); value = encodeURI(value);

    var kvp = document.location.search.substr(1).split('&');

    var i=kvp.length; var x; while(i--)
{
    x = kvp[i].split('=');

    if (x[0]==key)
    {
        x[1] = value;
        kvp[i] = x.join('=');
        break;
    }
}

    if(i<0) {kvp[kvp.length] = [key,value].join('=');}

    //this will reload the page, it's likely better to store this until finished
    document.location.search = kvp.join('&');
}

function removecaseimage(im,el){
    $('#cim_'+el).remove();

    $.ajax({
        url: '/admin/cases/removecaseimage',
        type: 'POST',
        dataType: 'JSON',
        data: {
            im: im,
            _csrf:$('input[name="_csrf"]').val(),
        },
        success: function(data) {
        },
        error: function () {
        }
    });
}
