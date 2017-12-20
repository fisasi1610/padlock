(function ($) {
    'use-strict';

    var $tabs = $(".tabUsers");

    var columns = function () {
        return [
            {
                field: 'id_app_user',
                title: 'Código',
                align: 'center',
                sortable: true,
                width: '50px',
            },
            {field: 'id_app', title: 'Identificador', align: 'center', sortable: true},
            {field: 'application', title: 'Aplicación', align: 'center', sortable: true},
            {field: 'role', title: 'Rol', align: 'center', sortable: true},
            {field: 'actions_allowed', title: 'Acciones Adicionales', align: 'center', sortable: true},
            {field: 'actions_restricted', title: 'Acciones Restringidas', align: 'center', sortable: true},
            {
                field: 'action',
                title: 'Acciones',
                align: 'center',
                sortable: false,
                width: '100px',
                formatter: _action_buttons,
                events: {
                    'click .edit': _action_edit,
                    'click .delete': _action_delete,
                    'click .addpermit': _action_add,
                    'click .removepermit': _action_remove,
                }
            }
        ];
    };

    var _action_buttons = function (value, row, index) {
        var default_actions = [
            '<a class="btn btn-default btn-icon btn-rounded edit cursor font-size-8" title="Editar"><i class="fa fa-pencil"></i></a>',
            '<a class="btn btn-default btn-icon btn-rounded delete cursor font-size-8" title="Eliminar"><i class="fa fa-trash"></i></a>'
        ];
        if (row.type == "users") {
            default_actions.push('<a class="btn btn-default btn-icon btn-rounded addpermit cursor font-size-8" title="Adicionar Permiso"><i class="fa fa-plus"></i></a>');
            default_actions.push('<a class="btn btn-default btn-icon btn-rounded removepermit cursor font-size-8" title="Restringir Permiso"><i class="fa fa-minus"></i></a>');
        }

        return default_actions.join('');
    };

    var _action_add = function (e, value, row, index) {
        _add_permit(row);
    };
    var _action_remove = function (e, value, row, index) {
        _remove_permit(row);
    };
    var _action_edit = function (e, value, row, index) {
        _edit_app(row);
    };

    var _action_delete = function (e, value, row, index) {
        if (typeof row.name == "undefined") {
            row.name = row.nombre_persona;
        }
        var message = "<h5 class='text-center'>";
        switch (row.type) {
            case "roles":
                message += "Está por eliminar el rol <strong>" + row.name + "</strong>";
                break;
            case "actions":
                message += "Está por eliminar la acción <strong>" + row.name + "</strong>";
                break;
            case "users":
                message += "Está por eliminar a <strong>" + row.name + "</strong> con rol <strong>" + row.role + "</strong>";
                break;
        }
        message += ", ¿Seguro que desea continuar?</h5>";
        var $row = row;
        _confirm(message, function () {
            _delete_type($row);
        }, function () {
            console.log("CANCELAR");
        });
    };

    var _edit_app = function (data) {
        var type = "application";
        var mdCreate = $("#md-manage-create-" + type);
        var frmCreate = "#form-create-" + type;
        var table = $("#tb-" + type);
        mdCreate.find("#cboRole").empty();
        mdCreate.find("#cboRole").append("<option value=''>Seleccione..</option>");
        $.get(Request.Host + Request.BaseUrl + 'application/manage/list_roles?id=' + data.id_app, function (response) {
            if (!response.error) {
                $.each(response.data, function (key, ele) {
                    mdCreate.find("#cboRole").append("<option value='" + ele.id_role + "'>" + ele.name + "</option>");
                });
                $.each(data, function (key, ele) {
                    mdCreate.find(frmCreate + ' [name="apps[' + key + ']"]').val(ele);
                    if (type == "application") {
                        if (key == "id_app") {
                            mdCreate.find("#content-edit").empty().append("<label>" + data.application + "</label>").removeClass('d-none');
                            mdCreate.find("#content-create").addClass("d-none");
                        }
                    }

                });
                mdCreate.find(frmCreate + ' input,select').removeClass('valid').removeClass('error');
                mdCreate.find(frmCreate + ' label.error').remove();
                mdCreate.modal('show');
                mdCreate.find(frmCreate).validate({
                    submitHandler: function (form) {
                        var btn = $(form).find('button[type=submit]');
                        var data = $(form).serialize() + '&id=' + Request._GET.id;
                        if (type == "application") {
                            var id_app_user = mdCreate.find('#form-create-' + type + ' [name="apps[id_app_user]"]').val();
                            if (id_app_user != "") {
                                _confirm("Si edita el Rol al usuario, se reiniciarán los permisos adicionales y restringidos, ¿Desea continuar?", function () {
                                    create_partial(type, mdCreate, table, data, btn);
                                }, function () {
                                    console.log("CANCELAR");
                                });
                            } else {
                                create_partial(type, mdCreate, table, data, btn);
                            }
                        } else {
                            create_partial(type, mdCreate, table, data, btn);
                        }
                    },
                    rules: {
                        'apps[id_app]': {
                            required: true
                        },
                        'apps[id_role]': {
                            required: true
                        },
                    }
                });
            }
        }, "json");
    };

    $tabs.on("click", function () {
        var type = $(this).attr("data-id");
        var list = $(this).attr("data-list");
        if (list === "true") {
            var table = $("#tb-" + type);
            table.bootstrapTable("destroy");
            table.bootstrapTable({
                escape: false,
                locale: 'es-SP',
                search: true,
                pagination: true,
                pageSize: 10,
                idField: 'id',
                url: moduleUrl + '/' + type + '/list?id=' + Request._GET.id,
                columns: columns()
            });
        }
    });

    $("#md-manage-create-application").on("hidden.bs.modal", function () {
        $("#md-manage-create-application").find("#content-edit").addClass("d-none");
        $("#md-manage-create-application").find("#content-create").removeClass("d-none");
    });

    $(".btnAddPartial").on("click", function () {
        var type = $(this).attr("data-id");
        var mdCreate = $("#md-manage-create-" + type);
        var frmCreate = "#form-create-" + type;
        var table = $("#tb-" + type);
        mdCreate.find(frmCreate + ' input,select').val('').removeClass('valid').removeClass('error');
        mdCreate.find(frmCreate + ' label.error').remove();
        mdCreate.find("#cboAplicacion").empty();
        mdCreate.find("#cboAplicacion").append("<option value=''>Seleccione..</option>");
        $.get(moduleUrl + '/application/list_users?id=' + Request._GET.id, function (response) {
            if (!response.error) {
                $.each(response.data, function (key, ele) {
                    mdCreate.find("#cboAplicacion").append("<option value='" + ele.id_app + "'>" + ele.name + "</option>");
                });
            }
        }, "json");
        mdCreate.find("#cboAplicacion").on("change", function () {
            var id_app = $(this).val();
            mdCreate.find("#cboRole").empty();
            mdCreate.find("#cboRole").append("<option value=''>Seleccione..</option>");
            $.get(Request.Host + Request.BaseUrl + 'application/manage/list_roles?id=' + id_app, function (response) {
                if (!response.error) {
                    $.each(response.data, function (key, ele) {
                        mdCreate.find("#cboRole").append("<option value='" + ele.id_role + "'>" + ele.name + "</option>");
                    });
                }
            }, "json");
        });
        mdCreate.modal('show');
        mdCreate.find(frmCreate).validate({
            submitHandler: function (form) {
                var btn = $(form).find('button[type=submit]');
                var data = $(form).serialize() + '&id=' + Request._GET.id;
                create_partial(type, mdCreate, table, data, btn);
            },
            rules: {
                'apps[id_app]': {
                    required: true
                },
                'apps[id_role]': {
                    required: true
                },
            }
        });
    });
    var create_partial = function (type, mdCreate, table, data, btn) {
        btn.prop({disabled: true}).html('Cargando...');
        $.post(moduleUrl + '/' + type + '/save', data, function (response) {
            if (!response.error) {
                mdCreate.find('#form-create-' + type + ' input').val('').removeClass('valid');
                btn.prop({disabled: false}).html('Guardar');
                table.bootstrapTable('refresh');
                mdCreate.modal('hide');
                noty({type: 'success', text: response.message, timeout: 1000}).show();
            }
        }, "json").fail(function (xhr, status, error) {
            if (xhr.status != 200) {
                noty({type: 'error', text: xhr.responseText, timeout: 1000}).show();
                btn.prop({disabled: false}).html('Guardar');
            }
        });
    };

    $("#form-update-user").validate({
        submitHandler: function (form) {
            var btn = $(form).find('button[type=submit]');
            var data = $(form).serialize();
            update_user(data, btn);
        },
        rules: {
            'general[cod_per]': {
                required: true
            },
            'general[username]': {
                required: true
            },
            'identis[Nombres]': {
                required: true
            },
            'identis[Ape1]': {
                required: true
            },
            'identis[Ape2]': {
                required: true
            },
            'identis[Sexo]': {
                required: true
            },
        }
    });

    $("#form-update-recovery").validate({
        submitHandler: function (form) {
            var btn = $(form).find('button[type=submit]');
            var data = $(form).serialize();
            update_user(data, btn);
        },
        rules: {
            'email': {
                required: true
            }
        }
    });

    var update_user = function (data, btn) {
        btn.prop({disabled: true}).html('Cargando...');
        $.post(controllerUrl + '/update', data, function (response) {
            if (!response.error) {
                btn.prop({disabled: false}).html('Actualizar');
                noty({type: 'success', text: response.message, timeout: 5000}).show();
            }
        }, 'json').fail(function (xhr, status, error) {
            if (xhr.status != 200) {
                noty({type: 'error', text: xhr.responseText}).show();
                btn.prop({disabled: false}).html('Guardar');
            }
        });
    };

    $(".showModalError").on("click", function () {
        var message = $(this).attr("data-message");
        var step = $(this).attr("data-step");
        console.log(message);
        console.log(step);
        swal({
            html: '<div class="table-responsive"><table class="table table-bordered font-size-12"><tr><th>Paso</th><th>Mensaje</th></tr><tr><td>' + step + '</td><td>' + message + '</td></tr></table></div>'
        });
    });
}(window.jQuery));