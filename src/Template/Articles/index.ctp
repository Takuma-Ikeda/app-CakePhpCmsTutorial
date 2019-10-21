<h1>記事一覧</h1>

<?=$this->Html->link('記事の追加', ['action' => 'add'])?>

<table>
    <tr>
        <th>タイトル</th>
        <th>作成日時</th>
        <th>操作</th>
    </tr>

    <!-- ArticlesController から渡される $articles を展開 -->
    <?php foreach ($articles as $article): ?>
    <tr>
        <td>
            <!-- HtmlHelper を使ってリンク作成 -->
            <!-- ArticlesController の view アクションに $article->slug を引数として渡す -->
            <?=$this->Html->link($article->title, ['action' => 'view', $article->slug])?>
        </td>
        <td>
            <?=$article->created->format(DATE_RFC850)?>
        </td>

        <td>
            <?=$this->Html->link('編集', ['action' => 'edit', $article->slug])?>
            <!-- JS で記事を削除する POST リクエストを行うリンクが作成される -->
            <?=$this->Form->postLink('削除', ['action' => 'delete', $article->slug], ['confirm' => 'よろしいですか?'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
