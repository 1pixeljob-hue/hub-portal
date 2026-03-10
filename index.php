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
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Quản lý liên kết</title>
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
            let greeting = 'Chào buổi sáng';
            if (h >= 12 && h < 18) {
                greeting = 'Chào buổi chiều';
            } else if (h >= 18) {
                greeting = 'Chào buổi tối';
            }
            if(greetingElement) greetingElement.textContent = `${greeting}, Sếp`;
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
<h2 class="text-slate-900 dark:text-white text-lg font-bold leading-tight tracking-[-0.015em]">Quản Lý Liên Kết</h2>
</div>
<div class="hidden md:flex items-center gap-9">
<a class="text-primary text-sm font-medium leading-normal" href="#">Tổng quan</a>
<a class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Liên kết</a>
<a class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Danh mục</a>
<a class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Cài đặt</a>
</div>
</div>
<div class="flex flex-1 justify-end gap-6 items-center">
<label class="hidden sm:flex flex-col min-w-40 !h-10 max-w-64">
<div class="flex w-full flex-1 items-stretch rounded-full h-full glass-card border-none">
<div class="text-slate-400 flex items-center justify-center pl-4 pr-2">
<span class="material-symbols-outlined text-[20px]">search</span>
</div>
<input id="link-search" class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-full rounded-l-none bg-transparent text-slate-900 dark:text-white focus:outline-0 focus:ring-0 border-none h-full placeholder:text-slate-400 px-2 text-sm font-normal leading-normal" placeholder="Tìm kiếm liên kết..." value=""/>
</div>
</label>
<button onclick="window.openAddLinkModal()" class="flex h-10 items-center justify-center gap-2 rounded-full bg-primary text-white hover:bg-orange-600 transition-colors px-4 text-sm font-medium">
    <span class="material-symbols-outlined text-[20px]">add</span> Thêm Liên Kết
</button>
</div>
</header>
<main class="flex-1 max-w-[1200px] w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
<!-- Header Section -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
<div>
<h1 class="text-slate-900 dark:text-white text-3xl font-bold leading-tight tracking-[-0.015em]" id="greeting-text">Chào buổi sáng, Sếp</h1>
<p class="text-slate-500 dark:text-slate-400 mt-1">Dưới đây là tổng quan về các liên kết của bạn.</p>
</div>
<!-- Digital Clock -->
<div class="flex gap-3 glass-card rounded-xl p-3">
<div class="flex flex-col items-center">
<span class="text-slate-900 dark:text-white text-xl font-bold font-mono" id="clock-hours">10</span>
<span class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-wider">Giờ</span>
</div>
<span class="text-slate-300 dark:text-slate-600 font-bold self-start mt-1">:</span>
<div class="flex flex-col items-center">
<span class="text-slate-900 dark:text-white text-xl font-bold font-mono" id="clock-minutes">30</span>
<span class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-wider">Phút</span>
</div>
<span class="text-slate-300 dark:text-slate-600 font-bold self-start mt-1">:</span>
<div class="flex flex-col items-center">
<span class="text-primary text-xl font-bold font-mono" id="clock-seconds">45</span>
<span class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-wider">Giây</span>
</div>
</div>
</div>
<!-- Stats Summary -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
<div class="glass-card rounded-xl p-5 flex flex-col gap-2 relative overflow-hidden group">
<div class="absolute top-0 right-0 w-24 h-24 bg-primary/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150"></div>
<div class="flex items-center gap-2 mb-1">
<span class="material-symbols-outlined text-slate-400 text-[20px]">link</span>
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Tổng Số Liên Kết</p>
</div>
<p class="text-slate-900 dark:text-white text-3xl font-bold"><?php echo $totalLinks; ?></p>
</div>
<div class="glass-card rounded-xl p-5 flex flex-col gap-2 relative overflow-hidden group">
<div class="absolute top-0 right-0 w-24 h-24 bg-primary/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150"></div>
<div class="flex items-center gap-2 mb-1">
<span class="material-symbols-outlined text-slate-400 text-[20px]">sell</span>
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Danh Mục (Thẻ)</p>
</div>
<p class="text-slate-900 dark:text-white text-3xl font-bold"><?php echo count($categories); ?></p>
</div>
<div class="glass-card rounded-xl p-5 flex flex-col gap-2 relative overflow-hidden group">
<div class="absolute top-0 right-0 w-24 h-24 bg-primary/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150"></div>
<div class="flex items-center gap-2 mb-1">
<span class="material-symbols-outlined text-slate-400 text-[20px]">touch_app</span>
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Lượt click Tuần này</p>
</div>
<p class="text-slate-900 dark:text-white text-3xl font-bold">342</p>
</div>
</div>
<!-- Filters -->
<div class="flex gap-2 overflow-x-auto pb-4 mb-4 scrollbar-hide" id="filters-row">
<button class="category-filter active flex h-9 shrink-0 items-center justify-center rounded-full bg-primary text-white px-5 text-sm font-medium transition-transform hover:scale-105" data-filter="all">
    Tất cả
