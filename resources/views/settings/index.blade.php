
@extends('layouts.app')

@section('content')
<header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-4 md:px-8 py-6 sticky top-0 z-10 transition-colors">
    <h2 class="text-2xl md:text-3xl font-black tracking-tight text-[#111318] dark:text-white">Settings</h2>
    <p class="text-sm text-[#616f89] dark:text-gray-400 mt-1">Manage system configurations and preferences.</p>
</header>

<div class="flex-1 overflow-y-auto p-4 md:p-8" x-data="settingsApp()">
    <div class="max-w-6xl mx-auto">
        
        <!-- Top Section: Business & Security -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-y-16 lg:gap-8">
            <!-- Business Settings -->
            <section>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">storefront</span>
                    General Configuration
                </h3>
                <div class="bg-white dark:bg-[#1e232f] rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm p-4 md:p-6 h-full transition-colors">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business Name</label>
                            <input type="text" name="business_name" value="{{ old('business_name', $settings['business_name'] ?? 'BizTrack Pro') }}" 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                            @error('business_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Displayed on dashboard and receipts.</p>
                        </div>

                        <!-- Logo Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Business Logo</label>
                            <div class="flex items-center gap-4">
                                <div class="shrink-0">
                                    @if(isset($settings['business_logo']) && $settings['business_logo'])
                                        <img src="{{ route('files.display', ['path' => $settings['business_logo']]) }}" alt="Current Logo" class="h-14 w-14 object-cover bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    @else
                                        <div class="h-14 w-14 flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400">
                                            <span class="material-symbols-outlined text-2xl">image</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="business_logo" accept="image/*" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Displayed on sidebar and receipts.</p>
                                </div>
                            </div>
                            @error('business_logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Currency Symbol</label>
                                <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '$') }}" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                @error('currency_symbol') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Low Stock Limit</label>
                                <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $settings['low_stock_threshold'] ?? 10) }}" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                @error('low_stock_threshold') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Financial & Localization -->
                         <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Financial & Localization</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax Rate (%)</label>
                                    <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate'] ?? 8) }}" 
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Applied to Sales & Invoices.</p>
                                    @error('tax_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Invoice Due (Days)</label>
                                    <input type="number" name="invoice_due_days" value="{{ old('invoice_due_days', $settings['invoice_due_days'] ?? 7) }}" 
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Default payment period.</p>
                                    @error('invoice_due_days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Timezone</label>
                                    <select name="timezone" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                        @foreach(timezone_identifiers_list() as $timezone)
                                            <option value="{{ $timezone }}" {{ (old('timezone', $settings['timezone'] ?? 'Africa/Lagos') == $timezone) ? 'selected' : '' }}>
                                                {{ $timezone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('timezone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="submit" class="bg-primary text-white px-5 py-2.5 rounded-lg font-medium hover:bg-primary/90 transition-colors w-full md:w-auto shadow-sm">
                                Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Security Settings -->
            <section>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">security</span>
                    Security & Account
                </h3>
                <div class="bg-white dark:bg-[#1e232f] rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm p-4 md:p-6 h-full transition-colors">
                    
                    <!-- Profile Update Form -->
                    <form action="{{ route('settings.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-5 mb-6">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Profile Picture Input -->
                        <div x-data="{ photoName: null, photoPreview: null, removePhoto: false }">
                            <input type="file" name="profile_photo" class="hidden" x-ref="photo" x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                        removePhoto = false;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            ">
                            <!-- Hidden input to signal removal -->
                            <input type="hidden" name="remove_photo" :value="removePhoto ? 1 : 0">
                            
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Profile Picture <span class="text-gray-400 font-normal">(Optional)</span></label>
                            
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <!-- Current Profile Photo (DB) -->
                                <div class="mt-2" x-show="!photoPreview && !removePhoto">
                                    <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="rounded-full h-14 w-14 object-cover border border-gray-200 dark:border-gray-700">
                                </div>
                                
                                <!-- New Profile Photo Preview -->
                                <div class="mt-2" x-show="photoPreview" style="display: none;">
                                    <span class="block rounded-full w-14 h-14 bg-cover bg-no-repeat bg-center border border-gray-200 dark:border-gray-700"
                                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                    </span>
                                </div>
                                
                                <!-- Placeholder (When Removed) -->
                                <div class="mt-2" x-show="!photoPreview && removePhoto" style="display: none;">
                                     <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" class="rounded-full h-14 w-14 object-cover border border-gray-200 dark:border-gray-700 opacity-75 grayscale">
                                </div>

                                <div class="flex items-center gap-3">
                                    <button type="button" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md px-3 py-2 text-sm font-medium shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" x-on:click.prevent="$refs.photo.click()">
                                        Select New Photo
                                    </button>
                                    
                                    <button type="button" class="text-red-600 hover:text-red-700 text-sm font-medium underline decoration-transparent hover:decoration-red-600 transition-all"
                                        x-show="photoPreview || (!removePhoto && '{{ Auth::user()->profile_photo_path }}')"
                                        x-on:click="removePhoto = true; photoPreview = null; photoName = null; $refs.photo.value = null"
                                        style="display: none;">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            @error('profile_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-gray-900 dark:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors shadow-sm w-full sm:w-auto">
                                Update Profile
                            </button>
                        </div>
                    </form>

                    <!-- Password Dropdown -->
                    <div x-data="{ open: {{ $errors->has('current_password') || $errors->has('password') ? 'true' : 'false' }} }" class="border-t border-gray-100 dark:border-gray-700 pt-5">
                        <button @click="open = !open" type="button" class="flex items-center justify-between w-full text-left group">
                            <span class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-400 group-hover:text-primary transition-colors">lock_reset</span>
                                Change Password
                            </span>
                            <span class="material-symbols-outlined text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        <div x-show="open" style="display: none;" class="mt-4">
                            <form action="{{ route('settings.password') }}" method="POST" class="space-y-4 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                                @csrf
                                @method('PUT')
                                
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Current Password</label>
                                    <input type="password" name="current_password" required 
                                        class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm focus:ring-primary focus:border-primary">
                                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">New Password</label>
                                    <input type="password" name="password" required 
                                        class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm focus:ring-primary focus:border-primary">
                                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" required 
                                        class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm focus:ring-primary focus:border-primary">
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm w-full sm:w-auto">
                                        Save Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <!-- Appearance Section -->
        <section class="mt-16 pt-10 border-t border-gray-100 dark:border-gray-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">palette</span>
                Appearance
            </h3>
            <div class="bg-white dark:bg-[#1e232f] rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm p-4 md:p-6 transition-colors">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Light Mode -->
                    <button @click="setTheme('light')" 
                        :class="currentTheme === 'light' ? 'ring-2 ring-primary bg-gray-50 dark:bg-gray-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800'"
                        class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 transition-all">
                        <div class="size-10 rounded-full bg-white border border-gray-200 flex items-center justify-center shadow-sm shrink-0">
                            <span class="material-symbols-outlined text-amber-500">light_mode</span>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-gray-900 dark:text-white">Light Mode</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Default bright theme</p>
                        </div>
                    </button>

                    <!-- Dark Mode -->
                    <button @click="setTheme('dark')" 
                        :class="currentTheme === 'dark' ? 'ring-2 ring-primary bg-gray-50 dark:bg-gray-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800'"
                        class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 transition-all">
                        <div class="size-10 rounded-full bg-gray-900 border border-gray-700 flex items-center justify-center shadow-sm shrink-0">
                            <span class="material-symbols-outlined text-blue-300">dark_mode</span>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-gray-900 dark:text-white">Dark Mode</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Easy on the eyes</p>
                        </div>
                    </button>

                    <!-- System -->
                    <button @click="setTheme('system')" 
                        :class="currentTheme === 'system' ? 'ring-2 ring-primary bg-gray-50 dark:bg-gray-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800'"
                        class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 transition-all">
                        <div class="size-10 rounded-full bg-gradient-to-br from-white to-gray-900 border border-gray-200 flex items-center justify-center shadow-sm shrink-0">
                            <span class="material-symbols-outlined text-gray-500 mix-blend-difference">settings_brightness</span>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-gray-900 dark:text-white">Match System</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Auto-detect preference</p>
                        </div>
                    </button>
                </div>
            </div>
        </section>

        <!-- Data Management Section -->
        <section class="mt-12 pb-12">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">auto_delete</span>
                Data Management
            </h3>
            <div class="bg-white dark:bg-[#1e232f] rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm p-4 md:p-6 transition-colors">
                 <div class="flex items-center justify-between gap-4">
                     <div>
                         <h4 class="font-bold text-gray-900 dark:text-white">Recently Deleted Items</h4>
                         <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View, restore, or permanently delete items that were removed in the last 30 days.</p>
                     </div>
                     <a href="{{ route('trash.index') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium transition-colors">
                         <span class="material-symbols-outlined">restore_from_trash</span>
                         <span>View Recycle Bin</span>
                     </a>
                 </div>
            </div>
        </section>
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
<script>
    function settingsApp() {
        return {
            currentTheme: localStorage.theme || 'system',
            
            init() {
                // Ensure UI matches current reality on load
                if (!('theme' in localStorage)) this.currentTheme = 'system';
            },

            setTheme(val) {
                this.currentTheme = val;
                
                if (val === 'system') {
                    localStorage.removeItem('theme');
                    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                } else if (val === 'dark') {
                    localStorage.theme = 'dark';
                    document.documentElement.classList.add('dark');
                } else {
                    localStorage.theme = 'light';
                    document.documentElement.classList.remove('dark');
                }
            }
        }
    }
</script>
@endsection
