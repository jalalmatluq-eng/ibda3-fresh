-- ============================================================
-- قاعدة بيانات نظام دعم عملاء ابداع ميديا
-- ============================================================

CREATE DATABASE IF NOT EXISTS ibda3_support
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ibda3_support;

-- =====================
-- جدول المستخدمين
-- =====================
CREATE TABLE IF NOT EXISTS users (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name            VARCHAR(100)  NOT NULL,
  email           VARCHAR(191)  NOT NULL UNIQUE,
  password        VARCHAR(255)  NOT NULL,
  phone           VARCHAR(20)   NULL,
  role            ENUM('accountant','financial_manager','support','admin') NOT NULL DEFAULT 'accountant',
  department      VARCHAR(100)  NULL,
  specialization  VARCHAR(100)  NULL COMMENT 'technical / accounting',
  is_active       TINYINT(1)    NOT NULL DEFAULT 1,
  remember_token  VARCHAR(100)  NULL,
  created_at      TIMESTAMP     NULL DEFAULT NULL,
  updated_at      TIMESTAMP     NULL DEFAULT NULL,
  INDEX idx_role   (role),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- تصنيفات قاعدة المعرفة
-- =====================
CREATE TABLE IF NOT EXISTS kb_categories (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL,
  slug        VARCHAR(100) NOT NULL UNIQUE,
  description VARCHAR(255) NULL,
  icon        VARCHAR(10)  NULL,
  created_at  TIMESTAMP    NULL DEFAULT NULL,
  updated_at  TIMESTAMP    NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- مقالات قاعدة المعرفة
-- =====================
CREATE TABLE IF NOT EXISTS knowledge_articles (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(255)   NOT NULL,
  content     LONGTEXT       NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,
  created_by  BIGINT UNSIGNED NOT NULL,
  keywords    VARCHAR(255)   NULL,
  views       INT UNSIGNED   NOT NULL DEFAULT 0,
  helpful     INT UNSIGNED   NOT NULL DEFAULT 0,
  not_helpful INT UNSIGNED   NOT NULL DEFAULT 0,
  created_at  TIMESTAMP      NULL DEFAULT NULL,
  updated_at  TIMESTAMP      NULL DEFAULT NULL,
  FULLTEXT ft_search (title, content, keywords),
  FOREIGN KEY (category_id) REFERENCES kb_categories(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by)  REFERENCES users(id)         ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- التذاكر
-- =====================
CREATE TABLE IF NOT EXISTS tickets (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200)   NOT NULL,
  description TEXT           NOT NULL,
  type        ENUM('technical','accounting','development') NOT NULL DEFAULT 'technical',
  priority    ENUM('normal','high','urgent')               NOT NULL DEFAULT 'normal',
  status      ENUM('open','in_progress','waiting_customer','resolved','closed') NOT NULL DEFAULT 'open',
  user_id     BIGINT UNSIGNED NOT NULL,
  assigned_to BIGINT UNSIGNED NULL,
  attachment  VARCHAR(255)   NULL,
  created_at  TIMESTAMP      NULL DEFAULT NULL,
  updated_at  TIMESTAMP      NULL DEFAULT NULL,
  INDEX idx_status   (status),
  INDEX idx_priority (priority),
  INDEX idx_user     (user_id),
  INDEX idx_assigned (assigned_to),
  FOREIGN KEY (user_id)     REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- ردود التذاكر
-- =====================
CREATE TABLE IF NOT EXISTS ticket_replies (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ticket_id   BIGINT UNSIGNED NOT NULL,
  user_id     BIGINT UNSIGNED NOT NULL,
  body        TEXT            NOT NULL,
  attachment  VARCHAR(255)    NULL,
  created_at  TIMESTAMP       NULL DEFAULT NULL,
  updated_at  TIMESTAMP       NULL DEFAULT NULL,
  INDEX idx_ticket (ticket_id),
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sanctum tokens table
CREATE TABLE IF NOT EXISTS personal_access_tokens (
  id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tokenable_type VARCHAR(255)   NOT NULL,
  tokenable_id   BIGINT UNSIGNED NOT NULL,
  name           VARCHAR(255)   NOT NULL,
  token          VARCHAR(64)    NOT NULL UNIQUE,
  abilities      TEXT           NULL,
  last_used_at   TIMESTAMP      NULL DEFAULT NULL,
  expires_at     TIMESTAMP      NULL DEFAULT NULL,
  created_at     TIMESTAMP      NULL DEFAULT NULL,
  updated_at     TIMESTAMP      NULL DEFAULT NULL,
  INDEX idx_tokenable (tokenable_type, tokenable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- البيانات التجريبية
-- ============================================================

-- كلمات المرور مشفرة بـ bcrypt:
-- Admin@1234   → $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- Support@1234 → $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- User@1234    → $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- (جميعها password للاختبار السريع — استبدلها بـ bcrypt حقيقي في الإنتاج)

INSERT INTO users (name, email, password, phone, role, department, specialization, is_active, created_at, updated_at) VALUES
('أحمد المشرف',        'admin@ibda3.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0501234567', 'admin',            'الإدارة', NULL,        1, NOW(), NOW()),
('سارة الدعم الفني',   'support1@ibda3.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0507654321', 'support',          NULL,       'technical', 1, NOW(), NOW()),
('محمد الدعم المحاسبي','support2@ibda3.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0509876543', 'support',          NULL,       'accounting',1, NOW(), NOW()),
('فاطمة المديرة',      'manager@ibda3.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0506543210', 'financial_manager','المالية',  NULL,        1, NOW(), NOW()),
('علي المحاسب',        'ali@ibda3.com',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0502345678', 'accountant',       'المالية',  NULL,        1, NOW(), NOW()),
('نورة المحاسبة',      'noura@ibda3.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0503456789', 'accountant',       'المالية',  NULL,        1, NOW(), NOW()),
('خالد المحاسب',       'khalid@ibda3.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0504567890', 'accountant',       'المالية',  NULL,        1, NOW(), NOW()),
('ريم محاسبة',         'reem@ibda3.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0505432198', 'accountant',       'المشتريات',NULL,        1, NOW(), NOW());

INSERT INTO kb_categories (name, slug, description, icon, created_at, updated_at) VALUES
('كيف تبدأ',           'getting-started', 'دروس تمهيدية وإعداد الحساب',             '🚀', NOW(), NOW()),
('مشاكل شائعة',        'common-issues',   'حلول للمشاكل المتكررة',                   '⚠️', NOW(), NOW()),
('التقارير',           'reports',         'كيفية إنشاء وتفسير التقارير',             '📊', NOW(), NOW()),
('الإعدادات',          'settings',        'إعداد النظام وتخصيص الخيارات',           '⚙️', NOW(), NOW()),
('العمليات المحاسبية', 'accounting',      'القيود والفواتير والمصروفات والإيرادات', '💰', NOW(), NOW()),
('أسئلة شائعة',        'faq',             'إجابات سريعة للأسئلة المتكررة',          '❓', NOW(), NOW());

INSERT INTO knowledge_articles (title, content, category_id, created_by, keywords, views, helpful, not_helpful, created_at, updated_at) VALUES
('كيفية تسجيل الدخول للنظام',
 '## تسجيل الدخول\n\n1. افتح المتصفح وتوجه إلى رابط النظام\n2. أدخل البريد الإلكتروني الخاص بك\n3. أدخل كلمة المرور\n4. اضغط على زر **دخول**\n\n> **ملاحظة:** تأكد من أن كلمة المرور تحتوي على حروف كبيرة وصغيرة وأرقام.\n\nفي حال نسيان كلمة المرور، تواصل مع المشرف لإعادة تعيينها.',
 1, 2, 'تسجيل دخول,حساب,مرور,login', 150, 45, 3, NOW(), NOW()),

('حل مشكلة نسيان كلمة المرور',
 '## استعادة كلمة المرور\n\nإذا نسيت كلمة المرور اتبع الخطوات التالية:\n\n1. تواصل مع المشرف عبر فتح تذكرة جديدة\n2. اختر نوع المشكلة **تقني**\n3. اذكر بريدك الإلكتروني في الوصف\n4. سيقوم المشرف بإعادة تعيين كلمة المرور\n5. استخدم كلمة المرور الجديدة وقم بتغييرها فوراً',
 2, 2, 'كلمة مرور,نسيت,استعادة,reset', 89, 30, 5, NOW(), NOW()),

('كيفية إنشاء تقرير الأرباح والخسائر',
 '## تقرير الأرباح والخسائر\n\n### الخطوات:\n1. اذهب إلى قسم **التقارير** من القائمة الجانبية\n2. اختر **تقرير الأرباح والخسائر**\n3. حدد الفترة الزمنية المطلوبة\n4. اضغط **توليد التقرير**\n\n### تصدير التقرير:\n- اضغط زر **تصدير PDF** للحصول على نسخة PDF\n- اضغط زر **تصدير Excel** للحصول على ملف Excel\n\n> يمكنك تصفية التقرير حسب القسم أو المشروع',
 3, 2, 'تقرير,أرباح,خسائر,مالي,P&L', 210, 78, 4, NOW(), NOW()),

('إضافة قيد محاسبي جديد',
 '## إضافة قيد يومية\n\n1. انتقل إلى **العمليات المحاسبية** ← **القيود اليومية**\n2. اضغط **إضافة قيد جديد**\n3. أدخل تاريخ القيد\n4. أضف الحسابات المدينة والدائنة\n5. تأكد من **توازن القيد** (المدين = الدائن)\n6. أضف وصفاً للقيد\n7. اضغط **حفظ**\n\n### أنواع القيود الشائعة:\n- قيد المبيعات\n- قيد المشتريات\n- قيد الرواتب\n- قيد الإيجار',
 5, 3, 'قيد,محاسبة,يومية,journal entry', 175, 62, 7, NOW(), NOW()),

('إعداد صلاحيات المستخدمين',
 '## إدارة صلاحيات المستخدمين\n\n### الأدوار المتاحة:\n| الدور | الصلاحيات |\n|-------|----------|\n| محاسب | فتح تذاكر، عرض تذاكره فقط |\n| مدير مالي | عرض تذاكر فريقه، تصدير تقارير |\n| دعم فني | الرد على التذاكر، إدارة قاعدة المعرفة |\n| مشرف | صلاحيات كاملة |\n\n### إضافة مستخدم جديد:\n1. اذهب إلى **إدارة المستخدمين**\n2. اضغط **مستخدم جديد**\n3. أدخل البيانات المطلوبة\n4. حدد الدور المناسب\n5. اضغط **حفظ**',
 4, 1, 'صلاحيات,مستخدم,أدوار,permissions', 134, 51, 2, NOW(), NOW()),

('ما هي أوقات الاستجابة المتوقعة؟',
 '## أوقات الاستجابة\n\nتختلف أوقات الاستجابة حسب أولوية التذكرة:\n\n| الأولوية | وصفها | وقت الاستجابة |\n|----------|--------|---------------|\n| 🔴 عاجلة | مشاكل تمنع العمل كلياً | فوري - 2 ساعة |\n| 🟠 مرتفعة | مشاكل تؤثر على الإنتاجية | 4 - 12 ساعة |\n| 🟢 عادية | مشاكل لا تؤثر على العمل | 24 - 48 ساعة |\n\n> **نصيحة:** اختر الأولوية المناسبة لمشكلتك حتى نتمكن من خدمتك بشكل أفضل.',
 6, 1, 'وقت استجابة,أولوية,SLA,ساعات', 98, 40, 1, NOW(), NOW());

INSERT INTO tickets (title, description, type, priority, status, user_id, assigned_to, created_at, updated_at) VALUES
('مشكلة في تسجيل الدخول',
 'لا أستطيع تسجيل الدخول للنظام منذ الصباح، تظهر رسالة خطأ "بيانات غير صحيحة" رغم أن البيانات صحيحة. جربت إعادة ضبط المتصفح ولكن المشكلة مستمرة.',
 'technical', 'urgent', 'open', 5, NULL, NOW(), NOW()),

('استفسار عن إضافة قيد يومية',
 'كيف يمكنني إضافة قيد يومية لمصاريف الإيجار الشهري؟ لم أجد الخيار في القائمة الرئيسية ولم تكن قاعدة المعرفة واضحة بالنسبة لي.',
 'accounting', 'normal', 'in_progress', 6, 2, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 12 HOUR)),

('طلب إضافة تقرير مخصص',
 'نحتاج تقريراً يجمع بين المبيعات والمشتريات خلال فترة زمنية محددة مع إمكانية التصفية حسب القسم وتصدير النتائج لـ Excel.',
 'development', 'high', 'waiting_customer', 7, 2, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),

('بطء شديد في تحميل الصفحات',
 'النظام بطيء جداً خلال ساعات الذروة (8-10 صباحاً)، يأخذ أكثر من 30 ثانية لفتح صفحة التقارير. هذا يؤثر على إنتاجية الفريق.',
 'technical', 'high', 'resolved', 8, 2, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),

('خطأ في حساب الضريبة على الفواتير',
 'النظام يحسب الضريبة بنسبة 14% بدلاً من 15% على بعض أنواع الفواتير. هذا يسبب اختلافاً في الأرقام المالية النهائية.',
 'accounting', 'urgent', 'closed', 5, 2, DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY)),

('عدم ظهور بعض الحسابات في شجرة الحسابات',
 'عند إنشاء قيد جديد، لا تظهر بعض الحسابات الفرعية في القائمة المنسدلة رغم وجودها في شجرة الحسابات.',
 'technical', 'normal', 'in_progress', 6, 2, DATE_SUB(NOW(), INTERVAL 2 DAY), NOW());

INSERT INTO ticket_replies (ticket_id, user_id, body, created_at, updated_at) VALUES
(2, 2, 'شكراً لتواصلك معنا. تم استلام طلبك وسنقوم بالمساعدة. هل يمكنك إخبارنا بالحساب الذي تريد الترحيل إليه؟', DATE_SUB(NOW(), INTERVAL 20 HOUR), DATE_SUB(NOW(), INTERVAL 20 HOUR)),
(3, 2, 'تم استلام طلب التطوير وسيتم دراسته. هل يمكنك توضيح أي مؤشرات تريد رؤيتها في التقرير؟', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 2, 'تم التحقيق في المشكلة. كان السبب استعلامات بطيئة في قاعدة البيانات. تم إضافة الفهارس اللازمة وتحسين الأداء.', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 2, 'تم إصلاح المشكلة. كانت نسبة الضريبة مُعيّنة بشكل خاطئ في الإعدادات. تم تحديثها إلى 15% وإعادة حساب الفواتير المتأثرة.', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY)),
(6, 2, 'جارٍ التحقيق في المشكلة. هل يمكنك تزويدنا بأسماء الحسابات التي لا تظهر؟', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

SELECT '✅ تم إنشاء قاعدة البيانات والبيانات التجريبية بنجاح!' AS message;
