<?php
$admin = \App\Models\User::where('name', 'like', '%admin%')->orWhere('email', 'like', '%admin%')->first();
if (!$admin) {
    $admin = \App\Models\User::first();
}

if ($admin) {
    // DB::table update won't touch eloquent timestamps like updated_at
    \Illuminate\Support\Facades\DB::table('foods')->update(['user_id' => $admin->id]);
    echo "Success: Updated all foods with admin user id " . $admin->id . "\n";
} else {
    echo "Error: Could not find any admin user.\n";
}
