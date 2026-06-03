<?php

require_once "search-component.php";

// Database Configuration
$host = 'localhost';
$db_name = 'joblens';
$username = 'joblens';
$password = 'joblens';

$companyId = $_GET['id'];
if (empty($companyId)) {
    header("Location: /");
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

	// Check if the given ID exists in the database
	$stmt = $pdo->prepare("SELECT Id FROM company WHERE Id = ? LIMIT 1");
	$stmt->execute([$companyId]);

	if (!$stmt->fetch()) {
		header("Location: /");
    	exit;
	}

    // Fetch data for the leaderboard (getting the most recent year available per company)
    $query = "
        SELECT 
            c.Id,
            c.Name,
            MAX(s.Average) Average,
            MAX(s.NonAdminstrativeAverage) NonAdminstrativeAverage,
            MAX(s.NonAdminstrativeMedian) NonAdminstrativeMedian,
            cc.Category,
            cc.Sector
        FROM company c
        INNER JOIN salary s ON c.Id = s.CompanyId
        INNER JOIN companycategory cc ON c.Id = cc.CompanyId
        WHERE s.Year = (SELECT MAX(Year) FROM salary)
        GROUP BY c.Id, cc.Category, cc.Sector
    ";
    
    $stmt = $pdo->query($query);
    $rawData = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Convert PHP array safely to JSON format for Javascript use
$jsonData = json_encode($rawData, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - 超級比一比</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="text-slate-800">

    <nav class="bg-slate-900 text-white p-4 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='index.php'">
                <img src="assets/magnifying-glass.png" alt="Logo" class="w-8 h-8 object-contain">
                <span class="text-xl font-bold tracking-wider">JobLens</span>
            </div>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="search.php?id=<?= $companyId ?>" class="hover:text-cyan-400 transition">企業資訊頁面</a>
                <a href="about.html" class="border border-cyan-500 text-cyan-400 px-5 py-2 rounded-full font-bold hover:bg-cyan-500 hover:text-white transition-all">關於我們</a>
            </div>
        </div>
    </nav>

    <header class="bg-gradient-to-r from-slate-800 to-slate-900 text-white py-10 px-4">
        <div class="container mx-auto text-center max-w-2xl">
            <h1 class="text-2xl md:text-3xl font-bold mb-6">給求職者透視企業的放大鏡</h1>
            <?php renderSearch($pdo); ?>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 max-w-5xl">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-slate-800">超級比一比</h1>
            <p class="text-slate-500 mt-2">完整產業薪資與福利數據比較</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-slate-100 p-6">
            
            <div id="category-selector-container" class="hidden bg-slate-50 border border-slate-200 p-4 rounded-lg flex flex-col lg:flex-row gap-4 justify-between items-center mb-4">
                <div class="flex items-center gap-2 w-full lg:w-auto">
                    <label class="text-xs font-bold text-slate-500 uppercase whitespace-nowrap"><i class="fa-solid fa-tags mr-1"></i> 比對類別</label>
                    <select id="filter-category-index" onchange="updateLabelsAndRender()" class="w-full lg:w-48 p-2 rounded border border-slate-300 text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none shadow-sm cursor-pointer"></select>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200 p-4 rounded-lg flex flex-col lg:flex-row gap-4 justify-between items-center mb-6">
                <div class="flex items-center gap-2 w-full lg:w-auto">
                    <label class="text-xs font-bold text-slate-500 uppercase whitespace-nowrap"><i class="fa-solid fa-filter mr-1"></i> 比較項目</label>
                    <select id="filter-metric" onchange="resetToTargetCompanyPage()" class="w-full lg:w-48 p-2 rounded border border-slate-300 text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none shadow-sm cursor-pointer">
                        <option value="NonAdminstrativeMedian">非擔任主管薪資中位數</option>
                        <option value="NonAdminstrativeAverage">非擔任主管薪資平均數</option>
                        <option value="Average">全員平均薪資</option>
                    </select>
                </div>
                <div class="flex gap-4 w-full lg:w-auto">
                    <div class="flex items-center gap-2 flex-1">
                        <label class="text-xs font-bold text-slate-500 uppercase">範圍</label>
                        <select id="filter-scope" onchange="resetToTargetCompanyPage()" class="w-full p-2 rounded border border-slate-300 text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none shadow-sm cursor-pointer">
                            <option value="category" id="opt-category" selected>同產業</option>
                            <option value="sector" id="opt-sector">同類股</option>
                            <option value="all">全產業</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 flex-1">
                        <label class="text-xs font-bold text-slate-500 uppercase">排序</label>
                        <select id="filter-order" onchange="resetToTargetCompanyPage()" class="w-full p-2 rounded border border-slate-300 text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none shadow-sm cursor-pointer">
                            <option value="desc" selected>由高到低</option>
                            <option value="asc">由低到高</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="chart-container" class="relative w-full transition-all duration-300">
                <p class="text-xs text-slate-400 mb-4 text-right">單位：萬元 / 年</p>
                <canvas id="leaderboard-chart"></canvas>
            </div>

            <div id="pagination-controls" class="flex flex-wrap justify-center items-center gap-2 mt-6 pt-4 border-t border-slate-100">
                </div>
        </div>
    </main>

    <script>
        Chart.defaults.font.family = "'Noto Sans TC', sans-serif";
        Chart.defaults.color = '#64748b';

        const currentCompanyId = <?= $companyId ?>; 
        const rawData = <?php echo $jsonData; ?>;
        
        let leaderboardChart = null; 
        let filteredSortedList = []; 
        let currentPage = 1;
        let companyBelongsToPage = 1; // Tracks the page index where the searched company is located
        const itemsPerPage = 10;

        const matchingCompanyProfiles = rawData.filter(c => c['Id'] === currentCompanyId);
        let selectedProfileIndex = 0;

        function initCategorySelector() {
            const container = document.getElementById('category-selector-container');
            if (matchingCompanyProfiles.length > 1) {
                const selectEl = document.getElementById('filter-category-index');
                selectEl.innerHTML = '';
                
                matchingCompanyProfiles.forEach((profile, index) => {
                    const option = document.createElement('option');
                    option.value = index;
                    option.textContent = profile['Category'];
                    if (profile['Sector']) {
                        option.textContent += ` - ${profile['Sector']}`;
                    }
                    selectEl.appendChild(option);
                });
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        function updateLabelsAndRender() {
            if (matchingCompanyProfiles.length > 1) {
                selectedProfileIndex = parseInt(document.getElementById('filter-category-index').value) || 0;
            }
            const currentActiveProfile = matchingCompanyProfiles[selectedProfileIndex] || matchingCompanyProfiles[0];

            if (currentActiveProfile) {
                document.getElementById('opt-category').textContent = `同產業 (${currentActiveProfile['Category'] || '未分類'})`;
                document.getElementById('opt-sector').textContent = `同類股 (${currentActiveProfile['Sector'] || '未分類'})`;
            }
            resetToTargetCompanyPage();
        }

        function resetToTargetCompanyPage() {
            const metricKey = document.getElementById('filter-metric').value;
            const scope = document.getElementById('filter-scope').value;
            const order = document.getElementById('filter-order').value;
            
            const activeProfile = matchingCompanyProfiles[selectedProfileIndex] || matchingCompanyProfiles[0];
            let tempGrid = [...rawData];
            
            if (activeProfile) {
                if (scope === 'category') {
                    tempGrid = rawData.filter(item => item['Category'] === activeProfile['Category']);
                } else if (scope === 'sector') {
                    tempGrid = rawData.filter(item => item['Sector'] === activeProfile['Sector']);
                }
            }

            const seen = new Set();
            tempGrid = tempGrid.filter(item => {
                const duplicate = seen.has(item.Id);
                seen.add(item.Id);
                return !duplicate;
            });

            tempGrid.sort((a, b) => order === 'desc' ? b[metricKey] - a[metricKey] : a[metricKey] - b[metricKey]);
            filteredSortedList = tempGrid;

            // Find target item rank inside full context to resolve active page number
            const targetIndex = filteredSortedList.findIndex(item => item.Id === currentCompanyId);
            if (targetIndex !== -1) {
                companyBelongsToPage = Math.floor(targetIndex / itemsPerPage) + 1;
                currentPage = companyBelongsToPage;
            } else {
                companyBelongsToPage = null;
                currentPage = 1;
            }

            renderLeaderboard();
        }

        function renderLeaderboard() {
            const metricKey = document.getElementById('filter-metric').value;
            
            const totalItems = filteredSortedList.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
            
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * itemsPerPage;
            const pageSlicedData = filteredSortedList.slice(startIndex, startIndex + itemsPerPage);

            const containerEl = document.getElementById('chart-container');
            const fixedBarHeight = 36; 
            const baselinePadding = 50; 
            const dynamicContainerHeight = (pageSlicedData.length * fixedBarHeight) + baselinePadding;
            containerEl.style.height = `${dynamicContainerHeight}px`;

            const labels = pageSlicedData.map((d, index) => {
                const absoluteRank = startIndex + index + 1;
                return `${absoluteRank}. ${d['Name']}`;
            });

            const data = pageSlicedData.map(d => d[metricKey] / 10000);
            const bgColors = pageSlicedData.map(d => d['Id'] === currentCompanyId ? 'rgba(8, 145, 178, 0.8)' : 'rgba(203, 213, 225, 0.6)');
            const borderColors = pageSlicedData.map(d => d['Id'] === currentCompanyId ? 'rgb(8, 145, 178)' : 'rgb(148, 163, 184)');
            const tickColors = pageSlicedData.map(d => d['Id'] === currentCompanyId ? '#0891b2' : '#64748b');

            const canvas = document.getElementById('leaderboard-chart');
            if (leaderboardChart) {
                leaderboardChart.destroy();
            }

            leaderboardChart = new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: bgColors,
                        borderColor: borderColors,
                        borderWidth: 1,
                        borderRadius: 4,
                        barThickness: 28, 
                    }]
                },
                options: {
                    indexAxis: 'y', 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false }, 
                        tooltip: { 
                            callbacks: { 
                                label: function(c) { return c.raw ? c.raw.toLocaleString() + ' 萬' : '0 萬'; } 
                            } 
                        } 
                    },
                    scales: {
                        y: { 
                            ticks: { 
                                color: function(c) { return tickColors[c.index]; }, 
                                font: function(c) { return { weight: tickColors[c.index] === '#0891b2' ? 'bold' : 'normal' }; } 
                            } 
                        },
                        x: { 
                            display: false, 
                            grid: { color: '#f1f5f9' } 
                        }
                    }
                }
            });

            renderPaginationControls(totalPages);
        }

        function renderPaginationControls(totalPages) {
            const paginationContainer = document.getElementById('pagination-controls');
            paginationContainer.innerHTML = '';

            if (totalPages <= 1) {
                return; 
            }

            // Back button
            const prevBtn = document.createElement('button');
            prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
            prevBtn.className = `px-3 py-1.5 text-sm rounded border ${currentPage === 1 ? 'text-slate-300 border-slate-200 cursor-not-allowed' : 'text-slate-600 border-slate-300 hover:bg-slate-100 transition'}`;
            if (currentPage !== 1) {
                prevBtn.onclick = () => { currentPage--; renderLeaderboard(); };
            }
            paginationContainer.appendChild(prevBtn);

            // Numeric Pagination Links
            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                
                let baseClasses = "px-3 py-1.5 text-sm rounded border transition ";
                
                if (i === currentPage) {
                    baseClasses += "bg-cyan-600 text-white font-bold border-cyan-600 shadow-sm";
                } else {
                    baseClasses += "border-slate-300 text-slate-600 hover:bg-slate-100";
                }
                
                if (i === companyBelongsToPage) {
                    baseClasses += " ring-cyan-400 ring-offset-1 font-black";
                    if (i !== currentPage) {
                        baseClasses += " bg-cyan-50 border-cyan-300 text-cyan-700 font-black";
                    } else {
                        baseClasses += " bg-cyan-700 font-black";
                    }
                }
                
                pageBtn.className = baseClasses;
                if (i !== currentPage) {
                    pageBtn.onclick = () => { currentPage = i; renderLeaderboard(); };
                }
                
                paginationContainer.appendChild(pageBtn);
            }

            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
            nextBtn.className = `px-3 py-1.5 text-sm rounded border ${currentPage === totalPages ? 'text-slate-300 border-slate-200 cursor-not-allowed' : 'text-slate-600 border-slate-300 hover:bg-slate-100 transition'}`;
            if (currentPage !== totalPages) {
                nextBtn.onclick = () => { currentPage++; renderLeaderboard(); };
            }
            paginationContainer.appendChild(nextBtn);
        }

        window.onload = function() {
            initCategorySelector();
            updateLabelsAndRender();
        };
    </script>
</body>
</html>