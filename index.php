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
        $color = $cat['color'];
        if (strpos($color, '#') === 0) {
            $catColorClass = "text-[{$color}] bg-[{$color}]/10 dark:bg-[{$color}]/20";
        }
        else {
            $catColorClass = 'text-' . $color . '-500 bg-' . $color . '-50 dark:bg-' . $color . '-500/10';
        }
        $categories[$cat['id']] = [
            'name' => $cat['name'],
            'icon' => $cat['icon'],
            'color' => $catColorClass,
            'baseColor' => $color,
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
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Link Manager Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#ec5b13",
                        "background-light": "#f8f6f6",
                        "background-dark": "#221610",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .dark .glass-card {
            background: rgba(34, 22, 16, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            document.getElementById('clock-hours').textContent = hours;
            document.getElementById('clock-minutes').textContent = minutes;
            document.getElementById('clock-seconds').textContent = seconds;

            const greetingElement = document.getElementById('greeting-text');
            const h = now.getHours();
            let greeting = 'Good morning';
            if (h >= 12 && h < 18) {
                greeting = 'Good afternoon';
            } else if (h >= 18) {
                greeting = 'Good evening';
            }
            if(greetingElement) greetingElement.textContent = `${greeting}, Alex`;
        }
        setInterval(updateTime, 1000);
        window.addEventListener('load', updateTime);
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen">
<div class="relative flex h-auto min-h-screen w-full flex-col overflow-x-hidden">
<div class="layout-container flex h-full grow flex-col">
<header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-slate-200 dark:border-slate-800 px-6 lg:px-10 py-3 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md sticky top-0 z-10">
<div class="flex items-center gap-8">
<div class="flex items-center gap-4 text-primary">
<div class="size-6">
<svg fill="currentColor" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
<path clip-rule="evenodd" d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V4Z" fill-rule="evenodd"></path>
</svg>
</div>
<h2 class="text-slate-900 dark:text-white text-lg font-bold leading-tight tracking-[-0.015em]">Link Manager</h2>
</div>
<div class="hidden md:flex items-center gap-9">
<a class="text-primary text-sm font-medium leading-normal" href="#">Dashboard</a>
<a class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Links</a>
<a class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Tags</a>
<a class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Settings</a>
</div>
</div>
<div class="flex flex-1 justify-end gap-6 items-center">
<label class="hidden sm:flex flex-col min-w-40 !h-10 max-w-64">
<div class="flex w-full flex-1 items-stretch rounded-full h-full glass-card border-none">
<div class="text-slate-400 flex items-center justify-center pl-4 pr-2">
<span class="material-symbols-outlined text-[20px]">search</span>
</div>
<input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-full rounded-l-none bg-transparent text-slate-900 dark:text-white focus:outline-0 focus:ring-0 border-none h-full placeholder:text-slate-400 px-2 text-sm font-normal leading-normal" placeholder="Search links..." value=""/>
</div>
</label>
<button class="sm:hidden text-slate-600 dark:text-slate-400">
<span class="material-symbols-outlined">search</span>
</button>
<div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10 border-2 border-primary/20" data-alt="User profile picture" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCFYLUbE1FZSMh-3udwV3q_JF1UUj7QCErnKR1Sf1ZaC2SLlIIYb2C6zorgkgRg4kGg3VSJoKTp5G588EqIjCnWN0BaveeHgEaRKFXri-xbMBh82yz0DI4wPtbH033qJOBSon5InGo0vGG8Kx87q3NzrkpZSqjJwxQs24Fd61gCdzLylVNh-hc-mbF3Qwnlmrq9PgPvsZx0xfvc7ct9X_mjJhy9PRe85Fyo2gVuFtESHewyqhcmenwCeHT8pm7RO314xt2JKZgJfXMA");'></div>
</div>
</header>
<main class="flex-1 max-w-[1200px] w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
<!-- Header Section -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
<div>
<h1 class="text-slate-900 dark:text-white text-3xl font-bold leading-tight tracking-[-0.015em]" id="greeting-text">Good morning, Alex</h1>
<p class="text-slate-500 dark:text-slate-400 mt-1">Here's an overview of your links.</p>
</div>
<!-- Digital Clock -->
<div class="flex gap-3 glass-card rounded-xl p-3">
<div class="flex flex-col items-center">
<span class="text-slate-900 dark:text-white text-xl font-bold font-mono" id="clock-hours">10</span>
<span class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-wider">Hours</span>
</div>
<span class="text-slate-300 dark:text-slate-600 font-bold self-start mt-1">:</span>
<div class="flex flex-col items-center">
<span class="text-slate-900 dark:text-white text-xl font-bold font-mono" id="clock-minutes">30</span>
<span class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-wider">Minutes</span>
</div>
<span class="text-slate-300 dark:text-slate-600 font-bold self-start mt-1">:</span>
<div class="flex flex-col items-center">
<span class="text-primary text-xl font-bold font-mono" id="clock-seconds">45</span>
<span class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-wider">Seconds</span>
</div>
</div>
</div>
<!-- Stats Summary -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
<div class="glass-card rounded-xl p-5 flex flex-col gap-2 relative overflow-hidden group">
<div class="absolute top-0 right-0 w-24 h-24 bg-primary/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150"></div>
<div class="flex items-center gap-2 mb-1">
<span class="material-symbols-outlined text-slate-400 text-[20px]">link</span>
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total Links</p>
</div>
<p class="text-slate-900 dark:text-white text-3xl font-bold"><?php echo $totalLinks; ?></p>
</div>
<div class="glass-card rounded-xl p-5 flex flex-col gap-2 relative overflow-hidden group">
<div class="absolute top-0 right-0 w-24 h-24 bg-primary/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150"></div>
<div class="flex items-center gap-2 mb-1">
<span class="material-symbols-outlined text-slate-400 text-[20px]">sell</span>
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Active Tags</p>
</div>
<p class="text-slate-900 dark:text-white text-3xl font-bold"><?php echo count($categories); ?></p>
</div>
<div class="glass-card rounded-xl p-5 flex flex-col gap-2 relative overflow-hidden group">
<div class="absolute top-0 right-0 w-24 h-24 bg-primary/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150"></div>
<div class="flex items-center gap-2 mb-1">
<span class="material-symbols-outlined text-slate-400 text-[20px]">touch_app</span>
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Clicks This Week</p>
</div>
<p class="text-slate-900 dark:text-white text-3xl font-bold">342</p>
</div>
</div>
<!-- Filters -->
<div class="flex gap-2 overflow-x-auto pb-4 mb-4 scrollbar-hide">
<button class="flex h-9 shrink-0 items-center justify-center rounded-full bg-primary text-white px-5 text-sm font-medium transition-transform hover:scale-105">
    All
</button>
<?php foreach ($categories as $cat): ?>
<button class="flex h-9 shrink-0 items-center justify-center rounded-full glass-card hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 px-5 text-sm font-medium transition-colors">
    <?php echo htmlspecialchars($cat['name']); ?>
</button>
<?php
endforeach; ?>
<button onclick="document.getElementById('add-category-modal').classList.add('active')" class="flex h-9 shrink-0 items-center justify-center rounded-full glass-card hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 px-5 text-sm font-medium transition-colors border-dashed border-slate-300 dark:border-slate-700">
<span class="material-symbols-outlined text-[18px] mr-1">add</span> New Filter
</button>
</div>
<!-- Link Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<?php foreach ($links as $link): ?>
<div class="glass-card rounded-2xl p-5 flex flex-col group hover:-translate-y-1 transition-transform duration-300">
<div class="flex justify-between items-start mb-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-primary border border-slate-200 dark:border-slate-700">
    <?php if ($link['logoUrl']): ?>
        <img src="<?php echo htmlspecialchars($link['logoUrl']); ?>" class="w-6 h-6 object-contain" />
    <?php
    else: ?>
        <span class="material-symbols-outlined"><?php echo htmlspecialchars($categories[$link['theme']]['icon'] ?? 'link'); ?></span>
    <?php
    endif; ?>
</div>
<div>
<h3 class="text-slate-900 dark:text-white font-semibold line-clamp-1"><?php echo htmlspecialchars($link['title']); ?></h3>
<p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5"><?php echo htmlspecialchars(parse_url($link['url'], PHP_URL_HOST) ?? $link['url']); ?></p>
</div>
</div>
<button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
<span class="material-symbols-outlined text-[20px]">more_vert</span>
</button>
</div>
<div class="flex flex-wrap gap-2 mb-5 mt-auto">
    <?php foreach (getTags($link['tags']) as $tag): ?>
        <span class="px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-medium border border-slate-200 dark:border-slate-700"><?php echo htmlspecialchars($tag['name'] ?? $tag); ?></span>
    <?php
    endforeach; ?>
</div>
<a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" class="w-full flex items-center justify-center gap-2 bg-transparent hover:bg-primary/5 text-primary border border-primary/20 hover:border-primary/50 py-2.5 rounded-xl text-sm font-semibold transition-all">
    Open Link <span class="material-symbols-outlined text-[18px]">open_in_new</span>
</a>
</div>
<?php
endforeach; ?>
</div>
</main>
</div>
</div>
</body>
</html>
