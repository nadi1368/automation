<?php

namespace hesabro\automation\models;

use hesabro\automation\interfaces\StorageModel;
use hesabro\automation\Module;
use Yii;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use hesabro\helpers\behaviors\JsonAdditional;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "automation_print_layout".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $logo
 * @property string|null $additional_data
 * @property int $status
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $deleted_at
 * @property int $slave_id
 *
 * @property string $logoImg
 */
class AuPrintLayoutBase extends \yii\db\ActiveRecord implements StorageModel
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const SIZE_A4_PORTRAIT = 1;
    const SIZE_A5_PORTRAIT = 2;
    const SIZE_A4_LANDSCAPE = 3;
    const SIZE_A5_LANDSCAPE = 4;

    const TEXT_ALIGN_RIGHT = 1;
    const TEXT_ALIGN_CENTER = 2;
    const TEXT_ALIGN_LEFT = 3;

    const FONT_ROYA = 'roya';
    const FONT_TITR = 'titr';
    const FONT_LOTUS = 'lotus';
    const FONT_nazanin = 'nazanin';
    const FONT_IRANNASTALIQ = 'irannastaliq';

    public ?string $headerText = '';
    public ?float $headerHeight = 3;
    public ?string $footerText = '';
    public ?float $footerHeight = 1;
    public ?float $marginTop = 1;
    public ?float $marginRight = 0.5;
    public ?float $marginBottom = 1;
    public ?float $marginLeft = 0.5;
    public ?int $signaturePosition = 1;
    public ?int $size = 1;
    public ?bool $showTitleHeader = true;
    public ?string $fontTitle = '';
    public ?string $fontCCRecipients = '';
    public ?bool $showBorderHeader = true;
    public ?bool $showBorderFooter = true;
    public ?string $orderTitle = 'number,date,attach';
    public ?float $marginTitleBetween = 0.1;
    public ?float $marginTitleLeft = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%automation_print_layout}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'slave_id', 'signaturePosition', 'size'], 'integer'],
            [['headerHeight', 'footerHeight', 'marginTop', 'marginRight', 'marginBottom', 'marginLeft', 'marginTitleBetween', 'marginTitleLeft'], 'number'],
            [['headerHeight', 'footerHeight', 'marginTop', 'marginRight', 'marginBottom', 'marginLeft', 'marginTitleBetween', 'marginTitleLeft', 'signaturePosition', 'size'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['headerText', 'footerText', 'fontTitle', 'fontCCRecipients', 'orderTitle'], 'string'],
            [['showTitleHeader', 'showBorderHeader', 'showBorderFooter'], 'boolean'],
            [['title'], 'string', 'max' => 64],
            ['logo', 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'svg'], 'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'], 'maxSize' => 4 * 1024 * 1024, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'title' => Module::t('module', 'Title'),
            'logo' => 'لوگو',
            'additional_data' => Module::t('module', 'Additional Data'),
            'status' => Module::t('module', 'Status'),
            'created_at' => Module::t('module', 'Created At'),
            'created_by' => Module::t('module', 'Created By'),
            'updated_at' => Module::t('module', 'Updated At'),
            'updated_by' => Module::t('module', 'Updated By'),
            'deleted_at' => Module::t('module', 'Deleted At'),
            'slave_id' => Module::t('module', 'Slave ID'),
            'headerText' => 'متن هدر',
            'headerHeight' => 'ارتفاع هدر (سانتی متر)',
            'footerText' => 'متن فوتر',
            'footerHeight' => 'ارتفاع فوتر (سانتی متر)',
            'marginTop' => 'فاصله از بالا (سانتی متر)',
            'marginRight' => 'فاصله از راست (سانتی متر)',
            'marginBottom' => 'فاصله از پایین (سانتی متر)',
            'marginLeft' => 'فاصله از چپ (سانتی متر)',
            'signaturePosition' => 'محل قرار گیری امضا',
            'size' => 'سایز',
            'showTitleHeader' => 'نمایش عنوان (شماره,تاریخ,پیوست) در هدر',
            'fontTitle' => 'فونت عنوان',
            'fontCCRecipients' => 'فونت رونوشت',
            'showBorderHeader' => 'نمایش خط افقی هدر',
            'showBorderFooter' => 'نمایش خط افقی فوتر',
            'orderTitle' => 'ترتیب نمایش',
            'marginTitleBetween' => 'فاصله بین خطوط عنوان (شماره,تاریخ,پیوست) (سانتی متر)',
            'marginTitleLeft' => 'فاصله عنوان (شماره,تاریخ,پیوست) از چپ (سانتی متر)'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdate()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return AuPrintLayoutQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new AuPrintLayoutQuery(get_called_class());
        return $query->active();
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canActive(): bool
    {
        return $this->status == self::STATUS_INACTIVE;
    }

    /**
     * @return bool
     */
    public function canInActive(): bool
    {
        return $this->status == self::STATUS_ACTIVE;
    }


    /**
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getLogoImg(int $width = 120, int $height = 120): string
    {
        if ($imgSrc = $this->getStorageFileUrl('logo')) {
            return Html::img($imgSrc, ['width' => $width, 'height' => $height]);
        }
        return '';
    }

    /**
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getLogoImgForPrint(): string
    {
        if ($imgSrc = $this->getStorageFileUrl('logo')) {
            return Html::img($imgSrc, ['class' => 'page-logo']);
        }
        return '';
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_ACTIVE;
        }
        $this->headerText = !empty(trim($this->headerText)) ? HtmlPurifier::process($this->headerText) : NULL;
        $this->footerText = !empty(trim($this->footerText)) ? HtmlPurifier::process($this->footerText) : NULL;
        return parent::beforeSave($insert);
    }

    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'Size' =>
                [
                    self::SIZE_A4_PORTRAIT => 'A4 portrait',
                    self::SIZE_A5_PORTRAIT => 'A5 portrait',
                    self::SIZE_A4_LANDSCAPE => 'A4 landscape',
                    self::SIZE_A5_LANDSCAPE => 'A5 landscape',
                ],
            'TextAlign' =>
                [
                    self::TEXT_ALIGN_RIGHT => 'text-right',
                    self::TEXT_ALIGN_CENTER => 'text-center',
                    self::TEXT_ALIGN_LEFT => 'text-left',
                ],
            'TextAlignTitle' =>
                [
                    self::TEXT_ALIGN_RIGHT => 'راست چین',
                    self::TEXT_ALIGN_CENTER => 'وسط چین',
                    self::TEXT_ALIGN_LEFT => 'چپ چین',
                ],
            'Fonts' =>
                [
                    self::FONT_ROYA => 'رویا',
                    self::FONT_TITR => 'تیتر',
                    self::FONT_LOTUS => 'لوتوس',
                    self::FONT_nazanin => 'نازنین',
                    self::FONT_IRANNASTALIQ => 'نستعلیق',
                ],
            'OrderTitle' => [
                'number,date,attach' => 'شماره - تاریخ - پیوست',
                'number,attach,date' => 'شماره - پیوست - تاریخ',
                'date,number,attach' => 'تاریخ - شماره - پیوست',
                'date,attach,number' => 'تاریخ - پیوست - شماره',
                'attach,number,date' => 'پیوست - شماره - تاریخ',
                'attach,date,number' => 'پیوست - تاریخ - شماره',
            ],
            'OrderTitleValue' => [
                'number' => 'printNumber',
                'date' => 'date',
                'attach' => 'printCountAttach',
            ],
            'OrderTitleLabel' => [
                'number' => 'شماره :',
                'date' => 'تاریخ :',
                'attach' => 'پیوست :',
            ],
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    public function getTitleHtml($letter)
    {
        if (str_contains((string)$this->orderTitle, ',')) {
            $explodeTitle = explode(',', $this->orderTitle);
        } else {
            $explodeTitle = explode(',', 'number,date,attach');
        }

        $html = '<div class="col-3 text-left" style="padding-left: ' . ((int)$this->marginTitleLeft) . 'cm;">';
        if ($this->showTitleHeader) {
            $html .= '<div><b class="float-right">' . self::itemAlias('OrderTitleLabel', $explodeTitle[0]) . '</b>' . Html::encode($letter->{self::itemAlias('OrderTitleValue', $explodeTitle[0])}) . '</div>';
            $html .= '<div style="margin-top: ' . ((int)$this->marginTitleBetween) . 'cm;"><b class="float-right">' . self::itemAlias('OrderTitleLabel', $explodeTitle[1]) . '</b>' . Html::encode($letter->{self::itemAlias('OrderTitleValue', $explodeTitle[1])}) . '</div>';
            $html .= '<div style="margin-top: ' . ((int)$this->marginTitleBetween) . 'cm;"><b class="float-right">' . self::itemAlias('OrderTitleLabel', $explodeTitle[2]) . '</b>' . Html::encode($letter->{self::itemAlias('OrderTitleValue', $explodeTitle[2])}) . '</div>';
        } else {
            $html .= '<div>' . Html::encode($letter->{self::itemAlias('OrderTitleValue', $explodeTitle[0])}) . '</div>';
            $html .= '<div style="margin-top: ' . ((int)$this->marginTitleBetween) . 'cm;">' . Html::encode($letter->{self::itemAlias('OrderTitleValue', $explodeTitle[1])}) . '</div>';
            $html .= '<div style="margin-top: ' . ((int)$this->marginTitleBetween) . 'cm;">' . Html::encode($letter->{self::itemAlias('OrderTitleValue', $explodeTitle[2])}) . '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at'
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by'
            ],
            [
                'class' => TraceBehavior::class,
                'ownerClassName' => self::class
            ],
            [
                'class' => LogBehavior::class,
                'ownerClassName' => self::class,
                'saveAfterInsert' => true
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'headerText' => 'String',
                    'headerHeight' => 'Float',
                    'footerText' => 'String',
                    'footerHeight' => 'Float',
                    'marginTop' => 'Float',
                    'marginRight' => 'Float',
                    'marginBottom' => 'Float',
                    'marginLeft' => 'Float',
                    'signaturePosition' => 'Integer',
                    'size' => 'Integer',
                    'showTitleHeader' => 'Boolean',
                    'fontTitle' => 'String',
                    'fontCCRecipients' => 'String',
                    'showBorderHeader' => 'Boolean',
                    'showBorderFooter' => 'Boolean',
                    'orderTitle' => 'String',
                    'marginTitleBetween' => 'Float',
                    'marginTitleLeft' => 'Float',
                ],
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'deleted_at' => time(),
                    'status' => self::STATUS_DELETED
                ],
                'restoreAttributeValues' => [
                    'deleted_at' => null,
                    'status' => self::STATUS_ACTIVE,
                ],
                'replaceRegularDelete' => false, // mutate native `delete()` method
                'allowDeleteCallback' => function () {
                    return false;
                },
                'invokeDeleteEvents' => true
            ]
        ];
    }

    public function getStorageFileContent(string $attribute)
    {
        return $this->getStorageFile($attribute)->one()?->getFileContent();
    }

    public function getStorageFileName(string $attribute)
    {
        return $this->getFileStorageName($attribute);
    }

    public function getStorageFileUrl(string $attribute)
    {
        return $this->getFileUrl($attribute);
    }
}
