<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "LED照明";
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
                <span class="bg-amber-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>

            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-amber-400 via-yellow-400 to-orange-500 rounded-t-2xl"></div>

                <div id="main_led_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative mb-12">

                    <!-- 上游 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-400 font-normal">基板材料</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>

                            <div class="flex flex-col gap-4 relative z-10 justify-center">
                                <div onclick="toggleCompanyList(companySectors, '藍寶石晶圓/晶片', 'amber')" class="cursor-pointer bg-white border border-slate-200 hover:border-amber-400 rounded-lg p-4 shadow-sm hover:shadow-md hover:bg-amber-50 transition-all text-center hover:-translate-y-1 flex items-center gap-4">
                                    <div class="text-amber-500 w-8 text-center"><i class="fa-solid fa-gem text-xl"></i></div>
                                    <h5 class="font-bold text-slate-700">藍寶石晶圓／晶片</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 中游 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-400 font-normal">磊晶製程</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>

                            <div class="flex flex-col gap-4 relative z-10 justify-center">
                                <div onclick="toggleCompanyList(companySectors, '生產製程、檢測設備及原物料', 'yellow')" class="cursor-pointer bg-white border border-slate-200 hover:border-yellow-400 hover:bg-yellow-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-700">生產製程、檢測設備及原物料</h5>
                                </div>

                                <div class="flex justify-center -my-1">
                                    <i class="fa-solid fa-arrow-down text-slate-400"></i>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '光源/磊晶', 'yellow')" class="cursor-pointer bg-white border border-slate-200 hover:border-yellow-400 hover:bg-yellow-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-700">光源／磊晶</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 下游 -->
                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-400 font-normal">封裝與應用</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="flex flex-col gap-4 relative z-10 justify-center">
                                <div onclick="toggleCompanyList(companySectors, '封裝/模組', 'orange')" class="cursor-pointer bg-white border border-slate-200 hover:border-orange-400 hover:bg-orange-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-box-open"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-700">封裝／模組</h5>
                                </div>

                                <div class="flex justify-center -my-1">
                                    <i class="fa-solid fa-arrow-down text-slate-400"></i>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '燈具/應用', 'orange')" class="cursor-pointer bg-white border border-slate-200 hover:border-orange-400 hover:bg-orange-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-lightbulb"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-700">燈具／應用</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-20 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-amber-500 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從藍寶石晶圓到燈具，透視 LED 照明產業鏈全貌");
    </script>
</body>
</html>
