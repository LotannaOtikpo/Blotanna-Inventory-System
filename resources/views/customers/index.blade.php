
@extends('layouts.app')

@section('content')
<div x-data="{ 
    showModal: {{ $errors->any() ? 'true' : 'false' }}, 
    editMode: {{ old('_method') === 'PUT' ? 'true' : 'false' }}, 
    form: { 
        id: @js(old('id')), 
        name: @js(old('name', '')), 
        email: @js(old('email', '')), 
        phone: @js(old('phone', '')), 
        address: @js(old('address', '')) 
    } 
}">
    <header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-4 md:px-8 py-6 sticky top-0 z-10 transition-colors">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-black tracking-tight text-[#111318] dark:text-white">Customers</h2>
                <p class="text-sm text-[#616f89] dark:text-gray-400 mt-1">Manage your customer database.</p>
            </div>
            <button @click="showModal = true; editMode = false; form = { id: null, name: '', email: '', phone: '', address: '' }" 
                class="bg-primary hover:bg-primary/90 text-white flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm w-full md:w-auto">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                <span>Add Customer</span>
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-6">
        <!-- Search -->
        <div class="bg-white dark:bg-[#1e232f] p-2 rounded-xl shadow-sm border border-[#dbdfe6] dark:border-gray-800 transition-colors">
            <form action="{{ route('customers.index') }}" method="GET" class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input name="search" value="{{ request('search') }}" class="block w-full pl-11 pr-4 py-2.5 text-sm border-none bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary/20 placeholder:text-gray-400" placeholder="Search by name, email or phone..." type="text"/>
            </form>
        </div>

        <!-- Table -->
        <div id="customers-table" class="bg-white dark:bg-[#1e232f] rounded-xl shadow-sm border border-[#dbdfe6] dark:border-gray-800 overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-800 border-b border-[#dbdfe6] dark:border-gray-700">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $customer->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $customer->email }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $customer->phone ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="showModal = true; editMode = true; form = { id: {{ $customer->id }}, name: @js($customer->name), email: @js($customer->email), phone: @js($customer->phone), address: @js($customer->address) }" 
                                        class="p-1.5 text-gray-400 hover:text-primary transition-colors" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this.form)" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <span class="material-symbols-outlined text-4xl mb-2 text-gray-300 dark:text-gray-600">group_off</span>
                                <p>No customers found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $customers->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

        <div x-show="showModal" x-transition.scale class="relative min-h-screen flex items-center justify-center p-4">
            <div @click.away="showModal = false" class="relative transform overflow-hidden rounded-xl bg-white dark:bg-[#1e232f] text-left shadow-xl transition-all w-full max-w-md border border-gray-200 dark:border-gray-800">
                <form :action="editMode ? '/customers/' + form.id : '{{ route('customers.store') }}'" method="POST">
                    @csrf
                    <!-- Hidden Method Field for PUT requests -->
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                    
                    <!-- Hidden ID Field to persist state on validation error -->
                    <input type="hidden" name="id" x-model="form.id">
                    
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4" x-text="editMode ? 'Edit Customer' : 'Add New Customer'"></h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" x-model="form.name" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" x-model="form.email" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                
                                {{-- Robust Error Display for Email --}}
                                @error('email') 
                                    <div class="mt-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-2.5">
                                        <p class="text-red-600 dark:text-red-400 text-xs font-bold flex items-start gap-1.5 leading-tight">
                                            <span class="material-symbols-outlined text-[16px] shrink-0">error</span>
                                            <span>{{ $message }}</span>
                                        </p>
                                    </div>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                <input type="text" name="phone" x-model="form.phone" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                                <textarea name="address" x-model="form.address" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" class="inline-flex justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 transition-colors">Save</button>
                        <button type="button" @click="showModal = false" class="inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-transparent px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(form) {
        window.showConfirm('Delete Customer?', 'Are you sure you want to delete this customer? This cannot be undone.')
            .then(result => { if(result) form.submit(); });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('customers-table');
        if (container) {
            container.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && container.contains(link) && link.closest('nav')) {
                    e.preventDefault();
                    const url = link.href;
                    if (!url) return;
                    
                    container.style.opacity = '0.6';
                    container.style.pointerEvents = 'none';
                    
                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.text();
                        })
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newContent = doc.getElementById('customers-table');
                            if (newContent) {
                                container.innerHTML = newContent.innerHTML;
                                const rect = container.getBoundingClientRect();
                                if (rect.top < 0) {
                                    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }
                            } else {
                                window.location.href = url;
                            }
                        })
                        .catch(() => window.location.href = url)
                        .finally(() => {
                            container.style.opacity = '1';
                            container.style.pointerEvents = 'auto';
                        });
                }
            });
        }
    });
</script>
@endpush
@endsection
