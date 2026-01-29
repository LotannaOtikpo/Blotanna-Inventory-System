<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'business_name' => 'required|string|max:255',
            'currency_symbol' => 'required|string|max:5',
            'low_stock_threshold' => 'required|integer|min:1|max:1000',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'invoice_due_days' => 'required|integer|min:0|max:365',
            'timezone' => 'required|string|timezone',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('business_logo')) {
            $oldLogo = Setting::where('key', 'business_logo')->value('value');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            $logoPath = $request->file('business_logo')->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'business_logo'], ['value' => $logoPath]);
        }

        $textSettings = collect($data)->except('business_logo');
        foreach ($textSettings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated Settings',
            'description' => "Modified business configuration.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Settings updated successfully.');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'], 
            'email' => ['required', 'string', 'email:filter', 'max:255', 'unique:users,email,'.$user->id],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $validated['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        } elseif ($request->boolean('remove_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $validated['profile_photo_path'] = null;
        }

        unset($validated['profile_photo']);
        unset($validated['remove_photo']);

        $user->update($validated);
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated Profile',
            'description' => "User updated profile details.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                'confirmed', 
                Password::min(8)->letters()->numbers()->mixedCase()->symbols()->uncompromised()
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Changed Password',
            'description' => "User changed their password.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
