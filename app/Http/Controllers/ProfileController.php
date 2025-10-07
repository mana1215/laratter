<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        if (auth()->user()->is($user)) {
            $tweets = Tweet::query()
                ->where('user_id', $user->id)  // 自分のツイート
                ->orWhereIn('user_id', $user->follows->pluck('id')) // フォローのツイート
                ->latest()
                ->paginate(10);
        } else {
            // 他ユーザー → その人のツイートのみ
            $tweets = $user->tweets()->latest()->paginate(10);
        }

        $user->load(['follows', 'followers']);
        return view('profile.show', compact('user', 'tweets'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // 画像アップロード（任意）
        if ($request->hasFile('avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $path = $request->file('avatar')->store('avatars', 'public'); // storage/app/public/avatars/...
            $validated['avatar_path'] = $path;
        }

        // email変更時は未検証化
        if (array_key_exists('email', $validated) && $validated['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        // 許可フィールドのみ反映
        $user->fill([
            'name'        => $validated['name']          ?? $user->name,
            'email'       => $validated['email']         ?? $user->email,
            'bio'         => $validated['bio']           ?? $user->bio,
            'avatar_path' => $validated['avatar_path']   ?? $user->avatar_path,
        ])->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
