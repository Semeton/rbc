<x-layouts.app title="Edit Transaction">
    <div>
        <x-breadcrumb :items="[
            ['name' => 'Daily Transactions', 'url' => route('transactions.index')],
            ['name' => 'Edit Transaction', 'url' => '#']
        ]" />

        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-6">
                        Edit Transaction - {{ $transaction->date->format('M d, Y') }}
                    </h3>

                    <form action="{{ route('transactions.update', $transaction) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer</label>
                                <select name="customer_id" required class="select2-customer w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Customer</option>
                                    @foreach(\App\Models\Customer::active()->orderBy('name')->get() as $customer)
                                        <option value="{{ $customer->id }}" {{ $transaction->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Driver</label>
                                <select name="driver_id" required class="select2-driver w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Driver</option>
                                    @foreach(\App\Models\Driver::active()->orderBy('name')->get() as $driver)
                                        <option value="{{ $driver->id }}" {{ $transaction->driver_id == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                                @error('driver_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- ATC Allocation Section -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">ATC Allocation</h3>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ATC</label>
                                    <select name="atc_id" required class="select2-atc w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                        <option value="">Select ATC</option>
                                        @foreach(\App\Models\Atc::active()->orderBy('atc_number')->get() as $atc)
                                            <option value="{{ $atc->id }}" {{ $transaction->atc_id == $atc->id ? 'selected' : '' }}>
                                                ATC #{{ $atc->atc_number }} ({{ $atc->company }}) - 
                                                {{ number_format($atc->remaining_tons, 2) }} tons left
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('atc_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    
                                    <!-- ATC Details Display -->
                                    @php
                                        $selectedAtc = \App\Models\Atc::find($transaction->atc_id);
                                    @endphp
                                    @if($selectedAtc)
                                        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                            <div class="text-sm text-blue-800 dark:text-blue-200">
                                                <div class="font-medium text-lg mb-2">ATC #{{ $selectedAtc->atc_number }} Capacity Details</div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <div class="font-medium">Total Capacity</div>
                                                        <div>{{ number_format($selectedAtc->tons, 2) }} tons (₦{{ number_format($selectedAtc->amount, 2) }})</div>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium">Price per Ton</div>
                                                        <div>₦{{ number_format($selectedAtc->price_per_ton, 2) }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium">Already Allocated</div>
                                                        <div>{{ number_format($selectedAtc->allocated_tons, 2) }} tons (₦{{ number_format($selectedAtc->transactions()->where('status', true)->sum('atc_cost'), 2) }})</div>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-green-700 dark:text-green-400">Available</div>
                                                        <div class="text-green-700 dark:text-green-400">{{ number_format($selectedAtc->remaining_tons, 2) }} tons (₦{{ number_format($selectedAtc->remaining_amount, 2) }})</div>
                                                    </div>
                                                </div>
                                                <div class="mt-3 pt-3 border-t border-blue-200 dark:border-blue-700">
                                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                                        Allocation: {{ number_format($selectedAtc->allocation_percentage, 1) }}% used
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tons</label>
                                    <input
                                        name="tons"
                                        type="number"
                                        step="0.01"
                                        value="{{ old('tons', $transaction->tons) }}"
                                        placeholder="Enter tons"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                    />
                                    @error('tons')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                                    <input
                                        name="date"
                                        type="date"
                                        value="{{ $transaction->date->format('Y-m-d') }}"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                    />
                                    @error('date')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Origin</label>
                                <input
                                    name="origin"
                                    value="{{ old('origin', $transaction->origin) }}"
                                    placeholder="Enter origin location"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                />
                                @error('origin')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Destination</label>
                                <input
                                    name="destination"
                                    value="{{ old('destination', $transaction->destination) }}"
                                    placeholder="Enter destination location"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                />
                                @error('destination')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cement Type</label>
                                <input
                                    name="cement_type"
                                    value="{{ old('cement_type', $transaction->cement_type) }}"
                                    placeholder="Enter cement type"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                />
                                @error('cement_type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <select name="status" required class="select2-status w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="active" {{ $transaction->status ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ !$transaction->status ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deport Details</label>
                            <textarea
                                name="deport_details"
                                placeholder="Enter deport details (optional)"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            >{{ old('deport_details', $transaction->deport_details) }}</textarea>
                            @error('deport_details')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ATC Cost</label>
                                <input
                                    name="atc_cost"
                                    type="number"
                                    step="0.01"
                                    value="{{ old('atc_cost', $transaction->atc_cost) }}"
                                    placeholder="Enter ATC cost"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                />
                                @error('atc_cost')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transport Cost</label>
                                <input
                                    name="transport_cost"
                                    type="number"
                                    step="0.01"
                                    value="{{ old('transport_cost', $transaction->transport_cost) }}"
                                    placeholder="Enter transport cost"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                />
                                @error('transport_cost')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('transactions.show', $transaction) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Update Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>