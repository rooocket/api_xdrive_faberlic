$(".form-phone").inputmask({"mask": "+7 (999) 999-99-99"});
$(".form-date").inputmask({"mask": "99.99.9999"});
$(".form-code").inputmask({"mask": "9999"});

$('select[name=country]').on('change', function() {

    var this_val = this.value,
        form_phone = $(".form-phone");

    if(this_val === 'kz') {
        form_phone.attr("placeholder","+7 (709) 999-99-99").inputmask({"mask": "+7 (709) 999-99-99"});
    }
    else if (this_val === 'by') {
        form_phone.attr("placeholder","+375 (99) 999-99-99").inputmask({"mask": "+375 (99) 999-99-99"});
    }
    else {
        form_phone.attr("placeholder","+7 (999) 999-99-99").inputmask({"mask": "+7 (999) 999-99-99"});
    }


});


$(document).on('click','.btn_check_required',function(){
    let btn = $(this),
        form = btn.parents('form'),
        required = form.find('[data-required]'),
        send = true,
        o_focus = false,
        error_form_text = 'Заполните все обязательные поля'

    form.find('label').removeClass('error_active');

    required.each(function(){
        let this_input = $(this),
            val = $(this).val();
        if(!val || val == 0) {
            if(!o_focus) {
                o_focus = true;
                //this_input.focus();
            }
            send = false;
            this_input.parents('label').addClass('error_active');
        }
    });

    if(send == true) {
        return true;
    } else {
        return false;
    }
});