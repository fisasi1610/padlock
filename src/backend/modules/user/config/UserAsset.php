<?php

namespace app\modules\user\config;

class UserAsset {

    public $js      = [];
    public $css     = [];
    public $depends = [];

    /**
     *
     * @var type 
     */
    public $controller = [
        "manage" => [
            "js"      => [],
            "css"     => [],
            "depends" => [
                'insite\asset\BootstrapTable',
                'insite\asset\JqueryValidation',
            ],
        ]
    ];

    /**
     *
     * @var type 
     */
    public $action = [
        "manage.index" => [
            "js"      => ["manage/index", "manage/list"],
            "css"     => [],
            "depends" => [
                'insite\asset\BootstrapDatepicker'
            ],
        ],
        "manage.edit"  => [
            "js"      => ["manage/edit"],
            "css"     => [],
            "depends" => ['app\assets\GlobalAsset'],
        ]
    ];

}
