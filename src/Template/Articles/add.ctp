<h1>記事の追加</h1>

<?php
// URL オプションなしで create() を呼び出した場合
// FormHelper はフォーム送信後、現在のアクションに戻す
echo $this->Form->create($article);

// 同名のフォーム要素を作成する (Validation も自動的に行う)
// 第 1 引数: 対応する Model のフィールド名
// 第 2 引数: オプション
echo $this->Form->control('user_id', ['type' => 'hidden', 'value' => 1]);
echo $this->Form->control('title');
echo $this->Form->control('body', ['rows' => '3']);

// 子:多 テーブルの $tags をセレクトボックスの選択肢として表示する
// echo $this->Form->control('tags._ids', ['options' => $tags]);

// 仮想/計算フィールド
echo $this->Form->control('tag_string', ['type' => 'text']);

echo $this->Form->button(__('Save Article'));

echo $this->Form->end();

?>
