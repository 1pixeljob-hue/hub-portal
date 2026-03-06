<?php
require_once __DIR__ . '/api/db.php';

// Fetch links from database
try {
    $stmt = $pdo->query("SELECT * FROM links ORDER BY created_at DESC");
    $links = $stmt->fetchAll();
}
catch (\PDOException $e) {
    $links = [];
    $dbError = $e->getMessage();
}

// Function to decode tags
function getTags($tagStr)
{
    return json_decode($tagStr, true) ?? [];
}

// Fetch categories from database
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY created_at ASC");
    $dbCategories = $stmt->fetchAll();

    $categories = [];
    foreach ($dbCategories as $cat) {
        $categories[$cat['id']] = [
            'name' => $cat['name'],
            'icon' => $cat['icon'],
            'color' => 'text-' . $cat['color'] . '-500 bg-' . $cat['color'] . '-50 dark:bg-' . $cat['color'] . '-500/10',
            'baseColor' => $cat['color'],
            'count' => 0
        ];
    }
}
catch (\PDOException $e) {
    $categories = [];
}

$totalLinks = 0;
foreach ($links as $link) {
    $theme = $link['theme'] ?? 'indigo';
    if (isset($categories[$theme])) {
        $categories[$theme]['count']++;
    }
    $totalLinks++;
}
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Vibrant Link Manager Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#6366f1",
                        "secondary": "#ec4899",
                        "accent": "#8b5cf6",
                        "background-light": "#f8fafc",
                        "background-dark": "#0f172a",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1e293b",
                        "surface-light-highlight": "#f1f5f9",
                        "surface-dark-highlight": "#334155",
                        "border-light": "#e2e8f0",
                        "border-dark": "#334155",
                        "text-primary-light": "#0f172a",
                        "text-primary-dark": "#f8fafc",
                        "text-secondary-light": "#64748b",
                        "text-secondary-dark": "#94a3b8"
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    backgroundImage: {
                        'mesh-light': 'radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%)',
                        'mesh-dark': 'radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,25%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%)',
                        'mesh-vibrant': 'radial-gradient(at 40% 20%, hsla(266,100%,70%,0.15) 0px, transparent 50%), radial-gradient(at 80% 0%, hsla(189,100%,56%,0.15) 0px, transparent 50%), radial-gradient(at 0% 50%, hsla(340,100%,76%,0.15) 0px, transparent 50%)',
                        'gradient-border': 'linear-gradient(to right, #6366f1, #a855f7, #ec4899)',
                        'card-gradient': 'linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%)'
                    },
                },
            },
        }
    </script>
    <style>
        .glass-panel { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .gradient-text { background-clip: text; -webkit-background-clip: text; color: transparent; background-image: linear-gradient(to right, #6366f1, #ec4899); }
        .interactive-card { position: relative; z-index: 1; border-radius: 1rem; padding: 1px; background: transparent; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); }
        .interactive-card::before { content: ""; position: absolute; inset: 0; border-radius: 1rem; padding: 1px; background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05)); -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0); -webkit-mask-composite: xor; mask-composite: exclude; pointer-events: none; transition: background 0.3s ease; }
        .interactive-card:hover { transform: scale(1.02); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); }
        .interactive-card:hover::before { background: linear-gradient(45deg, #6366f1, #ec4899, #8b5cf6, #3b82f6); opacity: 1; }
        .interactive-card-inner { height: 100%; border-radius: calc(1rem - 1px); background-color: rgb(255 255 255 / 0.8); backdrop-filter: blur(12px); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.5); }
        .dark .interactive-card-inner { background-color: rgb(30 41 59 / 0.6); border: 1px solid rgba(255, 255, 255, 0.05); }
        .interactive-card:hover .interactive-card-inner { background-color: rgb(255 255 255 / 0.95); box-shadow: inset 0 0 20px rgba(99, 102, 241, 0.15); border-color: transparent; }
        .dark .interactive-card:hover .interactive-card-inner { background-color: rgb(15 23 42 / 0.9); box-shadow: inset 0 0 20px rgba(99, 102, 241, 0.15); border-color: transparent; }
        .refined-btn { position: relative; overflow: hidden; z-index: 10; }
        .refined-btn::after { content: ''; position: absolute; inset: 0; background: linear-gradient(45deg, #6366f1, #ec4899); opacity: 0; transition: opacity 0.3s ease; z-index: -1; border-radius: inherit; }
        .refined-btn:hover::after { opacity: 1; }
        .refined-btn:hover { color: white !important; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-text-primary-light dark:text-text-primary-dark antialiased overflow-hidden transition-colors duration-300 selection:bg-pink-500 selection:text-white">
    <div class="fixed inset-0 pointer-events-none bg-mesh-vibrant z-0 opacity-100 dark:opacity-40"></div>
    <div class="flex h-screen w-full relative z-10">
        <!-- Sidebar -->
        <aside class="hidden md:flex w-72 flex-col border-r border-border-light/80 dark:border-border-dark/80 bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-xl transition-colors duration-300 z-20">
            <div class="flex h-20 items-center gap-4 px-6 border-b border-border-light/50 dark:border-border-dark/50">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 shadow-lg shadow-purple-500/20">
                    <span class="material-symbols-outlined text-[24px] text-white">webhook</span>
                </div>
                <h1 class="text-xl font-bold tracking-tight bg-gradient-to-r from-indigo-600 to-pink-500 bg-clip-text text-transparent dark:from-indigo-400 dark:to-pink-400">Hub Portal</h1>
            </div>
            
            <div class="flex flex-1 flex-col overflow-y-auto py-6 px-4 custom-scrollbar">
                <div class="mb-6">
                    <p class="px-3 mb-2 text-xs font-bold uppercase tracking-wider text-text-secondary-light dark:text-text-secondary-dark/70">Overview</p>
                    <nav class="space-y-1">
                        <button class="category-filter active group flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight" data-filter="all">
                            <div class="flex items-center gap-3 text-text-primary-light dark:text-text-primary-dark">
                                <span class="material-symbols-outlined text-[20px] text-indigo-500 dark:text-indigo-400">dashboard</span>
                                All Links
                            </div>
                            <span class="flex h-6 min-w-[24px] items-center justify-center rounded-full bg-indigo-100 px-2 text-xs font-bold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300"><?php echo $totalLinks; ?></span>
                        </button>
                    </nav>
                </div>
                
                <div>
                    <div class="flex items-center justify-between px-3 mb-2">
                        <p class="text-xs font-bold uppercase tracking-wider text-text-secondary-light dark:text-text-secondary-dark/70">Categories</p>
                        <button onclick="document.getElementById('add-category-modal').classList.add('active')" class="text-text-secondary-light hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors" title="Add new category">
                            <span class="material-symbols-outlined text-[16px]">add_circle</span>
                        </button>
                    </div>
                    <nav class="space-y-1" id="category-nav">
                        <?php foreach ($categories as $key => $cat): ?>
                        <button class="category-filter group flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium transition-all hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight" data-filter="<?php echo htmlspecialchars($key); ?>">
                            <div class="flex items-center gap-3 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-text-primary-light dark:group-hover:text-text-primary-dark transition-colors">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg <?php echo htmlspecialchars($cat['color']); ?> shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]"><?php echo htmlspecialchars($cat['icon']); ?></span>
                                </div>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </div>
                            <?php if ($cat['count'] > 0): ?>
                            <span class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-border-light dark:bg-border-dark px-1.5 text-[10px] font-bold text-text-secondary-light dark:text-text-secondary-dark group-hover:bg-indigo-100 group-hover:text-indigo-700 dark:group-hover:bg-indigo-500/20 dark:group-hover:text-indigo-300 transition-colors">
                                <?php echo $cat['count']; ?>
                            </span>
                            <?php
    endif; ?>
                        </button>
                        <?php
endforeach; ?>
                    </nav>
                </div>
            </div>
            
            <div class="mt-auto border-t border-border-light/50 dark:border-border-dark/50 p-4">
                <div class="rounded-2xl border border-indigo-100 dark:border-indigo-500/20 bg-gradient-to-b from-indigo-50/50 to-white dark:from-indigo-500/5 dark:to-surface-dark p-4 shadow-sm">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                        <span class="material-symbols-outlined text-[20px]">bolt</span>
                    </div>
                    <h4 class="mb-1 text-sm font-bold text-text-primary-light dark:text-text-primary-dark">Pro Workspace</h4>
                    <p class="text-xs text-text-secondary-light dark:text-text-secondary-dark">Manage all your essential portals and links effortlessly.</p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex h-full flex-1 flex-col overflow-hidden bg-transparent transition-colors duration-300">
            <header class="flex h-16 shrink-0 items-center justify-between border-b border-border-light/50 dark:border-border-dark/50 bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-md px-6 transition-colors duration-300">
                <div class="flex flex-1 items-center max-w-md">
                    <div class="relative w-full group">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-text-secondary-light dark:text-text-secondary-dark group-focus-within:text-indigo-500 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">search</span>
                        </div>
                        <input id="link-search" class="block w-full rounded-full border-2 border-transparent bg-surface-light-highlight dark:bg-surface-dark-highlight py-2 pl-10 pr-3 text-sm text-text-primary-light dark:text-white placeholder-text-secondary-light dark:placeholder-text-secondary-dark focus:border-indigo-500 focus:bg-white dark:focus:bg-surface-dark focus:outline-none focus:ring-0 transition-all shadow-inner" placeholder="Search links, tags..." type="text"/>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="relative rounded-lg p-2 text-text-secondary-light dark:text-text-secondary-dark hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" id="theme-toggle" title="Toggle Theme">
                        <span class="material-symbols-outlined text-[24px]">light_mode</span>
                    </button>
                    <button class="flex items-center gap-2 rounded-lg bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light/50 dark:border-border-dark/50 px-3.5 py-2 text-sm font-semibold text-text-primary-light dark:text-white hover:border-indigo-500/50 dark:hover:border-indigo-500/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:shadow-sm transition-all" onclick="document.getElementById('add-category-modal').classList.add('active')">
                        <span class="material-symbols-outlined text-[18px]">create_new_folder</span>
                        Category
                    </button>
                    <button class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:scale-[1.02] transition-all" onclick="document.getElementById('add-modal').classList.add('active')">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        New Link
                    </button>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="mx-auto max-w-7xl">
                    <!-- Greeting Section -->
                    <div class="relative mb-8 overflow-hidden rounded-2xl p-[2px] bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 shadow-xl shadow-purple-500/10">
                        <div class="relative flex flex-col md:flex-row md:items-end justify-between gap-4 rounded-2xl bg-white/90 dark:bg-surface-dark/90 backdrop-blur-xl p-6 md:p-8">
                            <div class="z-10">
                                <h2 id="greeting-text" class="text-3xl md:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-600 to-pink-500 bg-clip-text text-transparent dark:from-indigo-400 dark:to-pink-400 drop-shadow-sm">Good morning, Boss</h2>
                                <p class="text-text-secondary-light dark:text-text-secondary-dark mt-2 font-medium">Manage your portal links efficiently.</p>
                            </div>
                            <div class="z-10 flex items-center gap-3 rounded-xl bg-surface-light-highlight/50 dark:bg-surface-dark-highlight/50 p-3 backdrop-blur-md border border-white/20 dark:border-white/5">
                                <span id="clock-display" class="text-4xl font-light tracking-tight text-text-primary-light dark:text-white tabular-nums">00:00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Grid of Links -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" id="links-container">
                        <?php if (empty($links)): ?>
                            <div class="col-span-full py-16 text-center flex flex-col items-center justify-center">
                                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-3xl bg-surface-light-highlight dark:bg-surface-dark-highlight shadow-inner">
                                    <span class="material-symbols-outlined text-4xl text-text-secondary-light/40 dark:text-text-secondary-dark/40">link_off</span>
                                </div>
                                <h3 class="text-xl font-bold text-text-primary-light dark:text-white mb-2">No Links Found</h3>
                                <p class="text-text-secondary-light dark:text-text-secondary-dark max-w-sm">You haven't added any portal links yet. Click "Add New Link" to get started.</p>
                            </div>
                        <?php
else: ?>
                            <?php foreach ($links as $link):
        $cardTheme = $link['theme'] ?? 'indigo';
        // Determine gradient colors based on theme
        $gradients = [
            'indigo' => 'from-indigo-500 to-indigo-600 hover:shadow-indigo-500/20 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 border-indigo-100 dark:border-indigo-800/30',
            'purple' => 'from-purple-500 to-purple-600 hover:shadow-purple-500/20 text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 border-purple-100 dark:border-purple-800/30',
            'pink' => 'from-pink-500 to-pink-600 hover:shadow-pink-500/20 text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-900/20 border-pink-100 dark:border-pink-800/30',
            'emerald' => 'from-emerald-500 to-emerald-600 hover:shadow-emerald-500/20 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-800/30'
        ];
        $gClass = $gradients[$cardTheme] ?? $gradients['indigo'];
        // Splitting into color specific parts for tags
        preg_match('/text-([a-z]+)-600/', $gClass, $matches);
        $colorName = $matches[1] ?? 'indigo';
?>
                                <div class="group interactive-card link-card filter-item transition-all duration-300 transform" data-id="<?php echo $link['id']; ?>" data-category="<?php echo $cardTheme; ?>">
                                    <div class="interactive-card-inner flex flex-col justify-between p-5">
                                        <div class="absolute top-0 left-0 h-1.5 w-full bg-gradient-to-r <?php echo preg_replace('/hover:shadow-.*?\s.*/', '', $gClass); ?> opacity-0 group-hover:opacity-100 transition-opacity rounded-t-xl z-10"></div>
                                        <div>
                                            <div class="mb-5 flex items-start justify-between">
                                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white dark:bg-surface-dark-highlight p-2 border border-border-light/80 dark:border-white/10 shadow-sm relative z-10 overflow-hidden shrink-0 group-hover:scale-105 transition-transform">
                                                    <?php if ($link['logoUrl']): ?>
                                                        <img src="<?php echo htmlspecialchars($link['logoUrl']); ?>" class="h-10 w-10 object-contain" alt="Logo">
                                                    <?php
        else: ?>
                                                        <span class="text-2xl font-black bg-gradient-to-br <?php echo preg_replace('/hover:shadow-.*?\s.*/', '', $gClass); ?> bg-clip-text text-transparent"><?php echo htmlspecialchars($link['initial']); ?></span>
                                                    <?php
        endif; ?>
                                                </div>
                                                <div class="relative z-20">
                                                    <button class="rounded-lg p-1.5 text-text-secondary-light dark:text-text-secondary-dark hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight transition-all" onclick="window.toggleMenu('menu-<?php echo $link['id']; ?>', event)">
                                                        <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                                    </button>
                                                    <div id="menu-<?php echo $link['id']; ?>" class="action-menu hidden absolute right-0 mt-1 w-32 origin-top-right rounded-lg bg-white dark:bg-surface-dark shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden z-30 border border-border-light dark:border-border-dark">
                                                        <button class="flex w-full items-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" onclick="window.deleteLink('<?php echo $link['id']; ?>')">
                                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <h3 class="mb-1 text-lg font-bold text-text-primary-light dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"><?php echo htmlspecialchars($link['title']); ?></h3>
                                            <p class="mb-4 truncate text-sm text-text-secondary-light dark:text-text-secondary-dark"><?php echo htmlspecialchars(parse_url($link['url'], PHP_URL_HOST)); ?></p>
                                            <div class="flex flex-wrap gap-2 mt-auto">
                                                <?php foreach (getTags($link['tags']) as $tag):
            // use tag color style matching card theme
?>
                                                    <span class="rounded-full <?php echo "bg-{$colorName}-50 dark:bg-{$colorName}-900/20 text-{$colorName}-600 dark:text-{$colorName}-400 border-{$colorName}-200/50 dark:border-{$colorName}-800/30"; ?> px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide border">
                                                        <?php echo htmlspecialchars($tag['name'] ?? $tag); ?>
                                                    </span>
                                                <?php
        endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="mt-4 border-t border-border-light dark:border-white/5 pt-4 relative z-10">
                                            <a class="refined-btn flex w-full items-center justify-center gap-2 rounded-lg bg-surface-light-highlight dark:bg-surface-dark-highlight py-2 text-sm font-medium text-text-primary-light dark:text-white transition-all opacity-40 group-hover:opacity-100 bg-white/10 backdrop-blur-sm" href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank">
                                                Open Link
                                                <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php
    endforeach; ?>
                        <?php
endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Link Modal (Side-by-side Layout) -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 opacity-0 pointer-events-none transition-all duration-300" id="add-modal">
        <div class="bg-surface-light dark:bg-surface-dark w-full max-w-4xl rounded-3xl shadow-2xl border border-border-light/50 dark:border-border-dark/50 overflow-hidden transform scale-95 transition-all duration-300 modal-content flex flex-col md:flex-row">
            
            <!-- Left Side: Live Preview -->
            <div class="w-full md:w-5/12 bg-surface-light-highlight/50 dark:bg-black/20 p-8 flex flex-col border-b md:border-b-0 md:border-r border-border-light/50 dark:border-border-dark/50 relative overflow-hidden">
                <div class="absolute inset-0 bg-mesh-vibrant opacity-20 pointer-events-none"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-surface-light/80 dark:to-surface-dark/80 pointer-events-none"></div>
                
                <h3 class="text-sm font-bold uppercase tracking-wider text-text-secondary-light dark:text-text-secondary-dark mb-6 relative z-10 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                    Live Preview
                </h3>
                
                <div class="flex-1 flex items-center justify-center relative z-10">
                    <!-- The Preview Card -->
                    <div class="w-full interactive-card" id="preview-card-container">
                        <div class="interactive-card-inner flex flex-col justify-between p-5 bg-white dark:bg-surface-dark/90 transition-all duration-300" id="preview-card-inner">
                            <div class="absolute top-0 left-0 h-1.5 w-full bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-t-xl z-20" id="preview-gradient"></div>
                            <div>
                                <div class="mb-5 flex items-start justify-between">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white dark:bg-surface-dark-highlight p-2 border border-border-light/80 dark:border-white/10 shadow-sm relative z-10 overflow-hidden shrink-0">
                                        <span class="text-2xl font-black bg-gradient-to-br from-indigo-500 to-indigo-600 bg-clip-text text-transparent" id="preview-initial">N</span>
                                    </div>
                                    <div class="relative z-20">
                                        <button type="button" class="rounded-lg p-1.5 text-text-secondary-light dark:text-text-secondary-dark opacity-50 cursor-not-allowed">
                                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="mb-1 text-lg font-bold text-text-primary-light dark:text-white" id="preview-title">New Link</h3>
                                <p class="mb-4 truncate text-sm text-text-secondary-light dark:text-text-secondary-dark" id="preview-url">example.com</p>
                                <div class="flex flex-wrap gap-2 mt-auto" id="preview-tags-container">
                                    <span class="rounded-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border-indigo-200/50 dark:border-indigo-800/30 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide border">WORK</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Input Form -->
            <div class="w-full md:w-7/12 flex flex-col">
                <form id="add-link-form" onsubmit="window.submitForm(event)" class="h-full flex flex-col">
                    <div class="px-8 py-6 border-b border-border-light/50 dark:border-border-dark/50 flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-text-primary-light dark:text-white">Add New Link</h3>
                            <p class="text-sm text-text-secondary-light tracking-wide mt-1">Create a beautiful card for your portal.</p>
                        </div>
                        <button type="button" class="rounded-full p-2 text-text-secondary-light hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight transition-colors" onclick="document.getElementById('add-modal').classList.remove('active')">
                            <span class="material-symbols-outlined text-[24px]">close</span>
                        </button>
                    </div>
                    
                    <div class="p-8 space-y-6 flex-1 overflow-y-visible">
                        <div class="group">
                            <label class="block text-sm font-bold text-text-primary-light dark:text-text-primary-dark mb-2 group-focus-within:text-indigo-500 transition-colors">Title <span class="text-red-500">*</span></label>
                            <input id="link-title" class="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border-2 border-transparent focus:border-indigo-500 rounded-xl px-4 py-3.5 text-sm outline-none transition-all shadow-inner text-text-primary-light dark:text-white font-medium placeholder-text-secondary-light/60" required placeholder="e.g. My Awesome Workspace" type="text"/>
                        </div>
                        
                        <div class="group">
                            <label class="block text-sm font-bold text-text-primary-light dark:text-text-primary-dark mb-2 group-focus-within:text-indigo-500 transition-colors">URL Address <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-[18px] text-text-secondary-light">link</span>
                                </div>
                                <input id="link-url" class="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border-2 border-transparent focus:border-indigo-500 rounded-xl pl-11 pr-4 py-3.5 text-sm outline-none transition-all shadow-inner text-text-primary-light dark:text-white font-medium placeholder-text-secondary-light/60" required placeholder="e.g. google.com" type="text"/>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-sm font-bold text-text-primary-light dark:text-text-primary-dark mb-2 group-focus-within:text-indigo-500 transition-colors">Category Theme</label>
                                <div class="relative custom-select-container">
                                    <button type="button" id="category-select-btn" class="w-full flex items-center justify-between text-left appearance-none bg-surface-light-highlight dark:bg-surface-dark-highlight border-2 border-transparent focus:border-indigo-500 focus:ring-[3px] focus:ring-indigo-500/20 rounded-xl pl-4 pr-3 py-3 text-sm outline-none transition-all shadow-inner text-text-primary-light dark:text-white font-medium cursor-pointer relative z-10">
                                        <span id="category-select-text">Select Category...</span>
                                        <span class="material-symbols-outlined text-[20px] text-text-secondary-light transition-transform duration-200" id="category-select-icon">expand_more</span>
                                    </button>
                                    
                                    <div id="category-select-menu" class="absolute z-[100] bottom-full left-0 w-full mb-1.5 bg-white dark:bg-surface-dark border border-indigo-500/30 rounded-xl shadow-2xl opacity-0 invisible transform translate-y-2 transition-all duration-200 overflow-hidden origin-bottom">
                                        <ul class="max-h-60 overflow-y-auto custom-scrollbar p-1.5 space-y-0.5 bg-surface-light-highlight/20 dark:bg-surface-dark-highlight/20 backdrop-blur-md">
                                            <?php foreach ($categories as $key => $cat): ?>
                                            <li class="custom-select-option px-3 py-2 rounded-lg text-sm font-medium text-text-primary-light dark:text-text-primary-dark hover:bg-indigo-500 hover:text-white dark:hover:bg-indigo-600 dark:hover:text-white cursor-pointer flex items-center gap-2 transition-all" data-value="<?php echo htmlspecialchars($key); ?>">
                                                <span class="material-symbols-outlined text-[18px] opacity-70"><?php echo htmlspecialchars($cat['icon']); ?></span> 
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </li>
                                            <?php
endforeach; ?>
                                        </ul>
                                    </div>
                                    <input type="hidden" id="link-category" name="theme" value="<?php echo htmlspecialchars(array_key_first($categories) ?? 'indigo'); ?>">
                                </div>
                            </div>
                            <div class="group">
                                <label class="block text-sm font-bold text-text-primary-light dark:text-text-primary-dark mb-2 group-focus-within:text-indigo-500 transition-colors">Tags</label>
                                <input id="link-tags" class="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border-2 border-transparent focus:border-indigo-500 rounded-xl px-4 py-3.5 text-sm outline-none transition-all shadow-inner text-text-primary-light dark:text-white font-medium placeholder-text-secondary-light/60" placeholder="e.g. dev, design, tool"/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-surface-light-highlight/50 dark:bg-surface-dark-highlight/20 px-8 py-5 flex items-center justify-end gap-3 mt-auto border-t border-border-light/50 dark:border-border-dark/50">
                        <button class="px-6 py-2.5 text-sm font-bold text-text-secondary-light dark:text-text-secondary-dark hover:text-text-primary-light dark:hover:text-white transition-colors" onclick="document.getElementById('add-modal').classList.remove('active')" type="button">Cancel</button>
                        <button class="bg-gradient-to-r from-indigo-500 to-purple-600 px-8 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all" type="submit">Publish Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 opacity-0 pointer-events-none transition-all duration-300" id="add-category-modal">
        <div class="bg-surface-light dark:bg-surface-dark w-full max-w-md rounded-2xl shadow-2xl border border-border-light/50 dark:border-border-dark/50 overflow-hidden transform scale-95 transition-all duration-300 modal-content">
            <form id="add-category-form" onsubmit="window.submitCategoryForm(event)">
                <div class="px-6 py-5 border-b border-border-light/50 dark:border-border-dark/50 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-text-primary-light dark:text-white">New Category</h3>
                    <button type="button" class="rounded-full p-2 text-text-secondary-light hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight transition-colors" onclick="document.getElementById('add-category-modal').classList.remove('active')">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Category Name</label>
                        <input id="cat-title" class="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none" required placeholder="e.g. Design, API..." type="text"/>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Icon (Material)</label>
                            <input id="cat-icon" class="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none" placeholder="e.g. code, star" value="folder" type="text"/>
                            <p class="text-[10px] text-text-secondary-light mt-1"><a href="https://fonts.google.com/icons" target="_blank" class="hover:underline">Google Fonts Icons</a></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Color Theme</label>
                            <select id="cat-color" class="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                                <option value="indigo">Indigo</option>
                                <option value="purple">Purple</option>
                                <option value="pink">Pink</option>
                                <option value="emerald">Emerald</option>
                                <option value="rose">Rose</option>
                                <option value="amber">Amber</option>
                                <option value="cyan">Cyan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-surface-light-highlight/30 dark:bg-black/20 px-6 py-4 flex items-center justify-end gap-3">
                    <button class="px-5 py-2.5 text-sm font-semibold text-text-secondary-light" onclick="document.getElementById('add-category-modal').classList.remove('active')" type="button">Cancel</button>
                    <button class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg shadow-indigo-500/25 hover:scale-[1.02] transition-all" type="submit">Create Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-[200] flex flex-col gap-3"></div>

    <script src="js/script.js"></script>
    <style>
        #add-modal.active { opacity: 1; pointer-events: auto; }
        #add-modal.active .modal-content { transform: scale(1); }
        .action-menu.active { display: block; }
        .toast { display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-radius: 12px; background: white; border: 1px solid #e2e8f0; shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); animation: slideIn 0.3s ease-out; }
        .dark .toast { background: #1e293b; border-color: #334155; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { to { opacity: 0; transform: scale(0.95); } }
    </style>
</body>
</html>
