<?php

namespace hesabro\automation\components;


use hesabro\automation\Module;

/**
 * Class AutomationMenuItems
 * @package hesabro\automation\components
 * @author Nader <nader.bahadorii@gmail.com>
 */
class AutomationMenuItems
{

    public static function items()
    {
        $moduleId ='automation';
        return [
            [
                'label' => "داشبورد",
                'icon' => 'far fa-home',
                'group' => 'settings',
                'url' => ["/$moduleId"],
            ],
            [
                'label' => 'اطلاعات اولیه',
                'icon' => 'far fa-layer-group',
                //'url' => ['/employee/default/index'],
                'group' => 'GeneralInfo',
                'level' => "first-level",
                'items' => [
                    [
                        'label' => Module::t('module', "A Folders"),
                        'icon' => 'far fa-layer-group',
                        'url' => ["/$moduleId/au-folder/index"],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', "Au Signature"),
                        'icon' => 'far fa-layer-group',
                        'url' => ["/$moduleId/au-signature/index"],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', "Au Print Layouts"),
                        'icon' => 'far fa-layer-group',
                        'url' => ["/$moduleId/au-print-layout/index"],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', "Au Users"),
                        'icon' => 'far fa-layer-group',
                        'url' => ["/$moduleId/au-user/index"],
                        'group' => 'GeneralInfo',
                    ],
                ]
            ],
            [
                'label' => 'نامه ها',
                'icon' => 'far fa-envelope',
                //'url' => ['/employee/default/index'],
                'group' => 'letters',
                'level' => "first-level",
                'items' => [
                    [
                        'label' => Module::t('module', 'My Letters'),
                        'icon' => 'far fa-envelope',
                        'group' => 'letters',
                        'url' => ["/$moduleId/au-letter/index"],
                    ],
                    [
                        'label' => Module::t('module', 'Au Letters Internal'),
                        'icon' => 'far fa-envelope',
                        'group' => 'letters',
                        'url' => ["/$moduleId/au-letter-internal/index"],
                    ],
                    [
                        'label' => Module::t('module', 'Au Letters Input'),
                        'icon' => 'far fa-envelope',
                        'group' => 'letters',
                        'url' => ["/$moduleId/au-letter-input/index"],
                    ],
                    [
                        'label' => Module::t('module', 'Au Letters Output'),
                        'icon' => 'far fa-envelope',
                        'group' => 'letters',
                        'url' => ["/$moduleId/au-letter-output/index"],
                    ],
                ]
            ],

        ];
    }
}