<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "食品生技";
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
                <span class="bg-emerald-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <!-- 頂部綠色系漸層條 -->
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-500 rounded-t-2xl"></div>
                
                <div id="main_food_biotech_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative mb-12">
                    
                    <!-- 1. 上游：原料 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-400 font-normal">生技原料開發</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <div class="flex flex-col gap-4 relative z-10">
                                <!-- 原料按鈕 -->
                                <div onclick="toggleCompanyList(companySectors, '原料', 'emerald')" class="cursor-pointer bg-white border border-slate-200 hover:border-emerald-400 rounded-lg p-5 shadow-sm hover:shadow-md hover:bg-emerald-50 transition-all text-center hover:-translate-y-1 flex items-center gap-4">
                                    <div class="text-emerald-500 w-10 text-center"><i class="fa-solid fa-leaf text-2xl"></i></div>
                                    <div class="text-left">
                                        <h5 class="font-bold text-slate-700">原料</h5>
                                        <p class="text-xs text-slate-400">動植物萃取、微生物、發酵物等</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- 2. 中游：加工製成品 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-400 font-normal">加工與萃取製造</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <div class="flex flex-col gap-4 relative z-10">
                                <!-- 加工製成品按鈕 (主要核心步驟) -->
                                <div onclick="toggleCompanyList(companySectors, '加工製成品', 'teal')" class="cursor-pointer bg-gradient-to-br from-teal-500 to-emerald-600 text-white rounded-lg p-6 shadow-lg shadow-teal-100 hover:shadow-xl hover:scale-105 transition-all text-center ring-4 ring-white">
                                    <div class="mb-2"><i class="fa-solid fa-flask-vial text-3xl"></i></div>
                                    <h5 class="font-bold text-lg">加工製成品</h5>
                                    <p class="text-xs text-teal-100 mt-1">發酵工程、濃縮萃取、劑型製造</p>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- 3. 下游：代理銷售及通路 -->
                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-400 font-normal">品牌與銷售通路</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="flex flex-col gap-4 relative z-10">
                                <!-- 下游按鈕 -->
                                <div onclick="toggleCompanyList(companySectors, '保健食品代理銷售及通路', 'cyan')" class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-5 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-store"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">保健食品代理銷售及通路</h6>
                                        <p class="text-xs text-slate-400">連鎖藥局、美妝門市、電商平台</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- 點擊顯示公司列表區域 -->
                <div class="mt-20 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-emerald-500 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "整合生物技術與食品加工，透視保健食品產業生態圈");
    </script>
</body>
</html>