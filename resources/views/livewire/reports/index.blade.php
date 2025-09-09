<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Reports Dashboard</flux:heading>
                <flux:subheading>Generate and analyze business reports</flux:subheading>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Report Selection -->
        <div class="lg:col-span-1">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <h3 class="mb-4 text-lg font-medium text-zinc-900 dark:text-zinc-100">Select Report</h3>
                
                <div class="space-y-3">
                    @foreach($this->reportTypes as $key => $report)
                        <div class="cursor-pointer rounded-lg border p-3 transition-colors {{ $selectedReport === $key ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-zinc-200 hover:border-zinc-300 dark:border-zinc-700' }}"
                             wire:click="$set('selectedReport', '{{ $key }}')">
                            <div class="flex items-start space-x-3">
                                <flux:icon name="{{ $report['icon'] }}" class="mt-1 h-5 w-5 text-zinc-500" />
                                <div class="flex-1">
                                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $report['name'] }}</h4>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $report['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Filters and Controls -->
        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <h3 class="mb-4 text-lg font-medium text-zinc-900 dark:text-zinc-100">Report Filters</h3>
                
                <form wire:submit.prevent="generateReport" class="space-y-4">
                    <!-- Date Range -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <flux:field>
                                <flux:label>Start Date</flux:label>
                                <flux:input type="date" wire:model="startDate" />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>End Date</flux:label>
                                <flux:input type="date" wire:model="endDate" />
                            </flux:field>
                        </div>
                    </div>

                    <!-- Additional Filters -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        @if(in_array($selectedReport, ['customer-balance', 'monthly-sales']))
                            <div>
                                <flux:field>
                                    <flux:label>Customer</flux:label>
                                    <flux:select wire:model="customerId" searchable>
                                        <flux:select.option value="">All Customers</flux:select.option>
                                        @foreach($this->customers as $customer)
                                            <flux:select.option value="{{ $customer->id }}">{{ $customer->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                            </div>
                        @endif

                        @if(in_array($selectedReport, ['monthly-sales', 'driver-performance']))
                            <div>
                                <flux:field>
                                    <flux:label>Driver</flux:label>
                                    <flux:select wire:model="driverId" searchable>
                                        <flux:select.option value="">All Drivers</flux:select.option>
                                        @foreach($this->drivers as $driver)
                                            <flux:select.option value="{{ $driver->id }}">{{ $driver->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                            </div>
                        @endif

                        @if(in_array($selectedReport, ['truck-utilization', 'maintenance-cost']))
                            <div>
                                <flux:field>
                                    <flux:label>Truck</flux:label>
                                    <flux:select wire:model="truckId" searchable>
                                        <flux:select.option value="">All Trucks</flux:select.option>
                                        @foreach($this->trucks as $truck)
                                            <flux:select.option value="{{ $truck->id }}">{{ $truck->registration_number }} ({{ $truck->cab_number }})</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-4">
                        <flux:button variant="outline" wire:click="resetFilters">
                            Reset Filters
                        </flux:button>
                        
                        <div class="flex items-center space-x-3">
                            <flux:button variant="outline" wire:click="exportReport('pdf')">
                                <flux:icon name="document-arrow-down" />
                                Export PDF
                            </flux:button>
                            <flux:button variant="outline" wire:click="exportReport('excel')">
                                <flux:icon name="table-cells" />
                                Export Excel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                <flux:icon name="chart-bar" />
                                Generate Report
                            </flux:button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Report Results -->
            <div class="mt-6 rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <h3 class="mb-4 text-lg font-medium text-zinc-900 dark:text-zinc-100">Report Results</h3>
                
                <div id="report-results" class="min-h-[400px]">
                    <div class="flex h-64 items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart-bar" class="mx-auto h-12 w-12 mb-4" />
                            <p>Select filters and click "Generate Report" to view results</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        document.addEventListener('livewire:init', () => {
            // Listen for report generation events from Livewire
            Livewire.on('generate-report', (data) => {
                generateReport(data.report_type, data.filters);
            });

            Livewire.on('export-report', (data) => {
                exportReport(data.report_type, data.format, data.filters);
            });
        });

        function generateReport(reportType, filters) {
            const url = `/reports/${reportType}`;
            const params = new URLSearchParams(filters);
            
            // Show loading state
            const resultsContainer = document.getElementById('report-results');
            resultsContainer.innerHTML = `
                <div class="flex h-64 items-center justify-center text-zinc-500 dark:text-zinc-400">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p>Generating report...</p>
                    </div>
                </div>
            `;
            
            fetch(`${url}?${params}`)
                .then(response => response.json())
                .then(data => {
                    displayReportResults(data);
                })
                .catch(error => {
                    console.error('Error generating report:', error);
                    resultsContainer.innerHTML = `
                        <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                            <h4 class="font-medium text-red-800 dark:text-red-200">Error Generating Report</h4>
                            <p class="text-sm text-red-600 dark:text-red-300">Please try again or contact support if the problem persists.</p>
                        </div>
                    `;
                });
        }

        function exportReport(reportType, format, filters) {
            const url = `/reports/export/${reportType}`;
            const params = new URLSearchParams({...filters, format});
            
            // Create a temporary link to download the file
            const link = document.createElement('a');
            link.href = `${url}?${params}`;
            link.download = `${reportType}-report.${format === 'excel' ? 'xlsx' : 'pdf'}`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function displayReportResults(data) {
            const resultsContainer = document.getElementById('report-results');
            
            // This is a simplified display - in a real implementation,
            // you would render charts, tables, etc. based on the report type
            resultsContainer.innerHTML = `
                <div class="space-y-4">
                    <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                        <h4 class="font-medium text-green-800 dark:text-green-200">Report Generated Successfully</h4>
                        <p class="text-sm text-green-600 dark:text-green-300">Data loaded: ${data.data?.length || 0} records</p>
                    </div>
                    
                    ${data.summary ? `
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                            ${Object.entries(data.summary).map(([key, value]) => `
                                <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-700">
                                    <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">${key.replace(/_/g, ' ').toUpperCase()}</div>
                                    <div class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">${typeof value === 'number' ? value.toLocaleString() : value}</div>
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}
                    
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                        <p>Report generated at: ${new Date().toLocaleString()}</p>
                        <p>Filters applied: ${JSON.stringify(data.filters)}</p>
                    </div>
                </div>
            `;
        }
    </script>
    @endscript
</div>
