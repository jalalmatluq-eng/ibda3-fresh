<?php

namespace Database\Seeders;

use App\Models\KbCategory;
use App\Models\KnowledgeArticle;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== مستخدمون تجريبيون =====
        $admin = User::create([
            'name'       => ' جلال الصوفي',
            'email'      => 'admin@ibda3.com',
            'password'   => Hash::make('Admin@1234'),
            'phone'      => '967776077155',
            'role'       => 'admin',
            'department' => 'الإدارة',
            'is_active'  => true,
        ]);

        $support1 = User::create([
            'name'           => 'سارة الدعم الفني',
            'email'          => 'support1@ibda3.com',
            'password'       => Hash::make('Support@1234'),
            'phone'          => '0507654321',
            'role'           => 'support',
            'specialization' => 'technical',
            'is_active'      => true,
        ]);

        $support2 = User::create([
            'name'           => 'محمد الدعم المحاسبي',
            'email'          => 'support2@ibda3.com',
            'password'       => Hash::make('Support@1234'),
            'phone'          => '0509876543',
            'role'           => 'support',
            'specialization' => 'accounting',
            'is_active'      => true,
        ]);

        $manager = User::create([
            'name'       => 'فاطمة المديرة المالية',
            'email'      => 'manager@ibda3.com',
            'password'   => Hash::make('Manager@1234'),
            'phone'      => '0506543210',
            'role'       => 'financial_manager',
            'department' => 'المالية',
            'is_active'  => true,
        ]);

        $accountants = [];
        $accountantData = [
            ['علي المحاسب',    'ali@ibda3.com',    '0502345678'],
            ['نورة المحاسبة',  'noura@ibda3.com',  '0503456789'],
            ['خالد المحاسب',   'khalid@ibda3.com', '0504567890'],
        ];

        foreach ($accountantData as [$name, $email, $phone]) {
            $accountants[] = User::create([
                'name'       => $name,
                'email'      => $email,
                'password'   => Hash::make('User@1234'),
                'phone'      => $phone,
                'role'       => 'accountant',
                'department' => 'المالية',
                'is_active'  => true,
            ]);
        }

        // ===== تصنيفات قاعدة المعرفة =====
        $categories = [
            ['name' => 'كيف تبدأ',            'slug' => 'getting-started', 'icon' => '🚀', 'description' => 'دروس تمهيدية وإعداد الحساب'],
            ['name' => 'مشاكل شائعة',         'slug' => 'common-issues',   'icon' => '⚠️', 'description' => 'حلول للمشاكل المتكررة'],
            ['name' => 'التقارير',            'slug' => 'reports',         'icon' => '📊', 'description' => 'كيفية إنشاء وتفسير التقارير'],
            ['name' => 'الإعدادات',           'slug' => 'settings',        'icon' => '⚙️', 'description' => 'إعداد النظام وتخصيص الخيارات'],
            ['name' => 'العمليات المحاسبية',  'slug' => 'accounting',      'icon' => '💰', 'description' => 'القيود والفواتير والمصروفات'],
            ['name' => 'أسئلة شائعة',         'slug' => 'faq',             'icon' => '❓', 'description' => 'إجابات سريعة للأسئلة المتكررة'],
        ];

        $cats = [];
        foreach ($categories as $cat) {
            $cats[$cat['slug']] = KbCategory::create($cat);
        }

        // ===== مقالات قاعدة المعرفة =====
        $articles = [
            [
                'title'       => 'كيفية تسجيل الدخول للنظام',
                'content'     => "## تسجيل الدخول\n\n1. افتح المتصفح وتوجه إلى رابط النظام\n2. أدخل البريد الإلكتروني وكلمة المرور\n3. اضغط على زر **دخول**\n\n> تأكد من أن كلمة المرور تحتوي على حروف كبيرة وصغيرة وأرقام",
                'category_id' => $cats['getting-started']->id,
                'keywords'    => 'تسجيل دخول,حساب,مرور',
                'views'       => 150,
            ],
            [
                'title'       => 'حل مشكلة نسيان كلمة المرور',
                'content'     => "## استعادة كلمة المرور\n\nإذا نسيت كلمة المرور:\n\n1. تواصل مع المشرف\n2. سيقوم بإعادة تعيين كلمة المرور\n3. استخدم كلمة المرور الجديدة وقم بتغييرها فوراً",
                'category_id' => $cats['common-issues']->id,
                'keywords'    => 'كلمة مرور,نسيت,استعادة',
                'views'       => 89,
            ],
            [
                'title'       => 'كيفية إنشاء تقرير الأرباح والخسائر',
                'content'     => "## تقرير الأرباح والخسائر\n\n1. اذهب إلى قسم **التقارير**\n2. اختر **تقرير الأرباح والخسائر**\n3. حدد الفترة الزمنية\n4. اضغط **توليد التقرير**\n\nيمكنك تصدير التقرير بصيغة PDF أو Excel",
                'category_id' => $cats['reports']->id,
                'keywords'    => 'تقرير,أرباح,خسائر,مالي',
                'views'       => 210,
            ],
        ];

        foreach ($articles as $article) {
            KnowledgeArticle::create([...$article, 'created_by' => $support1->id]);
        }

        // ===== تذاكر تجريبية =====
        $ticketsData = [
            ['title' => 'مشكلة في تسجيل الدخول', 'description' => 'لا أستطيع تسجيل الدخول للنظام منذ الصباح، تظهر رسالة خطأ "بيانات غير صحيحة" رغم أن البيانات صحيحة', 'type' => 'technical', 'priority' => 'urgent', 'status' => 'open'],
            ['title' => 'استفسار عن قيد يومية', 'description' => 'كيف يمكنني إضافة قيد يومية لمصاريف الإيجار؟ لم أجد الخيار في القائمة الرئيسية', 'type' => 'accounting', 'priority' => 'normal', 'status' => 'in_progress'],
            ['title' => 'طلب إضافة تقرير مخصص', 'description' => 'نحتاج تقريراً يجمع بين المبيعات والمشتريات خلال فترة زمنية محددة مع إمكانية التصدير لـ Excel', 'type' => 'development', 'priority' => 'high', 'status' => 'waiting_customer'],
            ['title' => 'بطء شديد في النظام', 'description' => 'النظام بطيء جداً خلال ساعات الذروة، يأخذ أكثر من 30 ثانية لفتح أي صفحة', 'type' => 'technical', 'priority' => 'high', 'status' => 'resolved'],
            ['title' => 'خطأ في حساب الضريبة', 'description' => 'النظام يحسب الضريبة بشكل خاطئ على الفواتير، يظهر 14% بدلاً من 15%', 'type' => 'accounting', 'priority' => 'urgent', 'status' => 'closed'],
        ];

        foreach ($ticketsData as $i => $td) {
            $user = $accountants[$i % count($accountants)];
            $ticket = Ticket::create([
                ...$td,
                'user_id'     => $user->id,
                'assigned_to' => $td['status'] !== 'open' ? $support1->id : null,
            ]);

            if (in_array($td['status'], ['in_progress', 'waiting_customer', 'resolved', 'closed'])) {
                TicketReply::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $support1->id,
                    'body'      => 'تم استلام التذكرة وسيتم العمل عليها في أقرب وقت. هل يمكنك تزويدنا بمزيد من التفاصيل؟',
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->table(
            ['الدور', 'البريد', 'كلمة المرور'],
            [
                ['مشرف',           'admin@ibda3.com',    'Admin@1234'],
                ['دعم فني',        'support1@ibda3.com', 'Support@1234'],
                ['دعم محاسبي',     'support2@ibda3.com', 'Support@1234'],
                ['مدير مالي',      'manager@ibda3.com',  'Manager@1234'],
                ['محاسب',          'ali@ibda3.com',      'User@1234'],
            ]
        );
    }
}
