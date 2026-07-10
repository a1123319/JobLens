<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "休閒娛樂";
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
            [ "線上遊戲業", new Map([
                [ "設計開發", "fa-solid fa-lightbulb text-yellow-500" ],
                [ "營運發行", "fa-solid fa-sun text-orange-400" ],
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
                <span class="bg-cyan-500 w-2 h-8 rounded-full"></span> 產業種類
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500 rounded-t-2xl"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 relative mb-12">
                    <div class="group cursor-pointer bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center transition-all duration-300 hover:-translate-y-3 hover:shadow-2xl hover:bg-slate-50 hover:border-cyan-400 hover:ring-2 hover:ring-cyan-400/50" onclick="toggleCompanyList(companySectors, '高爾夫球具業', 'cyan')">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-5xl transition-all duration-300 group-hover:bg-cyan-100 group-hover:text-cyan-600">
                            <i class="fa-solid fa-golf-ball-tee transition-transform group-hover:scale-110"></i>
                        </div>
                        <h4 class="text-xl font-bold text-slate-700 transition-colors group-hover:text-cyan-700">高爾夫球具業</h4>
                    </div>

                    <div class="group cursor-pointer bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center transition-all duration-300 hover:-translate-y-3 hover:shadow-2xl hover:bg-slate-50 hover:border-blue-400 hover:ring-2 hover:ring-blue-400/50" onclick="toggleCompanyList(companySectors, '旅遊服務業', 'blue')">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-5xl transition-all duration-300 group-hover:bg-blue-100 group-hover:text-blue-600">
                            <i class="fa-solid fa-plane-up transition-transform group-hover:scale-110"></i>
                        </div>
                        <h4 class="text-xl font-bold text-slate-700 transition-colors group-hover:text-blue-700">旅遊服務業</h4>
                    </div>

                    <div class="group cursor-pointer bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center transition-all duration-300 hover:-translate-y-3 hover:shadow-2xl hover:bg-slate-50 hover:border-purple-400 hover:ring-2 hover:ring-purple-400/50" onclick="toggleCompanyList(companySectors, '線上遊戲業', 'purple', iconMap.get('線上遊戲業'))">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-5xl transition-all duration-300 group-hover:bg-purple-100 group-hover:text-purple-600">
                            <i class="fa-solid fa-gamepad transition-transform group-hover:scale-110"></i>
                        </div>
                        <h4 class="text-xl font-bold text-slate-700 transition-colors group-hover:text-purple-700">線上遊戲業</h4>
                    </div>

                    <div class="group cursor-pointer bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center transition-all duration-300 hover:-translate-y-3 hover:shadow-2xl hover:bg-slate-50 hover:border-rose-400 hover:ring-2 hover:ring-rose-400/50" onclick="toggleCompanyList(companySectors, '娛樂服務業', 'rose')">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-5xl transition-all duration-300 group-hover:bg-rose-100 group-hover:text-rose-600">
                            <i class="fa-solid fa-ticket transition-transform group-hover:scale-110"></i>
                        </div>
                        <h4 class="text-xl font-bold text-slate-700 transition-colors group-hover:text-rose-700">娛樂服務業</h4>
                    </div>

                    <div class="group cursor-pointer bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center transition-all duration-300 hover:-translate-y-3 hover:shadow-2xl hover:bg-slate-50 hover:border-amber-400 hover:ring-2 hover:ring-amber-400/50" onclick="toggleCompanyList(companySectors, '旅館服務業', 'amber')">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-5xl transition-all duration-300 group-hover:bg-amber-100 group-hover:text-amber-600">
                            <i class="fa-solid fa-hotel transition-transform group-hover:scale-110"></i>
                        </div>
                        <h4 class="text-xl font-bold text-slate-700 transition-colors group-hover:text-amber-700">旅館服務業</h4>
                    </div>

                    <div class="group cursor-pointer bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center transition-all duration-300 hover:-translate-y-3 hover:shadow-2xl hover:bg-slate-50 hover:border-emerald-400 hover:ring-2 hover:ring-emerald-400/50" onclick="toggleCompanyList(companySectors, '休閒車業', 'emerald')">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-5xl transition-all duration-300 group-hover:bg-emerald-100 group-hover:text-emerald-600">
                            <i class="fa-solid fa-caravan transition-transform group-hover:scale-110"></i>
                        </div>
                        <h4 class="text-xl font-bold text-slate-700 transition-colors group-hover:text-emerald-700">休閒車業</h4>
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
        banner("<?= $category ?>產業供應鏈", "從不同業別之間切入，認識休閒娛樂產業的上市公司資訊");
    </script>
</body>
</html>