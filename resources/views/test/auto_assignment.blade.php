<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Auto Assignment</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div x-data="testAutoAssignment()" class="max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h1 class="text-2xl font-bold mb-6">Test Auto Assignment API</h1>
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-3">Database Status</h2>
                <div class="grid grid-cols-4 gap-4 text-sm">
                    <div class="bg-blue-50 p-3 rounded">
                        <div class="font-medium">Papers</div>
                        <div class="text-2xl font-bold text-blue-600" x-text="stats.papers || 0"></div>
                    </div>
                    <div class="bg-green-50 p-3 rounded">
                        <div class="font-medium">Biddings</div>
                        <div class="text-2xl font-bold text-green-600" x-text="stats.biddings || 0"></div>
                    </div>
                    <div class="bg-purple-50 p-3 rounded">
                        <div class="font-medium">Assignments</div>
                        <div class="text-2xl font-bold text-purple-600" x-text="stats.assignments || 0"></div>
                    </div>
                    <div class="bg-orange-50 p-3 rounded">
                        <div class="font-medium">Unassigned</div>
                        <div class="text-2xl font-bold text-orange-600" x-text="stats.unassigned || 0"></div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-3">Available Papers</h2>
                <div class="space-y-2" x-show="papers.length > 0">
                    <template x-for="paper in papers" :key="paper.paper_id">
                        <div class="flex items-center justify-between p-3 border rounded">
                            <div>
                                <div class="font-medium" x-text="'Paper ' + paper.paper_id + ': ' + paper.title"></div>
                                <div class="text-sm text-gray-600" x-text="'Biddings: ' + paper.bidding_count + ' | Assignments: ' + paper.assignment_count"></div>
                            </div>
                            <button @click="form.paper_id = paper.paper_id" 
                                    class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                                Select
                            </button>
                        </div>
                    </template>
                </div>
                <div x-show="papers.length === 0" class="text-gray-500 text-center py-4">
                    Loading papers...
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-3">Test Auto Assignment</h2>
                <form @submit.prevent="testAutoAssign()" class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Paper ID</label>
                        <input type="number" x-model="form.paper_id" class="w-full border rounded px-3 py-2" placeholder="Enter paper ID">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Reviewer Count</label>
                        <input type="number" x-model="form.reviewer_count" min="1" max="5" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Min Bid</label>
                        <select x-model="form.min_bid" class="w-full border rounded px-3 py-2">
                            <option value="0">Không ưu tiên (0)</option>
                            <option value="1">Sẵn sàng (1)</option>
                            <option value="2">Có thể (2)</option>
                            <option value="3">Rất muốn (3)</option>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600" 
                                :disabled="loading">
                            <span x-show="!loading">🚀 Test Auto Assignment</span>
                            <span x-show="loading">⏳ Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="result" class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3" x-text="resultSuccess ? '✅ Success!' : '❌ Error!'"></h3>
            <div class="bg-gray-100 p-4 rounded overflow-x-auto">
                <pre x-text="JSON.stringify(result, null, 2)" class="text-sm"></pre>
            </div>
        </div>
    </div>

    <script>
        function testAutoAssignment() {
            return {
                stats: {},
                papers: [],
                form: {
                    paper_id: 1,
                    reviewer_count: 3,
                    min_bid: 1
                },
                loading: false,
                result: null,
                resultSuccess: false,

                async init() {
                    await this.loadStats();
                    await this.loadPapers();
                },

                async loadStats() {
                    try {
                        // Load actual stats from API calls
                        const statsResponse = await fetch('/api/test/stats', {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            }
                        });
                        
                        if (statsResponse.ok) {
                            this.stats = await statsResponse.json();
                        } else {
                            // Fallback to manual count
                            this.stats = {
                                papers: 5,
                                biddings: 22,
                                assignments: 12,
                                unassigned: 2
                            };
                        }
                    } catch (error) {
                        console.error('Error loading stats:', error);
                        this.stats = {
                            papers: '?',
                            biddings: '?',
                            assignments: '?',
                            unassigned: '?'
                        };
                    }
                },

                async loadPapers() {
                    try {
                        const papersResponse = await fetch('/api/test/papers', {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            }
                        });
                        
                        if (papersResponse.ok) {
                            this.papers = await papersResponse.json();
                        }
                    } catch (error) {
                        console.error('Error loading papers:', error);
                        this.papers = [];
                    }
                },

                async testAutoAssign() {
                    this.loading = true;
                    this.result = null;
                    
                    try {
                        const response = await fetch('/chair/assignments/auto-assign', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();
                        this.result = data;
                        this.resultSuccess = response.ok && data.success;
                        
                        if (this.resultSuccess) {
                            // Refresh stats after successful assignment
                            setTimeout(() => {
                                this.loadStats();
                            }, 1000);
                        }
                        
                    } catch (error) {
                        this.result = { error: error.message };
                        this.resultSuccess = false;
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>