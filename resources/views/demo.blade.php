{{-- Live showcase of all components and their states: /components-demo --}}
<x-layouts.guest title="دليل المكونات" :centered="false">
    <div class="mx-auto max-w-4xl space-y-10 py-10">
        <header>
            <h1 class="text-2xl font-bold">مكتبة مكونات Blade</h1>
            <p class="mt-1 text-slate-600 dark:text-slate-400">Laravel 13 · Tailwind v4.3 · Alpine.js</p>
        </header>

        <x-ui.card>
            <x-slot:header>الأزرار</x-slot:header>
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    @foreach (['primary', 'secondary', 'success', 'danger', 'outline', 'ghost'] as $variant)
                        <x-ui.button :variant="$variant">{{ $variant }}</x-ui.button>
                    @endforeach
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach (['xs', 'sm', 'md', 'lg'] as $size)
                        <x-ui.button :size="$size">{{ $size }}</x-ui.button>
                    @endforeach
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.button loading>جارٍ الحفظ</x-ui.button>
                    <x-ui.button disabled variant="outline">معطّل</x-ui.button>
                    <x-ui.button href="#" variant="ghost">رابط</x-ui.button>
                    <x-ui.button>
                        <x-slot:icon-left><x-ui.icon name="mail" size="size-4" /></x-slot:icon-left>
                        أيقونة
                    </x-ui.button>
                    <x-ui.tooltip text="تلميح عند التحويم" position="bottom">
                        <x-ui.button variant="outline">مرّر هنا</x-ui.button>
                    </x-ui.tooltip>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>الشارات والصور الشخصية</x-slot:header>
            <div class="flex flex-wrap items-center gap-3">
                @foreach (['neutral', 'brand', 'success', 'warning', 'danger', 'info'] as $variant)
                    <x-ui.badge :variant="$variant" dot>{{ $variant }}</x-ui.badge>
                @endforeach
                <x-ui.avatar name="سارة أحمد" status="online" />
                <x-ui.avatar name="خالد" size="lg" status="busy" />
                <x-ui.avatar shape="square" size="xl" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>التنبيهات والإشعارات</x-slot:header>
            <div class="space-y-3">
                <x-ui.alert type="info" title="معلومة">هذا تنبيه معلوماتي.</x-ui.alert>
                <x-ui.alert type="success" dismissible>تم الحفظ بنجاح.</x-ui.alert>
                <x-ui.alert type="warning" dismissible>اقتربت صلاحية اشتراكك من الانتهاء.</x-ui.alert>
                <x-ui.alert type="danger" title="خطأ" dismissible>تعذّر إكمال العملية.</x-ui.alert>
                <x-ui.button variant="outline"
                    x-on:click="$dispatch('toast', { type: 'success', title: 'تم الحفظ', message: 'تم تحديث بياناتك' })">
                    إظهار إشعار مؤقت
                </x-ui.button>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>النماذج</x-slot:header>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-forms.input name="demo_name" label="الاسم" hint="الاسم الكامل كما في الهوية" required>
                    <x-slot:icon-left><x-ui.icon name="user" /></x-slot:icon-left>
                </x-forms.input>
                <x-forms.select name="demo_role" label="الدور" placeholder="اختر الدور"
                                :options="['admin' => 'مدير', 'editor' => 'محرر', 'viewer' => 'مشاهد']" />
                <x-forms.textarea name="demo_bio" label="نبذة" wrapper-class="sm:col-span-2" />
                <x-forms.checkbox name="demo_terms" label="أوافق على الشروط" hint="يمكنك تغيير ذلك لاحقًا" />
                <x-forms.radio name="demo_plan" value="pro" label="الخطة الاحترافية" checked />
                <x-forms.switch name="demo_notify" label="إشعارات البريد" off-value="0" checked />
                <x-forms.input name="demo_disabled" label="حقل معطّل" value="للقراءة فقط" disabled />
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>التبويبات والقائمة المنسدلة والنافذة</x-slot:header>
            <div class="space-y-6">
                <x-ui.tabs :items="['profile' => 'الملف الشخصي', 'security' => 'الأمان', 'billing' => 'الفوترة']">
                    <x-ui.tab-panel name="profile">محتوى الملف الشخصي.</x-ui.tab-panel>
                    <x-ui.tab-panel name="security">إعدادات الأمان.</x-ui.tab-panel>
                    <x-ui.tab-panel name="billing">تفاصيل الفوترة.</x-ui.tab-panel>
                </x-ui.tabs>

                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.dropdown align="start">
                        <x-slot:trigger>
                            <x-ui.button variant="outline">
                                الحساب
                                <x-slot:icon-right><x-ui.icon name="chevron-down" size="size-4" /></x-slot:icon-right>
                            </x-ui.button>
                        </x-slot:trigger>
                        <x-ui.dropdown-item href="#">الملف الشخصي</x-ui.dropdown-item>
                        <x-ui.dropdown-item href="#">الإعدادات</x-ui.dropdown-item>
                        <x-ui.dropdown-item danger>تسجيل الخروج</x-ui.dropdown-item>
                    </x-ui.dropdown>

                    <x-ui.button variant="danger" x-on:click="$dispatch('open-modal-confirm-delete')">حذف الحساب</x-ui.button>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>التنقل: مسار وترقيم</x-slot:header>
            <div class="space-y-5">
                <x-ui.breadcrumb :items="[['label' => 'الرئيسية', 'href' => '#'], ['label' => 'المستخدمون', 'href' => '#'], ['label' => 'سارة أحمد']]" />
                <x-ui.pagination :paginator="new \Illuminate\Pagination\LengthAwarePaginator(collect(range(1, 10)), 120, 10, 6, ['path' => '#'])" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>الجدول</x-slot:header>
            <x-ui.table caption="مستخدمون تجريبيون" striped class="border-0 rounded-none">
                <x-slot:head>
                    <tr><th scope="col">الاسم</th><th scope="col">الدور</th><th scope="col">الحالة</th></tr>
                </x-slot:head>
                <tr><td>سارة أحمد</td><td>مدير</td><td><x-ui.badge variant="success" dot>نشط</x-ui.badge></td></tr>
                <tr><td>خالد علي</td><td>محرر</td><td><x-ui.badge variant="warning" dot>معلّق</x-ui.badge></td></tr>
                <tr><td>نورة سعد</td><td>مشاهد</td><td><x-ui.badge dot>غير نشط</x-ui.badge></td></tr>
            </x-ui.table>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>التقدم والتحميل وحالة الفراغ</x-slot:header>
            <div class="space-y-6">
                <x-ui.progress :value="65" label="اكتمال الملف الشخصي" show-value />
                <x-ui.progress :value="90" variant="success" size="sm" label="مساحة التخزين" show-value />
                <x-ui.progress indeterminate label="جارٍ المعالجة" variant="warning" />

                <div class="flex items-center gap-4">
                    <x-ui.skeleton shape="circle" class="size-12" />
                    <x-ui.skeleton shape="text" :lines="3" />
                </div>

                <x-ui.empty-state title="لا توجد مشاريع بعد" description="أنشئ أول مشروع لتبدأ.">
                    <x-ui.button size="sm">
                        <x-slot:icon-left><x-ui.icon name="plus" size="size-4" /></x-slot:icon-left>
                        مشروع جديد
                    </x-ui.button>
                </x-ui.empty-state>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>الأكورديون واللوحة الجانبية ورفع الملفات</x-slot:header>
            <div class="space-y-6">
                <x-ui.accordion>
                    <x-ui.accordion-item title="كيف أُنشئ حسابًا؟" open>من صفحة التسجيل أدخل بريدك وكلمة مرور قوية.</x-ui.accordion-item>
                    <x-ui.accordion-item title="هل يمكنني تغيير الوضع الداكن؟">نعم، من الزر أعلى الصفحة، ويُحفظ اختيارك.</x-ui.accordion-item>
                    <x-ui.accordion-item title="كيف أتواصل مع الدعم؟">عبر صفحة الاتصال أو البريد الموضّح في التذييل.</x-ui.accordion-item>
                </x-ui.accordion>

                <x-forms.file name="demo_avatar" label="الصورة الشخصية" accept="image/*" hint="PNG أو JPG حتى 2MB" />
                <x-forms.password name="demo_password" label="كلمة مرور جديدة" hint="8 أحرف على الأقل" />

                <x-ui.button variant="outline" x-on:click="$dispatch('open-drawer-filters')">فتح لوحة التصفية</x-ui.button>
            </div>
        </x-ui.card>

        <div class="grid gap-4 sm:grid-cols-3">
            <x-ui.stat-card label="المبيعات" value="12,500" change="+12%" trend="up" description="عن الشهر الماضي" icon="arrow-up" />
            <x-ui.stat-card label="الطلبات المرتجعة" value="38" change="-4%" trend="down" description="عن الشهر الماضي" icon="inbox" />
            <x-ui.stat-card label="الزوار" value="9,240" change="0%" description="هذا الأسبوع" icon="user" />
        </div>

        <x-ui.card>
            <x-slot:header>الخطوات والاختيار المتقدم</x-slot:header>
            <div class="space-y-6">
                <x-ui.steps :steps="[['title' => 'الحساب', 'description' => 'البيانات الأساسية'], ['title' => 'الملف الشخصي', 'description' => 'التفاصيل'], 'التأكيد']" :current="2" />

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-forms.combobox name="demo_country" label="الدولة" placeholder="ابحث عن دولة"
                        :options="['sa' => 'السعودية', 'ae' => 'الإمارات', 'eg' => 'مصر', 'jo' => 'الأردن', 'ma' => 'المغرب', 'kw' => 'الكويت']"
                        hint="اكتب للبحث ثم اضغط Enter" />
                    <x-forms.date-picker name="demo_date" label="تاريخ الموعد" :week-start="6" hint="يُحفظ بصيغة YYYY-MM-DD" />
                </div>
            </div>
        </x-ui.card>
    </div>

    <x-ui.modal name="confirm-delete" title="تأكيد الحذف">
        سيتم حذف الحساب نهائيًا ولا يمكن التراجع عن ذلك.
        <x-slot:footer>
            <x-ui.button variant="ghost" x-on:click="$dispatch('close-modal-confirm-delete')">إلغاء</x-ui.button>
            <x-ui.button variant="danger" x-on:click="$dispatch('close-modal-confirm-delete'); $dispatch('toast', { type: 'danger', message: 'تم الحذف' })">
                حذف
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    <x-ui.drawer name="filters" title="تصفية النتائج">
        <div class="space-y-5">
            <x-forms.select name="filter_role" label="الدور" placeholder="الكل" :options="['admin' => 'مدير', 'editor' => 'محرر']" />
            <x-forms.switch name="filter_active" label="النشطون فقط" />
        </div>
        <x-slot:footer>
            <x-ui.button variant="ghost" x-on:click="$dispatch('close-drawer-filters')">إغلاق</x-ui.button>
            <x-ui.button x-on:click="$dispatch('close-drawer-filters')">تطبيق</x-ui.button>
        </x-slot:footer>
    </x-ui.drawer>
</x-layouts.guest>
