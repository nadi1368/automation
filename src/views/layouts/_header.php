<?php

use hesabro\automation\models\AuLetter;
use common\models\Branch;
use common\models\Settings;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $resellerOrderCount integer */
/* @var $orderCount integer */
/* @var $cancelRequestCount integer */


$css = <<< CSS
header ul.navbar-nav.mr-auto i {
font-size: large;
}
CSS;
$this->registerCss($css);

$websiteName = Settings::get('web_site_name');
?>
<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header">
            <!-- This is for the sidebar toggle which is visible on mobile only -->
            <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
                <i class="fal fa-bars"></i></a>
            <a class="navbar-brand" href="<?= Url::to(['/site/index']) ?>">
                <!-- Logo icon -->
                <b class="logo-icon">
                    حسابرو <?= $websiteName != null ? Html::tag('span', '(' . $websiteName . ')', ['style' => 'font-size: 0.7rem; font-weight: 500', 'id' => 'client_name']) : '' ?>
                </b>
                <!--End Logo icon -->
            </a>
            <!-- ============================================================== -->
            <!-- End Logo -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Toggle which is visible on mobile only -->
            <!-- ============================================================== -->
            <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
               data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
               aria-expanded="false" aria-label="Toggle navigation"><i class="fal fa-ellipsis-h"></i></a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse collapse" id="navbarSupportedContent">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav float-left mr-auto">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light"
                                                          href="javascript:void(0)" data-sidebartype="mini-sidebar"><i
                                class="fal fa-bars font-18"></i></a>
                </li>


                <li class="nav-item btn-group">
                    <a class="nav-link dropdown-toggle overflow-hidden font-bold" data-toggle="dropdown"
                       aria-haspopup="true"
                       aria-expanded="false" href="javascript:void(0)">

                        <?= Branch::findOne(Yii::$app->user->identity->getCurrentBranch())?->b_name_1 ?>
                        <span class="fal fa-angle-down ml-2 user-text"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-left dropdown-menu-sm-right animated flipInY">
                        <?php foreach (Branch::itemAlias('MyList') as $id => $branch_name) : /** @var string $branch_name */ ?>
                            <?= Html::a($branch_name, ['/branch/set-current', 'branch_id' => $id], ['class' => 'dropdown-item']) ?>
                        <?php endforeach; ?>
                    </div>
                </li>


                <li class="nav-item btn-group">
                    <a class="nav-link dropdown-toggle overflow-hidden font-bold" data-toggle="dropdown"
                       aria-haspopup="true"
                       aria-expanded="false" href="javascript:void(0)">

                        ایجاد نامه
                        <span class="fal fa-angle-down ml-2 user-text"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-left dropdown-menu-sm-right animated flipInY">
                        <?= Html::a(Module::t('module', 'Create Letter Internal'), ['au-letter-internal/create'], ['class' => 'dropdown-item']) ?>
                        <?= Html::a(Module::t('module', 'Create Letter Input'), ['au-letter-input/create'], ['class' => 'dropdown-item']) ?>
                        <?= Html::a(Module::t('module', 'Create Letter Output Between System'), ['au-letter-output/create', 'type' => AuLetter::INPUT_OUTPUT_SYSTEM], ['class' => 'dropdown-item']) ?>
                        <?= Html::a(Module::t('module', 'Create Letter Output Out of System'), ['au-letter-output/create'], ['class' => 'dropdown-item']) ?>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <?= Html::a('ثبت مشخصات سازمان / شرکت',
                        "javascript:void(0)",
                        [
                            'class' => 'nav-link dropdown-toggle',
                            'title' => 'ثبت مشخصات سازمان / شرکت',
                            'id' => 'shortcut-customer-create-modal',
                            'data-size' => 'modal-xl',
                            'data-title' => 'ثبت مشخصات سازمان / شرکت',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-pjax',
                            'data-url' => Url::to(['au-user/create']),
                        ]);
                    ?>
                </li>


            </ul>

            <?= $this->render('@backend/views/layouts/_navbar_profile') ?>
        </div>
    </nav>
</header>
