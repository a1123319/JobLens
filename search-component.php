<?php
/**
 * Renders and initializes the complete JobLens Search Component.
 * Require include fuse 7.3.0 (e.g., <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.3.0"></script>)
 * * @param PDO $pdo An active database connection instance.
 */
function renderSearch(
    PDO $pdo, 
    string $inputId = 'searchInput',
    string $buttonId = 'searchButton',
    string $boxId = 'suggestionBox') {
    // 1. Fetch Company Data directly within the component
    try {
        // Code 是顯示與搜尋用的代碼：外國公司（自編 Id）用股票代碼，其餘仍是 Id。
        // foreigncompany 還沒建立的資料庫退回只用 Id 的寫法，搜尋不受影響。
        $sql = "
            SELECT
                c.Id,
                c.Name,
                %s AS Code,
                GROUP_CONCAT(DISTINCT cc.Category SEPARATOR ',') AS Category,
                GROUP_CONCAT(DISTINCT n.Name SEPARATOR ' ') AS Nickname
            FROM company c
            JOIN companycategory cc ON c.Id = cc.CompanyId
            LEFT JOIN nickname n ON c.Id = n.CompanyId
            %s
            GROUP BY c.Id, c.Name%s
        ";
        try {
            $stmt = $pdo->prepare(sprintf($sql, 'COALESCE(fc.Ticker, CAST(c.Id AS CHAR))',
                'LEFT JOIN foreigncompany fc ON fc.CompanyId = c.Id', ', fc.Ticker'));
            $stmt->execute();
        } catch (PDOException $e) {
            $stmt = $pdo->prepare(sprintf($sql, 'CAST(c.Id AS CHAR)', '', ''));
            $stmt->execute();
        }
        $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<p class='text-red-500'>Search component error: " . htmlspecialchars($e->getMessage()) . "</p>";
        return;
    }

    // 3. Render HTML & CSS Markup
    ?>
    <div class="relative max-w-xl mx-auto data-joblens-search">
        <input type="text" id="<?php echo $inputId; ?>" autocomplete="off"
               placeholder="輸入公司股票代碼或名稱" 
               class="w-full p-4 pl-6 rounded-full text-slate-900 shadow-2xl focus:outline-none focus:ring-4 focus:ring-cyan-600/50 transition text-lg border border-slate-100">
        
        <button id="<?php echo $buttonId; ?>" 
                class="absolute right-2 top-2 bg-cyan-600 hover:bg-cyan-700 text-white px-8 py-2.5 rounded-full transition font-bold text-lg shadow-lg opacity-50 cursor-not-allowed">
            透視
        </button>

        <div id="<?php echo $boxId; ?>" class="hidden absolute w-full bg-white mt-2 rounded-2xl shadow-xl overflow-hidden z-50 text-left border border-slate-100 max-h-64 overflow-y-auto no-scrollbar">
        </div>
    </div>

    <script type="module" >
        import Fuse from 'https://cdn.jsdelivr.net/npm/fuse.js@7.3.0';

        const companyData = <?php echo json_encode($companies); ?>;
        
        for (const company of companyData)
        {
            company.Category = company.Category.split(',');
        }

        const searchInput = document.getElementById('<?php echo $inputId; ?>');
        const searchButton = document.getElementById('<?php echo $buttonId; ?>');
        const suggestionBox = document.getElementById('<?php echo $boxId; ?>');

        let currentResults = [];

        const fuse = new Fuse(companyData, {
            keys: [
                { name: 'Code', weight: 0.7 },
                { name: 'Name', weight: 0.5 },
                { name: 'Nickname', weight: 0.3 }
            ],
            threshold: 0.3,
            includeMatches: true
        });

        function isSubsequence(query, target) {
            let queryIdx = 0, targetIdx = 0;
            while (queryIdx < query.length && targetIdx < target.length) {
                if (query[queryIdx] === target[targetIdx]) queryIdx++;
                targetIdx++;
            }
            return queryIdx === query.length;
        }

        function updateButtonState(len) {
            if (len === 1) {
                searchButton.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                searchButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Centralized redirection logic used by both click and enter press
        function executeRedirect() {
            const query = searchInput.value.trim();
            if (query && currentResults.length === 1) {
                const target = currentResults[0].item;
                window.location.href = `search.php?id=${encodeURIComponent(target.Id)}`;
            }
        }

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            if (query.length === 0) {
                currentResults = [];
                updateButtonState(0);
                suggestionBox.classList.add('hidden');
                return;
            }

            if (/^\d+$/.test(query)) {
                currentResults = companyData
                    .filter(c => isSubsequence(query, String(c.Code)))
                    .map(c => ({ item: c }));
            } else {
                currentResults = fuse.search(query);
            }

            updateButtonState(currentResults.length);

            if (currentResults.length === 0) {
                suggestionBox.innerHTML = `<div class="p-4 text-sm text-slate-500 text-center">找不到符合的公司</div>`;
                suggestionBox.classList.remove('hidden');
                return;
            }

            suggestionBox.innerHTML = currentResults.slice(0, 10).map(res => {
                const item = res.item;
                let categoryHtml = "";
                if (item.Category)
                {
                    categoryHtml = item.Category.slice(0, 3).map(cat => 
                        `<span class="text-xs text-slate-400 border border-slate-200 px-2 py-0.5 rounded-full group-hover:border-emerald-200 group-hover:text-cyan-600">${cat}</span>`
                    ).join('');
                    const remaining = item.Category.length - 3;
                    if (remaining > 0) 
                    {
                        categoryHtml += `<span class="text-xs text-slate-400">...以及其他${remaining}個產業</span>`;
                    }
                }

                return `
                    <div data-id="${item.Id}" data-code="${item.Code}" data-name="${item.Name}"
                         class="joblens-item p-4 hover:bg-slate-50 border-b border-slate-100 last:border-none cursor-pointer flex items-center justify-between transition-colors group">
                        <div class="flex flex-col text-left">
                            <span class="font-bold text-slate-700 group-hover:text-cyan-800">${item.Name}</span>
                            ${item.Nickname ? `<span class="text-xs text-slate-400">${item.Nickname}</span>` : ''}
                        </div>
                        <div class="flex items-center gap-3 text-right">
                            ${categoryHtml}
                            <span class="font-mono font-bold text-cyan-700 bg-cyan-50 px-2 py-0.5 rounded text-sm">${item.Code}</span>
                        </div>
                    </div>`;
            }).join('');
            suggestionBox.classList.remove('hidden');
        });

        // NEW: Listen for 'Enter' key presses inside the search input field
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                executeRedirect();
            }
        });

        // Event Delegation pattern avoids needing global function names on window object
        suggestionBox.addEventListener('click', (e) => {
            const row = e.target.closest('.joblens-item');
            if (row) {
                const id = row.getAttribute('data-id');
                const name = row.getAttribute('data-name');
                const code = row.getAttribute('data-code');
                searchInput.value = `${code} ${name}`;
                suggestionBox.classList.add('hidden');
                window.location.href = `search.php?id=${encodeURIComponent(id)}`;
            }
        });

        searchButton.addEventListener('click', () => {
            executeRedirect();
        });

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                suggestionBox.classList.add('hidden');
            }
        });
    </script>
    <?php
}
?>