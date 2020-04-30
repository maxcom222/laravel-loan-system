/**
 * Created by Tj on 7/24/2016.
 */
$(document).ready(function () {
    if (jQuery().wysihtml5) {
        $('.wysihtml5').wysihtml5({});
    }
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            orientation: "left",
            autoclose: true,
            format: "yyyy-mm-dd"
        });
        //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
    }
    $('.time-picker').datetimepicker({
        format: 'HH:mm'
    });
    if (jQuery().TouchSpin) {
        $(".touchspin").TouchSpin({
            buttondown_class: 'btn blue',
            buttonup_class: 'btn blue',
            min: 0,
            max: 10000000000,
            step: 0.01,
            decimals: 2,
            boostat: 5,
            maxboostedstep: 1,
            prefix: ''
        });
    }
    $('[data-toggle="confirmation"]').confirmation({
        popout: true
    });
    $('[data-toggle="tooltip"]').tooltip();
    if (jQuery().select2) {
        $(".select2").select2({
            theme: "bootstrap"
        });
    }
    $(".fancybox").fancybox();
    $('.delete').on('click', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        swal({
            title: 'Are you sure?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ok',
            cancelButtonText: 'Cancel'
        }).then(function () {
            window.location = href;
        })
    });

    tinyMCE.init({
        selector: ".tinymce",
        theme: "modern",
        link_list: [
            {title: 'My page 1', value: 'http://www.tinymce.com'},
            {title: 'My page 2', value: 'http://www.tecrail.com'}
        ],
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking spellchecker",
            "table contextmenu directionality emoticons paste textcolor code "
        ],
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
        browser_spellcheck: true,
        image_advtab: true,
        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
        toolbar2: "image | link unlink anchor | print preview code  | youtube | qrcode | flickr | picasa | forecolor backcolor | easyColorPicker"
    });
    $(".numeric").numeric();
    $(".positive").numeric({negative: false});
    $(".positive-integer").numeric({decimal: false, negative: false});
    $(".decimal-2-places").numeric({decimalPlaces: 2});
    $(".decimal-4-places").numeric({decimalPlaces: 4});
    $('.styled').uniform({
        radioClass: 'choice',
    });
    $(".file-styled").uniform({
        fileButtonClass: 'action btn btn-primary'
    });
    $.extend($.fn.dataTable.defaults, {

        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        drawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
            $('.delete').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                swal({
                    title: 'Are you sure?',
                    text: '',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ok',
                    cancelButtonText: 'Cancel'
                }).then(function () {
                    window.location = href;
                })
            });
        },
        preDrawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
        }
    });
    $('.basic-datable').DataTable();
});
function isDecimalKey(el, evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    var number = el.value.split('.');
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46 &&
        ((charCode != 65 && charCode != 86 && charCode != 67 && charCode != 99 && charCode != 120 && charCode != 118 && charCode != 97))) {
        alert("Only numbers or decimals are allowed");
        return false;
    }
    //1 decimal allowed
    if (number.length > 1 && charCode == 46) {
        return false;
    }

    var caratPos = getSelectionStart(el);
    var dotPos = el.value.indexOf(".");
    if (caratPos > dotPos && dotPos > -1 && (number[1].length > 1) && (charCode > 31)) {
        return false;
    }
    return true;
}
function isInterestKey(el, evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    var number = el.value.split('.');
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) {
        alert("Only numbers or decimals are allowed");
        return false;
    }
    //1 decimal allowed
    if (number.length > 1 && charCode == 46) {
        return false;
    }

    var caratPos = getSelectionStart(el);
    var dotPos = el.value.indexOf(".");
    if (caratPos > dotPos && dotPos > -1 && (number[1].length > 3) && (charCode > 31)) {
        return false;
    }
    return true;
}

function getSelectionStart(o) {
    if (o.createTextRange) {
        var r = document.selection.createRange().duplicate()
        r.moveEnd('character', o.value.length)
        if (r.text == '') return o.value.length
        return o.value.lastIndexOf(r.text)
    } else return o.selectionStart
}
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode != 65 && charCode != 86 && charCode != 67 && charCode != 99 && charCode != 120 && charCode != 118 && charCode != 97)) {
        alert("Only numbers are allowed");
        return false;
    }
    return true;
}