</button>
<?php foreach ($categories as $key => $cat): ?>
<div class="group/pill shrink-0 flex items-center h-9 rounded-full glass-card pl-3 pr-1.5 text-sm font-medium transition-all" style="cursor:default">
    <button class="category-filter flex items-center gap-1 text-slate-700 dark:text-slate-300 bg-transparent border-none outline-none cursor-pointer" data-filter="<?php echo htmlspecialchars($key); ?>">
        <span class="material-symbols-outlined text-[15px]" style="color:<?php echo htmlspecialchars($cat['baseColor']); ?>"><?php echo htmlspecialchars($cat['icon']); ?></span>
        <span><?php echo htmlspecialchars($cat['name']); ?></span>
    </button>
    <span class="flex items-center gap-0.5 ml-1 opacity-0 group-hover/pill:opacity-100 transition-opacity duration-150">
        <button title="Sửa" type="button"
            onclick="event.stopPropagation(); window.openEditCategory('<?php echo htmlspecialchars($key, ENT_QUOTES); ?>','<?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?>','<?php echo htmlspecialchars($cat['icon'], ENT_QUOTES); ?>','<?php echo htmlspecialchars($cat['baseColor'], ENT_QUOTES); ?>')"
            class="w-6 h-6 rounded-full flex items-center justify-center text-slate-400 hover:text-primary hover:bg-primary/10 transition-colors">
            <span class="material-symbols-outlined" style="font-size:13px">edit</span>
        </button>
        <button title="Xóa" type="button"
            onclick="event.stopPropagation(); window.deleteCategory('<?php echo htmlspecialchars($key, ENT_QUOTES); ?>','<?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?>', event)"
            class="w-6 h-6 rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors">
            <span class="material-symbols-outlined" style="font-size:13px">delete</span>
        </button>
    </span>
</div>
<?php
endforeach; ?>
<button onclick="document.getElementById('add-category-modal').classList.add('active')" class="flex h-9 shrink-0 items-center justify-center rounded-full glass-card hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 px-5 text-sm font-medium transition-colors border-dashed border-slate-300 dark:border-slate-700">
<span class="material-symbols-outlined text-[18px] mr-1">add</span> Bộ Lọc Mới
</button>
</div>
<!-- Link Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="links-container">
<?php foreach ($links as $link): ?>
<div class="glass-card rounded-2xl p-5 flex flex-col group hover:-translate-y-1 transition-transform duration-300 link-card filter-item" data-id="<?php echo $link['id']; ?>" data-category="<?php echo $link['theme'] ?? 'indigo'; ?>">
<div class="flex justify-between items-start mb-4 relative z-0">
<div class="flex items-center gap-3">
<?php $titleInitial = strtoupper(mb_substr($link['title'] ?? 'L', 0, 1));
    $catId = $link['theme'] ?? 'indigo';
    $catData = $categories[$catId] ?? null;
    $catColor = $catData['baseColor'] ?? '#ec5b13';
    $storedLogo = $link['logoUrl'] ?? '';
    $lhost = parse_url($link['url'] ?? '', PHP_URL_HOST);

    $isHex = strpos($catColor, '#') === 0;
    $gradMaps = [
        'indigo' => 'from-[#00DDB3] to-[#0066FF]',
        'purple' => 'from-[#0066FF] to-[#00b4d8]',
        'pink' => 'from-[#00DDB3] to-[#00b4d8]',
        'emerald' => 'from-[#00DDB3] to-[#00b4d8]',
        'rose' => 'from-[#00DDB3] to-[#0066FF]',
        'amber' => 'from-[#00DDB3] to-[#00b4d8]',
        'cyan' => 'from-[#00DDB3] to-[#00b4d8]'
    ];
    $tcClasses = [
        'indigo' => 'bg-primary/10 text-primary border-primary/30',
        'purple' => 'bg-secondary/10 text-secondary border-secondary/30',
        'pink' => 'bg-accent/10 text-accent border-accent/30',
        'emerald' => 'bg-primary/10 text-primary border-primary/30',
        'rose' => 'bg-secondary/10 text-secondary border-secondary/30',
        'amber' => 'bg-primary/10 text-primary border-primary/30',
        'cyan' => 'bg-accent/10 text-accent border-accent/30'
    ];
    $gradClass = $isHex ? '' : ($gradMaps[$catColor] ?? $gradMaps['indigo']);
    $tagClassCache = $isHex ? '' : ($tcClasses[$catColor] ?? $tcClasses['indigo']);
