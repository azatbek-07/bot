<?php
// Xabar yuborilganini tekshirish
$response = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    // .env ni o'qish
    $env = parse_ini_file(__DIR__ . '/.env');
    $token = $env['TELEGRAM_BOT_TOKEN'];

    // Textarea dan kelgan xabarni olish
    $message = trim($_POST['message']);

    // Tanlangan guruh/kanal/userlarni olish
    $selected_types = isset($_POST['send_to']) ? $_POST['send_to'] : ['users'];

    // users.json ni o'qish
    $users_data = json_decode(file_get_contents(__DIR__ . '/users.json'), true);

    $success_count = 0;
    $error_count = 0;
    $results = [];

    // Userlarga yuborish
    if (in_array('users', $selected_types) && isset($users_data['users'])) {
        foreach ($users_data['users'] as $user) {
            $res = sendTelegramMessage($token, $user['id'], $message);
            if ($res['ok']) {
                $success_count++;
            } else {
                $error_count++;
                $results[] = "User {$user['id']}: Xatolik";
            }
        }
    }

    // Guruhlarga yuborish
    if (in_array('groups', $selected_types) && isset($users_data['groups'])) {
        foreach ($users_data['groups'] as $group) {
            $res = sendTelegramMessage($token, $group['id'], $message);
            if ($res['ok']) {
                $success_count++;
            } else {
                $error_count++;
                $results[] = "Group {$group['id']}: Xatolik";
            }
        }
    }

    // Kanallarga yuborish
    if (in_array('channels', $selected_types) && isset($users_data['channels'])) {
        foreach ($users_data['channels'] as $channel) {
            $res = sendTelegramMessage($token, $channel['id'], $message);
            if ($res['ok']) {
                $success_count++;
            } else {
                $error_count++;
                $results[] = "Channel {$channel['id']}: Xatolik";
            }
        }
    }

    $response = [
        'success' => $success_count,
        'error' => $error_count,
        'details' => $results
    ];
}

