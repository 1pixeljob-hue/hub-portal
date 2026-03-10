<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hardcoded credentials
    if ($username === 'quydev' && $password === 'Spencil@123') {
        $_SESSION['logged_in'] = true;
        header("Location: index.php");
        exit;
    }
    else {
        $error = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hub Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#ec5b13',
                        background: { light: '#f8fafc', dark: '#0f172a' }
                    },
                    fontFamily: { display: ['"Plus Jakarta Sans"', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="relative w-full max-w-md">
        <!-- Floating shapes for design -->
        <div class="absolute -top-10 -left-10 w-32 h-32 bg-primary/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-blue-500/20 rounded-full blur-3xl"></div>

        <div class="glass-card rounded-3xl p-8 relative z-10 w-full">
            <div class="flex items-center justify-center gap-3 text-primary mb-8">
                <div class="size-8">
                    <svg fill="currentColor" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V4Z" fill-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 class="text-slate-900 dark:text-white text-2xl font-bold tracking-tight">Quản lý liên kết</h2>
            </div>

            <h3 class="text-xl font-semibold mb-6 text-center">Đăng nhập Quản trị</h3>

            <?php if ($error): ?>
                <div class="bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-3 rounded-xl text-sm mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php
endif; ?>

            <form method="POST" action="login.php" class="space-y-5">
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Tên đăng nhập</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-slate-400 text-[20px]">person</span>
                        </div>
                        <input type="text" name="username" id="username" required 
                            class="block w-full pl-10 pr-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none h-[42px]" 
                            placeholder="Nhập tên đăng nhập">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Mật khẩu</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-slate-400 text-[20px]">lock</span>
                        </div>
                        <input type="password" name="password" id="password" required 
                            class="block w-full pl-10 pr-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none h-[42px]" 
                            placeholder="Nhập mật khẩu">
                    </div>
                </div>

                <button type="submit" class="w-full h-11 bg-primary hover:bg-orange-600 text-white font-medium rounded-xl transition-all hover:shadow-lg hover:shadow-primary/30 flex items-center justify-center gap-2 mt-2">
                    <span>Đăng nhập</span>
                    <span class="material-symbols-outlined text-[20px]">login</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Script to sync theme if needed, but defaults to light for now -->
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        }
    </script>
</body>
</html>
