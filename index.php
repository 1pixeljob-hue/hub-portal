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
        <aside class="hidden md:flex w-64 flex-col border-r border-border-light/50 dark:border-border-dark/50 bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-md transition-colors duration-300">
            <div class="flex h-16 items-center gap-3 px-6 border-b border-border-light/50 dark:border-border-dark/50">
                <div class="flex items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 p-1.5 shadow-lg shadow-indigo-500/20">
                    <span class="material-symbols-outlined text-[24px] text-white">link</span>
                </div>
                <h1 class="text-lg font-bold tracking-tight bg-gradient-to-r from-indigo-600 to-pink-500 bg-clip-text text-transparent dark:from-indigo-400 dark:to-pink-400">Link Manager</h1>
            </div>
            <div class="flex flex-1 flex-col justify-between overflow-y-auto p-4">
                <nav class="flex flex-col gap-1">
                    <div class="px-2 py-2">
                        <p class="text-xs font-semibold uppercase tracking-wider text-text-secondary-light dark:text-text-secondary-dark">Dashboard</p>
                    </div>
                    <a class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-600 shadow-md shadow-indigo-500/20 transition-all hover:shadow-lg hover:shadow-indigo-500/30 hover:scale-[1.02]" href="#">
                        <span class="material-symbols-outlined text-[20px]">dashboard</span>
                        All Links
                    </a>
                </nav>
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
                <div class="flex items-center gap-4">
                    <button class="relative rounded-lg p-2 text-text-secondary-light dark:text-text-secondary-dark hover:bg-surface-light-highlight dark:hover:bg-surface-dark-highlight hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" id="theme-toggle" title="Toggle Theme">
                        <span class="material-symbols-outlined text-[24px]">light_mode</span>
                    </button>
                    <button class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:scale-[1.02] transition-all" onclick="document.getElementById('add-modal').classList.add('active')">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        Add New Link
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
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3" id="links-container">
                        <?php if (empty($links)): ?>
                            <div class="col-span-full py-12 text-center">
                                <span class="material-symbols-outlined text-6xl text-text-secondary-light/30">link_off</span>
                                <p class="mt-4 text-text-secondary-light">No links found. Start by adding one!</p>
                            </div>
                        <?php
else: ?>
                            <?php foreach ($links as $link): ?>
                                <div class="group interactive-card link-card" data-id="<?php echo $link['id']; ?>">
                                    <div class="interactive-card-inner flex flex-col justify-between p-5">
                                        <div class="absolute top-0 left-0 h-1 w-full bg-gradient-to-r from-indigo-400 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity rounded-t-xl z-10"></div>
                                        <div>
                                            <div class="mb-4 flex items-start justify-between">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white dark:bg-surface-dark-highlight p-2 border border-border-light/50 dark:border-white/5 shadow-sm relative z-10">
                                                    <?php if ($link['logoUrl']): ?>
                                                        <img src="<?php echo htmlspecialchars($link['logoUrl']); ?>" class="h-8 w-8" alt="Logo">
                                                    <?php
        else: ?>
                                                        <span class="text-xl font-bold bg-gradient-to-br from-indigo-500 to-purple-600 bg-clip-text text-transparent"><?php echo htmlspecialchars($link['initial']); ?></span>
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
                                            <div class="flex flex-wrap gap-2">
                                                <?php foreach (getTags($link['tags']) as $tag): ?>
                                                    <span class="rounded-full bg-indigo-50 dark:bg-indigo-900/20 px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800/30">
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

    <!-- Add Link Modal -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 opacity-0 pointer-events-none transition-all duration-300" id="add-modal">
        <div class="bg-surface-light dark:bg-surface-dark w-full max-w-lg rounded-2xl shadow-2xl border border-border-light/50 dark:border-border-dark/50 overflow-hidden transform scale-95 transition-all duration-300 modal-content">
            <form id="add-link-form" onsubmit="window.submitForm(event)">
                <div class="px-6 py-5 border-b border-border-light/50 dark:border-border-dark/50">
                    <h3 class="text-xl font-bold text-text-primary-light dark:text-white">Add New Link</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Title</label>
                        <input id="link-title" class="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none" required placeholder="My Website" type="text"/>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">URL</label>
                        <input id="link-url" class="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none" required placeholder="https://example.com" type="url"/>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Category</label>
                            <select id="link-category" class="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                                <option value="indigo">Work</option>
                                <option value="purple">Personal</option>
                                <option value="pink">Social</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Tags (Comma separated)</label>
                            <input id="link-tags" class="w-full bg-surface-light-highlight dark:bg-surface-dark-highlight border border-border-light dark:border-border-dark rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none" placeholder="dev, tools"/>
                        </div>
                    </div>
                </div>
                <div class="bg-surface-light-highlight/30 dark:bg-black/20 px-6 py-4 flex items-center justify-end gap-3">
                    <button class="px-5 py-2.5 text-sm font-semibold text-text-secondary-light" onclick="document.getElementById('add-modal').classList.remove('active')" type="button">Cancel</button>
                    <button class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg shadow-indigo-500/25 hover:scale-[1.02] transition-all" type="submit">Add Link</button>
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