?>
<div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center border border-slate-200 dark:border-slate-700 overflow-hidden relative flex-shrink-0 card-logo-box"
     data-host="<?php echo htmlspecialchars($lhost ?? ''); ?>"
     data-logo="<?php echo htmlspecialchars($storedLogo); ?>">
    <!-- Initial letter showing like the preview -->
    <?php if ($isHex): ?>
        <span class="text-[20px] font-black select-none bg-clip-text text-transparent" style="background-image:linear-gradient(to bottom right, <?php echo htmlspecialchars($catColor); ?>, <?php echo htmlspecialchars($catColor); ?>)"><?php echo htmlspecialchars($titleInitial); ?></span>
    <?php
    else: ?>
        <span class="text-[20px] font-black select-none bg-gradient-to-br <?php echo $gradClass; ?> bg-clip-text text-transparent"><?php echo htmlspecialchars($titleInitial); ?></span>
    <?php
    endif; ?>
</div>

<div>
<h3 class="text-slate-900 dark:text-white font-semibold line-clamp-1"><?php echo htmlspecialchars($link['title']); ?></h3>
<p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5"><?php echo htmlspecialchars(parse_url($link['url'], PHP_URL_HOST) ?? $link['url']); ?></p>
</div>
</div>
<div class="relative z-20">
    <button class="text-slate-400 hover:text-slate-600 p-1 rounded-full hover:bg-slate-100 transition-colors" onclick="window.toggleMenu('menu-<?php echo $link['id']; ?>', event)">
        <span class="material-symbols-outlined text-[20px]">more_vert</span>
    </button>
    <div id="menu-<?php echo $link['id']; ?>" class="action-menu hidden absolute right-0 mt-1 w-36 origin-top-right rounded-lg bg-white shadow-xl ring-1 ring-black/5 overflow-hidden z-30 border border-slate-200">
        <button class="flex w-full items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors" onclick='window.editLink(<?php echo json_encode($link, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>, event)'>
            <span class="material-symbols-outlined text-[18px]">edit</span> Sửa
        </button>
        <button class="flex w-full items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors" onclick="window.copyUrl('<?php echo htmlspecialchars($link['url'], ENT_QUOTES); ?>'); window.toggleMenu('menu-<?php echo $link['id']; ?>', event)">
            <span class="material-symbols-outlined text-[18px]">content_copy</span> Sao chép
        </button>
        <div class="h-px w-full bg-slate-200 border-0"></div>
        <button class="flex w-full items-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors" onclick="window.deleteLink('<?php echo $link['id']; ?>')">
            <span class="material-symbols-outlined text-[18px]">delete</span> Xóa
        </button>
    </div>
</div>
</div>
<div class="flex flex-wrap gap-2 mb-5 mt-auto">
    <!-- Category Badge -->
    <?php if ($catData): ?>
        <?php if ($isHex): ?>
        <span class="px-2.5 py-1 rounded-md text-xs font-bold border flex items-center gap-1"
              style="color:<?php echo htmlspecialchars($catColor); ?>;background:<?php echo htmlspecialchars($catColor); ?>15;border-color:<?php echo htmlspecialchars($catColor); ?>50">
            <span class="material-symbols-outlined text-[13px]"><?php echo htmlspecialchars($catData['icon']); ?></span>
            <?php echo htmlspecialchars($catData['name']); ?>
        </span>
        <?php
        else: ?>
        <span class="px-2.5 py-1 rounded-md text-xs font-bold border flex items-center gap-1 <?php echo $tagClassCache; ?>">
            <span class="material-symbols-outlined text-[13px]"><?php echo htmlspecialchars($catData['icon']); ?></span>
            <?php echo htmlspecialchars($catData['name']); ?>
        </span>
        <?php
        endif; ?>
    <?php
    endif; ?>
    
    <!-- Tags Badges -->
    <?php
    foreach (getTags($link['tags']) as $tag):
        $tagName = htmlspecialchars($tag['name'] ?? $tag);
