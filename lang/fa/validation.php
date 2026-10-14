<?php

return [
    'after_or_equal' => ':attribute باید برابر یا بعد از :date باشد.',
    'alpha_dash' => ':attribute فقط می‌تواند شامل حروف، اعداد، خط تیره و زیرخط باشد.',
    'array' => ':attribute باید یک آرایه باشد.',
    'before_or_equal' => ':attribute باید برابر یا قبل از :date باشد.',
    'between' => ['numeric' => ':attribute باید بین :min و :max باشد.'],
    'date' => ':attribute یک تاریخ معتبر نیست.',
    'distinct' => ':attribute تکراری است.',
    'email' => ':attribute باید یک ایمیل معتبر باشد.',
    'exists' => ':attribute انتخاب‌شده معتبر نیست.',
    'in' => ':attribute انتخاب‌شده معتبر نیست.',
    'integer' => ':attribute باید عدد صحیح باشد.',
    'max' => ['numeric' => ':attribute نباید بیشتر از :max باشد.', 'string' => ':attribute نباید بیشتر از :max کاراکتر باشد.'],
    'min' => ['array' => ':attribute باید حداقل :min مورد داشته باشد.', 'numeric' => ':attribute باید حداقل :min باشد.', 'string' => ':attribute باید حداقل :min کاراکتر باشد.'],
    'regex' => 'قالب :attribute معتبر نیست.',
    'required' => 'وارد کردن :attribute الزامی است.',
    'string' => ':attribute باید متن باشد.',
    'unique' => 'این :attribute قبلاً ثبت شده است.',

    'attributes' => [
        'name' => 'نام', 'email' => 'ایمیل', 'password' => 'رمز عبور', 'phone' => 'تلفن', 'company' => 'شرکت',
        'city' => 'شهر', 'status' => 'وضعیت', 'notes' => 'یادداشت', 'sku' => 'کد کالا', 'category' => 'دسته‌بندی',
        'price' => 'قیمت', 'stock' => 'موجودی', 'description' => 'توضیحات', 'customer_id' => 'مشتری',
        'ordered_at' => 'تاریخ سفارش', 'items' => 'اقلام سفارش', 'order_id' => 'سفارش', 'amount' => 'مبلغ',
        'tax_rate' => 'نرخ مالیات', 'issued_at' => 'تاریخ صدور', 'due_at' => 'تاریخ سررسید',
        'company_name' => 'نام شرکت', 'company_email' => 'ایمیل شرکت', 'currency' => 'واحد پول',
        'items_per_page' => 'تعداد ردیف', 'default_locale' => 'زبان پیش‌فرض',
    ],
];
