<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - إبداع ميديا</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap');
        body { font-family: 'Tajawal', sans-serif; }
        
        .glow-box {
            box-shadow: 0 0 40px rgba(63, 158, 143, 0.4), inset 0 0 10px rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(63, 158, 143, 0.3);
        }
        
        .input-line {
            background: transparent;
            border: none;
            border-bottom: 2px solid #374151;
            color: white;
            transition: all 0.3s ease;
        }
        
        .input-line:focus {
            outline: none;
            border-bottom-color: #ef4444;
            box-shadow: none;
        }

        .input-group:focus-within label {
            color: #ef4444;
        }
        
        .input-group:focus-within i {
            color: #ef4444;
        }

        .animate-slide-in {
            animation: slideIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideIn {
            0% { transform: translateY(30px); opacity: 0; filter: blur(10px); }
            100% { transform: translateY(0); opacity: 1; filter: blur(0); }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-[#111116] min-h-screen flex items-center justify-center p-4">

    <!-- Main Container -->
    <div class="glow-box flex flex-col-reverse md:flex-row w-full max-w-4xl rounded-3xl overflow-hidden animate-slide-in bg-white dark:bg-[#1a1a24] relative z-10">
        
        <!-- Left Side (Form) -->
        <div class="w-full md:w-1/2 p-10 md:p-14 flex flex-col justify-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">تسجيل الدخول</h2>

            @if($errors->any())
            <div class="mb-6 bg-primary-500/10 border border-primary-500/50 text-primary-400 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form method="POST" action="/login" class="space-y-8">
                @csrf
                
                <!-- Email Input -->
                <div class="input-group relative">
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 transition-colors mb-2">البريد الإلكتروني</label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="input-line w-full pb-2 text-left" dir="ltr" placeholder="admin@ibda3.com">
                        <i data-lucide="user" class="absolute right-0 bottom-3 w-5 h-5 text-gray-500 transition-colors pointer-events-none"></i>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="input-group relative">
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 transition-colors mb-2">كلمة المرور</label>
                    <div class="relative">
                        <input type="password" name="password" required
                            class="input-line w-full pb-2 text-left" dir="ltr" placeholder="••••••••">
                        <i data-lucide="lock" class="absolute right-0 bottom-3 w-5 h-5 text-gray-500 transition-colors pointer-events-none"></i>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-gray-600 dark:text-gray-400 cursor-pointer hover:text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-primary-500 focus:ring-primary-500 focus:ring-offset-gray-900 ml-2">
                        <span>تذكرني</span>
                    </label>
                    <a href="#" class="text-gray-500 hover:text-primary-400 transition-colors">نسيت كلمة المرور؟</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 mt-4 bg-gradient-to-r from-secondary-600 to-primary-600 hover:from-secondary-500 hover:to-primary-500 text-gray-900 dark:text-white font-bold rounded-full shadow-[0_0_20px_rgba(63, 158, 143,0.4)] hover:shadow-[0_0_30px_rgba(63, 158, 143,0.6)] transform hover:-translate-y-0.5 transition-all duration-300 text-lg">
                    تسجيل الدخول
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
                ليس لديك حساب؟ <a href="javascript:void(0)" onclick="document.getElementById('contactModal').classList.remove('hidden')" class="text-primary-500 hover:text-primary-400 font-bold ml-1 transition-colors">تواصل مع الإدارة</a>
            </div>
        </div>

        <!-- Right Side (Welcome Message) -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-secondary-600 via-red-600 to-primary-800 p-10 md:p-14 flex flex-col justify-center text-center relative overflow-hidden">
            <!-- Decorative overlay -->
            <div class="absolute inset-0 bg-black/10 mix-blend-overlay"></div>
            
            <div class="relative z-10">
                <div class="bg-white rounded-2xl p-6 shadow-2xl mx-auto inline-block mb-6">
                    <img src="{{ asset('logo.png') }}" alt="إبداع ميديا" class="w-48 md:w-64 mx-auto object-contain">
                </div>
                
                <!-- Decorative Elements -->
                <div class="mt-12 flex justify-center space-x-reverse space-x-3">
                    <span class="w-3 h-3 bg-white rounded-full opacity-100"></span>
                    <span class="w-3 h-3 bg-white rounded-full opacity-50"></span>
                    <span class="w-3 h-3 bg-white rounded-full opacity-20"></span>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Global Background Elements -->
    <div class="fixed top-1/4 left-1/4 w-96 h-96 bg-primary-600/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="fixed bottom-1/4 right-1/4 w-96 h-96 bg-secondary-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <!-- Contact Admin Modal -->
    <div id="contactModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="document.getElementById('contactModal').classList.add('hidden')"></div>
        
        <!-- Modal Content -->
        <div class="bg-white dark:bg-[#1a1a24] rounded-3xl border border-primary-500/30 p-8 max-w-md w-full shadow-[0_0_40px_rgba(63, 158, 143,0.2)] text-center relative z-10 animate-slide-in">
            <!-- Close Button -->
            <button onclick="document.getElementById('contactModal').classList.add('hidden')" class="absolute top-4 left-4 text-gray-600 dark:text-gray-400 hover:text-primary-500 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
            
            <div class="w-20 h-20 bg-primary-500/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-primary-500/20">
                <i data-lucide="headphones" class="w-10 h-10 text-primary-500"></i>
            </div>
            
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">تواصل مع الإدارة</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-8 text-sm leading-relaxed font-medium">
                للحصول على حساب جديد في نظام إبداع ميديا، أو لحل أي مشكلة تقنية، يرجى التواصل معنا عبر القنوات التالية:
            </p>
            
            <div class="space-y-4 mb-8 text-left" dir="ltr">
                <a href="mailto:admin@ibda3.com" class="flex items-center justify-center gap-3 bg-gray-200 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-2xl p-4 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:text-white hover:border-primary-500 hover:bg-primary-500/10 transition-all group">
                    <i data-lucide="mail" class="w-6 h-6 text-primary-500 group-hover:scale-110 transition-transform"></i>
                    <span class="font-bold text-lg tracking-wide">admin@ibda3.com</span>
                </a>
                <a href="tel:+967776077155" class="flex items-center justify-center gap-3 bg-gray-200 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-2xl p-4 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:text-white hover:border-primary-500 hover:bg-primary-500/10 transition-all group">
                    <i data-lucide="phone" class="w-6 h-6 text-primary-500 group-hover:scale-110 transition-transform"></i>
                    <span class="font-bold text-lg tracking-wide">+967 776 077 155</span>
                </a>
            </div>
            
            <button onclick="document.getElementById('contactModal').classList.add('hidden')" class="w-full bg-gradient-to-r from-secondary-600 to-primary-600 text-gray-900 dark:text-white font-bold py-4 rounded-xl hover:shadow-[0_0_20px_rgba(63, 158, 143,0.5)] hover:-translate-y-0.5 transition-all">
                حسناً، فهمت
            </button>
        </div>
    </div>

    <script>
        // Initialize lucide icons
        lucide.createIcons();
        
        // Ensure new icons in modal get rendered if modal is shown
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class' && !mutation.target.classList.contains('hidden')) {
                    lucide.createIcons();
                }
            });
        });
        observer.observe(document.getElementById('contactModal'), { attributes: true });
    </script>
</body>
</html>
