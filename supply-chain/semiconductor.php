<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "半導體";
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
            [ "IC設計", new Map([
                [ "LED驅動IC", "fa-solid fa-lightbulb text-yellow-500" ],
                [ "光源管理IC", "fa-solid fa-sun text-orange-400" ],
                [ "消費性IC", "fa-solid fa-laptop text-blue-500" ],
                [ "記憶體IC", "fa-solid fa-memory text-purple-500" ],
                [ "微控制器IC", "fa-solid fa-microchip text-emerald-500" ],
                [ "電源管理IC", "fa-solid fa-bolt text-green-500" ],
                [ "磁碟儲存控制器IC", "fa-solid fa-hard-drive text-slate-500" ],
                [ "網路通訊IC", "fa-solid fa-wifi text-blue-400" ],
                [ "輸出入介面IC", "fa-solid fa-plug text-indigo-400" ],
                [ "平面顯示器控制IC", "fa-solid fa-sliders text-cyan-500" ],
                [ "平面顯示器驅動IC", "fa-solid fa-tv text-sky-600" ],
                [ "光儲存控制IC", "fa-solid fa-compact-disc text-slate-400" ],
                [ "影像感測IC", "fa-solid fa-camera text-rose-500" ],
            ])], [ "IC/晶圓製造", new Map([
                [ "晶圓製造", "fa-solid fa-layer-group text-blue-600" ],
                [ "DRAM製造", "fa-solid fa-memory text-purple-600" ],
                [ "其他IC/二極體製造", "fa-solid fa-microchip text-slate-500" ],
            ])],
        ])
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
                <span class="bg-cyan-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500 rounded-t-2xl"></div>
                
                <div id="main_ic_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative mb-12">
                    <div class="chain-col relative group flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">
                                1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span
                                    class="text-sm text-slate-400 font-normal">設計與研發</span></h4>
                        </div>
                        <div
                            class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div
                                class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white">
                            </div>
                            <div class="flex flex-col gap-8 relative z-10">
                                <div onclick="toggleCompanyList(companySectors, 'IP設計/IC設計代工服務', 'cyan')"
                                    class="cursor-pointer bg-white border border-slant-200 hover:border-cyan-400 rounded-lg p-5 shadow-sm hover:shadow-md transition-all text-center group-node hover:-translate-y-1">
                                    <div class="text-cyan-600 mb-2"><i class="fa-solid fa-pen-ruler text-2xl"></i></div>
                                    <h5 class="font-bold text-slate-700">IP設計/IC設計代工服務</h5>
                                </div>
                                <div class="flex justify-center -my-4 z-0">
                                    <i class="fa-solid fa-arrow-down text-slate-400"></i>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, 'IC設計', 'cyan', iconMap.get('IC設計'))"
                                    class="cursor-pointer bg-gradient-to-br from-cyan-500 to-cyan-600 text-white rounded-lg p-6 shadow-lg shadow-cyan-200 hover:shadow-xl hover:scale-105 transition-all text-center ring-4 ring-white">
                                    <div class="mb-2"><i class="fa-solid fa-microchip text-3xl"></i></div>
                                    <h5 class="font-bold text-xl">IC設計</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">
                                2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-400 font-normal">製造</span>
                            </h4>
                        </div>
                        <div
                            class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div
                                class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white">
                            </div>
                            <div class="flex flex-col gap-6 h-full relative z-10 justify-center ">
                                <div class="flex flex-col gap-3 p-2 bg-slate-200 rounded-lg">
                                    <div onclick="toggleCompanyList(companySectors, '中游-生產製程及檢測設備', 'blue')"
                                        class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-robot"></i></div>
                                        <div class="text-left">
                                            <h6 class="font-bold text-slate-700 text-sm leading-tight">生產製程及檢測設備</h6>
                                        </div>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '光罩', 'blue')"
                                        class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-layer-group"></i></div>
                                        <div class="text-left">
                                            <h6 class="font-bold text-slate-700 text-sm">光罩</h6>
                                        </div>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '化學品', 'blue')"
                                        class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-flask"></i></div>
                                        <div class="text-left">
                                            <h6 class="font-bold text-slate-700 text-sm">化學品</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-center -my-2 z-0">
                                    <i class="fa-solid fa-arrow-down text-slate-400"></i>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, 'IC/晶圓製造', 'blue', iconMap.get('IC/晶圓製造'))"
                                    class="cursor-pointer bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg p-6 shadow-lg shadow-blue-200 hover:shadow-xl hover:scale-105 transition-all text-center ring-4 ring-white mt-2">
                                    <div class="mb-2"><i class="fa-solid fa-industry text-3xl"></i></div>
                                    <h5 class="font-bold text-xl">IC/晶圓製造</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold shadow-sm">
                                3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span
                                    class="text-sm text-slate-400 font-normal">封測與銷售</span></h4>
                        </div>
                        <div
                            class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-4 h-full relative z-10">
                
                                <div class="flex flex-col gap-3 p-2 bg-slate-200 rounded-lg">
                                    <div onclick="toggleCompanyList(companySectors, '下游-生產製程及檢測設備', 'purple')"
                                        class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                        <div
                                            class="w-10 h-10 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-screwdriver-wrench"></i></div>
                                        <div class="text-left">
                                            <h6 class="font-bold text-slate-700 text-sm leading-tight">生產製程及檢測設備</h6>
                                        </div>
                                    </div>
                                    <div id="ic_link_D700" onclick="toggleCompanyList(companySectors, '基板', 'purple')"
                                        class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                        <div
                                            class="w-10 h-10 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-border-all"></i></div>
                                        <div class="text-left">
                                            <h6 class="font-bold text-slate-700 text-sm">基板</h6>
                                        </div>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '導線架', 'purple')"
                                        class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                        <div
                                            class="w-10 h-10 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-grip-lines"></i></div>
                                        <div class="text-left">
                                            <h6 class="font-bold text-slate-700 text-sm">導線架</h6>
                                        </div>
                                    </div>
                                </div>
                
                                <div class="flex justify-center py-1">
                                    <i class="fa-solid fa-arrow-down text-slate-400"></i>
                                </div>
                
                                <div onclick="toggleCompanyList(companySectors, 'IC封裝測試', 'purple')"
                                    class="cursor-pointer bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg p-6 shadow-lg shadow-purple-200 hover:shadow-xl hover:scale-105 transition-all text-center ring-4 ring-white">
                                    <div class="mb-2"><i class="fa-solid fa-box-open text-3xl"></i></div>
                                    <h5 class="font-bold text-xl">IC封裝測試</h5>
                                </div>
                
                                <div class="flex justify-center py-1">
                                    <i class="fa-solid fa-arrow-down text-slate-400"></i>
                                </div>
                
                                <div onclick="toggleCompanyList(companySectors, 'IC模組', 'purple')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-10 h-10 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-cubes"></i></div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm uppercase tracking-wider">IC模組</h6>
                                    </div>
                                </div>
                                <div class="border-t-2 border-dashed border-slate-300 w-full"></div>
                                <div onclick="toggleCompanyList(companySectors, 'IC通路', 'purple')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-10 h-10 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-truck-ramp-box"></i></div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm uppercase tracking-wider">IC通路</h6>
                                    </div>
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
        banner("<?= $category ?>產業供應鏈", "從上游到下游，全面透視產業鏈夥伴");
    </script>
</body>
</html>