<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "汽車";

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
                <span class="bg-pink-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8 pb-12 overflow-visible relative">
                <!-- 頂部漸層條更換為統一淺粉色漸層 -->
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-pink-300 via-pink-400 to-rose-400 rounded-t-2xl"></div>
                
                <div class="mb-8 text-center">
                    <h2 class="text-xl font-bold text-slate-700 tracking-wide">汽車產業鏈簡介</h2>
                    <p class="text-sm text-slate-500 mt-1">依汽車產業架構分類，點擊各零組件與服務節點查看對應公司。</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 relative mb-12">
                    
                    <!-- 上游 汽車零配件生產 -->
                    <div class="bg-pink-50/50 p-6 rounded-2xl border border-pink-100 flex flex-col relative">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-500 font-normal">零組件生產</span></h4>
                        </div>
                        
                        <div class="bg-white/80 p-4 rounded-xl border border-pink-100 flex-1 flex flex-col">
                            <div class="text-center font-bold text-slate-700 text-base mb-3 pb-2 border-b border-pink-100">
                                汽車零配件生產
                            </div>
                            <div class="grid grid-cols-2 gap-2 flex-1">
                                <button type="button" onclick="showCompanyList('車燈', 'pink')"
                                    class="bg-white border border-pink-200 hover:border-pink-400 hover:bg-pink-50 rounded-lg p-3 transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow-md group">
                                    <i class="fa-solid fa-lightbulb text-pink-500 text-sm"></i>
                                    <span class="font-bold text-slate-700 text-sm">車燈</span>
                                </button>

                                <button type="button" onclick="showCompanyList('輪胎', 'pink')"
                                    class="bg-white border border-pink-200 hover:border-pink-400 hover:bg-pink-50 rounded-lg p-3 transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow-md group">
                                    <i class="fa-solid fa-compact-disc text-pink-500 text-sm"></i>
                                    <span class="font-bold text-slate-700 text-sm">輪胎</span>
                                </button>

                                <button type="button" onclick="showCompanyList('鈑金', 'pink')"
                                    class="bg-white border border-pink-200 hover:border-pink-400 hover:bg-pink-50 rounded-lg p-3 transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow-md group">
                                    <i class="fa-solid fa-layer-group text-pink-500 text-sm"></i>
                                    <span class="font-bold text-slate-700 text-sm">鈑金</span>
                                </button>

                                <button type="button" onclick="showCompanyList('鋁合金鋼圈', 'pink')"
                                    class="bg-white border border-pink-200 hover:border-pink-400 hover:bg-pink-50 rounded-lg p-3 transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow-md group">
                                    <i class="fa-solid fa-circle-notch text-pink-500 text-sm"></i>
                                    <span class="font-bold text-slate-700 text-sm">鋁合金鋼圈</span>
                                </button>

                                <button type="button" onclick="showCompanyList('引擎蓋', 'pink')"
                                    class="bg-white border border-pink-200 hover:border-pink-400 hover:bg-pink-50 rounded-lg p-3 transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow-md group">
                                    <i class="fa-solid fa-car-rear text-pink-500 text-sm"></i>
                                    <span class="font-bold text-slate-700 text-sm">引擎蓋</span>
                                </button>

                                <button type="button" onclick="showCompanyList('保險桿', 'pink')"
                                    class="bg-white border border-pink-200 hover:border-pink-400 hover:bg-pink-50 rounded-lg p-3 transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow-md group">
                                    <i class="fa-solid fa-shield-halved text-pink-500 text-sm"></i>
                                    <span class="font-bold text-slate-700 text-sm">保險桿</span>
                                </button>

                                <button type="button" onclick="showCompanyList('其他', 'pink')"
                                    class="col-span-2 bg-white border border-pink-200 hover:border-pink-400 hover:bg-pink-50 rounded-lg p-3 transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow-md group">
                                    <i class="fa-solid fa-ellipsis text-pink-500 text-sm"></i>
                                    <span class="font-bold text-slate-700 text-sm">其他</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 中游 整車組裝與技術服務 -->
                    <div class="bg-pink-50/50 p-6 rounded-2xl border border-pink-100 flex flex-col relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-500 font-normal">整車製造</span></h4>
                        </div>
                        
                        <div class="flex flex-col gap-4 flex-1">
                            <button type="button" onclick="showCompanyList('整車組裝、修理及技術服務', 'pink')"
                                class="bg-white border border-pink-200 hover:border-pink-400 hover:bg-pink-50 rounded-xl p-6 transition-all duration-200 flex flex-col items-center justify-center gap-4 shadow-sm hover:shadow-md text-center group hover:-translate-y-0.5 flex-1">
                                <div class="w-16 h-16 rounded-full bg-pink-100/60 text-pink-600 flex items-center justify-center text-2xl group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-screwdriver-wrench"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-lg leading-relaxed">
                                    整車組裝、修理<br>及技術服務
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- 下游 銷售與貿易 -->
                    <div class="bg-pink-50/50 p-6 rounded-2xl border border-pink-100 flex flex-col relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-500 font-normal">銷售貿易</span></h4>
                        </div>
                        
                        <div class="flex flex-col gap-4 flex-1">
                            <button type="button" onclick="showCompanyList('銷售、進出口業務', 'pink')"
                                class="bg-white border border-pink-200 hover:border-pink-400 hover:bg-pink-50 rounded-xl p-6 transition-all duration-200 flex flex-col items-center justify-center gap-4 shadow-sm hover:shadow-md text-center group hover:-translate-y-0.5 flex-1">
                                <div class="w-16 h-16 rounded-full bg-pink-100/60 text-pink-600 flex items-center justify-center text-2xl group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-car-side"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-lg leading-relaxed">
                                    銷售、進出口業務
                                </span>
                            </button>
                        </div>
                    </div>

                </div>
                
                <div class="mt-16 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-pink-500 flex items-center gap-2">
                        點擊上方區塊查看對應汽車產業廠商
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業鏈分析", "從上游汽車零配件生產到中下游組裝與銷售，全方位掌握精選企業");
    </script>
</body>
</html>