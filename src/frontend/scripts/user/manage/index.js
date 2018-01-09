(function ($) {
    'use-strict';

    var $table = $('#tbUsers');
    var $btnAddUser = $('#add-user');
    var $modalCheckUser = $('#md-manage-check-user');
    var $modalCreateUser = $('#md-manage-create-user');
    var $messageContent = $('#message-content');
    var $messageLabel = $('#message-label');

    //$('.datepicker-2').datepicker();

    $btnAddUser.on('click', function () {
        $modalCheckUser.find('#form-check-user input,select').val('').removeClass('valid').removeClass('error');
        $modalCheckUser.find('#form-check-user label.error').remove();
        $modalCreateUser.find('#form-create-user input,select').val('').removeClass('valid').removeClass('error');
        $modalCreateUser.find('#form-create-user label.error').remove();
        $modalCreateUser.find('input[name^="identis[CodTMoti]"]').val('*');
        $modalCheckUser.modal({backdrop: 'static'}); // no cerrar el modal al hacer click fuera de el
    });

    $modalCreateUser.on("shown.bs.modal", function () {
        $(".datepicker").datepicker({
            format: "dd-mm-yyyy",
            language: "es",
            autoclose: true
        });
    });

    // formulario de chequear usuario
    $modalCheckUser.find('#form-check-user').validate({
        submitHandler: function (form) {
            var btn = $(form).find('button[type=submit]');
            var formArr = $(form).serializeArray();
            $.each(formArr, function (i, field) {
                formArr[i].value = $.trim(field.value);
            });
            var data = $.param(formArr);
            check_user(data, btn);
        },
        rules: {
            'cod_per': {required: true}
        }
    });

    // formulario de crear usuario
    $modalCreateUser.find('#form-create-user').validate({
        submitHandler: function (form) {
            var btn = $(form).find('button[type=submit]');
            var formArr = $(form).serializeArray();
            $.each(formArr, function (i, field) {
                formArr[i].value = $.trim(field.value);
            });
            var data = $.param(formArr);
            create_user(data, btn);
        },
        rules: {
            'identis[CodPer]': {required: true},
            'identis[Nombres]': {required: true},
            'identis[Ape1]': {required: true},
            'identis[Ape2]': {required: true},
            'identis[telefono]': {required: true},
            'identis[email]': {required: true, email: true},
            'identis[acceso]': {required: true},
            'identis[correo_upch]': {required: true},
            'identis[situacion]': {required: true},
            'identis[Fexp]': {required: true},
            'identis[Fnac]': {required: true}
        }
    });

    var update_form_with_chacad = function (data) {
        var form = $modalCreateUser.find('#form-create-user');
        form.find('input[name^="identis[CodIden]"]').val(data['CodIden']);
        form.find('input[name^="identis[CodPer]"]').val(data['dni']);
        form.find('input[name^="identis[Nombres]"]').val(data['Nombres']);
        form.find('input[name^="identis[Ape1]"]').val(data['Ape1']);
        form.find('input[name^="identis[Ape2]"]').val(data['Ape2']);
        form.find('select[name^="identis[tdocu]"]').val(data['tdocu']);
        form.find('select[name^="identis[Sexo]"]').val(data['Sexo']);
        form.find('input[name^="identis[Celular]"]').val(data['celular_personal']);
        form.find('input[name^="identis[Telefono]"]').val(data['telefono_personal']);
        form.find('input[name^="identis[Email]"]').val(data['email_personal']);
        form.find('input[name^="identis[Direccion]"]').val(data['direccion_personal']);
    };

    var check_user = function (data, btn) {
        btn.prop({disabled: true}).html('Cargando...');
        $.post(controllerUrl + '/check', data, function (response) {
            if (!response.error) {
                var $id_user = response.data.id_user;
                //chequear si existe
                if (response.data.exist) {
                    _confirm("<h5 class='text-center'>El usuario ya existe en padlock, ¿Desea ver su perfil?</h5>", function () {
                        location.href = controllerUrl + '/edit?id=' + $id_user;
                    }, function () {
                        $modalCheckUser.modal('hide');
                        $table.bootstrapTable('refresh');
                    });
                } else {
                    $modalCheckUser.modal('hide');
                    if (response.data.chacad.exist) {
                        update_form_with_chacad(response.data.chacad.data);
                    }
                    $modalCreateUser.modal({backdrop: 'static'}); // no cerrar el modal al hacer click fuera de el
                    var form = $modalCreateUser.find('#form-create-user');
                    console.log(response.data.cod_per);
                    form.find('input[name^="identis[CodPer]"]').val(response.data.cod_per);
                }
                btn.prop({disabled: false}).html('Buscar');
                noty({type: 'information', text: response.message, timeout: 5000}).show();
            } else {
                noty({type: 'error', text: response.message, timeout: 5000}).show();
                btn.prop({disabled: false}).html('Buscar');
            }
        }, 'json').fail(function (xhr, status, error) {
            if (xhr.status != 200) {
                noty({type: 'error', text: xhr.responseText}).show();
                btn.prop({disabled: false}).html('Buscar');
            }
        });
    };

    $("#cboTipoDoc").on("change", function () {
        if ($(this).val() == "CE") {
            $("#row_pais").removeClass("d-none");
        } else {
            $("#row_pais").addClass("d-none");
        }
    });
    $("#cboSituacion").on("change", function () {
        var situacion = $(this).val();
        if (situacion == "ESTUDIANTE" || situacion == "PASANTIA ESTUDIANTE") {
            $("#row_modalidad").removeClass("d-none");
        } else {
            $("#row_modalidad").addClass("d-none");
        }
    });
    var create_user = function (data, btn) {
        btn.prop({disabled: true}).html('Cargando...');
        $.post(controllerUrl + '/save', data, function (response) {
            if (!response.error) {
                if (response.data.results.chacad) {
                    $messageContent.removeClass("d-none");
                    var message_error = "";
                    $.each(response.data.results.chacad, function (key, ele) {
                        var tipo_error = "";
                        if (ele.step == 1) {
                            tipo_error = "Registro de Usuario";
                        } else if (ele.step == 2) {
                            tipo_error = "Registro de Correo";
                        }
                        message_error += "Error al realizar el " + tipo_error + " en CHACAD<br/>";
                    });
                    $messageLabel.empty().html(message_error);
                }
                noty({type: 'success', text: response.message, timeout: 2000}).show();
                setTimeout(function () {
                    location.href = controllerUrl + '/edit?id=' + response.data.id_user;
                }, 2000);
            } else {
                noty({type: 'error', text: response.message, timeout: 5000}).show();
                btn.prop({disabled: false}).html('Guardar');
            }
        }, 'json').fail(function (xhr, status, error) {
            if (xhr.status != 200) {
                noty({type: 'error', text: xhr.responseText}).show();
                btn.prop({disabled: false}).html('Guardar');
            }
        });
    };

}(window.jQuery));