function sendTelegramMessage($token, $chat_id, $message)
{
    $url = "https://api.telegram.org/bot{$token}/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

// Statistikani olish
$users_data = json_decode(file_get_contents(__DIR__ . '/users.json'), true);
$users_count = isset($users_data['users']) ? count($users_data['users']) : 0;
$groups_count = isset($users_data['groups']) ? count($users_data['groups']) : 0;
$channels_count = isset($users_data['channels']) ? count($users_data['channels']) : 0;
$total_count = $users_count + $groups_count + $channels_count;
?>

<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Broadcast - Xabar Yuborish Tizimi</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'spin-slow': 'spin 3s linear infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'gradient': 'gradient 3s ease infinite',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'scale-in': 'scaleIn 0.3s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        gradient: {
                            '0%, 100%': { backgroundPosition: '0% 50%' },
                            '50%': { backgroundPosition: '100% 50%' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        scaleIn: {
                            '0%': { opacity: '0', transform: 'scale(0.9)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        },
                    },
                    backgroundSize: {
                        '300%': '300%',
                    },
                }
            }
        }
    </script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #2563eb, #7c3aed);
        }
        
        /* Dark scrollbar */
        .dark-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        
        .dark-scrollbar::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.5);
            border-radius: 10px;
        }
        
        .dark-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
            border-radius: 10px;
        }
        
        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Card hover effect */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        /* Glow effect */
        .glow {
            box-shadow: 0 0 40px rgba(59, 130, 246, 0.3);
        }
        
        .glow-purple {
            box-shadow: 0 0 40px rgba(139, 92, 246, 0.3);
        }
        
        .glow-pink {
            box-shadow: 0 0 40px rgba(236, 72, 153, 0.3);
        }
        
        /* Glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <!-- Decorative background elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 2s"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-blue-300 to-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-float" style="animation-delay: 4s"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10">
        <!-- Header / Navbar -->
        <header class="bg-white/80 backdrop-blur-xl shadow-lg border-b border-gray-200/50 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo and Title -->
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                                <i class="fas fa-paper-plane text-white transform -rotate-45"></i>
                            </div>
                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                Telegram Broadcast
                            </h1>
                            <p class="text-xs text-gray-500 font-medium">Professional Messaging System</p>
                        </div>
                    </div>
                    
                    <!-- Status Indicators -->
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 rounded-full">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            <span class="text-sm font-medium text-green-700">Online</span>
                        </div>
                        <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-full">
                            <i class="fas fa-shield-halved text-blue-600 text-sm"></i>
                            <span class="text-sm font-medium text-blue-700">Secured</span>
                        </div>
                        <div class="flex items-center gap-2 px-4 py-2 bg-purple-50 border border-purple-200 rounded-full">
                            <i class="fas fa-robot text-purple-600 text-sm"></i>
                            <span class="text-sm font-medium text-purple-700">Bot Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    
                    <!-- Total Statistics Card -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 card-hover">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Jami Statistika</h3>
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-chart-bar text-white"></i>
                            </div>
                        </div>
                        
                        <div class="text-center mb-6">
                            <div class="text-7xl font-black gradient-text mb-2">
                                <?php echo $total_count; ?>
                            </div>
                            <p class="text-gray-500 font-medium">Jami Qabul Qiluvchilar</p>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600 font-medium">
                                        <i class="fas fa-user text-blue-500 mr-1"></i> Users
                                    </span>
                                    <span class="text-gray-800 font-bold"><?php echo $users_count; ?></span>
                                </div>
                                <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all duration-1000" 
                                         style="width: <?php echo $total_count > 0 ? ($users_count/$total_count)*100 : 0; ?>%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600 font-medium">
                                        <i class="fas fa-users text-purple-500 mr-1"></i> Groups
                                    </span>
                                    <span class="text-gray-800 font-bold"><?php echo $groups_count; ?></span>
                                </div>
                                <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-purple-400 to-purple-600 rounded-full transition-all duration-1000" 
                                         style="width: <?php echo $total_count > 0 ? ($groups_count/$total_count)*100 : 0; ?>%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600 font-medium">
                                        <i class="fas fa-bullhorn text-pink-500 mr-1"></i> Channels
                                    </span>
                                    <span class="text-gray-800 font-bold"><?php echo $channels_count; ?></span>
                                </div>
                                <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-pink-400 to-pink-600 rounded-full transition-all duration-1000" 
                                         style="width: <?php echo $total_count > 0 ? ($channels_count/$total_count)*100 : 0; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recipients List Card -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">
                                <i class="fas fa-address-book text-purple-500 mr-2"></i>
                                Qabul Qiluvchilar
                            </h3>
                            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full font-medium">
                                <?php echo $total_count; ?> ta
                            </span>
                        </div>

                        <!-- Tab Buttons -->
                        <div class="flex bg-gray-100 rounded-2xl p-1.5 mb-6">
                            <button onclick="showTab('users')" id="tab-users"
                                class="tab-button flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-300 
                                       bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/30">
                                <i class="fas fa-user mr-2"></i>Users
                                <span class="ml-1 bg-white/20 px-2 py-0.5 rounded-full text-xs"><?php echo $users_count; ?></span>
                            </button>
                            <button onclick="showTab('groups')" id="tab-groups"
                                class="tab-button flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-300 text-gray-600 hover:bg-gray-200">
                                <i class="fas fa-users mr-2"></i>Groups
                                <span class="ml-1 bg-gray-200 px-2 py-0.5 rounded-full text-xs"><?php echo $groups_count; ?></span>
                            </button>
                            <button onclick="showTab('channels')" id="tab-channels"
                                class="tab-button flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-300 text-gray-600 hover:bg-gray-200">
                                <i class="fas fa-bullhorn mr-2"></i>Channels
                                <span class="ml-1 bg-gray-200 px-2 py-0.5 rounded-full text-xs"><?php echo $channels_count; ?></span>
                            </button>
                        </div>

                        <!-- Tab Content -->
                        <div class="custom-scrollbar max-h-[500px] overflow-y-auto pr-2">
                            <!-- Users Tab -->
                            <div id="content-users" class="tab-content space-y-3 animate-fade-in">
                                <?php if (isset($users_data['users']) && !empty($users_data['users'])): ?>
                                    <?php foreach ($users_data['users'] as $index => $user): ?>
                                        <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-2xl hover:shadow-md transition-all duration-300 border border-gray-100 hover:border-blue-200 group">
                                            <div class="relative">
                                                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                                                    <span class="text-white font-bold text-lg">
                                                        <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                                                    </span>
                                                </div>
                                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white"></div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-gray-800 truncate group-hover:text-blue-600 transition-colors">
                                                    <?php echo htmlspecialchars($user['name'] ?? 'User ' . ($index + 1)); ?>
                                                </p>
                                                <p class="text-xs text-gray-500">ID: <?php echo $user['id']; ?></p>
                                            </div>
                                            <span class="flex-shrink-0 text-xs px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full font-semibold border border-blue-200">
                                                <i class="fas fa-user mr-1"></i>User
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-12">
                                        <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-user-slash text-4xl text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Foydalanuvchilar mavjud emas</p>
                                        <p class="text-gray-400 text-sm mt-1">Botga qo'shilgan foydalanuvchilar bu yerda ko'rinadi</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Groups Tab -->
                            <div id="content-groups" class="tab-content space-y-3 hidden">
                                <?php if (isset($users_data['groups']) && !empty($users_data['groups'])): ?>
                                    <?php foreach ($users_data['groups'] as $index => $group): ?>
                                        <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-gray-50 to-purple-50 rounded-2xl hover:shadow-md transition-all duration-300 border border-gray-100 hover:border-purple-200 group">
                                            <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                                <i class="fas fa-users text-white text-lg"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-gray-800 truncate group-hover:text-purple-600 transition-colors">
                                                    <?php echo htmlspecialchars($group['name'] ?? 'Group ' . ($index + 1)); ?>
                                                </p>
                                                <p class="text-xs text-gray-500">ID: <?php echo $group['id']; ?></p>
                                            </div>
                                            <span class="flex-shrink-0 text-xs px-3 py-1.5 bg-purple-100 text-purple-700 rounded-full font-semibold border border-purple-200">
                                                <i class="fas fa-users mr-1"></i>Group
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-12">
                                        <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-users-slash text-4xl text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Guruhlar mavjud emas</p>
                                        <p class="text-gray-400 text-sm mt-1">Bot qo'shilgan guruhlar bu yerda ko'rinadi</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Channels Tab -->
                            <div id="content-channels" class="tab-content space-y-3 hidden">
                                <?php if (isset($users_data['channels']) && !empty($users_data['channels'])): ?>
                                    <?php foreach ($users_data['channels'] as $index => $channel): ?>
                                        <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-gray-50 to-pink-50 rounded-2xl hover:shadow-md transition-all duration-300 border border-gray-100 hover:border-pink-200 group">
                                            <div class="w-12 h-12 bg-gradient-to-br from-pink-400 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                                                <i class="fas fa-bullhorn text-white text-lg"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-gray-800 truncate group-hover:text-pink-600 transition-colors">
                                                    <?php echo htmlspecialchars($channel['name'] ?? 'Channel ' . ($index + 1)); ?>
                                                </p>
                                                <p class="text-xs text-gray-500">ID: <?php echo $channel['id']; ?></p>
                                            </div>
                                            <span class="flex-shrink-0 text-xs px-3 py-1.5 bg-pink-100 text-pink-700 rounded-full font-semibold border border-pink-200">
                                                <i class="fas fa-bullhorn mr-1"></i>Channel
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-12">
                                        <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-bullhorn text-4xl text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Kanallar mavjud emas</p>
                                        <p class="text-gray-400 text-sm mt-1">Bot admin bo'lgan kanallar bu yerda ko'rinadi</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Quick Stats Cards -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 card-hover cursor-pointer"
                             onclick="document.getElementById('tab-users').click()">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                                    <i class="fas fa-user text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-800"><?php echo $users_count; ?></p>
                                    <p class="text-sm text-gray-500 font-medium">Foydalanuvchilar</p>
                                </div>
                            </div>
                            <div class="mt-3 w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all duration-1000" 
                                     style="width: <?php echo $total_count > 0 ? ($users_count/$total_count)*100 : 0; ?>%"></div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 card-hover cursor-pointer"
                             onclick="document.getElementById('tab-groups').click()">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                                    <i class="fas fa-users text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-800"><?php echo $groups_count; ?></p>
                                    <p class="text-sm text-gray-500 font-medium">Guruhlar</p>
                                </div>
                            </div>
                            <div class="mt-3 w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-purple-400 to-purple-600 rounded-full transition-all duration-1000" 
                                     style="width: <?php echo $total_count > 0 ? ($groups_count/$total_count)*100 : 0; ?>%"></div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 card-hover cursor-pointer"
                             onclick="document.getElementById('tab-channels').click()">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-pink-400 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg shadow-pink-500/30">
                                    <i class="fas fa-bullhorn text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-800"><?php echo $channels_count; ?></p>
                                    <p class="text-sm text-gray-500 font-medium">Kanallar</p>
                                </div>
                            </div>
                            <div class="mt-3 w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-pink-400 to-pink-600 rounded-full transition-all duration-1000" 
                                     style="width: <?php echo $total_count > 0 ? ($channels_count/$total_count)*100 : 0; ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Form Card -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 card-hover">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="relative">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-xl shadow-purple-500/30">
                                    <i class="fas fa-paper-plane text-white text-2xl transform -rotate-45"></i>
                                </div>
                                <span class="absolute -top-2 -right-2 flex h-6 w-6">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-6 w-6 bg-green-500 border-2 border-white items-center justify-center">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </span>
                                </span>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800">Xabar Yuborish</h2>
                                <p class="text-gray-500 mt-1">Barcha tanlangan qabul qiluvchilarga xabar yuboring</p>
                            </div>
                        </div>

                        <form method="POST" action="" id="messageForm" class="space-y-6">
                            <!-- Send To Options -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-4">
                                    <i class="fas fa-crosshairs text-purple-500 mr-2"></i>
                                    Yuborish Manzili
                                </label>
                                <div class="grid grid-cols-3 gap-4">
                                    <!-- Users Checkbox -->
                                    <label class="relative flex flex-col items-center gap-2 p-4 bg-gray-50 rounded-2xl cursor-pointer 
                                                  hover:bg-blue-50 transition-all duration-300 border-2 border-gray-200 
                                                  has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:shadow-lg has-[:checked]:shadow-blue-500/20 group">
                                        <input type="checkbox" name="send_to[]" value="users" checked class="hidden">
                                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-has-[:checked]:bg-blue-500 transition-colors">
                                            <i class="fas fa-user text-blue-500 text-xl group-has-[:checked]:text-white transition-colors"></i>
                                        </div>
                                        <span class="font-bold text-gray-700 group-has-[:checked]:text-blue-600">Users</span>
                                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-semibold group-has-[:checked]:bg-blue-500 group-has-[:checked]:text-white transition-colors">
                                            <?php echo $users_count; ?> ta
                                        </span>
                                        <div class="absolute top-2 right-2 opacity-0 group-has-[:checked]:opacity-100 transition-opacity">
                                            <i class="fas fa-check-circle text-blue-500 text-lg"></i>
                                        </div>
                                    </label>

                                    <!-- Groups Checkbox -->
                                    <label class="relative flex flex-col items-center gap-2 p-4 bg-gray-50 rounded-2xl cursor-pointer 
                                                  hover:bg-purple-50 transition-all duration-300 border-2 border-gray-200 
                                                  has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 has-[:checked]:shadow-lg has-[:checked]:shadow-purple-500/20 group">
                                        <input type="checkbox" name="send_to[]" value="groups" class="hidden">
                                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-has-[:checked]:bg-purple-500 transition-colors">
                                            <i class="fas fa-users text-purple-500 text-xl group-has-[:checked]:text-white transition-colors"></i>
                                        </div>
                                        <span class="font-bold text-gray-700 group-has-[:checked]:text-purple-600">Groups</span>
                                        <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full font-semibold group-has-[:checked]:bg-purple-500 group-has-[:checked]:text-white transition-colors">
                                            <?php echo $groups_count; ?> ta
                                        </span>
                                        <div class="absolute top-2 right-2 opacity-0 group-has-[:checked]:opacity-100 transition-opacity">
                                            <i class="fas fa-check-circle text-purple-500 text-lg"></i>
                                        </div>
                                    </label>

                                    <!-- Channels Checkbox -->
                                    <label class="relative flex flex-col items-center gap-2 p-4 bg-gray-50 rounded-2xl cursor-pointer 
                                                  hover:bg-pink-50 transition-all duration-300 border-2 border-gray-200 
                                                  has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50 has-[:checked]:shadow-lg has-[:checked]:shadow-pink-500/20 group">
                                        <input type="checkbox" name="send_to[]" value="channels" class="hidden">
                                        <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center group-has-[:checked]:bg-pink-500 transition-colors">
                                            <i class="fas fa-bullhorn text-pink-500 text-xl group-has-[:checked]:text-white transition-colors"></i>
                                        </div>
                                        <span class="font-bold text-gray-700 group-has-[:checked]:text-pink-600">Channels</span>
                                        <span class="text-xs bg-pink-100 text-pink-600 px-2 py-1 rounded-full font-semibold group-has-[:checked]:bg-pink-500 group-has-[:checked]:text-white transition-colors">
                                            <?php echo $channels_count; ?> ta
                                        </span>
                                        <div class="absolute top-2 right-2 opacity-0 group-has-[:checked]:opacity-100 transition-opacity">
                                            <i class="fas fa-check-circle text-pink-500 text-lg"></i>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Message Input -->
                            <div>
                                <label for="message" class="block text-sm font-bold text-gray-700 mb-4">
                                    <i class="fas fa-message text-purple-500 mr-2"></i>
                                    Xabar Matni
                                </label>
                                <div class="relative group">
                                    <textarea
                                        id="message"
                                        name="message"
                                        placeholder="Xabaringizni shu yerga yozing...&#10;&#10;Masalan:&#10;Assalomu alaykum! Botimizga xush kelibsiz!"
                                        required
                                        rows="8"
                                        class="w-full p-6 bg-gray-50 border-2 border-gray-200 rounded-2xl text-gray-700 
                                               placeholder-gray-400 focus:outline-none focus:border-purple-500 
                                               focus:ring-4 focus:ring-purple-500/20 focus:bg-white
                                               resize-y transition-all duration-300 text-base font-medium"></textarea>
                                    
                                    <!-- Formatting Toolbar -->
                                    <div class="absolute bottom-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <button type="button" onclick="insertTag('b')" 
                                                class="w-10 h-10 bg-white hover:bg-blue-50 rounded-xl flex items-center justify-center 
                                                       transition-all duration-300 shadow-md hover:shadow-lg text-gray-600 hover:text-blue-600 border border-gray-200"
                                                title="Qalin (Bold)">
                                            <i class="fas fa-bold"></i>
                                        </button>
                                        <button type="button" onclick="insertTag('i')" 
                                                class="w-10 h-10 bg-white hover:bg-purple-50 rounded-xl flex items-center justify-center 
                                                       transition-all duration-300 shadow-md hover:shadow-lg text-gray-600 hover:text-purple-600 border border-gray-200"
                                                title="Kursiv (Italic)">
                                            <i class="fas fa-italic"></i>
                                        </button>
                                        <button type="button" onclick="insertTag('code')" 
                                                class="w-10 h-10 bg-white hover:bg-pink-50 rounded-xl flex items-center justify-center 
                                                       transition-all duration-300 shadow-md hover:shadow-lg text-gray-600 hover:text-pink-600 border border-gray-200"
                                                title="Kod (Code)">
                                            <i class="fas fa-code"></i>
                                        </button>
                                        <button type="button" onclick="insertTag('pre')" 
                                                class="w-10 h-10 bg-white hover:bg-green-50 rounded-xl flex items-center justify-center 
                                                       transition-all duration-300 shadow-md hover:shadow-lg text-gray-600 hover:text-green-600 border border-gray-200"
                                                title="Preformatted">
                                            <i class="fas fa-terminal"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                                    <i class="fas fa-info-circle text-blue-500"></i>
                                    <span>HTML teglar qo'llab-quvvatlanadi:</span>
                                    <code class="px-2 py-0.5 bg-gray-100 rounded text-blue-600 font-mono">&lt;b&gt;</code>
                                    <code class="px-2 py-0.5 bg-gray-100 rounded text-purple-600 font-mono">&lt;i&gt;</code>
                                    <code class="px-2 py-0.5 bg-gray-100 rounded text-pink-600 font-mono">&lt;code&gt;</code>
                                    <code class="px-2 py-0.5 bg-gray-100 rounded text-green-600 font-mono">&lt;pre&gt;</code>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                class="w-full relative group overflow-hidden bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 
                                       text-white font-bold py-5 px-8 rounded-2xl transition-all duration-300 
                                       transform hover:scale-[1.02] active:scale-[0.98] 
                                       flex items-center justify-center gap-3 shadow-xl hover:shadow-2xl 
                                       shadow-purple-500/30 hover:shadow-purple-500/50 text-lg bg-300% animate-gradient">
                                <i class="fas fa-paper-plane group-hover:translate-x-2 transition-transform duration-300"></i>
                                <span>Xabarni Yuborish</span>
                                <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform duration-300"></i>
                            </button>
                            
                            <p class="text-center text-xs text-gray-400">
                                <i class="fas fa-keyboard mr-1"></i>
                                Tez klaviatura: <kbd class="px-2 py-0.5 bg-gray-100 rounded font-mono text-gray-600">Ctrl + Enter</kbd>
                            </p>
                        </form>

                        <!-- Response Messages -->
                        <?php if ($response): ?>
                            <div class="mt-8 animate-slide-up">
                                <!-- Success Message -->
                                <?php if ($response['success'] > 0 && $response['error'] == 0): ?>
                                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/30">
                                                <i class="fas fa-check-circle text-white text-2xl"></i>
                                            </div>
                                            <div>
                                                <p class="text-xl font-bold text-green-800">Muvaffaqiyatli!</p>
                                                <p class="text-green-600 font-medium">
                                                    <?php echo $response['success']; ?> ta xabar muvaffaqiyatli yuborildi
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex items-center gap-2 text-sm text-green-600">
                                            <i class="fas fa-clock"></i>
                                            <span>Yuborilgan vaqt: <?php echo date('H:i:s'); ?></span>
                                        </div>
                                    </div>
                                
                                <!-- Partial Success -->
                                <?php elseif ($response['success'] > 0 && $response['error'] > 0): ?>
                                    <div class="bg-gradient-to-br from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-2xl p-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-yellow-500/30">
                                                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                                            </div>
                                            <div>
                                                <p class="text-xl font-bold text-yellow-800">Qisman Yuborildi</p>
                                                <p class="text-yellow-600 font-medium">
                                                    <?php echo $response['success']; ?> ta yuborildi, <?php echo $response['error']; ?> ta xatolik
                                                </p>
                                            </div>
                                        </div>
                                        <?php if (!empty($response['details'])): ?>
                                            <div class="mt-4 space-y-2">
                                                <?php foreach ($response['details'] as $detail): ?>
                                                    <div class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                                                        <i class="fas fa-times-circle text-red-500"></i>
                                                        <span class="text-sm text-red-700 font-medium"><?php echo htmlspecialchars($detail); ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                
                                <!-- Error -->
                                <?php else: ?>
                                    <div class="bg-gradient-to-br from-red-50 to-pink-50 border-2 border-red-200 rounded-2xl p-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 bg-gradient-to-br from-red-400 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg shadow-red-500/30">
                                                <i class="fas fa-times-circle text-white text-2xl"></i>
                                            </div>
                                            <div>
                                                <p class="text-xl font-bold text-red-800">Xatolik!</p>
                                                <p class="text-red-600 font-medium">Xabarlar yuborilmadi. Iltimos qaytadan urinib ko'ring.</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white/80 backdrop-blur-xl border-t border-gray-200/50 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <i class="fas fa-code text-purple-500"></i>
                        <span>Powered by</span>
                        <span class="font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Telegram Bot API</span>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-shield-halved text-green-500"></i>
                            Secure Connection
                        </span>
                        <span class="hidden sm:inline">•</span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-bolt text-yellow-500"></i>
                            Real-time Updates
                        </span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Tab switching function
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Reset all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('bg-gradient-to-r', 'from-blue-500', 'to-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-500/30');
                button.classList.add('text-gray-600', 'hover:bg-gray-200');
            });

            // Show selected content
            const contentElement = document.getElementById('content-' + tabName);
            if (contentElement) {
                contentElement.classList.remove('hidden');
                contentElement.classList.add('animate-fade-in');
            }

            // Activate selected button
            const buttonElement = document.getElementById('tab-' + tabName);
            if (buttonElement) {
                buttonElement.classList.add('bg-gradient-to-r', 'from-blue-500', 'to-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-500/30');
                buttonElement.classList.remove('text-gray-600', 'hover:bg-gray-200');
            }
        }

        // Insert HTML tags into textarea
        function insertTag(tag) {
            const textarea = document.getElementById('message');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;

            const before = text.substring(0, start);
            const selection = text.substring(start, end);
            const after = text.substring(end);

            let replacement;
            if (selection) {
                replacement = '<' + tag + '>' + selection + '</' + tag + '>';
            } else {
                replacement = '<' + tag + '></' + tag + '>';
            }

            textarea.value = before + replacement + after;
            textarea.focus();

            // Set cursor position
            const newPosition = start + replacement.length;
            if (!selection) {
                textarea.setSelectionRange(newPosition - ('</' + tag + '>').length - 1, newPosition - ('</' + tag + '>').length - 1);
            } else {
                textarea.setSelectionRange(newPosition, newPosition);
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + Enter to submit
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('messageForm').submit();
            }
            
            // Ctrl + B for bold
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                insertTag('b');
            }
            
            // Ctrl + I for italic
            if ((e.ctrlKey || e.metaKey) && e.key === 'i') {
                e.preventDefault();
                insertTag('i');
            }
        });

        // Auto-resize textarea
        const textarea = document.getElementById('message');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 400) + 'px';
            });
            
            // Character counter
            const updateCounter = function() {
                const length = this.value.length;
                // You can add a character counter display if needed
            };
            
            textarea.addEventListener('input', updateCounter);
        }

        // Add animation to response messages
        document.addEventListener('DOMContentLoaded', function() {
            const responseMessages = document.querySelectorAll('.animate-slide-up');
            responseMessages.forEach((element, index) => {
                element.style.animationDelay = (index * 0.1) + 's';
            });
        });
    </script>
</body>
</html>