?>
        <?php if ($isHex): ?>
        <span class="px-2.5 py-1 rounded-md text-xs font-semibold border"
              style="color:<?php echo htmlspecialchars($catColor); ?>;background:<?php echo htmlspecialchars($catColor); ?>15;border-color:<?php echo htmlspecialchars($catColor); ?>40">
            <?php echo $tagName; ?>
        </span>
        <?php
        else: ?>
        <span class="px-2.5 py-1 rounded-md text-xs font-semibold border <?php echo $tagClassCache; ?>">
            <?php echo $tagName; ?>
        </span>
        <?php
        endif; ?>
    <?php
    endforeach; ?>
</div>
<a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" class="w-full flex items-center justify-center gap-2 bg-transparent hover:bg-primary/5 text-primary border border-primary/20 hover:border-primary/50 py-2.5 rounded-xl text-sm font-semibold transition-all group-hover:bg-primary group-hover:text-white">
    Mở Liên Kết <span class="material-symbols-outlined text-[18px]">open_in_new</span>
</a>
</div>
<?php
endforeach; ?>
</div>
</main>
</div>
</div>

    <!-- Add Link Modal -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 opacity-0 pointer-events-none transition-all duration-300" id="add-modal">
        <div class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform scale-95 transition-all duration-300 modal-content flex flex-col md:flex-row">
            
            <!-- Left Side: Live Preview -->
            <div class="w-full md:w-5/12 bg-slate-50 p-8 flex flex-col border-b md:border-b-0 md:border-r border-slate-200 relative overflow-hidden">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-6 relative z-10 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">visibility</span> Xem Trước
                </h3>
                
                <div class="flex-1 flex items-center justify-center relative z-10 w-full" id="preview-card-container">
                    <div class="w-full bg-white shadow-md border border-slate-200 p-5 rounded-2xl flex flex-col group transition-all duration-300" id="preview-card-inner">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-primary border border-slate-200 overflow-hidden relative">
                                     <span class="text-xl font-black text-primary absolute z-0" id="preview-initial">N</span>
                                     <img src="" class="w-10 h-10 object-contain absolute z-10 hidden" id="preview-logo" alt="Logo">
                                </div>
                                <div>
                                    <h3 class="text-slate-900 font-semibold line-clamp-1" id="preview-title">Liên Kết Mới</h3>
                                    <p class="text-slate-500 text-xs mt-0.5" id="preview-url">example.com</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-auto" id="preview-tags-container">
                             <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-medium border border-slate-200">WORK</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Input Form -->
            <div class="w-full md:w-7/12 flex flex-col bg-white">
                <form id="add-link-form" onsubmit="window.submitForm(event)" class="h-full flex flex-col">
                    <div class="px-8 py-6 border-b border-slate-200 flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900" id="modal-title">Thêm Liên Kết Mới</h3>
                        </div>
                        <button type="button" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 transition-colors" onclick="document.getElementById('add-modal').classList.remove('active')">
                            <span class="material-symbols-outlined text-[24px]">close</span>
                        </button>
                    </div>
                    
                    <div class="flex-1 p-8 space-y-6" style="overflow: visible;">

                            <input type="hidden" id="link-id" name="id" value="">
                            <div class="group">
                                <label class="block text-sm font-bold text-slate-900 mb-2">Tiêu Đề <span class="text-red-500">*</span></label>
                                <input id="link-title" class="w-full bg-slate-50 border-2 border-transparent focus:border-primary rounded-xl px-4 py-3.5 text-sm outline-none transition-all text-slate-900 font-medium" required placeholder="vd: Không gian làm việc..." type="text"/>
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-bold text-slate-900 mb-2">Đường Dẫn URL <span class="text-red-500">*</span></label>
                                <input id="link-url" class="w-full bg-slate-50 border-2 border-transparent focus:border-primary rounded-xl px-4 py-3.5 text-sm outline-none transition-all text-slate-900 font-medium" required placeholder="example.com" type="text"/>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- Premium Custom Dropdown - menu rendered at body level via JS -->
                                <div class="group" id="cat-dropdown-wrapper">
                                    <label class="block text-sm font-bold text-slate-900 mb-2">Danh Mục</label>
                                    <input type="hidden" id="link-category" name="theme" value="<?php echo htmlspecialchars(array_key_first($categories) ?? ''); ?>">
                                    <button type="button" id="cat-dd-btn"
                                        class="w-full flex items-center justify-between gap-3 bg-slate-50 border-2 border-transparent hover:border-primary/30 rounded-xl px-4 py-3.5 text-sm text-slate-900 font-medium transition-all outline-none cursor-pointer">
                                        <span class="flex items-center gap-2" id="cat-dd-display">
                                            <?php $firstCat = reset($categories); ?>
                                            <span class="material-symbols-outlined text-[18px]" id="cat-dd-icon" style="color:<?php echo htmlspecialchars($firstCat['baseColor'] ?? '#ec5b13'); ?>"><?php echo htmlspecialchars($firstCat['icon'] ?? 'folder'); ?></span>
                                            <span id="cat-dd-label"><?php echo htmlspecialchars($firstCat['name'] ?? 'Chọn danh mục'); ?></span>
                                        </span>
                                        <span class="material-symbols-outlined text-[20px] text-slate-400 transition-transform duration-200" id="cat-dd-chevron">expand_more</span>
                                    </button>
                                    <!-- Options data stored inline, menu rendered to body by JS -->
                                    <script id="cat-options-data" type="application/json"><?php
