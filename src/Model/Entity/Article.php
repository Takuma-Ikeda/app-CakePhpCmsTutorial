<?php

namespace App\Model\Entity;

use Cake\Collection\Collection;
use Cake\ORM\Entity;

class Article extends Entity
{
    /*
     * 常に一括代入 (Mass Assignment) すると危険なので、
     * モデルに対するリクエスト値のアクセス設定を定義
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'slug' => false,
    ];

    /**
     * 仮想/計算フィールドを Entity 追加
     * $article->tag_string でアクセスできるようになる
     */
    protected function _getTagString()
    {
        if (isset($this->_properties['tag_string'])) {
            return $this->_properties['tag_string'];
        }

        if (empty($this->tags)) {
            return '';
        }

        $tags = new Collection($this->tags);
        $str = $tags->reduce(function ($string, $tag) {
            return $string . $tag->title . ', ';
        }, '');
        return trim($str, ', ');
    }
}
