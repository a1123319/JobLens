<?php
function nav($id = null)
{
    ?>
    <nav class="bg-slate-900 text-white p-4 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='../index.php'">
                <img src="../assets/magnifying-glass.png" alt="Logo" class="w-8 h-8 object-contain">
                <span class="text-xl font-bold tracking-wider">JobLens</span>
            </div>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                <?php if ($id !== null): ?>
                <a href="../search.php?id=<?= $id ?>" class="hover:text-cyan-400 transition">企業資訊</a>
                <?php endif; ?>
                <a href="../about.html" class="border border-cyan-500 text-cyan-400 px-5 py-2 rounded-full font-bold hover:bg-cyan-500 hover:text-white transition-all">
                    關於我們
                </a>
            </div>
        </div>
    </nav>
<?php }

function getCompanies($category) {
    $host = 'localhost';
    $db_name = 'joblens';
    $username = 'joblens';
    $password = '123456';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    $stmt = $pdo->prepare("
        SELECT cc.CompanyId, c.Name CompanyName, cc.Sector, cc.Subsector
        FROM companycategory cc JOIN company c
        ON cc.CompanyId = c.Id
        WHERE cc.Category = ?
    ");
    $stmt->execute([$category]);
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $companies;
}
?>