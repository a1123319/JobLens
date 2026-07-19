<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "軟體服務";
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode(getCompanies($category), JSON_UNESCAPED_UNICODE) ?>);
        function showCompanyList(sector, color) {
            if (companySectors.has(sector)) {
                toggleCompanyList(companySectors, sector, color);
                return;
            }

            const companyListDiv = document.getElementById('company-list');
            companyListDiv.className = 'bg-slate-50 p-6 rounded-xl border border-dashed border-slate-300 text-slate-600';
            companyListDiv.innerHTML = `<p class="font-bold text-slate-700 mb-1">${sector}</p><p class="text-sm">目前尚無可顯示的公司資料。</p>`;
            companyListDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .chain-step:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 50%;
            left: calc(100% + 1rem);
            width: calc(2rem - 2px);
            height: 2px;
            background: #cbd5e1;
        }
        .chain-step:not(:last-child)::before {
            content: "";
            position: absolute;
            top: calc(50% - 5px);
            right: -1rem;
            width: 0;
            height: 0;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-left: 7px solid #cbd5e1;
            z-index: 1;
        }
        @media (max-width: 1023px) {
            .chain-step:not(:last-child)::after,
            .chain-step:not(:last-child)::before { display: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24" aria-labelledby="supply-chain-heading">
            <h2 id="supply-chain-heading" class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-cyan-500 w-2 h-8 rounded-full" aria-hidden="true"></span>
                供應鏈結構圖
            </h2>

            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8 pb-12 relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500 rounded-t-2xl" aria-hidden="true"></div>

                <div class="mb-8 text-center">
                    <p class="text-sm text-slate-500">依產業價值鏈資訊平台分類，點擊各節點查看對應公司。</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:gap-8 relative">
                    <div class="chain-step relative">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold shadow-sm">1</span>
                            <h3 class="text-lg font-bold text-slate-700">設計開發</h3>
                        </div>
                        <button type="button" onclick="showCompanyList('應用/系統軟體設計開發', 'cyan')" class="w-full min-h-44 bg-cyan-50/60 border border-cyan-200 hover:border-cyan-400 hover:bg-cyan-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-200 text-left group">
                            <span class="w-11 h-11 rounded-full bg-white text-cyan-600 flex items-center justify-center text-xl mb-4 shadow-sm group-hover:scale-105 transition-transform" aria-hidden="true"><i class="fa-solid fa-code"></i></span>
                            <span class="block font-bold text-slate-700 leading-snug">應用／系統軟體<br>設計開發</span>
                        </button>
                    </div>

                    <div class="chain-step relative">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold shadow-sm">2</span>
                            <h3 class="text-lg font-bold text-slate-700">整合服務</h3>
                        </div>
                        <button type="button" onclick="showCompanyList('系統整合服務', 'blue')" class="w-full min-h-44 bg-blue-50/60 border border-blue-200 hover:border-blue-400 hover:bg-blue-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-200 text-left group">
                            <span class="w-11 h-11 rounded-full bg-white text-blue-600 flex items-center justify-center text-xl mb-4 shadow-sm group-hover:scale-105 transition-transform" aria-hidden="true"><i class="fa-solid fa-diagram-project"></i></span>
                            <span class="block font-bold text-slate-700 leading-snug">系統整合服務</span>
                        </button>
                    </div>

                    <div class="chain-step relative">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold shadow-sm">3</span>
                            <h3 class="text-lg font-bold text-slate-700">資料服務</h3>
                        </div>
                        <button type="button" onclick="showCompanyList('資料處理服務', 'indigo')" class="w-full min-h-44 bg-indigo-50/60 border border-indigo-200 hover:border-indigo-400 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-200 text-left group">
                            <span class="w-11 h-11 rounded-full bg-white text-indigo-600 flex items-center justify-center text-xl mb-4 shadow-sm group-hover:scale-105 transition-transform" aria-hidden="true"><i class="fa-solid fa-database"></i></span>
                            <span class="block font-bold text-slate-700 leading-snug">資料處理服務</span>
                        </button>
                    </div>

                    <div class="chain-step relative">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold shadow-sm">4</span>
                            <h3 class="text-lg font-bold text-slate-700">通路服務</h3>
                        </div>
                        <button type="button" onclick="showCompanyList('通路經銷', 'purple')" class="w-full min-h-44 bg-purple-50/60 border border-purple-200 hover:border-purple-400 hover:bg-purple-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-200 text-left group">
                            <span class="w-11 h-11 rounded-full bg-white text-purple-600 flex items-center justify-center text-xl mb-4 shadow-sm group-hover:scale-105 transition-transform" aria-hidden="true"><i class="fa-solid fa-store"></i></span>
                            <span class="block font-bold text-slate-700 leading-snug">通路經銷</span>
                        </button>
                    </div>
                </div>

                <div class="mt-16 space-y-6" aria-live="polite">
                    <h3 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-cyan-500">點擊上方節點查看公司列表</h3>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從軟體設計、系統整合到資料服務與通路經銷，探索產業鏈中的企業");
    </script>
</body>
</html>
