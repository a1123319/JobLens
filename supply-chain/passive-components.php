<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "被動元件";
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
                <span class="bg-cyan-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500 rounded-t-2xl"></div>
                
                <div id="main_passive_components_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative mb-12">
                    <div class="chain-col relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            <div class="flex flex-col gap-4 relative z-10">
                                <div onclick="toggleCompanyList(companySectors, '電阻器材料', 'cyan')" class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-grip-lines"></i>
                                    </div>
                                    <div class="text-left">
                                        <h5 class="font-bold text-slate-700">電阻器材料</h5>
                                        <p class="text-xs text-slate-500 font-normal mt-1">(氧化鋁陶瓷基板、導電漿墨)</p>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '電容器材料', 'cyan')" class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-battery-half"></i>
                                    </div>
                                    <div class="text-left">
                                        <h5 class="font-bold text-slate-700">電容器材料</h5>
                                        <p class="text-xs text-slate-500 font-normal mt-1">(如電蝕/化成鋁箔、介面瓷粉)</p>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '電感器材料', 'cyan')" class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-magnet"></i>
                                    </div>
                                    <div class="text-left">
                                        <h5 class="font-bold text-slate-700">電感器材料</h5>
                                        <p class="text-xs text-slate-500 font-normal mt-1">(如鐵氧體、導電漿墨)</p>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '濾波器、振盪器材料', 'cyan')" class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-wave-square"></i>
                                    </div>
                                    <div class="text-left">
                                        <h5 class="font-bold text-slate-700">濾波器、振盪器材料</h5>
                                        <p class="text-xs text-slate-500 font-normal mt-1">(鉭酸鋰/鈮酸鋰晶圓/片、石英基板、金屬及陶瓷封裝材料)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chain-col relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            <div class="flex flex-col gap-4 relative z-10">
                                <div onclick="toggleCompanyList(companySectors, '電阻器', 'blue')" class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-grip-lines"></i>
                                    </div>
                                    <div class="text-left">
                                        <h5 class="font-bold text-slate-700">電阻器</h5>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '電容器', 'blue')" class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-battery-half"></i>
                                    </div>
                                    <div class="text-left">
                                        <h5 class="font-bold text-slate-700">電容器</h5>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '電感器', 'blue')" class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-magnet"></i>
                                    </div>
                                    <div class="text-left">
                                        <h5 class="font-bold text-slate-700">電感器</h5>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '濾波器、振盪器', 'blue')" class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-wave-square"></i>
                                    </div>
                                    <div class="text-left">
                                        <h5 class="font-bold text-slate-700">濾波器、振盪器</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chain-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="flex flex-col gap-4 relative z-10">
                                <div aria-disabled="true" class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-8 flex flex-col items-center justify-center text-center shadow-sm cursor-not-allowed min-h-48">
                                    <div class="w-12 h-12 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-2xl mb-4">
                                        <i class="fa-solid fa-microchip"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-400 text-lg">各類電子</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-20 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-cyan-500 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從材料、元件製造到終端應用，全面透視被動元件產業鏈夥伴");
    </script>
</body>
</html>
