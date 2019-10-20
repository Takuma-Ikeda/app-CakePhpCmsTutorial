<?php

namespace App\Controller;

use App\Controller\AppController;

class ArticlesController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        // $this->Paginator など使えるように実体化
        $this->loadComponent('Paginator');

        // AppController 側で include してもよい
        $this->loadComponent('Flash');
    }

    /**
     * @action index
     */
    public function index()
    {
        // Articles モデルを使用して DB からレコードから全件取得 -> paginate() でページ数分け
        $articles = $this->Paginator->paginate($this->Articles->find());

        // src/Template/Articles/index.ctp に articles という変数名で値を渡す
        $this->set(compact('articles'));
    }

    /**
     * @action view
     * @param string $slug
     */
    public function view($slug = null)
    {
        // findBySlug: 「findBy + カラム名」とするだけで where でクエリ抽出できる
        // firstOrFail: 最初のレコードを取得する。失敗すれば NotFoundException を投げる
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        $this->set(compact('article'));
    }

    /**
     * @action add
     */
    public function add()
    {
        // 新規レコード作成用の Model
        $article = $this->Articles->newEntity();

        // POST の場合
        if ($this->request->is('post')) {
            // 1. リクエストデータを取得
            // 2. Article Entity に変換 (marshal)
            // 3. この Entity は ArticlesTable オブジェクトを使用して永続化される
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            // 一旦仮で代入
            $article->user_id = 1;

            // DB 登録実行 -> 成功
            if ($this->Articles->save($article)) {
                // success の引数はマジックメソッド。リダイレクト先に Flash メッセージを渡す
                // $this->Flash->render() の場合だと渡したあとにメッセージがクリアされる
                $this->Flash->success(__('Your article has been saved.'));

                // index アクションだけど URL は /articles に変換される
                return $this->redirect(['action' => 'index']);
            }
            // エラーメッセージで View 描画
            $this->Flash->error(__('Unable to add your article.'));
        }
        // tags テーブルからリストを取得
        $tags = $this->Articles->Tags->find('list');

        $this->set('tags', $tags);
        $this->set('article', $article);
    }

    /**
     * @action edit
     * @param string $slug
     */
    public function edit($slug)
    {
        $article = $this->Articles->findBySlug($slug)->firstOrFail();

        // POST / PUT の場合
        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }
        $tags = $this->Articles->Tags->find('list');
        $this->set('tags', $tags);
        $this->set('article', $article);
    }

    /**
     * @action delete
     * @param string $slug
     */
    public function delete($slug)
    {
        // Web クローラが誤って削除する可能性があるので GET は非推奨
        $this->request->allowMethod(['post', 'delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        if ($this->Articles->delete($article)) {
            // {0} には $article->title を埋め込む
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * @action tags
     * @param string $slug
     */
    /*
    public function tags()
    {
    // 'pass' キーを指定すると、リクエストに渡されたパラメータを取得する
    $tags = $this->request->getParam('pass');

    // ArticlesTable を使用してタグ付きの記事を検索
    $articles = $this->Articles->find('tagged', [
    'tags' => $tags,
    ]);

    $this->set([
    'articles' => $articles,
    'tags' => $tags,
    ]);
    }
     */

    // 上記のようにわざわざリクエスト取得しなくても、可変引数で最初から取れる
    public function tags(...$tags)
    {
        $articles = $this->Articles->find('tagged', [
            'tags' => $tags,
        ]);

        $this->set([
            'articles' => $articles,
            'tags' => $tags,
        ]);
    }
}
