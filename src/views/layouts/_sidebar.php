<?php

use backend\modules\employee\models\ContractTemplates;
use backend\modules\employee\models\EmployeeRequest;
use common\components\Menu;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use backend\models\AdvanceMoney;

$advanceMoneyRequest = AdvanceMoney::find()->wait()->exists();
$employeeRequest = EmployeeRequest::find()->pending()->exists();

$menu_items = [
    [
        'label' => "داشبورد",
        'icon' => 'far fa-home',
        'group' => 'settings',
        'url' => ['/automation'],
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
                'url' => ['/automation/au-folder/index'],
                'group' => 'GeneralInfo',
            ],
            [
                'label' => Module::t('module', "Au Signature"),
                'icon' => 'far fa-layer-group',
                'url' => ['/automation/au-signature/index'],
                'group' => 'GeneralInfo',
            ],
            [
                'label' => Module::t('module', "Au Print Layouts"),
                'icon' => 'far fa-layer-group',
                'url' => ['/automation/au-print-layout/index'],
                'group' => 'GeneralInfo',
            ],
            [
                'label' => Module::t('module', "Au Users"),
                'icon' => 'far fa-layer-group',
                'url' => ['/automation/au-user/index'],
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
                'url' => ['/automation/au-letter/index'],
            ],
            [
                'label' => Module::t('module', 'Au Letters Internal'),
                'icon' => 'far fa-envelope',
                'group' => 'letters',
                'url' => ['/automation/au-letter-internal/index'],
            ],
            [
                'label' => Module::t('module', 'Au Letters Input'),
                'icon' => 'far fa-envelope',
                'group' => 'letters',
                'url' => ['/automation/au-letter-input/index'],
            ],
            [
                'label' => Module::t('module', 'Au Letters Output'),
                'icon' => 'far fa-envelope',
                'group' => 'letters',
                'url' => ['/automation/au-letter-output/index'],
            ],
        ]
    ],

];
?>
    <aside class="left-sidebar">
        <div class="mb-2">
            <?php $form = ActiveForm::begin([
                'id' => 'ajax-shortcut-sidebar',
                'action' => ['#']
            ]); ?>
            <input class="form-control rounded-0" type='text' id='search' placeholder='جستجو...'>
            <?php ActiveForm::end(); ?>
        </div>
        <!-- Sidebar scroll-->
        <div class="scroll-sidebar">
            <!-- Sidebar navigation-->
            <nav class="sidebar-nav">
                <?= Menu::widget(
                    [
                        'options' => ['id' => 'sidebarnav'],
                        'itemOptions' => ['class' => 'sidebar-item'],
                        'items' => $menu_items,
                    ]
                ) ?>
            </nav>
            <!-- End Sidebar navigation -->
        </div>
        <!-- End Sidebar scroll-->
    </aside>
<?php
$script = <<<JS
var form_ajax =jQuery('#ajax-shortcut-sidebar');
form_ajax.on('beforeSubmit', function(e) {
    e.preventDefault();
    var key_current=$('#search').val();
    searchKeywordApp(key_current);
    $('#search').val('');
    return false;
});
$.extend($.expr[":"], {
"containsIN": function(elem, i, match, array) {
return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
}
});

 $('#search').keyup(function(){
     // Search text
  var text = $(this).val();
 
  // Hide all content class element
  $('.sidebar-item').hide();
  $('.devider').hide(); 

  var sidebar_item_contains_text = $('.sidebar-item:containsIN("'+text+'")');
  // Search and show
  //show sidebar item contains text + nex div.devider
  sidebar_item_contains_text.show().next('.devider').show();
  
  sidebar_item_contains_text.parent().addClass('in');
  
  sidebar_item_contains_text.parent().prev().addClass('active');

    if(text.length === 0){
          $("#sidebarnav ul").removeClass('in');
          $("#sidebarnav a").removeClass('active');
    }
 });
JS;
$this->registerJs($script);

