<h1>
    Articles tagged with
    <!-- h 関数で HTML エンコード出力 ※ htmlspecialchars のラッパー -->
    <!-- HTML インジェクションの問題を防ぐため、データを出力するときは常に h() を使用する -->
    <?=$this->Text->toList(h($tags), 'or')?>
</h1>

<section>
<?php foreach ($articles as $article): ?>
    <article>
        <h4><?=$this->Html->link($article->title, ['controller' => 'Articles', 'action' => 'view', $article->slug])?></h4>
        <span><?=h($article->created)?></span>
    </article>
<?php endforeach;?>
</section>
