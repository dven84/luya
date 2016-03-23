<?php

namespace admin\models;

use admin\Module;

/**
 * This is the model class for table "admin_group".
 *
 * @property int $group_id
 * @property string $name
 * @property string $text
 */
class Group extends \admin\ngrest\base\Model
{
    use \admin\traits\SoftDeleteTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['name'], 'required'],
                [['text'], 'string'],
                [['name'], 'string', 'max' => 255],
        ];
    }

    public function scenarios()
    {
        return [
            'restcreate' => ['name', 'text', 'users'],
            'restupdate' => ['name', 'text', 'users'],
        ];
    }

    public $extraFields = ['users'];
    
    // ngrest

    public $users = [];

    public function ngRestApiEndpoint()
    {
        return 'api-admin-group';
    }
    
    public function attributeLabels()
    {
        return [
            'name' => Module::t('model_group_name'),
            'text' => Module::t('model_group_description'),
        ];
    }
    
    public function ngrestAttributeTypes()
    {
        return [
            'name' => 'text',
            'text' => 'textarea',
        ];
    }
    
    public function ngrestExtraAttributeTypes()
    {
        return [
            'users' => [
                'checkboxRelation',
                'model' => User::className(),
                'refJoinTable' => 'admin_user_group',
                'refModelPkId' => 'group_id',
                'refJoinPkId' => 'user_id',
                'labelFields' => ['firstname', 'lastname', 'email'],
                'labelTemplate' =>  '%s %s (%s)'
            ],
        ];
    }

    public function ngRestConfig($config)
    {
        // load active window to config
        $config->aw->load(['class' => 'admin\aws\GroupAuth', 'alias' => Module::t('model_group_btn_aws_groupauth')]);
        
        // define config
        $this->ngRestConfigDefine($config, 'list', ['name', 'text']);
        $this->ngRestConfigDefine($config, 'create', ['name', 'text', 'users']);
        $this->ngRestConfigDefine($config, 'update', ['name', 'text', 'users']);
        
        // add ability to delete items
        $config->delete = true;
        
        return $config;
    }
}