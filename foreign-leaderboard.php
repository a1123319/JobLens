<?php
// 外國企業的完整薪資排行榜（介面比照 leaderboard.php）。
// 只和「同一資料來源、同一幣別」的外國企業比較，各公司取各自最新一期的揭露。
// 資料表（foreignsalary 等）尚未建立時視為沒有資料，不讓頁面出錯。

require "util.php";

$host = 'localhost';
$db_name = 'joblens';
$username = 'joblens';
$password = 'joblens';

$companyId = (int)($_GET['id'] ?? 0);
if ($companyId <= 0) {
    header("Location: /");
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$stmt = $pdo->prepare("SELECT Id, Name FROM company WHERE Id = ? LIMIT 1");
$stmt->execute([$companyId]);
$company = $stmt->fetch();
if (!$company) {
    header("Location: /");
    exit;
}
// 台灣公司的排行榜在 leaderboard.php
if ($companyId < 1000000) {
    header("Location: leaderboard.php?id=" . $companyId);
    exit;
}

function foreignRows(PDO $pdo, string $sql, array $params = []): array {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

$sourceLabels = [
    'SEC_PAYRATIO' => '美國 SEC 股東會委託書（員工薪酬中位數）',
    'EDINET'       => '日本有價證券報告書（平均年間給與）',
    'DART'         => '韓國事業報告書（平均薪資）',
];

// 目標公司的來源與幣別（取它最新一筆薪資資料所屬的來源）
$target = foreignRows($pdo, "SELECT Source, Currency, MedianPay FROM foreignsalary WHERE CompanyId = ? ORDER BY Year DESC LIMIT 1", [$companyId])[0] ?? null;
$metric = $target && $target['MedianPay'] !== null ? 'MedianPay' : 'AveragePay';
$rows = [];
if ($target) {
    $rows = foreignRows($pdo, "
        SELECT c.Id, c.Name, fc.Ticker, s.Year, s.Scope, s.{$metric} AS Pay
        FROM foreignsalary s
        JOIN (
            SELECT CompanyId, MAX(Year) AS Year
            FROM foreignsalary
            WHERE Source = ? AND Currency = ? AND {$metric} IS NOT NULL
            GROUP BY CompanyId
        ) latest ON latest.CompanyId = s.CompanyId AND latest.Year = s.Year
        JOIN company c ON c.Id = s.CompanyId
        LEFT JOIN foreigncompany fc ON fc.CompanyId = c.Id
        WHERE s.Source = ? AND s.Currency = ?
    ", [$target['Source'], $target['Currency'], $target['Source'], $target['Currency']]);

    // 每家公司可能屬於多個產業，和台灣排行榜一樣每個 (公司, 產業) 一列，前端再去重
    $cats = [];
    foreach (foreignRows($pdo, "SELECT DISTINCT CompanyId, Category, Sector FROM companycategory WHERE CompanyId >= 1000000") as $c) {
        $cats[$c['CompanyId']][] = ['Category' => $c['Category'], 'Sector' => $c['Sector']];
    }
    $data = [];
    foreach ($rows as $r) {
        foreach ($cats[$r['Id']] ?? [['Category' => null, 'Sector' => null]] as $c) {
            $data[] = $r + $c;
        }
    }
    $rows = $data;
}

$jsonData = json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
$currency = $target['Currency'] ?? 'USD';
$sourceLabel = $sourceLabels[$target['Source'] ?? ''] ?? '';
$metricLabel = $metric === 'MedianPay' ? '員工薪資中位數' : '員工平均薪資';
$symbols = ['USD' => '$', 'JPY' => '¥', 'KRW' => '₩', 'EUR' => '€'];
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - 超級比一比（外國企業）</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="text-slate-800">
    <?php nav($companyId) ?>

    <header class="bg-sky-50 border-b border-sky-100 text-slate-800 py-10 px-4">
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

        <?php if (empty($rows)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8">
            <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                    <i class="fa-solid fa-dollar-sign text-2xl"></i>
                </div>
                <p class="text-slate-500 font-bold">「<?= htmlspecialchars($company['Name']) ?>」尚無可比較的薪資資料</p>
                <a href="search.php?id=<?= $companyId ?>" class="mt-4 text-cyan-700 hover:underline text-sm">回到企業資訊頁面</a>
            </div>
        </div>
        <?php else: ?>
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
                    <select id="filter-metric" class="w-full lg:w-48 p-2 rounded border border-slate-300 text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none shadow-sm cursor-pointer">
                        <option value="Pay" selected><?= $metricLabel ?></option>
                    </select>
                </div>
                <div class="flex gap-4 w-full lg:w-auto">
                    <div class="flex items-center gap-2 flex-1">
                        <label class="text-xs font-bold text-slate-500 uppercase">範圍</label>
                        <select id="filter-scope" onchange="resetToTargetCompanyPage()" class="w-full p-2 rounded border border-slate-300 text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none shadow-sm cursor-pointer">
                            <option value="category" id="opt-category" selected>同產業</option>
                            <option value="sector" id="opt-sector">同類股</option>
                            <option value="all">全部外國企業</option>
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
                <p class="text-xs text-slate-400 mb-4 text-right">單位：<?= $currency ?> / 年</p>
                <canvas id="leaderboard-chart"></canvas>
            </div>

            <div id="pagination-controls" class="flex flex-wrap justify-center items-center gap-2 mt-6 pt-4 border-t border-slate-100">
            </div>
            <div class="text-xs text-slate-400 mt-4 space-y-1">
                <p>資料來源：<?= htmlspecialchars($sourceLabel) ?>。各公司取最新一期揭露，年度可能不同；僅與同一資料來源、同幣別的外國企業比較。</p>
                <p>標示「（僅總部）」的公司，申報主體為控股公司，數字只含總部員工，通常高於集團整體水準。</p>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <?php footer() ?>

    <?php if (!empty($rows)): ?>
    <script>
        Chart.defaults.font.family = "'Noto Sans TC', sans-serif";
        Chart.defaults.color = '#64748b';

        const currentCompanyId = <?= $companyId ?>;
        const rawData = <?= $jsonData ?>;
        const symbol = <?= json_encode($symbols[$currency] ?? ($currency . ' ')) ?>;
        const fmt = v => symbol + Math.round(v).toLocaleString();

        let leaderboardChart = null;
        let filteredSortedList = [];
        let currentPage = 1;
        let companyBelongsToPage = 1; // 目標公司所在的頁碼
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
                    if (profile['Sector']) option.textContent += ` - ${profile['Sector']}`;
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
            const profile = matchingCompanyProfiles[selectedProfileIndex] || matchingCompanyProfiles[0];
            if (profile) {
                document.getElementById('opt-category').textContent = `同產業 (${profile['Category'] || '未分類'})`;
                document.getElementById('opt-sector').textContent = `同類股 (${profile['Sector'] || '未分類'})`;
            }
            resetToTargetCompanyPage();
        }

        function resetToTargetCompanyPage() {
            const scope = document.getElementById('filter-scope').value;
            const order = document.getElementById('filter-order').value;
            const profile = matchingCompanyProfiles[selectedProfileIndex] || matchingCompanyProfiles[0];
            let grid = [...rawData];

            if (profile) {
                if (scope === 'category') grid = rawData.filter(item => item['Category'] === profile['Category']);
                else if (scope === 'sector') grid = rawData.filter(item => item['Sector'] === profile['Sector']);
            }

            const seen = new Set();
            grid = grid.filter(item => {
                const duplicate = seen.has(item.Id);
                seen.add(item.Id);
                return !duplicate;
            });

            grid.sort((a, b) => order === 'desc' ? b.Pay - a.Pay : a.Pay - b.Pay);
            filteredSortedList = grid;

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
            const totalItems = filteredSortedList.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * itemsPerPage;
            const pageSlicedData = filteredSortedList.slice(startIndex, startIndex + itemsPerPage);

            document.getElementById('chart-container').style.height = `${itemsPerPage * 45 + 50}px`;

            // 整個篩選結果的最大值，讓每一頁的 X 軸比例一致
            const globalMaxVal = filteredSortedList.length > 0 ? Math.max(...filteredSortedList.map(item => item.Pay)) : 100;

            const labels = pageSlicedData.map((d, index) => {
                const code = d.Ticker ? ` (${d.Ticker})` : '';
                const tag = d.Scope === '控股總部' ? '（僅總部）' : '';
                return `${startIndex + index + 1}. ${d.Name}${code}${tag}`;
            });
            const isSelf = d => d.Id === currentCompanyId;
            const bgColors = pageSlicedData.map(d => isSelf(d) ? 'rgba(8, 145, 178, 0.8)' : 'rgba(203, 213, 225, 0.6)');
            const borderColors = pageSlicedData.map(d => isSelf(d) ? 'rgb(8, 145, 178)' : 'rgb(148, 163, 184)');
            const tickColors = pageSlicedData.map(d => isSelf(d) ? '#0891b2' : '#64748b');

            if (leaderboardChart) leaderboardChart.destroy();
            leaderboardChart = new Chart(document.getElementById('leaderboard-chart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data: pageSlicedData.map(d => d.Pay),
                        backgroundColor: bgColors, borderColor: borderColors,
                        borderWidth: 1, borderRadius: 4, barThickness: 28,
                    }]
                },
                options: {
                    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: c => `${fmt(c.raw)}（${pageSlicedData[c.dataIndex].Year}年）` } }
                    },
                    scales: {
                        y: {
                            min: 0, max: itemsPerPage - 1,
                            ticks: {
                                autoSkip: false,
                                color: c => tickColors[c.index] || '#64748b',
                                font: c => ({ weight: tickColors[c.index] === '#0891b2' ? 'bold' : 'normal' })
                            }
                        },
                        x: { display: false, min: 0, max: globalMaxVal, grid: { color: '#f1f5f9' } }
                    }
                }
            });

            renderPaginationControls(totalPages);
        }

        function renderPaginationControls(totalPages) {
            const paginationContainer = document.getElementById('pagination-controls');
            paginationContainer.innerHTML = '';
            if (totalPages <= 1) return;

            const prevBtn = document.createElement('button');
            prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
            prevBtn.className = `px-3 py-1.5 text-sm rounded border ${currentPage === 1 ? 'text-slate-300 border-slate-200 cursor-not-allowed' : 'text-slate-600 border-slate-300 hover:bg-slate-100 transition'}`;
            if (currentPage !== 1) prevBtn.onclick = () => { currentPage--; renderLeaderboard(); };
            paginationContainer.appendChild(prevBtn);

            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                let baseClasses = "px-3 py-1.5 text-sm rounded border transition ";
                if (i === currentPage) baseClasses += "bg-cyan-600 text-white font-bold border-cyan-600 shadow-sm";
                else baseClasses += "border-slate-300 text-slate-600 hover:bg-slate-100";
                if (i === companyBelongsToPage) {
                    baseClasses += " ring-cyan-400 ring-offset-1 font-black";
                    baseClasses += i !== currentPage ? " bg-cyan-50 border-cyan-300 text-cyan-700 font-black" : " bg-cyan-700 font-black";
                }
                pageBtn.className = baseClasses;
                if (i !== currentPage) pageBtn.onclick = () => { currentPage = i; renderLeaderboard(); };
                paginationContainer.appendChild(pageBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
            nextBtn.className = `px-3 py-1.5 text-sm rounded border ${currentPage === totalPages ? 'text-slate-300 border-slate-200 cursor-not-allowed' : 'text-slate-600 border-slate-300 hover:bg-slate-100 transition'}`;
            if (currentPage !== totalPages) nextBtn.onclick = () => { currentPage++; renderLeaderboard(); };
            paginationContainer.appendChild(nextBtn);
        }

        window.onload = function() {
            initCategorySelector();
            updateLabelsAndRender();
        };
    </script>
    <?php endif; ?>
</body>
</html>
