<?php

namespace App\Model\Entity;

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
}
