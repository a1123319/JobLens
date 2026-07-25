<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "體驗科技";
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode(getCompanies($category), JSON_UNESCAPED_UNICODE) ?>);
        
        // 體驗科技專屬的 Icon Map 配置
        const iconMap = new Map([
            ["近眼顯示", new Map([
                ["光學元件/模組", "fa-solid fa-glasses text-teal-500"],
                ["微型顯示器/光機引擎(LightEngine)", "fa-solid fa-vr-cardboard text-emerald-500"]
            ])],
            ["感測器/模組", new Map([
                ["環境感知", "fa-solid fa-earth-americas text-cyan-500"],
                ["深度感測", "fa-solid fa-cube text-blue-500"],
                ["SLAM", "fa-solid fa-location-dot text-indigo-500"],
                ["使用者感知", "fa-solid fa-user-gear text-teal-600"],
                ["視覺", "fa-solid fa-eye text-sky-500"],
                ["手勢/動作", "fa-solid fa-hand-pointer text-amber-500"]
            ])],
            ["其他零組件", new Map([
                ["其他", "fa-solid fa-microchip text-slate-500"],
                ["電池模組", "fa-solid fa-battery-full text-green-500"],
                ["散熱元件/模組", "fa-solid fa-fan text-blue-400"]
            ])],
            ["頭顯裝置品牌廠", new Map([
                ["VRHeadset", "fa-solid fa-headset text-indigo-500"],
                ["ARSmartGlasses", "fa-solid fa-glasses text-purple-500"],
                ["MRHeadset&SG", "fa-solid fa-mask text-violet-600"]
            ])],
            ["硬體開發工具", new Map([
                ["工作流程創建", "fa-solid fa-diagram-project text-indigo-500"]
            ])],
            ["軟體開發工具", new Map([
                ["工作流程創建", "fa-solid fa-sitemap text-indigo-500"],
                ["內容管理與創建", "fa-solid fa-folder-plus text-blue-500"],
                ["3D工具", "fa-solid fa-cubes-stacked text-purple-500"]
            ])],
            ["應用軟體/內容", new Map([
                ["培訓", "fa-solid fa-graduation-cap text-purple-600"],
                ["會議", "fa-solid fa-users text-indigo-600"],
                ["工作流程引導", "fa-solid fa-route text-teal-600"],
                ["地圖", "fa-solid fa-map-location-dot text-emerald-600"],
                ["遊戲", "fa-solid fa-gamepad text-rose-500"]
            ])]
        ]);
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24" aria-labelledby="supply-chain-heading">
            <h2 id="supply-chain-heading" class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-indigo-600 w-2 h-8 rounded-full" aria-hidden="true"></span>
                供應鏈結構圖
            </h2>

            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8 pb-12 relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-teal-500 via-indigo-600 to-purple-600 rounded-t-2xl" aria-hidden="true"></div>

                <div class="mb-8 text-center">
                    <p class="text-sm text-slate-500">依產業價值鏈資訊平台分類，點擊各節點查看對應公司。</p>
                </div>

                <!-- 三大主要區塊容器 -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 relative items-stretch">
                    
                    <!-- 左區：關鍵硬體零組件 (佔 3 欄) -->
                    <div class="lg:col-span-3 bg-slate-100/70 p-5 rounded-2xl border border-slate-200 flex flex-col justify-between space-y-3">
                        <h3 class="text-center font-bold text-slate-700 pb-2 border-b border-slate-200">關鍵硬體零組件</h3>
                        
                        <!-- 屏蔽：處理器/IC (無上市公司) -->
                        <div class="w-full bg-slate-200/60 border border-slate-300 py-3 px-3 rounded-xl font-bold text-slate-400 shadow-sm flex items-center gap-3 cursor-not-allowed">
                            <div class="w-9 h-9 rounded-full bg-slate-300/60 text-slate-400 flex items-center justify-center text-base flex-shrink-0">
                                <i class="fa-solid fa-microchip"></i>
                            </div>
                            <div class="text-left">
                                <span class="block leading-tight text-sm">處理器 / IC</span>
                                <span class="text-xs block font-normal">（無上市公司）</span>
                            </div>
                        </div>
                        
                        <div onclick="toggleCompanyList(companySectors, '近眼顯示', 'teal', iconMap.get('近眼顯示'))" class="cursor-pointer w-full bg-white hover:bg-teal-50 border border-slate-200 hover:border-teal-500 py-3 px-3 rounded-xl font-bold text-slate-700 shadow-sm hover:shadow transition-all flex items-center gap-3 hover:-translate-y-0.5">
                            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-base flex-shrink-0">
                                <i class="fa-solid fa-glasses"></i>
                            </div>
                            <span>近眼顯示</span>
                        </div>
                        
                        <div onclick="toggleCompanyList(companySectors, '感測器/模組', 'teal', iconMap.get('感測器/模組'))" class="cursor-pointer w-full bg-white hover:bg-teal-50 border border-slate-200 hover:border-teal-500 py-3 px-3 rounded-xl font-bold text-slate-700 shadow-sm hover:shadow transition-all flex items-center gap-3 hover:-translate-y-0.5">
                            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-base flex-shrink-0">
                                <i class="fa-solid fa-rss"></i>
                            </div>
                            <span>感測器 / 模組</span>
                        </div>
                        
                        <div onclick="toggleCompanyList(companySectors, '其他', 'teal', iconMap.get('其他零組件'))" class="cursor-pointer w-full bg-white hover:bg-teal-50 border border-slate-200 hover:border-teal-500 py-3 px-3 rounded-xl font-bold text-slate-700 shadow-sm hover:shadow transition-all flex items-center gap-3 hover:-translate-y-0.5">
                            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-base flex-shrink-0">
                                <i class="fa-solid fa-ellipsis"></i>
                            </div>
                            <span>其他</span>
                        </div>
                    </div>

                    <!-- 中區：品牌與開發工具 (佔 5 欄) -->
                    <div class="lg:col-span-5 bg-slate-100/70 p-5 rounded-2xl border border-slate-200 flex flex-col justify-between space-y-3">
                        
                        <!-- 上層：頭顯品牌 -->
                        <div onclick="toggleCompanyList(companySectors, '頭顯裝置品牌廠', 'indigo', iconMap.get('頭顯裝置品牌廠'))" class="cursor-pointer w-full bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-500 py-3 px-4 rounded-xl font-bold text-slate-800 shadow-sm hover:shadow transition-all flex items-center gap-3 hover:-translate-y-0.5">
                            <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-base flex-shrink-0">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <span>頭顯裝置品牌廠</span>
                        </div>

                        <!-- 中層：開發引擎 -->
                        <div class="bg-indigo-100/70 p-3.5 rounded-xl border border-indigo-200/80 flex-1 flex flex-col justify-center space-y-2.5">
                            <p class="text-center font-bold text-indigo-900 text-sm tracking-wide">開發引擎 / 編輯工具</p>
                            <div class="grid grid-cols-2 gap-3 flex-1 items-stretch">
                                <div onclick="toggleCompanyList(companySectors, '硬體開發工具', 'indigo', iconMap.get('硬體開發工具'))" class="cursor-pointer bg-white hover:bg-indigo-50 border border-indigo-200 hover:border-indigo-400 p-2.5 rounded-lg font-bold text-slate-700 text-sm shadow-sm hover:shadow transition-all flex items-center gap-2 hover:-translate-y-0.5">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                    </div>
                                    <span>硬體開發工具</span>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '軟體開發工具', 'indigo', iconMap.get('軟體開發工具'))" class="cursor-pointer bg-white hover:bg-indigo-50 border border-indigo-200 hover:border-indigo-400 p-2.5 rounded-lg font-bold text-slate-700 text-sm shadow-sm hover:shadow transition-all flex items-center gap-2 hover:-translate-y-0.5">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-code"></i>
                                    </div>
                                    <span>軟體開發工具</span>
                                </div>
                            </div>
                        </div>

                        <!-- 下層：技術標準與資服 -->
                        <div class="grid grid-cols-2 gap-3">
                            <div onclick="toggleCompanyList(companySectors, '技術標準/聯盟', 'indigo')" class="cursor-pointer bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-500 py-3 px-3 rounded-xl font-bold text-slate-700 text-sm shadow-sm hover:shadow transition-all flex items-center gap-2 hover:-translate-y-0.5">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm flex-shrink-0">
                                    <i class="fa-solid fa-network-wired"></i>
                                </div>
                                <span>技術標準 / 聯盟</span>
                            </div>
                            <div onclick="toggleCompanyList(companySectors, '資服', 'indigo')" class="cursor-pointer bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-500 py-3 px-3 rounded-xl font-bold text-slate-700 text-sm shadow-sm hover:shadow transition-all flex items-center gap-2 hover:-translate-y-0.5">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm flex-shrink-0">
                                    <i class="fa-solid fa-handshake-angle"></i>
                                </div>
                                <span>資服</span>
                            </div>
                        </div>
                    </div>

                    <!-- 右區：組裝與應用內容 (佔 4 欄) -->
                    <div class="lg:col-span-4 bg-slate-100/70 p-5 rounded-2xl border border-slate-200 flex flex-col justify-between space-y-3">
                        <!-- 上層：組裝廠 -->
                        <div onclick="toggleCompanyList(companySectors, '組裝廠', 'purple')" class="cursor-pointer w-full bg-white hover:bg-purple-50 border border-slate-200 hover:border-purple-500 py-3 px-3 rounded-xl font-bold text-slate-700 shadow-sm hover:shadow transition-all flex items-center gap-3 hover:-translate-y-0.5">
                            <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-base flex-shrink-0">
                                <i class="fa-solid fa-industry"></i>
                            </div>
                            <span>組裝廠</span>
                        </div>

                        <!-- 下層：應用軟體&內容 -->
                        <div class="bg-purple-100/60 p-3.5 rounded-xl border border-purple-200 flex-1 flex flex-col justify-between space-y-2">
                            <p class="text-center font-bold text-purple-900 text-sm tracking-wide">應用軟體 & 內容</p>
                            
                            <div onclick="toggleCompanyList(companySectors, '應用軟體/內容', 'purple', iconMap.get('應用軟體/內容'))" class="cursor-pointer w-full bg-white hover:bg-purple-50 border border-purple-200 hover:border-purple-400 py-2.5 px-3 rounded-lg font-bold text-slate-700 text-sm shadow-sm hover:shadow transition-all flex items-center gap-3 hover:-translate-y-0.5">
                                <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-sm flex-shrink-0">
                                    <i class="fa-solid fa-cubes"></i>
                                </div>
                                <span>應用軟體 / 內容</span>
                            </div>
                            
                            <!-- 屏蔽：垂直應用方案 (無上市公司) -->
                            <div class="w-full bg-slate-200/60 border border-slate-300 py-2.5 px-3 rounded-lg font-bold text-slate-400 text-sm shadow-sm flex items-center gap-3 cursor-not-allowed">
                                <div class="w-8 h-8 rounded-full bg-slate-300/60 text-slate-400 flex items-center justify-center text-sm flex-shrink-0">
                                    <i class="fa-solid fa-sitemap"></i>
                                </div>
                                <div class="text-left">
                                    <span class="block leading-tight">垂直應用方案</span>
                                    <span class="text-xs block font-normal">（無上市公司）</span>
                                </div>
                            </div>
                            
                            <div onclick="toggleCompanyList(companySectors, '支援服務', 'purple')" class="cursor-pointer w-full bg-white hover:bg-purple-50 border border-purple-200 hover:border-purple-400 py-2.5 px-3 rounded-lg font-bold text-slate-700 text-sm shadow-sm hover:shadow transition-all flex items-center gap-3 hover:-translate-y-0.5">
                                <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-sm flex-shrink-0">
                                    <i class="fa-solid fa-headset"></i>
                                </div>
                                <span>支援服務</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-12 space-y-6 relative z-20">
                    <h3 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-indigo-600">點擊上方節點查看公司列表</h3>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "包含關鍵硬體零組件、開發引擎、頭顯品牌廠至應用軟體與垂直方案");
    </script>
</body>
</html>