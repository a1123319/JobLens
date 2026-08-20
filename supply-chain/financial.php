<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "金融";

function safeGetCompanies($cat) {
    try {
        return getCompanies($cat);
    } catch (Throwable $e) {
        return [];
    }
}
$companyData = safeGetCompanies($category);
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js?v=<?= time() ?>"></script>
    <script>
        function buildCompanySectors(entities) {
            const sectors = new Map();
            if (!Array.isArray(entities)) return sectors;
            for (const entity of entities) {
                if (entity.Sector) {
                    if (!sectors.has(entity.Sector)) sectors.set(entity.Sector, []);
                    sectors.get(entity.Sector).push(entity);
                }
                const sub = entity.Subsector || entity.SubSector || entity.subsector;
                if (sub && sub !== entity.Sector) {
                    if (!sectors.has(sub)) sectors.set(sub, []);
                    sectors.get(sub).push(entity);
                }
            }
            return sectors;
        }
        const companySectors = buildCompanySectors(<?= json_encode($companyData, JSON_UNESCAPED_UNICODE) ?>);
        function showCompanyList(sector, color) {
            if (companySectors.has(sector)) {
                toggleCompanyList(companySectors, sector, color);
                return;
            }

            const companyListDiv = document.getElementById('company-list');
            companyListDiv.className = 'bg-white p-6 rounded-xl border border-slate-200 shadow-sm ring-2 ring-' + color + '-200';
            companyListDiv.innerHTML = `<p class="font-bold text-slate-700 text-lg mb-2 flex items-center gap-2 border-b border-slate-100 pb-3"><span class="w-3 h-3 rounded-full bg-${color}-500"></span>${sector}</p><p class="text-sm text-slate-500">目前尚無可顯示的公司資料。</p>`;
            companyListDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .chain-step:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -1.25rem;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-left: 12px solid #cbd5e1;
            z-index: 10;
        }
        .chain-step:not(:last-child)::before {
            content: '';
            position: absolute;
            right: -1.4rem;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 12px solid transparent;
            border-bottom: 12px solid transparent;
            border-left: 14px solid #94a3b8;
            z-index: 9;
        }
        @media (max-width: 1023px) {
            .chain-step:not(:last-child)::after,
            .chain-step:not(:last-child)::before { display: none; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <nav class="bg-slate-900 text-white p-4 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='../index.php'">
                <img src="../assets/magnifying-glass.png" alt="Logo" class="w-8 h-8 object-contain">
                <span class="text-xl font-bold tracking-wider">JobLens</span>
            </div>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="../about.html" class="border border-cyan-500 text-cyan-400 px-5 py-2 rounded-full font-bold hover:bg-cyan-500 hover:text-white transition-all">
                    關於我們
                </a>
            </div>
        </div>
    </nav>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24">
            <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-indigo-600 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 via-blue-500 to-sky-600 rounded-t-2xl"></div>
                
                <div class="mb-8 text-center">
                    <h2 class="text-xl font-bold text-slate-700 tracking-wide">金融產業簡介</h2>
                    <p class="text-sm text-slate-500 mt-1">依金融產業分類（金控/銀行/保險、證券、期貨、租賃），點擊各節點查看對應上市企業。</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative mb-12">
                    
                    <!-- 金控業/銀行業/保險業 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-lg font-bold text-slate-700">金控/銀行/保險</h4>
                        </div>
                        
                        <div class="flex flex-col gap-4 flex-1 justify-between">
                            <button type="button" onclick="showCompanyList('金控業/銀行業/保險業', 'indigo')"
                                class="bg-white border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">金控業/銀行業/保險業</span>
                            </button>
                        </div>
                    </div>

                    <!-- 證券業 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-lg font-bold text-slate-700">證券業</h4>
                        </div>
                        
                        <div class="flex flex-col gap-4 flex-1 justify-between">
                            <button type="button" onclick="showCompanyList('證券業', 'blue')"
                                class="bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-chart-line"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">證券業</span>
                            </button>
                        </div>
                    </div>

                    <!-- 期貨業 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-lg font-bold text-slate-700">期貨業</h4>
                        </div>
                        
                        <div class="flex flex-col gap-4 flex-1 justify-between">
                            <button type="button" onclick="showCompanyList('期貨業', 'sky')"
                                class="bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-arrow-trend-up"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">期貨業</span>
                            </button>
                        </div>
                    </div>

                    <!-- 租賃業 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">4</div>
                            <h4 class="text-lg font-bold text-slate-700">租賃業</h4>
                        </div>
                        
                        <div class="flex flex-col gap-4 flex-1 justify-between">
                            <button type="button" onclick="showCompanyList('租賃業', 'cyan')"
                                class="bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-handshake"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">租賃業</span>
                            </button>
                        </div>
                    </div>

                </div>
                
                <div class="mt-16 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-indigo-600 flex items-center gap-2">
                        點擊上方區塊查看對應金融機構與企業
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("金融產業鏈分析", "涵蓋金控、銀行、保險、證券、期貨及租賃業，探索台灣金融市場體系與上市企業");
    </script>
</body>
</html>
