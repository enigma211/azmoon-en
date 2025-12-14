<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute باید پذیرفته شود.',
    'accepted_if' => ':attribute باید پذیرفته شود وقتی :other برابر :value باشد.',
    'active_url' => ':attribute یک URL معتبر نیست.',
    'after' => ':attribute باید تاریخی بعد از :date باشد.',
    'after_or_equal' => ':attribute باید تاریخی بعد از یا برابر :date باشد.',
    'alpha' => ':attribute باید فقط شامل حروف باشد.',
    'alpha_dash' => ':attribute باید فقط شامل حروف، اعداد، خط تیره و زیرخط باشد.',
    'alpha_num' => ':attribute باید فقط شامل حروف و اعداد باشد.',
    'array' => ':attribute باید یک آرایه باشد.',
    'ascii' => ':attribute باید فقط شامل کاراکترهای ASCII و اعداد باشد.',
    'before' => ':attribute باید تاریخی قبل از :date باشد.',
    'before_or_equal' => ':attribute باید تاریخی قبل از یا برابر :date باشد.',
    'between' => [
        'array' => ':attribute باید بین :min و :max آیتم داشته باشد.',
        'file' => ':attribute باید بین :min و :max کیلوبایت باشد.',
        'numeric' => ':attribute باید بین :min و :max باشد.',
        'string' => ':attribute باید بین :min و :max کاراکتر باشد.',
    ],
    'boolean' => ':attribute باید true یا false باشد.',
    'can' => ':attribute شامل یک مقدار غیرمجاز است.',
    'confirmed' => 'تأیید :attribute مطابقت ندارد.',
    'current_password' => 'رمز عبور نادرست است.',
    'date' => ':attribute یک تاریخ معتبر نیست.',
    'date_equals' => ':attribute باید برابر :date باشد.',
    'date_format' => ':attribute با فرمت :format مطابقت ندارد.',
    'decimal' => ':attribute باید :decimal رقم اعشار داشته باشد.',
    'declined' => ':attribute باید رد شود.',
    'declined_if' => ':attribute باید رد شود وقتی :other برابر :value باشد.',
    'different' => ':attribute و :other باید متفاوت باشند.',
    'digits' => ':attribute باید :digits رقم باشد.',
    'digits_between' => ':attribute باید بین :min و :max رقم باشد.',
    'dimensions' => ':attribute دارای ابعاد تصویر نامعتبر است.',
    'distinct' => ':attribute دارای مقدار تکراری است.',
    'doesnt_end_with' => ':attribute نباید با یکی از مقادیر زیر خاتمه یابد: :values.',
    'doesnt_start_with' => ':attribute نباید با یکی از مقادیر زیر شروع شود: :values.',
    'email' => ':attribute باید یک آدرس ایمیل معتبر باشد.',
    'ends_with' => ':attribute باید با یکی از مقادیر زیر خاتمه یابد: :values.',
    'enum' => ':attribute انتخاب شده نامعتبر است.',
    'exists' => ':attribute انتخاب شده نامعتبر است.',
    'file' => ':attribute باید یک فایل باشد.',
    'filled' => ':attribute باید دارای مقدار باشد.',
    'gt' => [
        'array' => ':attribute باید بیش از :value آیتم داشته باشد.',
        'file' => ':attribute باید بزرگتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگتر از :value باشد.',
        'string' => ':attribute باید بزرگتر از :value کاراکتر باشد.',
    ],
    'gte' => [
        'array' => ':attribute باید :value آیتم یا بیشتر داشته باشد.',
        'file' => ':attribute باید بزرگتر یا برابر :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگتر یا برابر :value باشد.',
        'string' => ':attribute باید بزرگتر یا برابر :value کاراکتر باشد.',
    ],
    'image' => ':attribute باید یک تصویر باشد.',
    'in' => ':attribute انتخاب شده نامعتبر است.',
    'in_array' => ':attribute در :other وجود ندارد.',
    'integer' => ':attribute باید یک عدد صحیح باشد.',
    'ip' => ':attribute باید یک آدرس IP معتبر باشد.',
    'ipv4' => ':attribute باید یک آدرس IPv4 معتبر باشد.',
    'ipv6' => ':attribute باید یک آدرس IPv6 معتبر باشد.',
    'json' => ':attribute باید یک رشته JSON معتبر باشد.',
    'lowercase' => ':attribute باید با حروف کوچک باشد.',
    'lt' => [
        'array' => ':attribute باید کمتر از :value آیتم داشته باشد.',
        'file' => ':attribute باید کمتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کمتر از :value باشد.',
        'string' => ':attribute باید کمتر از :value کاراکتر باشد.',
    ],
    'lte' => [
        'array' => ':attribute نباید بیش از :value آیتم داشته باشد.',
        'file' => ':attribute باید کمتر یا برابر :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کمتر یا برابر :value باشد.',
        'string' => ':attribute باید کمتر یا برابر :value کاراکتر باشد.',
    ],
    'mac_address' => ':attribute باید یک آدرس MAC معتبر باشد.',
    'max' => [
        'array' => ':attribute نباید بیش از :max آیتم داشته باشد.',
        'file' => ':attribute نباید بزرگتر از :max کیلوبایت باشد.',
        'numeric' => ':attribute نباید بزرگتر از :max باشد.',
        'string' => ':attribute نباید بیش از :max کاراکتر باشد.',
    ],
    'max_digits' => ':attribute نباید بیش از :max رقم داشته باشد.',
    'mimes' => ':attribute باید یک فایل از نوع: :values باشد.',
    'mimetypes' => ':attribute باید یک فایل از نوع: :values باشد.',
    'min' => [
        'array' => ':attribute باید حداقل :min آیتم داشته باشد.',
        'file' => ':attribute باید حداقل :min کیلوبایت باشد.',
        'numeric' => ':attribute باید حداقل :min باشد.',
        'string' => ':attribute باید حداقل :min کاراکتر باشد.',
    ],
    'min_digits' => ':attribute باید حداقل :min رقم داشته باشد.',
    'missing' => ':attribute باید وجود نداشته باشد.',
    'missing_if' => ':attribute باید وجود نداشته باشد وقتی :other برابر :value باشد.',
    'missing_unless' => ':attribute باید وجود نداشته باشد مگر اینکه :other برابر :value باشد.',
    'missing_with' => ':attribute باید وجود نداشته باشد وقتی :values وجود دارد.',
    'missing_with_all' => ':attribute باید وجود نداشته باشد وقتی :values وجود دارد.',
    'multiple_of' => ':attribute باید مضربی از :value باشد.',
    'not_in' => ':attribute انتخاب شده نامعتبر است.',
    'not_regex' => 'فرمت :attribute نامعتبر است.',
    'numeric' => ':attribute باید یک عدد باشد.',
    'password' => [
        'letters' => ':attribute باید حداقل یک حرف داشته باشد.',
        'mixed' => ':attribute باید حداقل یک حرف بزرگ و یک حرف کوچک داشته باشد.',
        'numbers' => ':attribute باید حداقل یک عدد داشته باشد.',
        'symbols' => ':attribute باید حداقل یک نماد داشته باشد.',
        'uncompromised' => ':attribute در یک نشت داده ظاهر شده است. لطفاً :attribute دیگری انتخاب کنید.',
    ],
    'present' => ':attribute باید وجود داشته باشد.',
    'prohibited' => ':attribute ممنوع است.',
    'prohibited_if' => ':attribute ممنوع است وقتی :other برابر :value باشد.',
    'prohibited_unless' => ':attribute ممنوع است مگر اینکه :other در :values باشد.',
    'prohibits' => ':attribute مانع حضور :other می‌شود.',
    'regex' => 'فرمت :attribute نامعتبر است.',
    'required' => ':attribute الزامی است.',
    'required_array_keys' => ':attribute باید شامل ورودی‌هایی برای: :values باشد.',
    'required_if' => ':attribute الزامی است وقتی :other برابر :value باشد.',
    'required_if_accepted' => ':attribute الزامی است وقتی :other پذیرفته شود.',
    'required_unless' => ':attribute الزامی است مگر اینکه :other در :values باشد.',
    'required_with' => ':attribute الزامی است وقتی :values وجود دارد.',
    'required_with_all' => ':attribute الزامی است وقتی :values وجود دارد.',
    'required_without' => ':attribute الزامی است وقتی :values وجود ندارد.',
    'required_without_all' => ':attribute الزامی است وقتی هیچ‌کدام از :values وجود ندارد.',
    'same' => ':attribute و :other باید مطابقت داشته باشند.',
    'size' => [
        'array' => ':attribute باید :size آیتم داشته باشد.',
        'file' => ':attribute باید :size کیلوبایت باشد.',
        'numeric' => ':attribute باید :size باشد.',
        'string' => ':attribute باید :size کاراکتر باشد.',
    ],
    'starts_with' => ':attribute باید با یکی از مقادیر زیر شروع شود: :values.',
    'string' => ':attribute باید یک رشته باشد.',
    'timezone' => ':attribute باید یک منطقه زمانی معتبر باشد.',
    'unique' => ':attribute قبلاً استفاده شده است.',
    'uploaded' => 'بارگذاری :attribute با شکست مواجه شد.',
    'uppercase' => ':attribute باید با حروف بزرگ باشد.',
    'url' => ':attribute باید یک URL معتبر باشد.',
    'ulid' => ':attribute باید یک ULID معتبر باشد.',
    'uuid' => ':attribute باید یک UUID معتبر باشد.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'پیام سفارشی',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'نام',
        'username' => 'نام کاربری',
        'email' => 'ایمیل',
        'password' => 'رمز عبور',
        'password_confirmation' => 'تأیید رمز عبور',
        'current_password' => 'رمز عبور فعلی',
        'phone' => 'تلفن',
        'mobile' => 'موبایل',
        'age' => 'سن',
        'sex' => 'جنسیت',
        'gender' => 'جنسیت',
        'day' => 'روز',
        'month' => 'ماه',
        'year' => 'سال',
        'hour' => 'ساعت',
        'minute' => 'دقیقه',
        'second' => 'ثانیه',
        'title' => 'عنوان',
        'text' => 'متن',
        'content' => 'محتوا',
        'description' => 'توضیحات',
        'excerpt' => 'گزیده',
        'date' => 'تاریخ',
        'time' => 'زمان',
        'available' => 'موجود',
        'size' => 'اندازه',
        'file' => 'فایل',
        'image' => 'تصویر',
        'photo' => 'عکس',
        'avatar' => 'آواتار',
        'address' => 'آدرس',
        'city' => 'شهر',
        'country' => 'کشور',
        'province' => 'استان',
        'postal_code' => 'کد پستی',
        'message' => 'پیام',
        'subject' => 'موضوع',
        'price' => 'قیمت',
        'currency' => 'واحد پول',
        'status' => 'وضعیت',
        'type' => 'نوع',
        'category' => 'دسته‌بندی',
        'website' => 'وبسایت',
        'role' => 'نقش',
        'permissions' => 'مجوزها',
    ],

];
