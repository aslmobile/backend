$(document).ready(function(){
    /*
    $('.sortable').nestedSortable({
        handle: 'div',
        items: 'li',
        toleranceElement: '> div',
        stop: function(e) { save_order(e); }
    });
        */
   

    if($(".sel2").length){
        $('.sel2').select2();
        if($('.mpsel').length){
            var vn = parseInt($('select.mpsel').attr('vn'));
            if(vn>1){
                $(".mpsel").select2('val',$('select.mpsel').attr('selvalue').split(','));
            }
            else{
                $(".mpsel").select2('val',$('select.mpsel').attr('selvalue'));
            }

        }
    }

    if($('.rangepick').length){
       $('.rangepick').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        }
      });
    }
    if($('.tags').length) {
        $('.tags').tagsinput({
            tagClass: function (item) {
                return (item.length > 10 ? 'big' : 'small');
            }
        });
    }
    masks();
});



function select2html(targ, html){
    targ
        .html(html)
        .select2('destroy')
        .show().css('display','block')
        .select2();
}





function intval( mixed_var, base ) {	// Get the integer value of a variable

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
