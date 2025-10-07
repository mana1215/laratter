<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Tweet一覧') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">

          @foreach ($tweets as $tweet)
            <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">

              {{-- アイコン＋名前＋日時（Twitter風の上部ヘッダ） --}}
              <div class="flex items-start gap-3 mb-2">
                <a href="{{ route('profile.show', $tweet->user) }}">
                  <img
                    src="{{ optional($tweet->user)->avatar_url ?? 'https://placehold.co/40x40?text=User' }}"
                    alt="{{ optional($tweet->user)->name }} avatar"
                    class="rounded-full object-cover shrink-0"
                    style="width: 40px; height: 40px;"
                  >
                </a>

                <div class="flex-1">
                  <div class="flex items-center gap-2">
                    <a href="{{ route('profile.show', $tweet->user) }}"
                       class="text-sm font-semibold text-gray-800 dark:text-gray-200 hover:underline">
                      {{ optional($tweet->user)->name ?? 'Unknown' }}
                    </a>
                    <span class="text-xs text-gray-500">
                      {{ $tweet->created_at->format('Y/m/d H:i') }}
                    </span>
                  </div>

                  {{-- 本文 --}}
                  <p class="text-gray-800 dark:text-gray-300 mt-1">
                    {{ $tweet->tweet }}
                  </p>

                  {{-- 詳細リンク --}}
                  <a href="{{ route('tweets.show', $tweet) }}"
                     class="text-blue-500 hover:text-blue-700 text-sm mt-1 inline-block">
                    詳細を見る
                  </a>

                  {{-- アクション行：いいね & リポスト --}}
                  <div class="flex items-center gap-4 mt-2">
                    {{-- いいね --}}
                    @if ($tweet->liked->contains(auth()->id()))
                      <form action="{{ route('tweets.dislike', $tweet) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700">
                          dislike {{ $tweet->liked->count() }}
                        </button>
                      </form>
                    @else
                      <form action="{{ route('tweets.like', $tweet) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-blue-500 hover:text-blue-700">
                          like {{ $tweet->liked->count() }}
                        </button>
                      </form>
                    @endif

                    {{-- リポスト表示＆ボタン --}}
                    <div class="text-gray-700 dark:text-gray-300">
                      🔁 {{ $tweet->reposts_count }}
                    </div>

                    @php
                      $alreadyReposted = ($tweet->my_repost_count ?? 0) > 0;
                    @endphp

                    @if ($alreadyReposted)
                      <form method="POST" action="{{ route('reposts.destroy', $tweet) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-green-500 hover:text-green-700">リポスト取り消し</button>
                      </form>
                    @else
                      @if ($tweet->user_id !== auth()->id())
                        <form method="POST" action="{{ route('reposts.store', $tweet) }}">
                          @csrf
                          <button class="text-green-500 hover:text-green-700">リポスト</button>
                        </form>
                      @endif
                    @endif
                  </div>
                </div>
              </div>

            </div>
          @endforeach

          {{-- ページネーション --}}
          <div class="mt-6">
            {{ $tweets->links() }}
          </div>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>
