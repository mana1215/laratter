<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('User詳細') }}
    </h2>
  </x-slot>

  <div class="py-10">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

      {{-- プロフィールヘッダー（重ねない・横並び） --}}
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
        <div class="p-6 sm:p-8">
          <div class="flex items-start gap-4">

            {{-- アバター（64px固定） --}}
            <img
              src="{{ $user->avatar_url ?? 'https://placehold.co/64x64?text=U' }}"
              alt="{{ $user->name }} avatar"
              class="rounded-full object-cover shrink-0 ring-2 ring-white dark:ring-gray-700"
              style="width:64px; height:64px;"
            >

            <div class="flex-1">
              {{-- ユーザー名 --}}
              <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ $user->name }}
              </h1>

              {{-- ★ 自分/他人ボタン行（hover無関係・常時表示を強制） --}}
              @php
                $me = auth()->user();
                $isMe =
                  auth()->check() && (
                    ($me && method_exists($me, 'is') && $me->is($user)) ||
                    ($me && (string)$me->id === (string)$user->id) ||
                    ($me && method_exists($me, 'getAuthIdentifier') && (string)$me->getAuthIdentifier() === (string)$user->getAuthIdentifier())
                  );
              @endphp

              <div class="mt-2">
                @if ($isMe)
                  <a href="{{ route('profile.edit') }}"
                     class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                     style="display:inline-flex;opacity:1;visibility:visible;z-index:10;position:relative;">
                    プロフィールを編集
                  </a>
                @elseif(auth()->check())
                  @if ($user->followers->contains(auth()->id()))
                    <form action="{{ route('follow.destroy', $user) }}" method="POST" class="inline"
                          style="display:inline-block;opacity:1;visibility:visible;z-index:10;position:relative;">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="px-3 py-1.5 rounded-md text-sm font-medium bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600">
                        unfollow
                      </button>
                    </form>
                  @else
                    <form action="{{ route('follow.store', $user) }}" method="POST" class="inline"
                          style="display:inline-block;opacity:1;visibility:visible;z-index:10;position:relative;">
                      @csrf
                      <button type="submit"
                              class="px-3 py-1.5 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700">
                        follow
                      </button>
                    </form>
                  @endif
                @endif
              </div>

              {{-- サブ情報 --}}
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                アカウント作成日時: {{ $user->created_at->format('Y-m-d H:i') }}
              </div>
              <div class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                following: <span class="font-semibold">{{ $user->follows->count() }}</span>
                /
                followers: <span class="font-semibold">{{ $user->followers->count() }}</span>
              </div>

              {{-- 自己紹介 --}}
              <div class="mt-3 text-gray-800 dark:text-gray-200 whitespace-pre-line">
                @if(!empty($user->bio))
                  {{ $user->bio }}
                @else
                  <span class="text-gray-500 dark:text-gray-400 text-sm">自己紹介はまだありません。</span>
                @endif
              </div>

              <div class="mt-3">
                <a href="{{ route('tweets.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">一覧に戻る</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ツイート一覧（40pxアイコン） --}}
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
        <div class="p-6">
          <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">ツイート</h2>

          @if ($tweets->count())
            <div class="mb-4">
              {{ $tweets->appends(request()->input())->links() }}
            </div>

            @foreach ($tweets as $tweet)
              <div class="py-4 first:pt-0 last:pb-0 border-b last:border-none border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-3">
                  <a href="{{ route('profile.show', $tweet->user) }}">
                    <img
                      src="{{ optional($tweet->user)->avatar_url ?? 'https://placehold.co/40x40?text=U' }}"
                      alt="{{ optional($tweet->user)->name }} avatar"
                      class="rounded-full object-cover shrink-0"
                      style="width:40px; height:40px;"
                    >
                  </a>
                  <div class="flex-1">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                      <a href="{{ route('profile.show', $tweet->user) }}"
                         class="font-semibold text-gray-900 dark:text-gray-100 hover:underline">
                        {{ optional($tweet->user)->name ?? 'Unknown' }}
                      </a>
                      <span class="ml-2 text-xs text-gray-500">
                        {{ $tweet->created_at->format('Y/m/d H:i') }}
                      </span>
                    </div>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $tweet->tweet }}</p>

                    <div class="mt-2 flex items-center gap-4">
                      @if ($tweet->liked->contains(optional(auth()->user())->id))
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

                      <a href="{{ route('tweets.show', $tweet) }}" class="text-blue-500 hover:text-blue-700 text-sm">
                        詳細を見る
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach

            <div class="mt-4">
              {{ $tweets->appends(request()->input())->links() }}
            </div>
          @else
            <p class="text-gray-500">投稿はまだありません。</p>
          @endif
        </div>
      </div>

    </div>
  </div>
</x-app-layout>
