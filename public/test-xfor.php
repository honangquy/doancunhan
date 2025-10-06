<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Minimal Alpine Test</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .card { border: 1px solid #ccc; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Alpine.js x-for Test</h1>
    
    <div x-data="{ 
        items: [
            {id: 1, name: 'Item 1'}, 
            {id: 2, name: 'Item 2'}, 
            {id: 3, name: 'Item 3'}
        ] 
    }">
        <p>Total items: <span x-text="items.length"></span></p>
        
        <h2>Using x-for:</h2>
        <template x-for="item in items" :key="item.id">
            <div class="card">
                <strong x-text="item.name"></strong> (ID: <span x-text="item.id"></span>)
            </div>
        </template>
    </div>
    
    <hr>
    
    <h2>Test với 69 reviewers (giống real data):</h2>
    <div x-data="{ 
        reviewers: <?php 
            $reviewers = [];
            for ($i = 25; $i <= 93; $i++) {
                $reviewers[] = [
                    'user_id' => $i,
                    'full_name' => 'Reviewer User ' . $i,
                    'email' => 'reviewer' . $i . '@huit.edu.vn',
                    'organization' => 'HUIT',
                    'workload' => rand(0, 3),
                    'has_coi' => false
                ];
            }
            echo json_encode($reviewers);
        ?>
    }">
        <p>Total reviewers: <span x-text="reviewers.length"></span></p>
        <p>First reviewer: <span x-text="reviewers[0].full_name"></span></p>
        
        <h3>List (first 5):</h3>
        <template x-for="reviewer in reviewers.slice(0, 5)" :key="reviewer.user_id">
            <div class="card" style="cursor: pointer; border: 2px solid #ddd;">
                <strong x-text="reviewer.full_name"></strong><br>
                <small x-text="reviewer.email"></small><br>
                Workload: <span x-text="reviewer.workload"></span>
            </div>
        </template>
    </div>
</body>
</html>
