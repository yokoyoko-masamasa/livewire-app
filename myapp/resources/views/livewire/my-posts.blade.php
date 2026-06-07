<div class="mx-w-4xl mx-auto p-6 space-y-6" id="my-posts-component" wire:id="{{ $this->getId() }}">
    <flux:heading size="xl" level="1" class="mb-5">自分の記事投稿一覧</flux:heading>

    <div class="flex justify-between items-center mb-6 gap-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" class="w-64" placeholder="タイトルで検索" />

        @auth
            <flux:button href="{{ route('posts.create') }}" wire:navigate variant="primary">
                新規作成
            </flux:button>
        @endauth
    </div>

    @if (session('status'))
        <div class="p-4 bg-green-100 text-green-700 rounded">
            {{ session('status') }}
        </div>
    @endif

    @foreach ($posts as $post)
        <article class="p-4 shadow-lg">
            <flux:text class="mt-4 mb-2">{{ $post->created_at->format('y/m/d') }}</flux:text>
            <flux:heading size="lg" level="2">{{ $post->title }}</flux:heading>
            <div class="flex items-center gap-2 shrink-0 mt-2">
                <flux:button
                    href="posts/{{ $post->id }}/edit"
                    wire:navigate
                    icon="pencil-square"
                    size="sm"
                >編集</flux:button>

                <flux:button
                    onclick="openModal({{ $post->id }})"
                    variant="danger"
                    icon="trash"
                    size="sm"
                >削除</flux:button>
            </div>
        </article>
    @endforeach

    {{ $posts->links() }}
    <div id="modal-overlay" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 w-80 shadow-xl">
            <h2 class="text-lg font-semibold mb-2">記事の削除</h2>
            <p class="text-sm text-zinc-500 mb-6">この操作は取り消せません。本当に削除しますか？</p>
            <div class="flex justify-end gap-3">
                <button id="modal-cancel" class="px-4 py-2 rounded-lg text-sm bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200">
                    キャンセル
                </button>
                <button id="modal-confirm" class="px-4 py-2 rounded-lg text-sm bg-red-500 hover:bg-red-600 text-white">
                    削除する
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    let deleteTargetId = null; // 削除対象の記事IDを一時保存する変数

    function openModal(postId) {
        deleteTargetId = postId; // IDを保存
        document.getElementById('modal-overlay').classList.remove('hidden'); // モーダルを表示
    }

    function closeModal() {
        deleteTargetId = null; // IDをリセット
        document.getElementById('modal-overlay').classList.add('hidden');    // モーダルを非表示
    }

    document.addEventListener('livewire:navigated', function () {
        // 古いイベント登録をリセットする
        ['modal-cancel', 'modal-confirm'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) el.replaceWith(el.cloneNode(true));
        });

        document.getElementById('modal-cancel').addEventListener('click', closeModal);

        document.getElementById('modal-confirm').addEventListener('click', function () {
            const componentId = document.getElementById('my-posts-component').getAttribute('wire:id');
            Livewire.find(componentId).delete(deleteTargetId);
            closeModal();
        });

        document.getElementById('modal-overlay').addEventListener('click', function (e) {
            if (e.target === this) closeModal(); // 背景クリックで閉じる
        });
    });
</script>