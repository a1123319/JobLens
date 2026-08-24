<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "鋼鐵";
$steelCompanies = getCompanies($category);
$availableSectors = [];

foreach ($steelCompanies as $company) {
    if (!empty($company["Sector"])) {
        $availableSectors[$company["Sector"]] = true;
    }
}

function steelLabel(string $label, string $sector, array $availableSectors): string
{
    return isset($availableSectors[$sector]) ? $label : $label . "（無上市公司）";
}
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
        const companySectors = fromCompanyDatabase(<?= json_encode($steelCompanies, JSON_UNESCAPED_UNICODE) ?>);
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .steel-stage-grid {
            --steel-lane-height: 286px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 48px minmax(0, 1.55fr) 48px minmax(0, 1.15fr);
            gap: 0;
            align-items: stretch;
            margin-bottom: 3rem;
        }
        .steel-stage { min-width: 0; display: flex; flex-direction: column; }
        .steel-stage-heading { display: flex; align-items: center; gap: .75rem; margin-bottom: 1.5rem; }
        .steel-stage-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            padding: 1.25rem;
            flex: 1;
            box-shadow: 0 1px 2px rgb(15 23 42 / .05);
            display: grid;
            grid-template-rows: repeat(2, var(--steel-lane-height));
            gap: 1.5rem;
            min-height: 636px;
            overflow: visible;
            transition: box-shadow .2s ease;
        }
        .steel-stage-card:hover { box-shadow: 0 4px 6px -1px rgb(15 23 42 / .08), 0 2px 4px -2px rgb(15 23 42 / .06); }
        .steel-lane { min-width: 0; height: 100%; border-radius: .55rem; padding: .75rem; position: relative; z-index: 1; display: flex; flex-direction: column; justify-content: center; }
        .steel-lane-carbon { background: #eff6ff; border: 1px solid #bfdbfe; }
        .steel-lane-stainless { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .steel-lane-label { color: rgb(100 116 139); font-size: .7rem; font-weight: 700; letter-spacing: .08em; margin-bottom: .65rem; text-transform: uppercase; }
        .steel-lane-flow { display: flex; align-items: stretch; gap: .6rem; min-width: 0; }
        .steel-stage-upstream .steel-lane-flow { display: grid; grid-template-columns: minmax(0, 1.55fr) 1rem minmax(4.75rem, .8fr); }
        .steel-node { min-height: 72px; }
        .steel-stage-upstream .steel-lane::after,
        .steel-stage-middle .steel-lane::before {
            content: "";
            position: absolute;
            top: 50%;
            width: 21px;
            height: 4px;
            background: #cbd5e1;
            transform: translateY(-50%);
        }
        .steel-stage-upstream .steel-lane::after { right: -22px; }
        .steel-stage-middle .steel-lane::before { left: -22px; }
        .steel-stage-middle .steel-stage-card { position: relative; }
        .steel-stage-middle .steel-stage-card::after {
            content: "";
            position: absolute;
            top: 50%;
            right: -22px;
            width: 22px;
            height: 4px;
            background: #cbd5e1;
            transform: translateY(-50%);
        }
        .steel-stage-upstream .steel-node { flex: 1 1 0; min-width: 0; min-height: 112px; padding: .75rem .5rem; justify-content: center; text-align: center; }
        .steel-stage-upstream .steel-node { flex-direction: column; }
        .steel-stage-upstream .steel-node span:last-child { overflow-wrap: anywhere; }
        .steel-stage-upstream .steel-node > span:first-child { width: 2.25rem; height: 2.25rem; border-radius: 9999px; display: inline-flex; align-items: center; justify-content: center; }
        .steel-stage-upstream .steel-lane-carbon .steel-node > span:first-child { background: #eff6ff; }
        .steel-stage-upstream .steel-lane-stainless .steel-node > span:first-child { background: #f0fdf4; }
        .steel-flow-arrow { align-self: center; color: rgb(148 163 184); font-size: 1rem; line-height: 1; flex: 0 0 auto; }
        .steel-product-list { display: grid; grid-template-columns: minmax(0, 1fr); gap: .5rem; flex: 1; min-height: 0; }
        .steel-product-list .steel-node { min-width: 0; min-height: 0; }
        .steel-stainless-flow { display: grid; grid-template-columns: minmax(0, 1.55fr) 2rem minmax(5.5rem, .75fr); gap: .4rem; align-items: stretch; flex: 1; min-height: 0; }
        .steel-stainless-products { display: grid; grid-template-columns: minmax(0, 1fr); gap: .5rem; min-height: 0; }
        .steel-processing-list { display: flex; flex-direction: column; gap: .65rem; }
        .steel-stainless-products .steel-node,
        .steel-processing-list .steel-node { min-width: 0; min-height: 0; flex: 1; }
        .steel-branch-arrow { align-self: center; justify-self: center; color: rgb(148 163 184); font-size: 1rem; line-height: 1; }
        .steel-connector { padding-top: 4.8125rem; display: grid; grid-template-rows: repeat(2, var(--steel-lane-height)); gap: 1.5rem; align-content: start; }
        .steel-connector-lane { min-height: 0; position: relative; display: flex; align-items: center; justify-content: center; }
        .steel-connector-lane::before { content: ""; position: absolute; inset-inline: 0; top: 50%; height: 4px; background: #cbd5e1; transform: translateY(-50%); }
        .steel-connector-shared-lane { grid-row: 1 / span 2; }
        .steel-connector-arrow { width: 1.75rem; height: 1.75rem; border-radius: 9999px; background: #f8fafc; border: 1px solid #cbd5e1; color: rgb(148 163 184); font-size: .75rem; display: inline-flex; align-items: center; justify-content: center; position: relative; z-index: 1; }
        .steel-stage-downstream .steel-stage-card { display: block; min-height: 636px; }
        .steel-downstream-list { display: flex; flex-direction: column; justify-content: center; gap: .55rem; height: 100%; }
        .steel-downstream-list .steel-node { min-height: 0; flex: 1; }
        .steel-sector-disabled { filter: grayscale(1); opacity: .58; cursor: not-allowed; }

        @media (max-width: 1023px) {
            .steel-stage-grid { grid-template-columns: minmax(0, 1fr); gap: 1.25rem; }
            .steel-stage-heading { margin-bottom: 1rem; }
            .steel-stage-card { display: flex; min-height: 0; height: auto; flex: initial; gap: 1rem; }
            .steel-stage-upstream .steel-lane,
            .steel-stage-middle .steel-lane { height: auto; margin: 0; border-radius: .55rem; }
            .steel-stage-upstream .steel-lane::after,
            .steel-stage-middle .steel-lane::before,
            .steel-stage-middle .steel-stage-card::after { display: none; }
            .steel-stage-upstream .steel-node { min-height: 116px; }
            .steel-connector { padding: 0; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-template-rows: 52px; gap: .75rem; }
            .steel-connector-lane::before { inset: 0 auto; left: 50%; top: 0; width: 4px; height: 100%; transform: translateX(-50%); }
            .steel-connector-shared { grid-template-columns: minmax(0, 1fr); }
            .steel-connector-shared-lane { grid-row: 1; }
            .steel-connector-arrow { transform: rotate(90deg); }
            .steel-stage-downstream .steel-stage-card { min-height: 0; }
            .steel-downstream-list .steel-node { min-height: 64px; }
        }

        @media (max-width: 639px) {
            .steel-lane-flow { flex-direction: column; }
            .steel-stage-upstream .steel-lane-flow { grid-template-columns: minmax(0, 1fr); grid-template-rows: auto 2rem auto; }
            .steel-flow-arrow,
            .steel-branch-arrow { width: 1.75rem; height: 1.75rem; border-radius: 9999px; background: #f8fafc; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; justify-content: center; justify-self: center; font-size: .75rem; transform: rotate(90deg); }
            .steel-stage-upstream .steel-node { min-height: 88px; }
            .steel-stainless-flow { grid-template-columns: minmax(0, 1fr); grid-template-rows: auto 2rem auto; }
            .steel-processing-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24">
            <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-sky-600 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>

            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-5 md:p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-400 via-slate-500 to-green-500 rounded-t-2xl"></div>
                <div id="main_steel_panel" class="steel-stage-grid relative">
                    <!-- 上游：碳鋼與不鏽鋼兩條獨立支線 -->
                    <div class="steel-stage steel-stage-upstream">
                        <div class="steel-stage-heading">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-400 font-normal">原料與煉鋼</span></h4>
                        </div>
                        <div class="steel-stage-card">
                            <div class="steel-lane steel-lane-carbon">
                                <div class="steel-lane-label">碳鋼路線</div>
                                <div class="steel-lane-flow">
                                    <button type="button" data-sector="煤、鐵礦砂、廢鋼" data-color="blue" class="steel-sector steel-node bg-white border border-slate-200 rounded-lg shadow-sm transition-all flex items-center gap-2">
                                        <span class="text-blue-600 text-lg flex-shrink-0"><i class="fa-solid fa-mountain"></i></span>
                                        <span class="font-bold text-slate-700 text-sm"><?= steelLabel("煤、鐵礦砂、廢鋼", "煤、鐵礦砂、廢鋼", $availableSectors) ?></span>
                                    </button>
                                    <span class="steel-flow-arrow" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></span>
                                    <button type="button" data-sector="鋼胚" data-color="blue" class="steel-sector steel-node bg-white border border-slate-200 rounded-lg shadow-sm transition-all flex items-center gap-2">
                                        <span class="text-blue-600 text-lg flex-shrink-0"><i class="fa-solid fa-cubes-stacked"></i></span>
                                        <span class="font-bold text-slate-700 text-sm"><?= steelLabel("鋼胚", "鋼胚", $availableSectors) ?></span>
                                    </button>
                                </div>
                            </div>
                            <div class="steel-lane steel-lane-stainless">
                                <div class="steel-lane-label">不鏽鋼路線</div>
                                <div class="steel-lane-flow">
                                    <button type="button" data-sector="煤、鐵礦砂、鎳鐵、鉻鐵、廢鋼" data-color="green" class="steel-sector steel-node bg-white border border-slate-200 rounded-lg shadow-sm transition-all flex items-center gap-2">
                                        <span class="text-green-700 text-lg flex-shrink-0"><i class="fa-solid fa-recycle"></i></span>
                                        <span class="font-bold text-slate-700 text-sm"><?= steelLabel("煤、鐵礦砂、鎳鐵、鉻鐵、廢鋼", "煤、鐵礦砂、鎳鐵、鉻鐵、廢鋼", $availableSectors) ?></span>
                                    </button>
                                    <span class="steel-flow-arrow" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></span>
                                    <button type="button" data-sector="不鏽鋼胚" data-color="green" class="steel-sector steel-node bg-white border border-slate-200 rounded-lg shadow-sm transition-all flex items-center gap-2">
                                        <span class="text-green-700 text-lg flex-shrink-0"><i class="fa-solid fa-cubes-stacked"></i></span>
                                        <span class="font-bold text-slate-700 text-sm"><?= steelLabel("不鏽鋼胚", "不鏽鋼胚", $availableSectors) ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="steel-connector" aria-hidden="true">
                        <div class="steel-connector-lane steel-connector-carbon"><span class="steel-connector-arrow"><i class="fa-solid fa-arrow-right"></i></span></div>
                        <div class="steel-connector-lane steel-connector-stainless"><span class="steel-connector-arrow"><i class="fa-solid fa-arrow-right"></i></span></div>
                    </div>

                    <!-- 中游：兩條支線並列，不在碳鋼與不鏽鋼之間放箭頭 -->
                    <div class="steel-stage steel-stage-middle">
                        <div class="steel-stage-heading">
                            <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-400 font-normal">軋延與加工</span></h4>
                        </div>
                        <div class="steel-stage-card">
                            <div class="steel-lane steel-lane-carbon">
                                <div class="steel-lane-label">碳鋼產品</div>
                                <div class="steel-product-list">
                                    <?php foreach (["冷熱軋鋼板捲", "鋼筋", "線材盤元", "棒鋼盤元(捲狀條鋼)"] as $sector): ?>
                                        <button type="button" data-sector="<?= $sector ?>" data-color="blue" class="steel-sector steel-node w-full bg-white border border-slate-200 rounded-lg p-3 transition-all text-left flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-bars-staggered text-sm"></i></span>
                                            <span class="font-bold text-slate-700 text-xs leading-5"><?= steelLabel($sector, $sector, $availableSectors) ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="steel-lane steel-lane-stainless">
                                <div class="steel-lane-label">不鏽鋼產品</div>
                                <div class="steel-stainless-flow">
                                    <div class="steel-stainless-products">
                                        <?php foreach (["冷熱軋不鏽鋼板捲", "不鏽鋼棒線", "不鏽鋼型鋼"] as $sector): ?>
                                            <button type="button" data-sector="<?= $sector ?>" data-color="green" class="steel-sector steel-node w-full bg-white border border-slate-200 rounded-lg p-3 transition-all text-left flex items-center gap-3">
                                                <span class="w-8 h-8 rounded-full bg-green-50 text-green-700 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-gears text-sm"></i></span>
                                                <span class="font-bold text-slate-700 text-xs leading-5"><?= steelLabel($sector, $sector, $availableSectors) ?></span>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                    <span class="steel-branch-arrow" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></span>
                                    <div class="steel-processing-list">
                                        <?php foreach (["裁剪加工", "製管"] as $sector): ?>
                                            <button type="button" data-sector="<?= $sector ?>" data-color="green" class="steel-sector steel-node w-full bg-white border border-slate-200 rounded-lg p-3 transition-all text-center flex items-center justify-center">
                                                <span class="font-bold text-slate-700 text-xs leading-5"><?= steelLabel($sector, $sector, $availableSectors) ?></span>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="steel-connector steel-connector-shared" aria-hidden="true">
                        <div class="steel-connector-lane steel-connector-shared-lane"><span class="steel-connector-arrow"><i class="fa-solid fa-arrow-right"></i></span></div>
                    </div>

                    <!-- 下游：共同的終端應用，不與中游兩支線互相穿插 -->
                    <div class="steel-stage steel-stage-downstream">
                        <div class="steel-stage-heading">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-400 font-normal">終端製品與應用</span></h4>
                        </div>
                        <div class="steel-stage-card">
                            <div class="steel-downstream-list">
                                <?php foreach ([
                                    "金屬製品" => "fa-solid fa-shapes",
                                    "機械設備(如產業機械、精密機械、工具機)" => "fa-solid fa-gears",
                                    "運輸工具" => "fa-solid fa-truck-front",
                                    "模具" => "fa-solid fa-shapes",
                                    "螺絲螺帽" => "fa-solid fa-screwdriver-wrench",
                                    "鋼線鋼纜" => "fa-solid fa-grip-lines",
                                    "工業設施" => "fa-solid fa-industry",
                                    "建築工程" => "fa-solid fa-building"
                                ] as $sector => $icon): ?>
                                    <button type="button" data-sector="<?= $sector ?>" data-color="green" class="steel-sector steel-node w-full bg-white border border-slate-200 rounded-lg p-3 transition-all text-left flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-full bg-green-50 text-green-700 flex items-center justify-center flex-shrink-0"><i class="<?= $icon ?> text-sm"></i></span>
                                        <span class="font-bold text-slate-700 text-xs leading-5"><?= steelLabel($sector, $sector, $availableSectors) ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-20 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-sky-600 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        document.querySelectorAll('.steel-sector').forEach((button) => {
            const sector = button.dataset.sector;
            const color = button.dataset.color;
            const hasCompanies = companySectors.has(sector) && companySectors.get(sector).length > 0;

            if (!hasCompanies) {
                button.disabled = true;
                button.setAttribute('aria-disabled', 'true');
                button.classList.add('steel-sector-disabled');
                return;
            }

            button.classList.add('cursor-pointer', 'hover:-translate-y-1', 'hover:shadow-md');
            button.addEventListener('click', () => toggleCompanyList(companySectors, sector, color));
        });

        banner("<?= $category ?>產業供應鏈", "從原料、煉鋼到終端應用，掌握碳鋼與不鏽鋼的產業鏈脈絡");
    </script>
</body>
</html>
