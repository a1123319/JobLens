<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "電子商務";

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
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
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
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-600 rounded-t-2xl"></div>
                
                <div class="mb-8 text-center">
                    <h2 class="text-xl font-bold text-slate-700 tracking-wide">電子商務產業鏈簡介</h2>
                    <p class="text-sm text-slate-500 mt-1">依電子商務架構分類，點擊各服務與銷售節點查看對應公司。</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 relative mb-12">
                    
                    <!-- 上游 支援服務業 -->
                    <div class="bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-500 font-normal">支援服務業</span></h4>
                        </div>
                        
                        <div class="flex flex-col gap-3 flex-1">
                            <button type="button" onclick="showCompanyList('物流倉儲服務', 'cyan')"
                                class="bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                                <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-warehouse"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-base">物流倉儲服務</span>
                            </button>

                            <button type="button" onclick="showCompanyList('資訊系統建置服務', 'cyan')"
                                class="bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                                <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-laptop-code"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-base">資訊系統建置服務</span>
                            </button>

                            <button type="button" onclick="showCompanyList('金流串接處理服務', 'cyan')"
                                class="bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                                <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-credit-card"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-base">金流串接處理服務</span>
                            </button>

                            <button type="button" onclick="showCompanyList('資料分析處理服務', 'cyan')"
                                class="bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                                <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-chart-pie"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-base">資料分析處理服務</span>
                            </button>

                            <button type="button" onclick="showCompanyList('行銷廣告服務', 'cyan')"
                                class="bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                                <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-base">行銷廣告服務</span>
                            </button>
                        </div>
                    </div>

                    <!-- 中游 平台服務業 -->
                    <div class="bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-500 font-normal">平台服務業</span></h4>
                        </div>
                        
                        <div class="flex flex-col gap-4 flex-1 justify-between">
                            <button type="button" onclick="showCompanyList('店點開設管理', 'blue')"
                                class="bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-store"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-base">店點開設管理</span>
                            </button>

                            <button type="button" onclick="showCompanyList('交易撮(媒)合', 'blue')"
                                class="bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-handshake"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-base">交易撮(媒)合</span>
                            </button>

                            <button type="button" onclick="showCompanyList('資訊聚合', 'blue')"
                                class="bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-base">資訊聚合</span>
                            </button>
                        </div>
                    </div>

                    <!-- 下游 銷售服務業 -->
                    <div class="bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-500 font-normal">銷售服務業</span></h4>
                        </div>
                        
                        <div class="flex flex-col gap-4 flex-1 justify-between">
                            <button type="button" onclick="showCompanyList('自有產品(服務)銷售', 'indigo')"
                                class="bg-white border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-base">自有產品(服務)銷售</span>
                            </button>

                            <button type="button" onclick="showCompanyList('一般零售', 'indigo')"
                                class="bg-white border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-base">一般零售</span>
                            </button>

                            <button type="button" onclick="showCompanyList('票券銷售', 'indigo')"
                                class="bg-white border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-ticket"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-base">票券銷售</span>
                            </button>
                        </div>
                    </div>

                </div>
                
                <div class="mt-16 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-cyan-600 flex items-center gap-2">
                        點擊上方區塊查看對應電子商務廠商
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業鏈分析", "從支援服務、平台服務到銷售服務，探索電子商務產業生態系與企業");
    </script>
</body>
</html>
