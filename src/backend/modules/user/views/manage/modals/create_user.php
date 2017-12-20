<div class="modal fade" id="md-manage-create-user">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4><strong>Nuevo Usuario</strong></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal mrg-top-20" id="form-create-user">
                    <div class="row">
                        <div class="col-md-6">
                            <h3>Datos Personales</h3>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-3 control-label">Código personal</label>
                                <div class="col-md-9">
                                    <input type="hidden" class="form-control" name="identis[CodIden]">
                                    <input type="hidden" class="form-control" name="identis[CodTMoti]">
                                    <input type="text" class="form-control" name="identis[CodPer]" autocomplete="off" placeholder="Código Personal">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-3 control-label">Tipo Documento</label>
                                <div class="col-md-9"> 
                                    <?= yii\helpers\Html::dropDownList("identis[Tdocu]", "", app\components\Utils::systemTypeListData(app\components\Constante::TYPE_DOCUMENTO), ["class" => "form-control", "prompt" => "Seleccione...", "id" => "cboTipoDoc"]); ?>
                                </div>
                            </div>
                            <div class="form-group row d-none" id="row_pais">
                                <label for="form-1-1" class="col-md-3 control-label">Pais Proveniente</label>
                                <div class="col-md-9"> 
                                    <?= yii\helpers\Html::dropDownList("identis[Pais]", "", app\components\UChacad::getPais(), ["class" => "form-control", "prompt" => "Seleccione..."]); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-3 control-label">Nombres</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="identis[Nombres]" autocomplete="off" placeholder="Nombres">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-3 control-label">Apellido Paterno</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="identis[Ape1]" autocomplete="off" placeholder="Apellido Paterno">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-3 control-label">Apellido Materno</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="identis[Ape2]" autocomplete="off" placeholder="Apellido Materno">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-3 control-label">Sexo</label>
                                <div class="col-md-9">
                                    <?= yii\helpers\Html::dropDownList("identis[Sexo]", "", app\components\Utils::systemTypeListData(app\components\Constante::TYPE_GENERO), ["class" => "form-control", "prompt" => "Seleccione..."]); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-3 control-label">Fecha de Nacimiento</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control datepicker" name="identis[Fnac]" autocomplete="off" placeholder="Fecha de Nacimiento">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h3>Contacto</h3>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-3 control-label">Teléfono</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="identis[Telefono]" autocomplete="off" placeholder="Teléfono">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-3 control-label">Email</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="identis[Email]" autocomplete="off" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-3 control-label">Dirección</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="identis[Direccion]" autocomplete="off" placeholder="Email">
                                </div>
                            </div>
                            <h3>Configuración</h3>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-4 control-label">¿Acceso a la Red UPCH?</label>
                                <div class="col-md-8"> 
                                    <?= yii\helpers\Html::dropDownList("identis[Acceso]", "", app\components\Utils::systemTypeListData(app\components\Constante::TYPE_PREGUNTA), ["class" => "form-control", "prompt" => "Seleccione..."]); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-4 control-label">¿Correo UPCH?</label>
                                <div class="col-md-8"> 
                                    <?= yii\helpers\Html::dropDownList("identis[CorreoUPCH]", "", app\components\Utils::systemTypeListData(app\components\Constante::TYPE_PREGUNTA), ["class" => "form-control", "prompt" => "Seleccione..."]); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-4 control-label">Situación</label>
                                <div class="col-md-8"> 
                                    <?= yii\helpers\Html::dropDownList("identis[Situacion]", "", app\components\Utils::systemTypeListData(app\components\Constante::TYPE_SITUACION), ["class" => "form-control", "prompt" => "Seleccione...", "id" => "cboSituacion"]); ?>
                                </div>
                            </div>
                            <div class="form-group row d-none" id="row_modalidad">
                                <label for="form-1-1" class="col-md-4 control-label">Modalidad</label>
                                <div class="col-md-8"> 
                                    <?= yii\helpers\Html::dropDownList("identis[Modalidad]", "", app\components\Utils::systemTypeListData(app\components\Constante::TYPE_MODALIDAD), ["class" => "form-control", "prompt" => "Seleccione..."]); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-4 control-label">Fecha de Expiración</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control datepicker" name="identis[Fexp]" autocomplete="off" placeholder="Fecha de Expiración">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="form-1-1" class="col-md-4 control-label">Unidad Solicitante</label>
                                <div class="col-md-8"> 
                                    <?= yii\helpers\Html::dropDownList("identis[Unidad]", "", app\components\UChacad::getUnidad(), ["class" => "form-control", "prompt" => "Seleccione..."]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row d-none" id="message-content">
                        <div class="col-md-12">
                            <div class="alert alert-danger" id="message-label"></div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-success btn-sm" type="submit">Crear</button>
                        <a class="btn btn-default btn-sm" data-dismiss="modal">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>