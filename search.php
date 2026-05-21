<?php
$year = 2024;

// 1. Database Connection
$host = 'localhost';
$db_name = 'joblens';
$username = 'root';
$password = 'ggyy1155';
$category_links = [
    "半導體" => "semicondictor.html",
    "電腦周邊" => "computer-peripherals.html",
    "休閒娛樂" => "leisure-entertainment.html"
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function getCompany(PDO $pdo, string $id, bool $isUniformId) {
    try {
        $stmt = $pdo->prepare($isUniformId ? "SELECT * FROM company WHERE UniformId = ?" : "SELECT * FROM company WHERE Id = ?");
        // Execute with the provided parameters
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        // Handle or log the error as needed for your application
        die("Database Query Failed: " . $e->getMessage());
    }
}

function formatDate(string $date) {
    $datetime = new DateTime($date);
    $formattedDate = $datetime->format('Y/m/d');

    return $formattedDate;
}

function formatNumber(int $num) {
    $fmt = NumberFormatter::create(Locale::getDefault(), NumberFormatter::DECIMAL);
    $fmt->setAttribute(NumberFormatter::ROUNDING_MODE, NumberFormatter::ROUND_DOWN);

    return $fmt->format($num, NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
}

function formatPercentage(float $num, int $digit) {
    $fmt = NumberFormatter::create(Locale::getDefault(), NumberFormatter::PERCENT);
    $fmt->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $digit);
    $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $digit);
    
    return $fmt->format($num, NumberFormatter::TYPE_DOUBLE);
}

// 2. Validate ID and Redirect if missing
$idInput = isset($_GET['id']) ? trim($_GET['id']) : '';
if (empty($idInput)) {
    header("Location: not-found.html");
    exit;
}

// 3. Fetch Core Company Data (Search by Id or UniformId)
$stmt = $pdo->prepare("
    SELECT c.*, cc.Category, cc.Sector, cc.Subsector,
           s.NonAdminstrativeAverage, s.NonAdminstrativeMedian,
           sr.Year as OccupationalInjuryYear, sr.OccupationalInjuryRate, sr.OccupationInjuryCount, sr.FireIncidentCount,
           ge.Scope1EmissionTonCO2e, ge.Scope2EmissionTonCO2e, ge.Scope3EmissionTonCO2e,
           em.RenewEnergyUsageRate,
           rs.OneHundredAndFour, rs.Official,
           m.FemaleManagerRatio
    FROM company c
    LEFT JOIN salary s ON c.Id = s.CompanyId AND s.Year = 2024
    LEFT JOIN companycategory cc ON c.Id = cc.CompanyId
    LEFT JOIN safetyrisk sr ON c.Id = sr.CompanyId
    LEFT JOIN ghgemissions ge ON c.Id = ge.CompanyId
    LEFT JOIN em ON c.Id = em.CompanyId
    LEFT JOIN recruitmentsource rs ON c.Id = rs.CompanyId
    LEFT JOIN manager m ON c.Id = m.CompanyId
    WHERE c.Id = ? OR c.UniformId = ?
");
$stmt->execute([$idInput, $idInput]);
$company = null;
$safetyRecords = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Save the general company data only once
    if (!$company) {
        $company = $row;
    }

    // Collect safety data into a nested array if a year exists
    if ($row['OccupationalInjuryYear'] !== null) {
        $safetyRecords[$row['OccupationalInjuryYear']] = [
            'rate'  => $row['OccupationalInjuryRate'],
            'count' => $row['OccupationInjuryCount'],
            'fire'  => $row['FireIncidentCount']
        ];
    }
}

// Redirect if no rows were returned at all
if (!$company) {
    header("Location: not-found.html");
    exit;
}

