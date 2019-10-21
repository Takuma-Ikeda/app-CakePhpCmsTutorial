<?php

namespace App\Model\Table;

use Cake\ORM\Query;
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

        // Articles (親:多) Tags (子:多) の Relation を貼る
        $this->belongsToMany('Tags');
    }

    /**
     * Model を使った DB 保存する前に施す処理
     * @param $event
     * @param $entity
     * @param $options
     */
    public function beforeSave($event, $entity, $options)
    {
        if ($entity->tag_string) {
            $entity->tags = $this->_buildTags($entity->tag_string);
        }

        if ($entity->isNew() && !$entity->slug) {
            // ヘルパーで記事の title から slug を作成する
            $sluggedTitle = Text::slug($entity->title);
            // スキーマで定義されている最大長に調整
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }

    protected function _buildTags($tagString)
    {
        // カンマ区切りのタグをトリミング
        $newTags = array_map('trim', explode(',', $tagString));
        // 空っぽのタグを削除
        $newTags = array_filter($newTags);
        // 重複するタグの削減
        $newTags = array_unique($newTags);

        $out = [];
        $query = $this->Tags->find()
            ->where(['Tags.title IN' => $newTags]);

        // 新しいタグのリストの中で既存タグと重複しているものは削除
        foreach ($query->extract('title') as $existing) {
            $index = array_search($existing, $newTags);
            if ($index !== false) {
                unset($newTags[$index]);
            }
        }
        // 既存のタグを追加し直す
        foreach ($query as $tag) {
            $out[] = $tag;
        }
        // 新しいタグを追加
        foreach ($newTags as $tag) {
            $out[] = $this->Tags->newEntity(['title' => $tag]);
        }
        return $out;
    }

    /**
     * DB 保存する前に Model の値を Validation
     * エラーメッセージも自動的に表示する
     * @param Validator $validator
     * @return Validator
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

    /**
     * /articles/tagged にアクセスしたときに呼ばれるカスタムファインダーメソッド
     * @param Query $query
     * @param array $options (tags が渡される)
     * @return Query
     */
    public function findTagged(Query $query, array $options)
    {
        $columns = [
            'Articles.id',
            'Articles.user_id',
            'Articles.title',
            'Articles.body',
            'Articles.published',
            'Articles.created',
            'Articles.slug',
        ];

        $query = $query
            ->select($columns)
            ->distinct($columns);

        if (empty($options['tags'])) {
            // タグが指定されていない場合、タグのない記事を検索
            $query->leftJoinWith('Tags')
                ->where(['Tags.title IS' => null]);
        } else {
            // タグがある場合、そのタグの記事を検索
            $query->innerJoinWith('Tags')
                ->where(['Tags.title IN' => $options['tags']]);
        }

        return $query->group(['Articles.id']);
    }
}
