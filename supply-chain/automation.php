<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "自動化";
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
        // 自動讀取 Localhost 資料庫資料
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
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500 rounded-t-2xl"></div>
                
                <!-- 主圖表網格：上游 (佔 4 欄) + 下游 (佔 8 欄) -->
                <div id="main_automation_panel" class="grid grid-cols-1 lg:grid-cols-12 gap-6 relative mb-12">
                    
                    <!-- ================= 上游 (4 欄) ================= -->
                    <div class="lg:col-span-4 flex flex-col gap-4">
                        <div class="bg-slate-100 border border-slate-300 py-2 text-center rounded-t-lg">
                            <h4 class="text-lg font-bold text-slate-700">上游</h4>
                        </div>

                        <div class="bg-slate-100/70 rounded-b-xl p-4 border border-slate-200 flex-1 flex flex-col justify-between gap-6">
                            
                            <!-- 硬體元件框 -->
                            <div class="bg-slate-200/60 border border-slate-300 rounded-xl p-4 space-y-3">
                                <h5 class="font-bold text-slate-800 text-center text-base mb-2">硬體元件</h5>
                                <div class="space-y-3">
                                    <!-- 區塊 1: 感測器 (無上市公司) -->
                                    <div class="w-full bg-slate-100 opacity-60 border border-slate-200 py-3 rounded-lg font-bold text-slate-400 shadow-sm text-center cursor-not-allowed">
                                        感測器 (無上市公司)
                                    </div>
                                    <!-- 區塊 2: 控制器 (無上市公司) -->
                                    <div class="w-full bg-slate-100 opacity-60 border border-slate-200 py-3 rounded-lg font-bold text-slate-400 shadow-sm text-center cursor-not-allowed">
                                        控制器 (無上市公司)
                                    </div>
                                    <!-- 區塊 3: HMI (無上市公司) -->
                                    <div class="w-full bg-slate-100 opacity-60 border border-slate-200 py-3 rounded-lg font-bold text-slate-400 shadow-sm text-center cursor-not-allowed">
                                        HMI (無上市公司)
                                    </div>
                                </div>
                            </div>

                            <!-- 區塊 4: 軟體工具 (無上市公司) -->
                            <div class="w-full bg-slate-100 opacity-60 border border-slate-200 p-4 rounded-xl shadow-sm text-center flex flex-col items-center justify-center cursor-not-allowed">
                                <div class="font-bold text-slate-400 text-base mb-1">軟體工具 (無上市公司)</div>
                                <div class="text-xs text-slate-400 font-normal">（例如 CAD/CAE/CAM、數據分析與視覺化工具）</div>
                            </div>

                        </div>
                    </div>

                    <!-- ================= 下游 (8 欄) ================= -->
                    <div class="lg:col-span-8 flex flex-col gap-4">
                        <div class="bg-slate-100 border border-slate-300 py-2 text-center rounded-t-lg">
                            <h4 class="text-lg font-bold text-slate-700">下游</h4>
                        </div>

                        <div class="bg-slate-100/70 rounded-b-xl p-4 border border-slate-200 flex-1 flex flex-col justify-between gap-4">
                            
                            <!-- 上半部：左區塊群 (機器人、自動化機台、資訊系統) + 右區塊 (整體解決方案) -->
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 flex-1">
                                
                                <!-- 左區塊群 (佔 7 欄) -->
                                <div class="md:col-span-7 flex flex-col gap-4">
                                    
                                    <!-- 機器人外框 -->
                                    <div class="bg-blue-100/60 border border-blue-200/80 rounded-xl p-3 space-y-2">
                                        <h5 class="text-center font-bold text-slate-700 text-base mb-2">機器人</h5>
                                        <div class="grid grid-cols-3 gap-2">
                                            <!-- 區塊 5: 工業型機器人 (唯二保留為可點擊按鈕) -->
                                            <button onclick="toggleCompanyList(companySectors, '工業型機器人', 'blue')" class="bg-white border border-slate-200 hover:border-blue-500 hover:bg-blue-50/50 p-2 py-4 rounded-lg font-bold text-xs md:text-sm text-slate-700 shadow-sm transition-all hover:-translate-y-0.5 text-center leading-snug flex items-center justify-center">
                                                工業型<br>機器人
                                            </button>
                                            <!-- 區塊 6: AGV 及 AMR (無上市公司) -->
                                            <div class="bg-slate-100 opacity-60 border border-slate-200 p-2 py-4 rounded-lg font-bold text-xs md:text-sm text-slate-400 shadow-sm text-center leading-snug flex items-center justify-center cursor-not-allowed">
                                                AGV 及 AMR<br>(無上市公司)
                                            </div>
                                            <!-- 區塊 7: 服務型及人型機器人 (無上市公司) -->
                                            <div class="bg-slate-100 opacity-60 border border-slate-200 p-2 py-4 rounded-lg font-bold text-xs md:text-sm text-slate-400 shadow-sm text-center leading-snug flex items-center justify-center cursor-not-allowed">
                                                服務型及人型機器人<br>(無上市公司)
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 區塊 8: 自動化機台 (無上市公司) -->
                                    <div class="w-full bg-slate-100 opacity-60 border border-slate-200 p-3 rounded-xl shadow-sm text-center cursor-not-allowed">
                                        <div class="font-bold text-slate-400 text-base mb-1">自動化機台 (無上市公司)</div>
                                        <div class="text-xs text-slate-400 font-normal">（例如加工、運輸、檢測、包裝等機台）</div>
                                    </div>

                                    <!-- 區塊 9: 資訊系統 (無上市公司) -->
                                    <div class="w-full bg-slate-100 opacity-60 border border-slate-200 p-3 rounded-xl shadow-sm text-center cursor-not-allowed">
                                        <div class="font-bold text-slate-400 text-base mb-1">資訊系統 (無上市公司)</div>
                                        <div class="text-xs text-slate-400 font-normal">（如 MES、SCADA、APS、PLM 等）</div>
                                    </div>
                                </div>

                                <!-- 右區塊: 區塊 10: 整體解決方案 (無上市公司) -->
                                <div class="md:col-span-5 flex">
                                    <div class="w-full bg-slate-200/80 opacity-60 border border-slate-300 text-slate-400 rounded-xl p-6 shadow-sm flex flex-col items-center justify-center text-center h-full cursor-not-allowed">
                                        <div class="font-bold text-lg md:text-xl mb-3">整體解決方案 (無上市公司)</div>
                                        <div class="text-xs md:text-sm text-slate-400 font-light leading-relaxed">
                                            （例如自動倉儲、運輸搬運、智慧工廠、能源管理等）
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- 下半部：綠色服務諮詢區域 -->
                            <div class="bg-emerald-100/70 border border-emerald-200/80 rounded-xl p-3">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <!-- 區塊 11: 系統整合 (無上市公司) -->
                                    <div class="bg-slate-100 opacity-60 border border-slate-200 py-3 rounded-lg font-bold text-slate-400 shadow-sm text-center cursor-not-allowed">
                                        系統整合 (無上市公司)
                                    </div>
                                    <!-- 區塊 12: 顧問諮詢 (無上市公司) -->
                                    <div class="bg-slate-100 opacity-60 border border-slate-200 py-3 rounded-lg font-bold text-slate-400 shadow-sm text-center cursor-not-allowed">
                                        顧問諮詢 (無上市公司)
                                    </div>
                                    <!-- 區塊 13: 導入部署 (無上市公司) -->
                                    <div class="bg-slate-100 opacity-60 border border-slate-200 py-3 rounded-lg font-bold text-slate-400 shadow-sm text-center cursor-not-allowed">
                                        導入部署 (無上市公司)
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                
                <!-- 點擊結果顯示區域 -->
                <div class="mt-16 space-y-6 relative z-20">
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
        banner("<?= $category ?>產業供應鏈", "智慧製造核心脈絡，精準掌握自動化技術夥伴");
    </script>
</body>
</html>