// 4. Fetch Sub-data for JavaScript Arrays
// Recruitment Jobs
$stmt = $pdo->prepare("SELECT Name, Url, Salary FROM recruitment WHERE CompanyId = ?");
$stmt->execute([$company['Id']]);
$jobsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Salary histories
$stmt = $pdo->prepare("SELECT * FROM company c LEFT JOIN salary s ON c.Id = s.CompanyId WHERE c.Id = ? ORDER BY s.Year ASC");
$stmt->execute([$company['Id']]);
$salaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Company list
$stmt = $pdo->prepare("SELECT * FROM company");
$stmt->execute();
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Industry Rankings (Top 10 in same Sector)
$stmt = $pdo->prepare("
    SELECT DISTINCT c.Name, c.Id, s.NonAdminstrativeMedian
    FROM company c
    JOIN salary s ON c.Id = s.CompanyId AND s.Year = 2024
    JOIN companycategory cc ON c.Id = cc.CompanyId
    WHERE cc.Sector = ?
    ORDER BY s.NonAdminstrativeMedian DESC
");
$stmt->execute([$company['Sector']]);
$medians = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Industry Rankings (Top 10 in same Sector)
$stmt = $pdo->prepare("
    SELECT DISTINCT c.Name, c.Id, s.NonAdminstrativeAverage
    FROM company c
    JOIN salary s ON c.Id = s.CompanyId AND s.Year = 2024
    JOIN companycategory cc ON c.Id = cc.CompanyId
    WHERE cc.Sector = ?
    ORDER BY s.NonAdminstrativeAverage DESC
");
$stmt->execute([$company['Sector']]);
$averages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Disasters
$stmt = $pdo->prepare("SELECT * FROM disaster WHERE BusinessUnitUniformId = ? OR ProjectOwnerUniformId  = ?");
$stmt->execute([$company['UniformId'], $company['UniformId']]);
$disasters = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Wordcloud
$stmt = $pdo->prepare("SELECT Emotion as sentiment, Content as text, Frequency as size FROM wordcloud WHERE CompanyId = ?");
$stmt->execute([$company['Id']]);
$wordcloudData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Comments
$stmt = $pdo->prepare("SELECT * FROM comment WHERE CompanyId = ?");
$stmt->execute([$company['Id']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// News
$stmt = $pdo->prepare("SELECT * FROM news n JOIN pnn p ON n.NewsId = p.Id AND n.CompanyId = ?");
$stmt->execute([$company['Id']]);
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?php echo htmlspecialchars($company['Name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.2.2/wordcloud2.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; background-color: #f8fafc; }
        .input-focus-effect:focus { box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.2); border-color: #0891b2; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
        .no-select { user-select: none; -webkit-user-select: none; }
    </style>
</head>
<body class="text-slate-800 relative">

    <nav class="bg-slate-900 text-white p-4 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='/index.html'">
                <img src="/assets/magnifying-glass.png" alt="Logo" class="w-8 h-8 object-contain">
                <span class="text-xl font-bold tracking-wider">JobLens</span>
            </div>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="/supply-chain/<?= $category_links[$company['Category']] ?>" class="hover:text-cyan-400 transition">產業鏈</a>
                <a href="/about.html" class="border border-cyan-500 text-cyan-400 px-5 py-2 rounded-full font-bold hover:bg-cyan-500 hover:text-white transition-all">關於我們</a>
            </div>
        </div>
    </nav>

    <div class="fixed right-4 xl:right-12 top-1/2 transform -translate-y-1/2 z-50 hidden xl:flex items-stretch gap-4 h-[400px]">
        <div class="relative w-24" id="slider-labels"></div>
        <div class="relative w-1.5 bg-slate-200 rounded-full h-full" id="vertical-track">
            <div id="vertical-progress" class="absolute top-0 left-0 w-full bg-cyan-600 rounded-full pointer-events-none" style="height: 0%;"></div>
            <div id="track-dots" class="absolute w-full h-full pointer-events-none"></div>
            <div id="vertical-thumb" class="absolute left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-4 h-4 bg-white border-[3px] border-cyan-600 rounded-full shadow-md cursor-grab z-20 hover:scale-125 transition-transform" style="top: 0%;"></div>
        </div>
    </div>

    <header class="bg-gradient-to-r from-slate-800 to-slate-900 text-white py-10 px-4">
        <div class="container mx-auto text-center max-w-2xl">
            <h1 class="text-2xl md:text-3xl font-bold mb-3">給求職者透視企業的放大鏡</h1>
            <form action="search.php" method="GET" class="relative mt-6">
                 <input type="text" name="id" placeholder="請輸入公司代號或名稱" class="w-full p-3 pl-6 rounded-full text-slate-900 shadow-xl focus:outline-none ring-4 ring-cyan-500/50 transition">
                <button type="submit" class="absolute right-1.5 top-1.5 bg-cyan-600 hover:bg-cyan-700 text-white px-5 py-1.5 rounded-full transition font-medium text-sm">重新搜尋</button>
            </form>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 space-y-16 max-w-6xl">
        
        <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col md:flex-row justify-between items-start md:items-center border-l-8 border-cyan-600">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-3xl font-bold"><?php echo htmlspecialchars($company['Name']); ?> (<?php echo htmlspecialchars($company['Id']); ?>)</h2>
                    <span class="bg-slate-200 text-slate-600 px-2 py-1 rounded text-xs font-bold"><?= htmlspecialchars($company['Category']); ?> - <?php echo htmlspecialchars($company['Subsector'] ?? $company['Sector']); ?></span>
                </div>
                <p class="text-slate-500 text-sm">資料年度：<?= $year ?> | 資料來源：公開資訊觀測站</p>
            </div>
            
            <div class="mt-6 md:mt-0 flex flex-col md:flex-row items-end md:items-center gap-3">
                <a href="/supply-chain/<?= $category_links[$company['Category']] ?>" class="bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white px-5 py-2.5 rounded-full font-bold text-sm transition-all shadow-sm flex items-center gap-2 group border border-blue-200">
                    <img src="/assets/value-chain.png" class="w-5 h-5 object-contain">
                    查看所屬產業鏈
                </a>
            </div>
        </div>

        <section id="section-jobs" class="scroll-mt-24 space-y-8 relative">
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="bg-cyan-600 w-1.5 h-6 rounded-full"></span> 官方徵才管道
                </h3>      
                <?php if (isset($company['Official']) || isset($company['OneHundredAndFour'])): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if (isset($company['Official'])): ?>
                    <a href="<?php echo htmlspecialchars($company['Official']); ?>" target="_blank" class="block group">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center justify-between hover:shadow-md hover:border-cyan-300 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center p-2.5 group-hover:scale-110 transition-transform">
                                    <img src="/assets/work.png" class="w-full h-full object-contain">
                                </div>
                                <div><h4 class="font-bold text-base text-slate-800">「<?= $company['Name'] ?>」招募網站</h4><p class="text-xs text-slate-500">瀏覽最完整的職缺列表</p></div>
                            </div>
                            <div class="text-slate-300 group-hover:text-cyan-600 transition-colors"><i class="fa-solid fa-arrow-up-right-from-square"></i></div>
                        </div>
                    </a>
                    <?php else: ?>
                    <div class="w-full flex items-center justify-left gap-4 p-5 bg-white border-2 border-dashed border-slate-200 rounded-xl">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 shrink-0">
                            <i class="fa-solid fa-briefcase text-lg"></i>
                        </div>
                        <p class="text-slate-500 font-bold text-sm">「<?= $company['Name'] ?>」沒有官網頁面</p>
                    </div>
                    <?php endif ?>
                    <?php if (isset($company['OneHundredAndFour'])): ?>
                    <a href="<?php echo htmlspecialchars($company['OneHundredAndFour']); ?>" target="_blank" class="block group">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center justify-between hover:shadow-md hover:border-orange-300 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-orange-50 rounded-full flex items-center justify-center text-orange-500 text-xl group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-base text-slate-800">104 人力銀行</h4>
                                    <p class="text-xs text-slate-500">查看104上的職缺和薪資</p>
                                </div>
                            </div>
                            <div class="text-slate-300 group-hover:text-orange-500 transition-colors">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </div>
                        </div>
                    </a>
                    <?php else: ?>
                    <div class="w-full flex items-center justify-left gap-4 p-5 bg-white border-2 border-dashed border-slate-200 rounded-xl">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 shrink-0">
                            <i class="fa-solid fa-briefcase text-lg"></i>
                        </div>
                        <p class="text-slate-500 font-bold text-sm">「<?= $company['Name'] ?>」沒有 104 人力銀行頁面</p>
                    </div>
                    <?php endif ?>
                </div>
                <?php else: ?>
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8">
                    <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                            <i class="fa-solid fa-briefcase text-2xl"></i>
                        </div>
                        <p class="text-slate-500 font-bold">「<?= $company['Name'] ?>」沒有官網職缺頁面或 104 人力銀行頁面</p>
                    </div>
                </div>
                <?php endif ?>
            </div>
            
            <?php if (isset($company['OneHundredAndFour'])): ?>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <h4 class="font-bold text-slate-700">最新職缺摘要</h4>
                    <span class="text-xs text-slate-400" id="job-count-label"></span>
                </div>
                <?php if (!empty($jobsList)): ?>
                <div id="job-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4"></div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                    <button onclick="changePage(-1)" id="prev-btn" class="text-sm font-bold text-slate-500 hover:text-cyan-700 disabled:opacity-30 disabled:hover:text-slate-500 transition flex items-center gap-1">
                        <i class="fa-solid fa-chevron-left"></i> 上一頁
                    </button>
                    <span class="text-xs font-bold text-slate-400" id="page-indicator">Page 1</span>
                    <button onclick="changePage(1)" id="next-btn" class="text-sm font-bold text-slate-500 hover:text-cyan-700 disabled:opacity-30 disabled:hover:text-slate-500 transition flex items-center gap-1">
                        下一頁 <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                <?php else: ?>
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8">
                    <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                            <i class="fa-solid fa-briefcase text-2xl"></i>
                        </div>
                        <p class="text-slate-500 font-bold">「<?= $company['Name'] ?>」未在 104 人力銀行上釋出職缺，請稍後再來！</p>
                    </div>
                </div>
                <?php endif ?>
            </div>
            <?php endif ?>
        </section>

        <section id="section-salary" class="scroll-mt-24 relative">
            <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="bg-cyan-600 w-1.5 h-6 rounded-full"></span> 薪資與福利透視
            </h3>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-slate-100 flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-bold flex items-center gap-2 text-slate-700">
                            <img src="/assets/money.png" class="w-6 h-6 object-contain">
                            近年薪資趨勢
                        </h4>
                    </div>
                    <div class="w-full flex-1 min-h-[300px]">
                        <canvas id="salary-trend-chart"></canvas>
                    </div>
                </div>
                <div class="lg:col-span-1 flex flex-col gap-6">
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100 flex-1 flex flex-col justify-center">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-bold text-slate-700">年度薪資結構 (<?= $year ?>)</h4>
                            <span class="text-[10px] bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-bold">非主管全時員工</span>
                        </div>
                        <div class="flex flex-col gap-3">
                            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                <p class="text-slate-500 text-xs mb-1">平均數 (Mean)</p>
                                <p class="text-2xl font-bold text-slate-700"><?= number_format($company['NonAdminstrativeAverage'] / 10000, 1); ?> <span class="text-sm font-normal text-slate-500">萬 / 年</span></p>
                            </div>
                            <div class="bg-cyan-50 p-4 rounded-lg border border-cyan-200">
                                <p class="text-cyan-800 text-xs mb-1 font-bold"><i class="fa-solid fa-bullseye"></i> 中位數 (Median)</p>
                                <p class="text-2xl font-bold text-cyan-800"><?= number_format($company['NonAdminstrativeMedian'] / 10000, 1); ?> <span class="text-sm font-normal text-cyan-700">萬 / 年</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100 flex-1 flex flex-col justify-center">
                        <h4 class="text-md font-bold flex items-center gap-2 mb-4 text-slate-700">
                            <img src="/assets/people.png" class="w-5 h-5 object-contain"> 職場環境
                        </h4>
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-600">女性主管佔比</span>
                                <?php if (isset($company["FemaleManagerRatio"])): ?>
                                <span class="text-xl font-bold text-purple-600"><?= formatPercentage($company["FemaleManagerRatio"], 1) ?></span>
                                <?php else: ?>
                                <span class="text-xl font-bold text-slant-600">未揭露</span>
                                <?php endif ?>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full relative" style="width: <?= formatPercentage($company["FemaleManagerRatio"] ?? 0, 2) ?>"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="section-rank" class="scroll-mt-24 relative">
            <?php if (isset($company['NonAdminstrativeMedian']) || isset($company['NonAdminstrativeAverage'])): ?>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="bg-cyan-600 w-1.5 h-6 rounded-full"></span> 前 10 名薪資排名【<?= $company['Category'] ?> - <?= $company['Sector'] ?>】
                </h3>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex gap-1 bg-slate-100 p-1.5 rounded-lg border border-slate-200">
                        <button id="btn-median" onclick="updateChart('median')" class="px-4 py-1.5 text-sm font-bold rounded-md bg-white text-cyan-700 shadow-sm transition">中位數</button>
                        <button id="btn-average" onclick="updateChart('average')" class="px-4 py-1.5 text-sm font-bold rounded-md text-slate-500 hover:text-slate-700 transition">平均數</button>
                    </div>
                    <a href="/leaderboard.html" class="bg-blue-600 text-white px-5 py-1.5 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm flex items-center gap-2 group">
                        查看完整排行榜 <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            <div class="bg-white border border-slate-100 rounded-xl shadow-lg p-6 relative overflow-hidden">
                <p class="text-xs text-slate-400 mb-4 text-right">單位：仟元 / 年</p>
                <div class="w-full h-[450px]">
                    <canvas id="salary-rank-chart" style="display: block; box-sizing: border-box; height: 450px; width: 941.6px;" width="1177" height="562"></canvas>
                </div>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                    <i class="fa-solid fa-briefcase text-2xl"></i>
                </div>
                <p class="text-slate-500 font-bold">「<?= $company['Name'] ?>」沒有揭露薪資資訊</p>
            </div>
            <?php endif ?>
        </section>

        <section id="section-safety" class="scroll-mt-24 space-y-6 relative">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="bg-cyan-600 w-1.5 h-6 rounded-full"></span> 職業安全
                </h3>
                <?php if (!empty($safetyRecords)): ?>
                <div class="relative">
                    <select id="safety-year-select" onchange="updateSafetyData()" class="bg-white text-slate-700 text-sm font-bold py-2 pl-3 pr-8 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-cyan-600 cursor-pointer shadow-sm hover:bg-slate-50">
                        <?php foreach ($safetyRecords as $year => $data): ?>
                        <option value="<?= $year ?>"><?= $year ?> 年 (民國<?= $year - 1911 ?>年)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif ?>
            </div>
            <div class="bg-white rounded-xl shadow-lg border border-slate-100 p-8">
                <div class="mb-6 pb-4 border-b border-slate-100">
                    <h4 class="text-lg font-bold text-slate-700">職業災害與火災指標快篩</h4>
                    <p class="text-slate-400 text-xs mt-1">資料來源：職業災害統計</p>
                </div>
                            
                <?php if (!empty($safetyRecords)): ?>
                <div class="flex flex-col md:flex-row gap-6 w-full">
                    <div class="flex-1 bg-orange-50 p-6 rounded-xl border border-orange-100 flex flex-col items-center justify-center text-center hover:shadow-md transition cursor-default">
                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center mb-3 p-2.5">
                            <img src="/assets/injury.png" class="w-full h-full object-contain">
                        </div>
                        <h5 class="text-slate-500 text-xs font-bold mb-1 uppercase">職業災害人數</h5>
                        <p class="text-3xl font-bold text-slate-800" id="safety-count">-</p>
                        <p class="text-xs text-slate-400 mt-2">人</p>
                    </div>

                    <div class="flex-1 bg-blue-50 p-6 rounded-xl border border-blue-100 flex flex-col items-center justify-center text-center hover:shadow-md transition cursor-default">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-3 p-2.5">
                            <img src="/assets/ratio.png" class="w-full h-full object-contain">
                        </div>
                        <h5 class="text-slate-500 text-xs font-bold mb-1 uppercase">職業災害比率</h5>
                        <p class="text-3xl font-bold text-slate-800" id="safety-rate">-</p>
                        <p class="text-xs text-slate-400 mt-2">占員工總人數</p>
                    </div>

                    <div id="fire-section" class="flex-1 bg-red-50 p-6 rounded-xl border border-red-100 flex flex-col items-center justify-center text-center hover:shadow-md transition cursor-default">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-3 p-2.5">
                            <img src="/assets/fire.png" class="w-full h-full object-contain">
                        </div>
                        <h5 class="text-slate-500 text-xs font-bold mb-1 uppercase">火災件數</h5>
                        <p class="text-3xl font-bold text-slate-800" id="safety-fire">-</p>
                        <p class="text-xs text-slate-400 mt-2">件</p>
                    </div>
                </div>
                <?php else: ?>
                <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                        <i class="fa-solid fa-fire text-2xl"></i>
                    </div>
                    <p class="text-slate-500 font-bold">「<?= $company["Name"]?>」未揭露相關資料</p>
                </div>
                <?php endif ?>
            </div>

            <?php if (!empty($disasters)): ?>
            <details class="group bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-4">   
                <summary class="flex justify-between items-center font-bold cursor-pointer p-5 text-slate-700 hover:bg-slate-50 transition select-none">
                    <span class="flex items-center gap-3"><i class="fa-solid fa-triangle-exclamation text-red-500"></i> 重大職災事件紀錄</span>
                    <span class="text-slate-400 group-open:-rotate-180 transition-transform"><i class="fa-solid fa-chevron-down"></i></span>
                </summary>
                <div class="p-5 border-t border-slate-100 bg-slate-50 overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[1000px] table-fixed">
                        <thead>
                            <tr class="bg-slate-200 text-slate-600 text-sm">
                                <th class="p-3 border-b border-slate-300 font-bold w-[10%]">災害類型</th>
                                <th class="p-3 border-b border-slate-300 font-bold w-[6%] text-center">罹災人數</th>
                                <th class="p-3 border-b border-slate-300 font-bold w-[18%]">業主</th>
                                <th class="p-3 border-b border-slate-300 font-bold w-[12%]">事業單位</th>
                                <th class="p-3 border-b border-slate-300 font-bold w-[20%]">工程名稱</th>
                                <th class="p-3 border-b border-slate-300 font-bold w-[12%]">位置</th>
                                <th class="p-3 border-b border-slate-300 font-bold w-[13%]">地址</th>
                                <th class="p-3 border-b border-slate-300 font-bold w-[9%]">日期</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($disasters as $d):
                                $project_owner_name = $d['ProjectOwnerUniformId'];
                                if ($project_owner_name !== null) {
                                    $project_owner_name = getCompany($pdo, $project_owner_name, true)['Name'];
                                }

                                $business_unit_name = $d['BusinessUnitUniformId'];
                                if ($business_unit_name !== null) {
                                    $business_unit_name = getCompany($pdo, $business_unit_name, true)['Name'];
                                }
                            ?>
                            <tr class="bg-white hover:bg-red-50 transition text-sm">
                                <td class="p-3 border-b border-slate-200 text-red-600 font-bold break-words"><?= htmlspecialchars($d['Kind']) ?? '不明' ?></td>
                                <td class="p-3 border-b border-slate-200 text-center font-bold"><?= htmlspecialchars($d['Number'] ?? '不明') ?></td>
                                <td class="p-3 border-b border-slate-200 font-medium text-slate-800 break-words"><?= htmlspecialchars($project_owner_name ?? '不明') ?></td>
                                <td class="p-3 border-b border-slate-200 font-medium text-slate-800 break-words"><?= htmlspecialchars($business_unit_name ?? '不明') ?></td>
                                <td class="p-3 border-b border-slate-200 font-medium text-slate-800 break-words"><?= htmlspecialchars($d['Name'] ?? '不明') ?></td>
                                <td class="p-3 border-b border-slate-200 text-slate-600 break-words"><?= htmlspecialchars($d['Location'] ?? '不明') ?></td>
                                <td class="p-3 border-b border-slate-200 text-slate-500 text-xs leading-relaxed break-words"><?= htmlspecialchars($d['Address'] ?? '不明')  ?></td>
                                <td class="p-3 border-b border-slate-200 text-slate-600"><?= htmlspecialchars(formatDate($d['Date']) ?? '不明') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </details>
            <?php else: ?>
            <div class="bg-white rounded-xl shadow-sm border border-emerald-100 overflow-hidden p-5 mt-4">
                <div class="flex items-center gap-3 font-bold text-slate-700 mb-4">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    重大職災事件紀錄
                </div>
                <div class="w-full flex flex-col items-center justify-center py-8 bg-emerald-50/50 border-2 border-dashed border-emerald-200 rounded-xl">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-500 mb-4 shadow-sm">
                        <i class="fa-solid fa-check-circle text-3xl"></i>
                    </div>
                    <p class="text-emerald-700 font-bold text-lg tracking-wide">查無重大職災紀錄</p>
                    <p class="text-emerald-600/80 text-sm mt-2">太棒了！這代表該公司近期並無通報嚴重的工安意外，或許這可以成為您的選擇？</p>
                </div>
            </div>
            <?php endif ?>
        </section>

        <section id="section-esg" class="scroll-mt-24 space-y-6 relative mt-16">
            <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2 mb-4">
                <span class="bg-cyan-600 w-1.5 h-6 rounded-full"></span> 環境永續 (ESG)
            </h3>
            
            <div class="bg-white rounded-xl shadow-lg border border-slate-100 p-8">

                <div class="space-y-12">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center p-2">
                                <img src="/assets/air-pollution.png" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">溫室氣體排放量分析</h3>
                                <p class="text-xs text-slate-500">單位：公噸 CO₂e</p>
                            </div>
                        </div>
                        <?php if (isset($company['Scope1EmissionTonCO2e']) || isset($company['Scope2EmissionTonCO2e']) || isset($company['Scope3EmissionTonCO2e'])): ?>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <div class="lg:col-span-2 h-[280px]">
                                <canvas id="ghgChart" style="display: block; box-sizing: border-box; height: 280px; width: 606.4px;" width="758" height="350"></canvas>
                            </div>
                            <div class="space-y-3">
                                <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-100">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-sm font-bold text-emerald-700 mb-1">直接排放 (範疇一)</h4>
                                            <?php if (isset($company['Scope1EmissionTonCO2e'])): ?>
                                            <p class="text-xl font-bold text-emerald-800 font-mono"><?= htmlspecialchars(formatNumber($company['Scope1EmissionTonCO2e'])) ?><span class="text-xl font-bold text-emerald-800 font-sans"> 公噸 CO₂e</span></p>
                                            <?php else: ?>
                                            <p class="text-xl font-bold text-emerald-800">未揭露</p>
                                            <?php endif ?>
                                        </div>
                                        <span class="text-[10px] bg-emerald-200 text-emerald-700 px-1.5 py-0.5 rounded">已驗證</span>
                                    </div>
                                </div>
                                <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-100 relative overflow-hidden">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-sm font-bold text-emerald-700 mb-1">能源間接排放 (範疇二)</h4>
                                            <?php if (isset($company['Scope2EmissionTonCO2e'])): ?>
                                            <p class="text-xl font-bold text-emerald-800 font-mono"><?= htmlspecialchars(formatNumber($company['Scope2EmissionTonCO2e'])) ?><span class="text-xl font-bold text-emerald-800 font-sans"> 公噸 CO₂e</span></p>
                                            <?php else: ?>
                                            <p class="text-xl font-bold text-emerald-800">未揭露</p>
                                            <?php endif ?>
                                        </div>
                                        <span class="text-[10px] bg-emerald-200 text-emerald-700 px-1.5 py-0.5 rounded">已驗證</span>
                                    </div>
                                </div>
                                <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-100">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-sm font-bold text-emerald-700 mb-1">其他間接排放 (範疇三)</h4>
                                            <?php if (isset($company['Scope3EmissionTonCO2e'])): ?>
                                            <p class="text-xl font-bold text-emerald-800 font-mono"><?= htmlspecialchars(formatNumber($company['Scope3EmissionTonCO2e'])) ?><span class="text-xl font-bold text-emerald-800 font-sans"> 公噸 CO₂e</span></p>
                                            <?php else: ?>
                                            <p class="text-xl font-bold text-emerald-800">未揭露</p>
                                            <?php endif ?>
                                        </div>
                                        <span class="text-[10px] bg-emerald-200 text-emerald-700 px-1.5 py-0.5 rounded">已驗證</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                <i class="fa-solid fa-briefcase text-2xl"></i>
                            </div>
                            <p class="text-slate-500 font-bold">「<?= $company['Name'] ?>」沒有揭露溫室氣體排放量資料</p>
                        </div>
                        <?php endif ?>
                    </div>
                    <div class="border-t border-slate-100"></div>

                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center p-2">
                                <img src="/assets/renewable-energy.png" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">再生能源使用概況</h3>
                                <p class="text-xs text-slate-500">Renewable Energy Usage</p>
                            </div>
                        </div>
                        <?php if (isset($company['RenewEnergyUsageRate'])): ?>
                        <div class="flex flex-col md:flex-row items-center justify-center gap-12">
                            <div class="relative w-100 h-100">
                                <canvas id="energyChart"></canvas>
                                <div class="absolute top-[50%] translate-y-[-50%] left-[196px] translate-x-[-50%] flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-3xl font-bold text-emerald-600 font-mono"><?= htmlspecialchars(formatPercentage($company['RenewEnergyUsageRate'], 2)) ?></span>
                                    <span class="text-xs text-slate-400 font-bold uppercase mt-1">再生能源使用率</span>
                                </div>
                            </div>

                            <div class="max-w-md space-y-6 w-full md:w-auto">
                                <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                                    <h4 class="font-bold text-sm text-slate-500 mb-3">能源結構比例</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="font-bold text-emerald-700"><i class="fa-solid fa-leaf mr-1"></i>再生能源</span>
                                                <span class="font-bold text-emerald-700"><?= htmlspecialchars(formatPercentage($company['RenewEnergyUsageRate'], 2)) ?></span>
                                            </div>
                                            <div class="w-full bg-emerald-100 rounded-full h-2">
                                                <div class="bg-emerald-500 h-2 rounded-full" style="width: <?= $company['RenewEnergyUsageRate'] * 100 ?>%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="font-bold text-slate-600"><i class="fa-solid fa-industry mr-1"></i>傳統/其他能源</span>
                                                <span class="font-bold text-slate-600"><?= htmlspecialchars(formatPercentage(1 - $company['RenewEnergyUsageRate'], 2)) ?></span>
                                            </div>
                                            <div class="w-full bg-slate-200 rounded-full h-2">
                                                <div class="bg-slate-400 h-2 rounded-full" style="width: <?= 100 - $company['RenewEnergyUsageRate'] * 100 ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2 text-xs text-slate-400 justify-center md:justify-start">
                                    <i class="fa-solid fa-check-circle text-emerald-500"></i>
                                    <span>數據已取得第三方驗證 (ISO 14064 / ISO 50001)</span>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                <i class="fa-solid fa-briefcase text-2xl"></i>
                            </div>
                            <p class="text-slate-500 font-bold">「<?= $company['Name'] ?>」沒有揭露再生能源使用資料</p>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </section>

        <section id="section-reviews" class="scroll-mt-24 relative">
            <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2"><span class="bg-cyan-600 w-1.5 h-6 rounded-full"></span> 職場輿情 AI 分析</h3>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[600px]">
                <div class="lg:col-span-7 bg-white rounded-xl shadow-lg border p-6 flex flex-col">
                    <canvas id="word-cloud-canvas" class="w-full h-full flex-1"></canvas>
                </div>
                <div class="lg:col-span-5 flex flex-col gap-6 h-full">
                    <div class="bg-white rounded-xl shadow-lg border border-slate-100 p-6 flex flex-col h-[50%]">
                        <h4 class="text-lg font-bold text-slate-700 mb-4 flex items-center justify-between flex-shrink-0">
                            <span class="flex items-center gap-2">
                                <img src="/assets/review.png" class="w-6 h-6 object-contain"> 評論摘錄
                            </span>
                        </h4>
                        <?php if ($comments): ?>
                        <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
                            <?php foreach($comments as $c): ?>
                            <div class="p-3 bg-slate-50 rounded-lg border mb-3 text-sm">
                                <?php if (strtoupper($c['Source']) === 'DCARD'): ?>
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded">Dcard</span>
                                <?php else: ?>
                                <span class="bg-slate-800 text-white text-xs font-bold px-2 py-0.5 rounded">PTT</span>
                                <?php endif ?>
                                <p class="mt-2"><?= htmlspecialchars($c['Content']); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                <i class="fa-solid fa-comment text-2xl"></i>
                            </div>
                            <p class="text-slate-500 font-bold">目前無相關評論</p>
                        </div>
                        <?php endif ?>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg border border-slate-100 p-6 flex flex-col h-[50%]">
                        <h4 class="text-lg font-bold text-slate-700 mb-4 flex items-center justify-between flex-shrink-0">
                            <span class="flex items-center gap-2">
                                <img src="/assets/news.png" class="w-6 h-6 object-contain"> 相關新聞
                            </span>
                        </h4>
                        <?php if ($news): ?>
                        <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
                            <?php foreach($news as $n): ?>
                            <div class="flex gap-3 group cursor-pointer border-b border-slate-50 pb-3 hover:bg-slate-50 p-2 rounded transition-all">
                                <div class="w-16 h-16 bg-slate-100 rounded flex-shrink-0 flex items-center justify-center text-slate-300">
                                    <?php if (isset($n["ThumbnailUrl"])): ?>
                                    <img src="<?= $n["ThumbnailUrl"] ?>" style="height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                    <span class="fa-solid fa-image"></span>
                                    <?php endif ?>
                                </div>
                                <div>
                                    <a class="text-sm font-bold text-slate-800 group-hover:text-cyan-700 transition-colors line-clamp-2" href="<?= $n["Url"] ?>" target="_blank">
                                        <?= $n["Title"] ?>
                                    </a>
                                    <p class="text-[10px] text-slate-400 mt-1">上傳於 <?= formatDate($n['PublishedTime']) ?>
                                    <?php if (isset($n['UpdatedTime']) && $n['UpdatedTime'] != $n['PublishedTime']):
                                        echo "| 更新於 " . formatDate($n['UpdatedTime']);
                                    endif; ?>
                                    | 公視新聞</p>
                                </div>
                            </div>
                            <?php endforeach ?>
                        </div>
                        <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                <i class="fa-solid fa-newspaper text-2xl"></i>
                            </div>
                            <p class="text-slate-500 font-bold">最近無相關新聞</p>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        Chart.defaults.font.family = "'Noto Sans TC', sans-serif";
        Chart.defaults.color = '#64748b';

        // --- 0. 懸浮拖拉滑桿 (ScrollSpy Navigation) ---
        const sectionsList = ['section-jobs', 'section-salary', 'section-rank', 'section-safety', 'section-esg', 'section-reviews'];
        const sectionNames = ['徵才職缺', '薪資福利', '同業排名', '工安指標', '環境永續', '輿情分析'];
        let isDragging = false;
        const track = document.getElementById('vertical-track');
        const thumb = document.getElementById('vertical-thumb');

        // 點擊平滑滾動至區塊
        function scrollToSection(id) {
            const el = document.getElementById(id);
            if(el) { el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        }

        // 初始化滑桿標籤位置與刻度點
        function initSliderDotsAndLabels() {
            const labelsContainer = document.getElementById('slider-labels');
            const dotsContainer = document.getElementById('track-dots');
            labelsContainer.innerHTML = '';
            dotsContainer.innerHTML = '';
            
            const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
            if (maxScroll <= 0) return;

            sectionsList.forEach((sec, idx) => {
                const el = document.getElementById(sec);
                if (el) {
                    let percent = (el.offsetTop / maxScroll) * 100;
                    percent = Math.max(0, Math.min(100, percent));

                    // 建立文字標籤
                    const lbl = document.createElement('div');
                    lbl.className = "absolute right-0 text-xs font-bold text-slate-400 transition-colors cursor-pointer whitespace-nowrap text-right px-2 hover:text-cyan-600 slider-text-label";
                    lbl.style.top = percent + '%';
                    lbl.style.transform = 'translateY(-50%)';
                    lbl.innerText = sectionNames[idx];
                    lbl.onclick = () => scrollToSection(sec);
                    labelsContainer.appendChild(lbl);

                    // 建立小刻度點
                const dot = document.createElement('div');
                // 加上 pointer-events-auto 突破父層限制，加 z-30 確保在上層，並放大範圍為 w-2.5 h-2.5
                dot.className = "absolute left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-2.5 h-2.5 bg-slate-300 rounded-full cursor-pointer hover:bg-cyan-500 transition-all pointer-events-auto z-30 hover:scale-150 shadow-sm";
                dot.style.top = percent + '%';
                dot.onclick = () => scrollToSection(sec);
                dotsContainer.appendChild(dot);
                }
            });
        }

        // 處理自定義拖曳 (滑鼠與觸控)
        function handleDrag(clientY) {
            const rect = track.getBoundingClientRect();
            let y = clientY - rect.top;
            let percent = Math.max(0, Math.min(1, y / rect.height));
            const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
            window.scrollTo({ top: percent * maxScroll, behavior: 'auto' });
        }

        thumb.addEventListener('mousedown', (e) => {
            isDragging = true;
            document.body.classList.add('no-select');
            thumb.style.cursor = 'grabbing';
            thumb.style.transform = 'translate(-50%, -50%) scale(1.25)';
        });
        
        window.addEventListener('mouseup', () => {
            isDragging = false;
            document.body.classList.remove('no-select');
            thumb.style.cursor = 'grab';
            thumb.style.transform = 'translate(-50%, -50%) scale(1)';
        });

        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            handleDrag(e.clientY);
        });

        // 觸控螢幕支援
        thumb.addEventListener('touchstart', (e) => {
            isDragging = true;
            document.body.classList.add('no-select');
            thumb.style.transform = 'translate(-50%, -50%) scale(1.25)';
        }, {passive: true});

        window.addEventListener('touchend', () => {
            isDragging = false;
            document.body.classList.remove('no-select');
            thumb.style.transform = 'translate(-50%, -50%) scale(1)';
        });

        window.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            handleDrag(e.touches[0].clientY);
        }, {passive: true});

        // 監聽網頁滾動來更新滑桿位置與顏色標示
        window.addEventListener('scroll', () => {
            const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
            if (maxScroll <= 0) return;
            
            let percent = (window.scrollY / maxScroll) * 100;
            percent = Math.max(0, Math.min(100, percent));
            
            document.getElementById('vertical-progress').style.height = percent + '%';
            document.getElementById('vertical-thumb').style.top = percent + '%';

            // 更新文字高亮狀態
            let currentIdx = 0;
            sectionsList.forEach((sec, index) => {
                const el = document.getElementById(sec);
                if(el && window.scrollY >= el.offsetTop - 250) {
                    currentIdx = index;
                }
            });
            
            const labels = document.querySelectorAll('.slider-text-label');
            labels.forEach((lbl, idx) => {
                if(idx === currentIdx) {
                    lbl.classList.add('text-cyan-600', 'scale-110');
                    lbl.classList.remove('text-slate-400');
                } else {
                    lbl.classList.remove('text-cyan-600', 'scale-110');
                    lbl.classList.add('text-slate-400');
                }
            });
        });

        // --- 1. 職缺資料 ---
        const jobData = <?= json_encode($jobsList) ?>;

        let currentPage = 1;
        const itemsPerPage = 6;

        function renderJobs() {
            const listContainer = document.getElementById('job-list');
            if(!listContainer) return;
            const totalItems = jobData.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * itemsPerPage;
            const currentJobs = jobData.slice(start, start + itemsPerPage);

            document.getElementById('job-count-label').innerText = `共 ${totalItems} 筆資料`;
            listContainer.innerHTML = currentJobs.map(job => `
                <div class="bg-white border border-slate-200 rounded-xl p-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between h-full group">
                    <div class="mb-4">
                        <h5 class="font-bold text-slate-800 text-lg mb-3 line-clamp-2 h-[3.5rem]">${job.Name}</h5>
                        <div class="flex items-center text-sm text-slate-500 font-medium">
                            <span class="bg-slate-50 border border-slate-100 px-3 py-1 rounded-full text-slate-600 flex items-center">
                                <img src="/assets/money.png" class="w-4 h-4 mr-1 object-contain">
                                ${job.Salary}
                            </span>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 mt-auto">
                        <a href="${job.Url}" target="_blank" class="block w-full text-center py-2.5 bg-cyan-100 text-cyan-700 font-bold rounded-lg hover:bg-cyan-600 hover:text-white transition-colors group-hover:shadow-md">
                            立即應徵 <i class="fa-solid fa-arrow-up-right-from-square ml-1"></i>
                        </a>
                    </div>
                </div>
            `).join('');

            document.getElementById('page-indicator').innerText = `Page ${currentPage} / ${totalPages}`;
            document.getElementById('prev-btn').disabled = currentPage === 1;
            document.getElementById('next-btn').disabled = currentPage === totalPages;
        }

        function changePage(direction) {
            currentPage += direction;
            renderJobs();
        }

        // --- 2. 薪資趨勢圖 ---
        function initSalaryTrendChart() {
            const canvas = document.getElementById('salary-trend-chart');
            if(!canvas) return;
            
            <?php 
            $years = array_map(fn($a) => $a['Year'], array_filter($salaries, fn($s) => isset($s['NonAdminstrativeAverage']) || isset($s['NonAdminstrativeAverage'])));
            ?>
            const labels = <?= json_encode(array_map(fn($year) => sprintf('%s年', $year), $years)); ?>;
            const averages = <?php
            echo json_encode(
                array_map(function ($year) {
                    global $salaries;
                    foreach ($salaries as $s) {
                        if ($s['Year'] === $year) {
                            return $s['NonAdminstrativeAverage'] / 10000;
                        }
                    }
                    return null;
                }, $years)
            );
            ?>;
            const medians = <?php
            echo json_encode(
                array_map(function ($year) {
                    global $salaries;
                    foreach ($salaries as $s) {
                        if ($s['Year'] === $year) {
                            return $s['NonAdminstrativeMedian'] / 10000;
                        }
                    }
                    return null;
                }, $years)
            );
            ?>;
            new Chart(canvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '平均數', data: averages,
                            borderColor: '#94a3b8', backgroundColor: 'rgba(148, 163, 184, 0.1)',
                            borderWidth: 3, tension: 0, pointBackgroundColor: '#94a3b8',
                            pointRadius: 4, pointHoverRadius: 6
                        },
                        {
                            label: '中位數', data: medians,
                            borderColor: '#0891b2', backgroundColor: 'rgba(8, 145, 178, 0.1)',
                            borderWidth: 3, tension: 0, pointBackgroundColor: '#0891b2',
                            pointRadius: 4, pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { font: { weight: 'bold' } } },
                        tooltip: { callbacks: { label: function(c) { return c.dataset.label + ': ' + c.raw.toLocaleString() + ' 萬'; } } }
                    },
                    scales: {
                        y: { 
                            beginAtZero: false, 
                            grid: { 
                                color: '#f1f5f9' 
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + '萬';
                                }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
        
        // --- 3. 工安數據 ---
        const safetyData = {
        <?php foreach ($safetyRecords as $year => $data): ?>
        "<?= $year ?>": { 
            count: "<?= $data['count'] ?? "無資料" ?>", 
            rate: "<?= isset($data['rate']) ? $data['rate'] * 100 . "%" : "無資料" ?>", 
            fire: "<?= $data['fire'] ?? "無資料" ?>"
        },
        <?php endforeach; ?>
        };
        
        function updateSafetyData() {
            const year = document.getElementById('safety-year-select').value;
            const data = safetyData[year] || { count: "無資料", rate: "無資料", fire: "無資料" };

            if (year <= 2022) {
                document.getElementById('fire-section').classList.add('hidden');
            } else {
                document.getElementById('fire-section').classList.remove('hidden');
            }
            

            document.getElementById('safety-count').innerText = data.count;
            document.getElementById('safety-rate').innerText = data.rate;
            document.getElementById('safety-fire').innerText = data.fire;
        }

        // --- 4. 排行榜 ---
        const medians = <?= json_encode($medians) ?>;
        const averages = <?= json_encode($averages) ?>;
        let rankChart = null;

        function updateChart(type) {
            const canvas = document.getElementById('salary-rank-chart');
            if(!canvas) return; 

            document.getElementById('btn-median').className = type === 'median' 
                ? 'px-4 py-1.5 text-sm font-bold rounded-md bg-white text-cyan-700 shadow-sm transition' 
                : 'px-4 py-1.5 text-sm font-bold rounded-md text-slate-500 hover:text-slate-700 transition';
            document.getElementById('btn-average').className = type === 'average' 
                ? 'px-4 py-1.5 text-sm font-bold rounded-md bg-white text-cyan-700 shadow-sm transition' 
                : 'px-4 py-1.5 text-sm font-bold rounded-md text-slate-500 hover:text-slate-700 transition';

            // 1. Determine which dataset and property key to use based on the type
            const rawData = type === 'median' ? medians : averages;
            const sortKey = type === 'median' ? 'NonAdminstrativeMedian' : 'NonAdminstrativeAverage';
            
            // 2. Clone and sort the full array to establish complete ranking positions
            let sectorData = [...rawData];
            sectorData.sort((a, b) => b[sortKey] - a[sortKey]);

            // 3. Map the rank dynamically while the full list is intact (Index + 1)
            sectorData = sectorData.map((d, index) => ({
                ...d,
                rankPosition: index + 1
            }));

            const targetId = <?= $company["Id"] ?>;

            // 4. Extract the top 10 rows
            let displayData = sectorData.slice(0, 10);

            // 5. Check if the current company exists in the top 10 slice
            const includesTarget = displayData.some(d => String(d['Id']) === String(targetId));

            // 6. If missing from top 10, find its true rank from the full ranked list and append it
            if (!includesTarget) {
                const globalTarget = sectorData.find(d => String(d['Id']) === String(targetId));
                
                if (globalTarget) {
                    displayData.push(globalTarget);
                } else {
                    // Safe fallback if the current company somehow isn't in the raw dataset at all
                    displayData.push({
                        'Id': targetId,
                        'Name': <?= json_encode($company['Name']) ?>,
                        [sortKey]: type === 'median' ? <?= (float)($company['NonAdminstrativeMedian'] ?? 0) ?> : <?= (float)($company['NonAdminstrativeAverage'] ?? 0) ?>,
                        rankPosition: 'N/A'
                    });
                }
            }

            // 7. Format the labels to display the rank number on the axis
            const labels = displayData.map(d => `No.${d.rankPosition} ${d['Name']} (${d['Id']})`);
            const data = displayData.map(d => d[sortKey] / 1000);

            // 8. Visual styling mappings
            const bgColors = displayData.map(d => String(d['Id']) === String(targetId) ? 'rgba(8, 145, 178, 0.8)' : 'rgba(203, 213, 225, 0.6)');
            const borderColors = displayData.map(d => String(d['Id']) === String(targetId) ? 'rgb(8, 145, 178)' : 'rgb(148, 163, 184)');
            const tickColors = displayData.map(d => String(d['Id']) === String(targetId) ? '#0891b2' : '#64748b');

            if (rankChart) rankChart.destroy();
            rankChart = new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{ data: data, backgroundColor: bgColors, borderColor: borderColors, borderWidth: 1, borderRadius: 4 }]
                },
                options: {
                    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(c) { return c.raw.toLocaleString() + ' 仟元'; } } } },
                    scales: {
                        y: { ticks: { color: function(c) { return tickColors[c.index]; }, font: function(c) { return { weight: tickColors[c.index]==='#0891b2'?'bold':'normal' }; } } },
                        x: { display: false, grid: { color: '#f1f5f9' } }
                    }
                }
            });
        }

        // --- 5. ESG 圖表 ---
        function initEsgCharts() {
            const canvasGHG = document.getElementById('ghgChart');
            const co2eIsNull = [
                <?= isset($company['Scope1EmissionTonCO2e']) ?>,
                <?= isset($company['Scope2EmissionTonCO2e']) ?>,
                <?= isset($company['Scope3EmissionTonCO2e']) ?>,
            ]

            const co2eData = [
                <?= intval($company['Scope1EmissionTonCO2e'] ?? 0) ?>,
                <?= intval($company['Scope2EmissionTonCO2e'] ?? 0) ?>,
                <?= intval($company['Scope3EmissionTonCO2e'] ?? 0) ?>,
            ]
            const labels = ['直接排放', '能源間接排放', '其他間接排放']
            if (canvasGHG) {
                new Chart(canvasGHG.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '排放量', data: co2eData,
                            backgroundColor: ['#34d399', '#10b981', '#047857'],
                            borderRadius: 4, barPercentage: 0.5
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, plugins: 
                        { 
                            legend: { 
                                display: false,
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        if (co2eIsNull(labels.indexOf(context.label))) {
                                            return '未揭露'
                                        }

                                        return context.raw
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { 
                                    borderDash: [2, 4], 
                                    color: '#f1f5f9' 
                                }, ticks: { 
                                    callback: v => {
                                        const units = ["", "K", "M", "B"];
                                        let unit = units[0];
                                        for (unit of units) {
                                            if (v < 1000) {
                                                break;
                                            }
                                            v *= 1e-3;
                                        }

                                        return v.toFixed(1) + unit;
                                    }
                                }
                            },
                            x: { grid: {display: false } }
                        }
                    }
                });
            }
        }

        const canvasEnergy = document.getElementById('energyChart');
        if (canvasEnergy) {
            const reUsageRate = <?= $company['RenewEnergyUsageRate'] ?? 0 ?>;
            new Chart(canvasEnergy.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['再生能源', '其他能源'],
                    datasets: [{ data: [reUsageRate, 1 - reUsageRate], backgroundColor: ['#10b981', '#e7e7e7'], borderWidth: 0, hoverOffset: 4 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '80%',
                    plugins: { 
                        legend: { 
                            display: true,
                            position: 'left',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => (context.raw * 100).toFixed(2) + '%'
                            }
                        }
                    }
                }
            });
        }

        // --- 6. 文字雲渲染 ---
        const rawWordsData = [
            { text: "分紅大方", size: 60, sentiment: 0.9 },
            { text: "常態加班", size: 55, sentiment: -0.8 },
            { text: "年薪300萬", size: 45, sentiment: 0.95 },
            { text: "壓力山大", size: 42, sentiment: -0.7 },
            { text: "學到很多", size: 38, sentiment: 0.6 },
            { text: "輪班星人", size: 35, sentiment: -0.5 },
            { text: "福利佳", size: 28, sentiment: 0.8 },
            { text: "午餐好吃", size: 22, sentiment: 0.3 }
        ];

        function renderWordCloud() {
            const canvas = document.getElementById('word-cloud-canvas');
            if(!canvas) return;
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            
            const sizes = rawWordsData.map(item => item.size);
            const maxSize = Math.max(...sizes); const minSize = Math.min(...sizes);

            WordCloud(canvas, {
                list: rawWordsData.map(item => [item.text, item.size]),
                gridSize: 8, weightFactor: 1.5, fontFamily: "'Noto Sans TC', sans-serif",
                color: (word) => {
                    const d = rawWordsData.find(w => w.text === word);
                    let ratio = 1;
                    if (maxSize !== minSize) ratio = (d.size - minSize) / (maxSize - minSize);
                    
                    if (d.sentiment > 0.1) return `hsl(155, ${40 + ratio * 50}%, ${65 - ratio * 25}%)`;
                    else if (d.sentiment < -0.1) return `hsl(350, ${50 + ratio * 40}%, ${70 - ratio * 25}%)`;
                    else return `hsl(215, ${5 + ratio * 15}%, ${75 - ratio * 35}%)`;
                },
                rotateRatio: 0, shape: 'circle', shrinkToFit: true
            });
        }

        // --- 頁面初始化 ---
        window.onload = () => {
            renderJobs();
            initSalaryTrendChart(); 
            updateSafetyData();
            updateChart('median');  
            initEsgCharts();        
            
            // 延遲繪製以確保容器已經佈局完成
            setTimeout(() => {
                renderWordCloud();
                initSliderDotsAndLabels();
                window.dispatchEvent(new Event('scroll'));
            }, 150); 
        };

        window.addEventListener('resize', () => {
            renderWordCloud();
            initSliderDotsAndLabels();
        });
    </script>
</body>
</html>