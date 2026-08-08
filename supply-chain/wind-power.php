<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "風力發電";
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>供應鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode(getCompanies($category)) ?>);
        const iconMap = new Map([
            [ "風機設備", new Map([
                [ "離岸風機", "fa-solid fa-water text-teal-600" ],
                [ "陸域風機", "fa-solid fa-mountain text-emerald-600" ],
            ])], [ "次系統", new Map([
                [ "監控系統", "fa-solid fa-display text-teal-600" ],
                [ "電力系統", "fa-solid fa-bolt text-emerald-600" ],
            ])],
        ]);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .chart-container { position: relative; height: 300px; width: 100%; }
        /* 隱藏捲軸但保持功能 */
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
                <span class="bg-teal-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-400 via-teal-500 to-emerald-500 rounded-t-2xl"></div>
                
                <div id="main_ic_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative mb-12 items-stretch">
                    
                    <!-- 上游：設備製造業 -->
                    <div class="chain-col relative flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-400 font-normal">設備製造業</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col gap-5 justify-between">
                            <!-- 上游到中游的連接線與原點 -->
                            <div class="hidden lg:block absolute top-1/2 left-full w-8 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-8 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <!-- 原材料 -->
                            <div class="bg-slate-200 rounded-xl p-4 relative z-10">
                                <h5 class="font-bold text-slate-700 mb-3 text-center text-base">原材料</h5>
                                <div class="grid grid-cols-3 gap-3">
                                    <div onclick="toggleCompanyList(companySectors, '鋼材', 'cyan')"
                                         class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all text-center shadow-sm hover:-translate-y-0.5 flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-cubes text-cyan-600 text-lg"></i>
                                        <span class="font-bold text-slate-700 text-sm">鋼材</span>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '玻/碳纖', 'cyan')"
                                         class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all text-center shadow-sm hover:-translate-y-0.5 flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-align-justify text-cyan-600 text-lg"></i>
                                        <span class="font-bold text-slate-700 text-sm">玻/碳纖</span>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '樹酯', 'cyan')"
                                         class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all text-center shadow-sm hover:-translate-y-0.5 flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-flask text-cyan-600 text-lg"></i>
                                        <span class="font-bold text-slate-700 text-sm">樹脂</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 零組/配件 -->
                            <div class="bg-slate-200 rounded-xl p-4 relative z-10">
                                <h5 class="font-bold text-slate-700 mb-3 text-center text-base">零組/配件</h5>
                                <div class="grid grid-cols-3 gap-3 mb-3">
                                    <div 
                                         class="cursor-not-allowed bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-3 text-center shadow-sm flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-fan text-slate-400 text-lg"></i>
                                        <span class="font-bold text-slate-400 text-sm">葉片 (無上市公司)</span>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '齒輪箱', 'cyan')"
                                         class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all text-center shadow-sm hover:-translate-y-0.5 flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-gears text-cyan-600 text-lg"></i>
                                        <span class="font-bold text-slate-700 text-sm">齒輪箱</span>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '塔架', 'cyan')"
                                         class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all text-center shadow-sm hover:-translate-y-0.5 flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-archway text-cyan-600 text-lg"></i>
                                        <span class="font-bold text-slate-700 text-sm">塔架</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div onclick="toggleCompanyList(companySectors, '電纜', 'cyan')"
                                         class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all text-center shadow-sm hover:-translate-y-0.5 flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-network-wired text-cyan-600 text-lg"></i>
                                        <span class="font-bold text-slate-700 text-sm">電纜</span>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '其他配件', 'cyan')"
                                         class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all text-center shadow-sm hover:-translate-y-0.5 flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-shapes text-cyan-600 text-lg"></i>
                                        <span class="font-bold text-slate-700 text-sm">其他配件</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 次系統 -->
                            <div class="bg-slate-200 rounded-xl p-4 relative z-10">
                                <h5 class="font-bold text-slate-700 mb-3 text-center text-base">次系統</h5>
                                <div class="grid grid-cols-2 gap-3">
                                    <div onclick="toggleCompanyList(companySectors, '監控系統', 'cyan')"
                                         class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all text-center shadow-sm hover:-translate-y-0.5 flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-desktop text-cyan-600 text-lg"></i>
                                        <span class="font-bold text-slate-700 text-sm">監控系統</span>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '電力系統', 'cyan')"
                                         class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all text-center shadow-sm hover:-translate-y-0.5 flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-bolt text-cyan-600 text-lg"></i>
                                        <span class="font-bold text-slate-700 text-sm">電力系統</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 風機設備 -->
                            <div class="bg-slate-200 rounded-xl p-4 relative z-10">
                                <h5 class="font-bold text-slate-700 mb-3 text-center text-base">風機設備</h5>
                                <div class="grid grid-cols-2 gap-3">
                                    <div 
                                         class="cursor-not-allowed bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-3 text-center shadow-sm flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-water text-slate-400 text-lg"></i>
                                        <span class="font-bold text-slate-400 text-sm">離岸風機 (無上市公司)</span>
                                    </div>
                                    <div 
                                         class="cursor-not-allowed bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-3 text-center shadow-sm flex flex-col items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-mountain-sun text-slate-400 text-lg"></i>
                                        <span class="font-bold text-slate-400 text-sm">陸域風機 (無上市公司)</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- 中游：整合服務業 -->
                    <div class="chain-col relative flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-400 font-normal">整合服務業</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col gap-4 justify-between">
                            <!-- 中游到下游的連接線與原點 -->
                            <div class="hidden lg:block absolute top-1/2 left-full w-8 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-8 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>

                            <div onclick="toggleCompanyList(companySectors, '風場規劃', 'teal')"
                                 class="cursor-pointer bg-white border border-slate-200 hover:border-teal-400 hover:bg-teal-50 rounded-xl p-6 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 shadow-sm h-full relative z-10">
                                <div class="w-10 h-10 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-lg flex-shrink-0">
                                    <i class="fa-solid fa-compass-drafting"></i>
                                </div>
                                <h5 class="font-bold text-slate-700 text-lg">風場規劃</h5>
                            </div>

                            <div onclick="toggleCompanyList(companySectors, '風場營造', 'teal')"
                                 class="cursor-pointer bg-white border border-slate-200 hover:border-teal-400 hover:bg-teal-50 rounded-xl p-6 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 shadow-sm h-full relative z-10">
                                <div class="w-10 h-10 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-lg flex-shrink-0">
                                    <i class="fa-solid fa-helmet-safety"></i>
                                </div>
                                <h5 class="font-bold text-slate-700 text-lg">風場營造</h5>
                            </div>

                            <div onclick="toggleCompanyList(companySectors, '風機維護', 'teal')"
                                 class="cursor-pointer bg-white border border-slate-200 hover:border-teal-400 hover:bg-teal-50 rounded-xl p-6 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 shadow-sm h-full relative z-10">
                                <div class="w-10 h-10 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-lg flex-shrink-0">
                                    <i class="fa-solid fa-wrench"></i>
                                </div>
                                <h5 class="font-bold text-slate-700 text-lg">風機維護</h5>
                            </div>

                        </div>
                    </div>

                    <!-- 下游：發電業 -->
                    <div class="chain-col relative flex flex-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-400 font-normal">發電業</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col gap-4 justify-between">
                            
                            <div onclick="toggleCompanyList(companySectors, '風場開發', 'emerald')"
                                 class="cursor-pointer bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 rounded-xl p-6 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 shadow-sm h-full relative z-10">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                                    <i class="fa-solid fa-chart-line"></i>
                                </div>
                                <h5 class="font-bold text-slate-700 text-lg">風場開發</h5>
                            </div>

                            <div onclick="toggleCompanyList(companySectors, '發電營運', 'emerald')"
                                 class="cursor-pointer bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 rounded-xl p-6 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 shadow-sm h-full relative z-10">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                                    <i class="fa-solid fa-plug-circle-bolt"></i>
                                </div>
                                <h5 class="font-bold text-slate-700 text-lg">發電營運</h5>
                            </div>

                        </div>
                    </div>

                </div>
                
                <div class="mt-20 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-teal-500 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
        
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從設備製造到發電業，全面透視產業鏈夥伴");
    </script>
</body>
</html>