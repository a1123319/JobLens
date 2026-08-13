<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "智慧電網";
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
                
                <div id="main_smart_grid_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative mb-12">
                    <div class="chain-col relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-400 font-normal">發電</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            <div class="flex flex-col gap-3 relative z-10">
                                <div aria-disabled="true" class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 text-center shadow-sm cursor-not-allowed">
                                    <h5 class="font-bold text-slate-400 text-base">傳統火/水力發電</h5>
                                </div>
                                <div aria-disabled="true" class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 text-center shadow-sm cursor-not-allowed">
                                    <h5 class="font-bold text-slate-400 text-base">風力發電</h5>
                                </div>
                                <div aria-disabled="true" class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 text-center shadow-sm cursor-not-allowed">
                                    <h5 class="font-bold text-slate-400 text-base">太陽能發電</h5>
                                </div>
                                <div aria-disabled="true" class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 text-center shadow-sm cursor-not-allowed">
                                    <h5 class="font-bold text-slate-400 text-base">汽電共生</h5>
                                </div>
                                <div aria-disabled="true" class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 text-center shadow-sm cursor-not-allowed">
                                    <h5 class="font-bold text-slate-400 text-base">…</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chain-col relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-400 font-normal">電網暨裝置設置</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            <div class="flex flex-col gap-4 relative z-10">
                                <div class="bg-sky-100/80 border border-sky-200 rounded-xl p-4 shadow-sm">
                                    <h5 class="font-bold text-sky-800 text-base text-center mb-3">電網設備製造</h5>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div onclick="toggleCompanyList(companySectors, '高/低壓輸電設施', 'blue')" class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 text-center transition-all hover:-translate-y-1 shadow-sm">
                                            <h6 class="font-bold text-slate-700 text-base">高/低壓輸電設施</h6>
                                        </div>
                                        <div onclick="toggleCompanyList(companySectors, '配電管理設施', 'blue')" class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 text-center transition-all hover:-translate-y-1 shadow-sm">
                                            <h6 class="font-bold text-slate-700 text-base">配電管理設施</h6>
                                        </div>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '電網營造', 'blue')" class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-4 text-center transition-all hover:-translate-y-1 shadow-sm">
                                    <h5 class="font-bold text-slate-700 text-base">電網營造</h5>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '電網維運', 'blue')" class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-4 text-center transition-all hover:-translate-y-1 shadow-sm">
                                    <h5 class="font-bold text-slate-700 text-base">電網維運</h5>
                                </div>
                                <div class="bg-emerald-100/80 border border-emerald-200 rounded-xl p-4 shadow-sm">
                                    <h5 class="font-bold text-emerald-800 text-base text-center mb-3">智慧電表製造</h5>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div onclick="toggleCompanyList(companySectors, '高壓AMI', 'blue')" class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 text-center transition-all hover:-translate-y-1 shadow-sm">
                                            <h6 class="font-bold text-slate-700 text-base">高壓AMI</h6>
                                        </div>
                                        <div onclick="toggleCompanyList(companySectors, '低壓AMI', 'blue')" class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 text-center transition-all hover:-translate-y-1 shadow-sm">
                                            <h6 class="font-bold text-slate-700 text-base">低壓AMI</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chain-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-400 font-normal">應用服務</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="flex flex-col gap-4 relative z-10">
                                <div onclick="toggleCompanyList(companySectors, '能源管理服務', 'purple')" class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-6 text-center transition-all hover:-translate-y-1 shadow-sm min-h-24 flex items-center justify-center">
                                    <h5 class="font-bold text-slate-700 text-lg">能源管理服務</h5>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '系統整合服務', 'purple')" class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-6 text-center transition-all hover:-translate-y-1 shadow-sm min-h-24 flex items-center justify-center">
                                    <h5 class="font-bold text-slate-700 text-lg">系統整合服務</h5>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '電力零售業', 'purple')" class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-6 text-center transition-all hover:-translate-y-1 shadow-sm min-h-24 flex items-center justify-center">
                                    <h5 class="font-bold text-slate-700 text-lg">電力零售業</h5>
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
        banner("<?= $category ?>產業供應鏈", "從發電、電網設置到應用服務，全面透視智慧電網產業鏈夥伴");
    </script>
</body>
</html>
