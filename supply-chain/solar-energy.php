<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "太陽能";
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
                <span class="bg-lime-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-lime-500 via-green-400 to-emerald-500 rounded-t-2xl"></div>
                
                <div id="main_solar_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative mb-12">
                    
                    <!-- 上游 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-lime-100 text-lime-700 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <div class="flex flex-col gap-4 relative z-10 justify-center">
                                <div onclick="toggleCompanyList(companySectors, '材料', 'lime')" class="cursor-pointer bg-white border border-slate-200 hover:border-lime-400 rounded-lg p-4 shadow-sm hover:shadow-md hover:bg-lime-50 transition-all text-center hover:-translate-y-1 flex items-center gap-4">
                                    <div class="text-lime-500 w-8 text-center"><i class="fa-solid fa-atom text-xl"></i></div>
                                    <h5 class="font-bold text-slate-700">材料</h5>
                                </div>

                                <div class="flex justify-center -my-1">
                                    <i class="fa-solid fa-arrow-down text-slate-400"></i>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '矽晶圓/矽晶片', 'lime')" class="cursor-pointer bg-white border border-slate-200 hover:border-lime-400 rounded-lg p-4 shadow-sm hover:shadow-md hover:bg-lime-50 transition-all text-center hover:-translate-y-1 flex items-center gap-4">
                                    <div class="text-lime-500 w-8 text-center"><i class="fa-solid fa-microchip text-xl"></i></div>
                                    <h5 class="font-bold text-slate-700">矽晶圓／矽晶片</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- 中游 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <div class="flex flex-col gap-4 relative z-10 justify-center">
                                <div onclick="toggleCompanyList(companySectors, '太陽能電池', 'green')" class="cursor-pointer bg-white border border-slate-200 hover:border-green-400 hover:bg-green-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-car-battery"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-700">太陽能電池</h5>
                                </div>

                                <div class="flex justify-center -my-1">
                                    <i class="fa-solid fa-arrow-down text-slate-400"></i>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '太陽能電池模組', 'green')" class="cursor-pointer bg-white border border-slate-200 hover:border-green-400 hover:bg-green-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-solar-panel"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-700">太陽能電池模組</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- 下游 -->
                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="flex flex-col gap-4 relative z-10 justify-center">
                                <div onclick="toggleCompanyList(companySectors, '太陽能發電設備/系統及系統工程', 'emerald')" class="cursor-pointer bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-gears"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-700">太陽能發電設備／系統及系統工程</h5>
                                </div>

                                <div class="flex justify-center -my-1">
                                    <i class="fa-solid fa-arrow-down text-slate-400"></i>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '太陽能電廠', 'emerald')" class="cursor-pointer bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-bolt"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-700">太陽能電廠</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-20 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-lime-500 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "全面剖析綠能科技轉型，掌握太陽能產業鏈脈絡");
    </script>
</body>
</html>