$optArr = [];
foreach ($categories as $k => $c) {
    $optArr[] = ['value' => $k, 'icon' => $c['icon'], 'label' => $c['name'], 'color' => $c['baseColor']];
}
echo json_encode($optArr);
?></script>
                                </div>
                                <div class="group">
                                    <label class="block text-sm font-bold text-slate-900 mb-2">Thẻ Phân Loại</label>
                                    <input id="link-tags" class="w-full bg-slate-50 border-2 border-transparent focus:border-primary rounded-xl px-4 py-3.5 text-sm outline-none transition-all text-slate-900 font-medium" placeholder="vd: dev, design..."/>
                                </div>
                            </div>
                    </div>
                    
                    <div class="bg-slate-50 px-8 py-5 flex items-center justify-end gap-3 border-t border-slate-200">
                        <button class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors" onclick="document.getElementById('add-modal').classList.remove('active')" type="button">Hủy</button>
                        <button class="bg-primary px-8 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg shadow-primary/25 hover:scale-105 transition-transform" type="submit" id="submit-btn-text">Lưu Liên Kết</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 opacity-0 pointer-events-none transition-all duration-300" id="add-category-modal">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform scale-95 transition-all duration-300 modal-content relative">
            <form id="add-category-form" onsubmit="window.submitCategoryForm(event)" class="relative z-10 flex flex-col h-full">
                <div class="px-8 py-6 border-b border-slate-200 flex justify-between items-center relative">
                    <div class="absolute top-0 left-0 h-1 w-full bg-primary"></div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900">Danh Mục Mới</h3>
                    </div>
                    <button type="button" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 transition-colors" onclick="document.getElementById('add-category-modal').classList.remove('active')">
                        <span class="material-symbols-outlined text-[24px]">close</span>
                    </button>
                </div>
                <div class="p-8 space-y-6 flex-1">
                    <div class="group">
                        <label class="block text-sm font-bold text-slate-900 mb-2">Tên Danh Mục <span class="text-red-500">*</span></label>
                        <input id="cat-title" class="w-full bg-slate-50 border-2 border-transparent focus:border-primary rounded-xl px-4 py-3.5 text-sm outline-none transition-all text-slate-900 font-medium" required placeholder="vd: Thiết Kế, Công Việc..." type="text"/>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-bold text-slate-900 mb-2">Mã Biểu Tượng</label>
                            <input id="cat-icon" class="w-full bg-slate-50 border-2 border-transparent focus:border-primary rounded-xl px-4 py-3.5 text-sm outline-none transition-all text-slate-900 font-medium" placeholder="folder" value="folder" type="text"/>
                            <p class="text-[11px] font-bold text-primary mt-2"><a href="https://fonts.google.com/icons" target="_blank">Thư Viện Icon</a></p>
                        </div>
                        <div class="group">
                            <label class="block text-sm font-bold text-slate-900 mb-2">Màu Sắc</label>
                            <input id="cat-color" class="w-full h-[52px] bg-slate-50 border-2 border-transparent focus:border-primary rounded-xl px-2 py-1 outline-none transition-all cursor-pointer p-0" type="color" value="#ec5b13"/>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 border-t border-slate-200 px-8 py-5 flex items-center justify-end gap-3 mt-auto">
                    <button class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors" onclick="document.getElementById('add-category-modal').classList.remove('active')" type="button">Hủy</button>
                    <button class="bg-primary px-8 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg shadow-primary/25 hover:scale-105 transition-transform" type="submit">Lưu Danh Mục</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-[200] flex flex-col gap-3 pointer-events-none"></div>

    <!-- Edit Category Modal -->
    <div class="fixed inset-0 z-[120] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 opacity-0 pointer-events-none transition-all duration-300" id="edit-category-modal">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform scale-95 transition-all duration-300 modal-content relative">
            <form id="edit-category-form" onsubmit="window.submitEditCategory(event)" class="relative z-10 flex flex-col h-full">
                <input type="hidden" id="edit-cat-id">
                <div class="px-8 py-6 border-b border-slate-200 flex justify-between items-center relative">
                    <div class="absolute top-0 left-0 h-1 w-full bg-primary"></div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900">Chỉnh Sửa Danh Mục</h3>
                    </div>
                    <button type="button" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 transition-colors" onclick="document.getElementById('edit-category-modal').classList.remove('active')">
                        <span class="material-symbols-outlined text-[24px]">close</span>
                    </button>
                </div>
                <div class="p-8 space-y-6 flex-1">
                    <div class="group">
                        <label class="block text-sm font-bold text-slate-900 mb-2">Tên Danh Mục <span class="text-red-500">*</span></label>
                        <input id="edit-cat-title" class="w-full bg-slate-50 border-2 border-transparent focus:border-primary rounded-xl px-4 py-3.5 text-sm outline-none transition-all text-slate-900 font-medium" required placeholder="vd: Thiết Kế, Công Việc..." type="text"/>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-bold text-slate-900 mb-2">Mã Biểu Tượng</label>
                            <input id="edit-cat-icon" class="w-full bg-slate-50 border-2 border-transparent focus:border-primary rounded-xl px-4 py-3.5 text-sm outline-none transition-all text-slate-900 font-medium" placeholder="folder" value="folder" type="text"/>
                            <p class="text-[11px] font-bold text-primary mt-2"><a href="https://fonts.google.com/icons" target="_blank">Thư Viện Icon</a></p>
                        </div>
                        <div class="group">
                            <label class="block text-sm font-bold text-slate-900 mb-2">Màu Sắc</label>
                            <input id="edit-cat-color" class="w-full h-[52px] bg-slate-50 border-2 border-transparent focus:border-primary rounded-xl px-2 py-1 outline-none transition-all cursor-pointer" type="color" value="#ec5b13"/>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 border-t border-slate-200 px-8 py-5 flex items-center justify-end gap-3">
                    <button class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors" onclick="document.getElementById('edit-category-modal').classList.remove('active')" type="button">Hủy</button>
                    <button class="bg-primary px-8 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg shadow-primary/25 hover:scale-105 transition-transform" type="submit">Cập Nhật</button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="fixed inset-0 z-[200] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 opacity-0 pointer-events-none transition-all duration-300" id="confirm-modal">
        <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform scale-95 transition-all duration-300 modal-content relative p-8">
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-3xl">warning</span>
            </div>
            <h3 class="text-xl font-bold text-slate-900 text-center mb-2">Xác Nhận</h3>
            <p class="text-slate-500 text-center text-sm mb-8" id="confirm-message">Bạn có chắc chắn muốn thực hiện hành động này?</p>
            <div class="flex items-center gap-3">
                <button class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-colors" onclick="document.getElementById('confirm-modal').classList.remove('active')" id="confirm-cancel-btn">Hủy</button>
                <button class="flex-1 px-4 py-3 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 shadow-lg shadow-red-500/30 hover:scale-105 transition-all" id="confirm-ok-btn">Đồng Ý</button>
            </div>
        </div>
    </div>

    <script src="js/script.js?v=<?php echo time(); ?>"></script>
    <style>
        #add-modal.active, #add-category-modal.active, #edit-category-modal.active, #confirm-modal.active { opacity: 1; pointer-events: auto; }
        #add-modal.active .modal-content, #add-category-modal.active .modal-content, #edit-category-modal.active .modal-content, #confirm-modal.active .modal-content { transform: scale(1); }
        .action-menu.active { display: block; }
        .toast { pointer-events: auto; display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-radius: 12px; background: white; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { to { opacity: 0; transform: scale(0.95); } }

        /* Premium Custom Category Dropdown */
        #cat-dropdown-wrapper { position: relative; }
        #cat-dd-btn { background: #f8fafc; }
        #cat-dd-btn:hover, #cat-dd-btn.open { border-color: var(--color-primary, #ec5b13); background: white; }
        #cat-dd-btn.open { box-shadow: 0 0 0 3px rgba(236,91,19,.12); }
        .cat-dropdown-menu {
            position: absolute;
            left: 0; top: calc(100% + 6px);
            width: 100%;
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,.15), 0 4px 12px rgba(0,0,0,.08);
            z-index: 9999;
            overflow: hidden;
            transform-origin: top center;
            animation: ddSlideIn .18s ease-out;
        }
        @keyframes ddSlideIn {
            from { opacity: 0; transform: scaleY(.95) translateY(-6px); }
            to   { opacity: 1; transform: scaleY(1)  translateY(0); }
        }
        .cat-dropdown-menu.hidden { display: none; }
        .cat-dd-option {
            border-radius: 10px;
            transition: background .15s;
        }
        .cat-dd-option:hover { background: rgba(236,91,19,.08); }
        .cat-dd-option.selected { background: rgba(236,91,19,.1); }
        .cat-dd-option.selected .check-icon { opacity: 1 !important; }
        #cat-dd-chevron.rotated { transform: rotate(180deg); }
    </style>
    <script>
    (function() {
        function initCatDropdown() {
            const btn = document.getElementById('cat-dd-btn');
            const label = document.getElementById('cat-dd-label');
            const icon = document.getElementById('cat-dd-icon');
            const chevron = document.getElementById('cat-dd-chevron');
            const hiddenInput = document.getElementById('link-category');
            const dataEl = document.getElementById('cat-options-data');
            if (!btn || !dataEl) return;

            const options = JSON.parse(dataEl.textContent);
            let selectedIdx = 0;

            // Build the floating menu DOM
            const menu = document.createElement('div');
            menu.id = 'cat-dd-menu';
            menu.style.cssText = [
                'position:fixed',
                'z-index:99999',
                'background:white',
                'border:1.5px solid #e2e8f0',
                'border-radius:16px',
                'box-shadow:0 20px 40px rgba(0,0,0,.18),0 4px 12px rgba(0,0,0,.1)',
                'overflow:hidden',
                'display:none',
                'min-width:200px',
                'animation:ddSlideIn .18s ease-out'
            ].join(';');

            const inner = document.createElement('div');
            inner.style.cssText = 'padding:6px;';

            options.forEach(function(opt, idx) {
                const row = document.createElement('div');
                row.className = 'cat-dd-option custom-select-option';
                row.setAttribute('data-value', opt.value);
                row.setAttribute('data-color', opt.color);
                row.style.cssText = [
                    'display:flex', 'align-items:center', 'gap:10px',
                    'padding:9px 10px', 'border-radius:10px',
                    'cursor:pointer', 'transition:background .15s'
                ].join(';');

                // Icon bubble
                const bubble = document.createElement('span');
                bubble.style.cssText = [
                    'width:28px', 'height:28px', 'border-radius:8px',
                    'display:flex', 'align-items:center', 'justify-content:center',
                    'flex-shrink:0',
                    'background:' + opt.color + '22'
                ].join(';');
                const ico = document.createElement('span');
                ico.className = 'material-symbols-outlined';
                ico.style.cssText = 'font-size:16px;color:' + opt.color;
                ico.textContent = opt.icon;
                bubble.appendChild(ico);

                // Label
                const lbl = document.createElement('span');
                lbl.style.cssText = 'flex:1;font-size:14px;font-weight:500;color:#1e293b';
                lbl.textContent = opt.label;

                // Check mark
                const check = document.createElement('span');
                check.className = 'material-symbols-outlined check-icon';
                check.style.cssText = 'font-size:16px;color:#ec5b13;opacity:0;transition:opacity .15s';
                check.textContent = 'check';

                row.appendChild(bubble);
                row.appendChild(lbl);
                row.appendChild(check);

                row.addEventListener('mouseenter', () => row.style.background = 'rgba(236,91,19,.08)');
                row.addEventListener('mouseleave', () => row.style.background = idx === selectedIdx ? 'rgba(236,91,19,.1)' : '');

                row.addEventListener('click', function() {
                    selectedIdx = idx;
                    // Update hidden input
                    hiddenInput.value = opt.value;
                    hiddenInput.setAttribute('data-color', opt.color);
                    // Update button display
                    label.textContent = opt.label;
                    icon.textContent  = opt.icon;
                    icon.style.color  = opt.color;
                    // Mark selected rows
                    inner.querySelectorAll('.cat-dd-option').forEach((r, i) => {
                        r.style.background = i === idx ? 'rgba(236,91,19,.1)' : '';
                        r.querySelector('.check-icon').style.opacity = i === idx ? '1' : '0';
                    });
                    closeMenu();
                    if (typeof window.updatePreview === 'function') window.updatePreview();
                });

                inner.appendChild(row);
            });

            // Mark first as selected
            if (inner.children.length > 0) {
                inner.children[0].style.background = 'rgba(236,91,19,.1)';
                inner.children[0].querySelector('.check-icon').style.opacity = '1';
            }

            menu.appendChild(inner);
            document.body.appendChild(menu);

            function positionMenu() {
                const r = btn.getBoundingClientRect();
                menu.style.top   = (r.bottom + 6) + 'px';
                menu.style.left  = r.left + 'px';
                menu.style.width = r.width + 'px';
            }

            function openMenu() {
                positionMenu();
                menu.style.display = 'block';
                btn.style.borderColor = '#ec5b13';
                btn.style.boxShadow   = '0 0 0 3px rgba(236,91,19,.15)';
                chevron.style.transform = 'rotate(180deg)';
            }
            function closeMenu() {
                menu.style.display = 'none';
                btn.style.borderColor = '';
                btn.style.boxShadow   = '';
                chevron.style.transform = '';
            }

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                menu.style.display === 'none' ? openMenu() : closeMenu();
            });

            document.addEventListener('click', function(e) {
                if (!btn.contains(e.target) && !menu.contains(e.target)) closeMenu();
            });

            // Reposition on scroll/resize
            window.addEventListener('scroll', positionMenu, true);
            window.addEventListener('resize', function() { if (menu.style.display !== 'none') positionMenu(); });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCatDropdown);
        } else {
            initCatDropdown();
        }
    })();
    </script>
    <script>
    // Lazy-load favicons on card logo boxes
    // Uses naturalWidth check: Google Favicon API returns 16x16 globe placeholder
    // so we skip if the loaded image is too small (<=16px wide)
    (function loadCardFavicons() {
        function tryLoadFavicon(box) {
            var host = box.dataset.host;
            var storedLogo = box.dataset.logo;
            var src = storedLogo || (host ? 'https://t3.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=http://' + host + '&size=128' : '');
            if (!src) return;
            var img = new Image();
            img.onload = function() {
                // Skip globe placeholder (≤16×16) or blank image
                if (img.naturalWidth <= 16) return;
                var el = document.createElement('img');
                el.src = src;
                el.className = 'w-7 h-7 object-contain absolute z-10 rounded';
                el.style.cssText = 'top:50%;left:50%;transform:translate(-50%,-50%)';
                el.onerror = function() { el.remove(); };
                box.appendChild(el);
            };
            img.onerror = function() { /* leave initial letter */ };
            img.src = src;
        }
        function init() {
            document.querySelectorAll('.card-logo-box').forEach(tryLoadFavicon);
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
        else init();
    })();
    </script>
</body>
</html>
