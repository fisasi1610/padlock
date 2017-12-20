<div class="card">
    <div class="tab-info">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a href="#card-tab-1" class="nav-link active tabUsers" data-id='general' data-list='false' role="tab" data-toggle="tab" aria-expanded="true">Datos Generales</a>
            </li>
            <li class="nav-item">
                <a href="#card-tab-2" class="nav-link tabUsers" data-id='recovery' data-list='false' role="tab" data-toggle="tab" aria-expanded="false">Datos de Recuperación</a>
            </li>
            <li class="nav-item">
                <a href="#card-tab-3" class="nav-link tabUsers" data-id='application' data-list='true' role="tab" data-toggle="tab" aria-expanded="false">Aplicaciones</a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active show" id="card-tab-1" aria-expanded="true">
                <div class="pdd-horizon-15 pdd-vertical-20">
                    <?= $this->render("partials/_datos_generales", ["data" => $data, "chacad" => $chacad, "log" => $log]); ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="card-tab-2" aria-expanded="false">
                <div class="pdd-horizon-15 pdd-vertical-20">
                    <?= $this->render("partials/_datos_recuperacion", ["data" => $data]); ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="card-tab-3" aria-expanded="false">
                <div class="pdd-horizon-15 pdd-vertical-20">
                    <?= $this->render("partials/_aplicaciones", ["data" => $data]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
