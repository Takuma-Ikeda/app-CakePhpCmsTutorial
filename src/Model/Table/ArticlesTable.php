<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Validation\Validator;

class ArticlesTable extends Table
{
    /**
     * 初期化処理
     * @param array $config
     */
    public function initialize(array $config)
    {
        // created や modified カラムを自動的に更新する Timestamp Behavior を追加
        $this->addBehavior('Timestamp');
    }

    /**
     * Model を使った DB 保存する前に施す処理
     * @param Validator $validator
     */
    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isNew() && !$entity->slug) {
            // ヘルパーで記事の title から slug を作成する
            $sluggedTitle = Text::slug($entity->title);
            // スキーマで定義されている最大長に調整
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }

    /**
     * DB 保存する前に Model の値を Validation
     * エラーメッセージも自動的に表示する
     * @param Validator $validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('title', false)
            ->minLength('title', 10)
            ->maxLength('title', 255)

            ->allowEmptyString('body', false)
            ->minLength('body', 10);

        return $validator;
    }
}
