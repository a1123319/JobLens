<?php
require_once "search-component.php";

// 1. Database Connection
$host = 'localhost';
$db_name = 'joblens';
$username = 'joblens';
$password = 'joblens';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$stmt = $pdo->prepare(
    "SELECT c.Id, 
        c.Name, 
        cc.Category, 
        GROUP_CONCAT(n.Name SEPARATOR ' ') AS Nickname 
    FROM company c 
    LEFT JOIN companycategory cc ON c.Id = cc.CompanyId
    LEFT JOIN nickname n ON c.Id = n.CompanyId
    GROUP BY c.Id, c.Name");
$stmt->execute();
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - 全方位職場透視系統</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; background-color: #f8fafc; }
        .hover-card:hover { transform: translateY(-5px); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="text-slate-800">

    <nav class="bg-slate-900 text-white p-4 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='/index.html'">
                <img src="assets/magnifying-glass.png" alt="JobLens Logo" class="w-8 h-8 object-contain">
                <span class="text-xl font-bold tracking-wider">JobLens</span>
            </div>
            <div class="hidden md:flex gap-6 text-sm font-medium">
                <a href="/about.html" class="border border-cyan-500 text-cyan-400 px-5 py-2 rounded-full font-full hover:bg-cyan-500 hover:text-white transition-all">
                    關於我們
                </a>
            </div>
        </div>
    </nav>

    <header class="bg-gradient-to-r from-slate-800 to-slate-900 text-white py-20 px-4">
        <div class="container mx-auto text-center max-w-3xl">
            <h1 class="text-3xl md:text-5xl font-bold mb-6">給求職者透視企業的放大鏡</h1>
            <p class="text-slate-400 mb-10 text-lg">整合證交所 ESG 揭露與職安署大數據，提供最真實的薪資與風險情報。</p>
            <?php renderSearch($pdo); ?>
        </div>
    </header>

    <section class="container mx-auto px-4 py-16 -mt-10 relative z-10">
        <h3 class="text-2xl font-bold text-center mb-8 flex items-center justify-center gap-2">
            <i class="fa-solid fa-layer-group text-blue-500"></i> 或者，從產業開始探索
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <a href="/supply-chain/semiconductor.html" class="block group">
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 transition-all duration-300 hover:shadow-2xl hover-card cursor-pointer h-full flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-600 transition-colors p-3">
                        <img src="assets/chip.png" alt="半導體" class="w-full h-full object-contain">
                    </div>
                    <h4 class="text-xl font-bold group-hover:text-blue-600">半導體產業</h4>
                </div>
            </a>

            <a href="/supply-chain/computer-peripherals.html" class="block group">
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 transition-all duration-300 hover:shadow-2xl hover-card cursor-pointer h-full flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-600 transition-colors p-3">
                        <img src="assets/pc.png" alt="電腦周邊" class="w-full h-full object-contain">
                    </div>
                    <h4 class="text-xl font-bold group-hover:text-blue-600">電腦周邊</h4>
                </div>
            </a>

            <a href="/supply-chain/leisure-entertainment.html" class="block group">
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 transition-all duration-300 hover:shadow-2xl hover-card cursor-pointer h-full flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-600 transition-colors p-3">
                        <img src="assets/ecommerce.png" alt="休閒娛樂" class="w-full h-full object-contain">
                    </div>
                    <h4 class="text-xl font-bold group-hover:text-blue-600">休閒娛樂產業</h4>
                </div>
            </a>
        </div>
    </section>

    <footer class="border-t border-slate-200 mt-12 py-8 text-center text-xs text-slate-400">
        <p>JobLens 2026 Beta | 本系統使用政府資料開放平臺數據</p>
        <p>Icons created by Freepik, Smashicons, Vectorslab - Flaticon</p>
    </footer>
</body>
</html>