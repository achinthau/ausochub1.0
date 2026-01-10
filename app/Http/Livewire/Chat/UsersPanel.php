<?php

namespace App\Http\Livewire\Chat;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;

class UsersPanel extends Component
{
    public $users;
    public $highlightedUserIds = []; // Store the user to highlight

    protected $listeners = ['highlightUser' => 'highlightUsers'];

    public function mount()
    {
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = User::where('id', '!=', Auth::id())->orderBy('name')->get(); // Default sorting
        $this->reorderUsers();
    }

    public function highlightUsers($userId)
    {
        $userIds = is_array($userId) ? $userId : [$userId];




        // $loggedUserId = Auth::id();
        // $redisKey = "highlighted_users:$loggedUserId";

        // Redis::select(5);

        // // Retrieve existing highlighted user IDs for this user
        // $storedUserIds = Redis::get($redisKey);
        // $storedUserIds = $storedUserIds ? json_decode($storedUserIds, true) : [];

        // // Merge new users while ensuring uniqueness
        // $storedUserIds = array_unique(array_merge($storedUserIds, $userIds));

        // // Save updated list back to Redis
        // Redis::set($redisKey, json_encode($storedUserIds));

        // $this->highlightedUserIds = $storedUserIds;



        $this->reorderUsers();
    }

    // private function reorderUsers()
    // {
    //     // $highlightedUsers = $this->users->whereIn('id', $this->highlightedUserIds);



    //     $loggedUserId = Auth::id();
    // $redisKey = "highlighted_users:$loggedUserId";

    // Redis::select(5);
    // $highlightedUsers = Redis::get($redisKey);
    // $highlightedUsers = $highlightedUsers ? json_decode($highlightedUsers, true) : [];

    // // $normalUsers = $this->users->whereNotIn('id', $highlightedUsers);
    // // Fetch full user objects from the database
    // $highlightedUsers = User::whereIn('id', $highlightedUsers)->get();

    // // Get normal users that are NOT highlighted
    // $normalUsers = User::whereNotIn('id', $highlightedUsers)->get();

    // // Merge both lists while keeping user objects
    // $this->users = $highlightedUsers->merge($normalUsers)->values();



    //     // $normalUsers = $this->users->whereNotIn('id', $this->highlightedUserIds);

    //     // $this->users = $highlightedUsers->merge($normalUsers)->values(); // Reset array keys
    // }


    public function reorderUsers()
    {
        $loggedUserId = Auth::id();
        $redisKey = "highlighted_users:$loggedUserId";

        Redis::select(5);


        $highlightedUserIds = Redis::get($redisKey);
        $highlightedUserIds = $highlightedUserIds ? json_decode($highlightedUserIds, true) : [];


        $highlightedUserIds = Arr::flatten($highlightedUserIds);
        $highlightedUserIds = array_map('intval', $highlightedUserIds);


        $highlightedUserIds = array_diff($highlightedUserIds, [$loggedUserId]);

        $this->highlightedUserIds = $highlightedUserIds;


        $highlightedUsers = User::whereIn('id', $highlightedUserIds)->get();


        $normalUsers = User::whereNotIn('id', array_merge($highlightedUserIds, [$loggedUserId]))->get();


        $this->users = $highlightedUsers->merge($normalUsers)->values();
    }



    public function userSelected($userId)
    {
        // dd($userId);
        $this->emit('userSelected', $userId);

        $loggedUserId = Auth::id();
        $redisKey = "highlighted_users:$loggedUserId";

        Redis::select(5);


        $highlightedUserIds = Redis::get($redisKey);
        $highlightedUserIds = $highlightedUserIds ? json_decode($highlightedUserIds, true) : [];


        if (in_array($userId, $highlightedUserIds)) {

            $highlightedUserIds = array_diff($highlightedUserIds, [$userId]);


            Redis::set($redisKey, json_encode(array_values($highlightedUserIds)));

            $this->reorderUsers(); // To remove highlight & refresh 
        }
    }


    public function render()
    {
        return view('livewire.chat.users-panel');
    }
}
