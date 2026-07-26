<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "太空衛星科技";
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業供應鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode(getCompanies($category)) ?>);
        const iconMap = new Map([
            [ "零組件/材料", new Map([
                [ "天線/射頻基頻", "fa-solid fa-satellite-dish text-indigo-500" ],
            ])],
        ]);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .chart-container { position: relative; height: 300px; width: 100%; }
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
                <span class="bg-indigo-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 via-violet-400 to-sky-500 rounded-t-2xl"></div>
                
                <div id="main_space_satellite_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative mb-12">
                    
                    <!-- 上游：設備製造 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-400 font-normal">設備製造</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <div class="flex flex-col gap-4 relative z-10">
                                <div onclick="toggleCompanyList(companySectors, '零組件/材料', 'indigo', iconMap.get('零組件/材料'))" class="cursor-pointer bg-white border border-slate-200 hover:border-indigo-400 rounded-lg p-4 shadow-sm hover:shadow-md hover:bg-indigo-50 transition-all text-center hover:-translate-y-1 flex items-center gap-4">
                                    <div class="text-indigo-500 w-8 text-center"><i class="fa-solid fa-microchip text-xl"></i></div>
                                    <h5 class="font-bold text-slate-700">零組件/材料</h5>
                                </div>
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="text-slate-400 w-8 text-center"><i class="fa-solid fa-diagram-project text-xl"></i></div>
                                    <h5 class="font-bold text-slate-400">次系統 (無上市公司)</h5>
                                </div>
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="text-slate-400 w-8 text-center"><i class="fa-solid fa-satellite text-xl"></i></div>
                                    <h5 class="font-bold text-slate-400">整機 (無上市公司)</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- 中游：發射營運 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-violet-100 text-violet-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-400 font-normal">發射營運</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <div class="flex flex-col gap-4 h-full relative z-10 justify-center">
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-rocket"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-400 text-sm">發射服務 (無上市公司)</h6>
                                </div>
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-handshake"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-400 text-sm">仲介服務 (無上市公司)</h6>
                                </div>
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-gears"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-400 text-sm">營運管理 (無上市公司)</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- 下游：應用服務 -->
                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-400 font-normal">應用服務</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-4 h-full relative z-10 justify-center">
                
                                <div onclick="toggleCompanyList(companySectors, '通訊', 'sky')" class="cursor-pointer bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-tower-broadcast"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-sm">通訊</h6>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '影像遙測', 'sky')" class="cursor-pointer bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-earth-asia"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-sm">影像遙測</h6>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '導航定位', 'sky')" class="cursor-pointer bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-location-crosshairs"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-sm">導航定位</h6>
                                </div>
                
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-20 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-indigo-500 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從設備製造、發射營運到應用服務，掌握太空衛星科技產業鏈脈絡");
    </script>
</body>
</html>
