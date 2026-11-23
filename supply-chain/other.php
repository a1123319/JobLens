<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "其他";

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
                <span class="bg-emerald-600 w-2 h-8 rounded-full"></span> 產業鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 rounded-t-2xl"></div>
                
                <div class="mb-8 text-center">
                    <h2 class="text-xl font-bold text-slate-700 tracking-wide">其他產業鏈簡介</h2>
                    <p class="text-sm text-slate-500 mt-1">涵蓋文化印刷、環保潔能、電子服務、特殊照明、農業科技及綜合性領域，點擊各板塊查看對應企業與職缺情報。</p>
                </div>

                <!-- 6大子產業直接按鈕網格（無副標題） -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative mb-12">
                    
                    <!-- 1. 文化印刷業 -->
                    <button type="button" onclick="showCompanyList('文化印刷業', 'emerald')"
                        class="bg-slate-50/80 hover:bg-emerald-50/60 border border-slate-200 hover:border-emerald-400 rounded-2xl p-6 transition-all duration-200 flex items-center gap-4 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                        <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-print"></i>
                        </div>
                        <span class="font-bold text-slate-700 text-base">文化印刷業</span>
                    </button>

                    <!-- 2. 環保潔能服務產業 -->
                    <button type="button" onclick="showCompanyList('環保潔能服務產業', 'emerald')"
                        class="bg-slate-50/80 hover:bg-emerald-50/60 border border-slate-200 hover:border-emerald-400 rounded-2xl p-6 transition-all duration-200 flex items-center gap-4 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                        <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-leaf"></i>
                        </div>
                        <span class="font-bold text-slate-700 text-base">環保潔能服務產業</span>
                    </button>

                    <!-- 3. 其他電子產品及電子服務產業 -->
                    <button type="button" onclick="showCompanyList('其他電子產品及電子服務產業', 'emerald')"
                        class="bg-slate-50/80 hover:bg-emerald-50/60 border border-slate-200 hover:border-emerald-400 rounded-2xl p-6 transition-all duration-200 flex items-center gap-4 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                        <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-microchip"></i>
                        </div>
                        <span class="font-bold text-slate-700 text-base">其他電子產品及電子服務產業</span>
                    </button>

                    <!-- 4. 特殊照明業 -->
                    <button type="button" onclick="showCompanyList('特殊照明業', 'emerald')"
                        class="bg-slate-50/80 hover:bg-emerald-50/60 border border-slate-200 hover:border-emerald-400 rounded-2xl p-6 transition-all duration-200 flex items-center gap-4 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                        <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-lightbulb"></i>
                        </div>
                        <span class="font-bold text-slate-700 text-base">特殊照明業</span>
                    </button>

                    <!-- 5. 農業科技業 -->
                    <button type="button" onclick="showCompanyList('農業科技業', 'emerald')"
                        class="bg-slate-50/80 hover:bg-emerald-50/60 border border-slate-200 hover:border-emerald-400 rounded-2xl p-6 transition-all duration-200 flex items-center gap-4 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                        <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-seedling"></i>
                        </div>
                        <span class="font-bold text-slate-700 text-base">農業科技業</span>
                    </button>

                    <!-- 6. 其他 -->
                    <button type="button" onclick="showCompanyList('其他', 'emerald')"
                        class="bg-slate-50/80 hover:bg-emerald-50/60 border border-slate-200 hover:border-emerald-400 rounded-2xl p-6 transition-all duration-200 flex items-center gap-4 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                        <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-globe"></i>
                        </div>
                        <span class="font-bold text-slate-700 text-base">其他</span>
                    </button>

                </div>
                
                <div class="mt-16 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-emerald-600 flex items-center gap-2">
                        點擊上方區塊查看對應企業
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("其他產業供應鏈", "整合文化印刷、環保潔能、電子服務、特殊照明與農業科技，探索多元產業職缺與薪資情報");
    </script>
</body>
</html>