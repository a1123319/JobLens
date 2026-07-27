<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "能源元件";

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
                const sub = entity.Subsector || entity.SubSector;
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
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .chain-step:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 50%;
            left: calc(100% + 0.5rem);
            width: calc(1.5rem - 2px);
            height: 2px;
            background: #cbd5e1;
        }
        .chain-step:not(:last-child)::before {
            content: "";
            position: absolute;
            top: calc(50% - 5px);
            right: -0.75rem;
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
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24">
            <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-cyan-600 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-500 via-cyan-500 to-blue-600 rounded-t-2xl"></div>
                
                <div class="mb-8 text-center">
                    <h2 class="text-xl font-bold text-slate-700 tracking-wide">能源元件產業簡介</h2>
                    <p class="text-sm text-slate-500 mt-1">依能源元件產業鏈分類，點擊各節點查看對應公司。</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 relative mb-12">
                    
                    <!-- 上游 原材料 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游</h4>
                        </div>
                        
                        <div class="flex-1 flex flex-col justify-center">
                            <button type="button" onclick="showCompanyList('原材料', 'emerald')"
                                class="w-full min-h-36 bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4 text-left group hover:-translate-y-0.5">
                                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-atom"></i>
                                </div>
                                <div>
                                    <span class="block font-bold text-slate-700 text-lg leading-snug">原材料</span>
                                    <span class="text-xs text-slate-400 mt-1 block">正負極材料、電解液、隔離膜等</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- 中游 電池芯 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游</h4>
                        </div>
                        
                        <div class="flex-1 flex flex-col justify-center">
                            <button type="button" onclick="showCompanyList('電池芯', 'cyan')"
                                class="w-full min-h-36 bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50/50 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4 text-left group hover:-translate-y-0.5">
                                <div class="w-12 h-12 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-battery-half"></i>
                                </div>
                                <div>
                                    <span class="block font-bold text-slate-700 text-lg leading-snug">電池芯</span>
                                    <span class="text-xs text-slate-400 mt-1 block">電芯製造與封裝技術</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- 下游 電池模組 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游</h4>
                        </div>
                        
                        <div class="flex-1 flex flex-col justify-center">
                            <button type="button" onclick="showCompanyList('電池模組', 'blue')"
                                class="w-full min-h-36 bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4 text-left group hover:-translate-y-0.5">
                                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-plug-circle-bolt"></i>
                                </div>
                                <div>
                                    <span class="block font-bold text-slate-700 text-lg leading-snug">電池模組</span>
                                    <span class="text-xs text-slate-400 mt-1 block">模組組裝、BMS電池管理系統</span>
                                </div>
                            </button>
                        </div>
                    </div>

                </div>
                
                <div class="mt-16 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-cyan-600 flex items-center gap-2">
                        點擊上方區塊查看對應能源元件廠商
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業鏈分析", "從原材料、電池芯到電池模組，探索能源元件產業生態系與企業");
    </script>
</body>
</html>
