<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "紡織";

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
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24">
            <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-indigo-600 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-600 rounded-t-2xl"></div>
                
                <div class="mb-8 text-center">
                    <h2 class="text-xl font-bold text-slate-700 tracking-wide">紡織產業鏈簡介</h2>
                    <p class="text-sm text-slate-500 mt-1">涵蓋石化原料、人造/天然纖維、紡紗織布至染整與成衣，點擊各節點查看對應上市企業。</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 relative mb-12">
                    
                    <!-- 上游 石化原料 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-500 font-normal">石化原料</span></h4>
                        </div>
                        
                        <div class="flex flex-col gap-4 flex-1 justify-between">
                            <button type="button" onclick="showCompanyList('石化原料', 'indigo')"
                                class="bg-white border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-atom"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-700 text-base block">石化原料</span>
                                    <span class="text-xs text-slate-400">純對苯二甲酸(PTA)、乙二醇(EG)、己內醯胺(CPL)、丙烯腈(AN)</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- 中游 纖維、助劑、紡紗與織布 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-500 font-normal">纖維加工與紡織</span></h4>
                        </div>
                        
                        <div class="flex flex-col gap-3 flex-1">
                            <button type="button" onclick="showCompanyList('人造纖維產品', 'purple')"
                                class="bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50/50 rounded-xl p-3 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                                <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-base flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-dna"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">人造纖維產品 (尼龍/聚酯/嫘縈)</span>
                            </button>

                            <button type="button" onclick="showCompanyList('天然纖維產品', 'purple')"
                                class="bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50/50 rounded-xl p-3 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                                <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-base flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-seedling"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">天然纖維產品 (純棉/純羊毛)</span>
                            </button>

                            <button type="button" onclick="showCompanyList('化學助劑', 'purple')"
                                class="bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50/50 rounded-xl p-3 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                                <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-base flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-flask"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">化學助劑 (界面活性劑/染料)</span>
                            </button>

                            <button type="button" onclick="showCompanyList('紡紗', 'purple')"
                                class="bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50/50 rounded-xl p-3 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                                <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-base flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-scroll"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">紡紗 (短纖紗/長纖紗/混紡紗)</span>
                            </button>

                            <button type="button" onclick="showCompanyList('織布', 'purple')"
                                class="bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50/50 rounded-xl p-3 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5">
                                <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-base flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">織布 (針織布/平織布/機能布)</span>
                            </button>
                        </div>
                    </div>

                    <!-- 下游 染整與成衣家居 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-500 font-normal">染整與終端成衣</span></h4>
                        </div>
                        
                        <div class="flex flex-col gap-4 flex-1 justify-between">
                            <button type="button" onclick="showCompanyList('染整', 'pink')"
                                class="bg-white border border-slate-200 hover:border-pink-400 hover:bg-pink-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-pink-50 text-pink-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-fill-drip"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-700 text-base block">染整</span>
                                    <span class="text-xs text-slate-400">印花、染色、防潑水/透氣等機能後處理</span>
                                </div>
                            </button>

                            <button type="button" onclick="showCompanyList('成衣及其它家居紡織類品', 'pink')"
                                class="bg-white border border-slate-200 hover:border-pink-400 hover:bg-pink-50/50 rounded-xl p-4 transition-all duration-200 flex items-center gap-3 shadow-sm hover:shadow-md text-left group hover:-translate-y-0.5 flex-1">
                                <div class="w-10 h-10 rounded-full bg-pink-50 text-pink-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-shirt"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-700 text-base block">成衣及其它家居紡織類品</span>
                                    <span class="text-xs text-slate-400">運動休閒成衣、羽絨衣、不織布、家飾寢具</span>
                                </div>
                            </button>
                        </div>
                    </div>

                </div>
                
                <div class="mt-16 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-indigo-600 flex items-center gap-2">
                        點擊上方區塊查看對應紡織產業廠商
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("紡織產業鏈分析", "從石化原料、人造與天然纖維、紡紗織布到染整成衣，探索台灣紡織產業生態系");
    </script>
</body>
</html>
