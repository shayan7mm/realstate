<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش دسترسی‌ها - {{ $user->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        .module-row {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }
        .module-row:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        .module-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .module-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a2e;
        }
        .toggle-switch {
            position: relative;
            width: 50px;
            height: 28px;
            background: #e5e7eb;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            flex-shrink: 0;
        }
        .toggle-switch.active {
            background: #4f46e5;
        }
        .toggle-switch .slider {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            background: #fff;
            border-radius: 50%;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .toggle-switch.active .slider {
            left: 25px;
        }
        .toggle-switch input {
            display: none;
        }
        .item-selector {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f1f3f5;
        }
        .item-selector .items-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        .item-selector .items-grid label {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: #f8fafc;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }
        .item-selector .items-grid label:hover {
            border-color: #4f46e5;
        }
        .item-selector .items-grid label.selected {
            border-color: #4f46e5;
            background: rgba(79, 70, 229, 0.06);
        }
        .item-selector .items-grid input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #4f46e5;
        }
        .item-selector .all-items {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }
        .item-selector .all-items input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #4f46e5;
        }
        .item-selector .all-items label {
            font-size: 14px;
            color: #6b7280;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="container">

        <header class="header">
            <div class="header-left">
                <div class="logo-box">حس</div>
                <div>
                    <h1>ویرایش دسترسی‌ها</h1>
                    <div class="subtitle">{{ $user->name }}</div>
                </div>
            </div>
            <div class="header-right">
                <div class="user-badge">
                    <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="user-meta">
                        <div class="name">{{ auth()->user()->name }}</div>
                        <div class="role">مدیر ارشد</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">خروج</button>
                </form>
            </div>
        </header>

        <a href="{{ route('permissions.index', $user) }}" class="back-link">بازگشت به لیست دسترسی‌ها</a>

        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
            <div>
                <h2 style="font-size:20px; color:#1a1a2e;">تنظیم دسترسی‌های {{ $user->name }}</h2>
                <p style="color:#6b7280; font-size:14px;">نقش: {{ $user->isMaster() ? 'مدیر ارشد' : 'حسابدار' }}</p>
            </div>
        </div>

        @if(session('success'))
            <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.15); border-radius:14px; padding:14px 20px; margin-bottom:20px; color:#059669; font-size:15px;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('permissions.update', $user) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:16px; background:#f8fafc; padding:16px 20px; border-radius:14px; border:1px solid #e5e7eb;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <input type="checkbox" id="select_all" onchange="toggleAll(this)">
                    <label for="select_all" style="font-weight:500; cursor:pointer;">انتخاب همه ماژول‌ها</label>
                </div>
            </div>

            @foreach($modules as $moduleKey => $moduleName)
                @php
                    $permission = $permissions->where('module', $moduleKey)->first();
                    $access = $permission ? $permission->access : false;
                    $itemIds = $permission ? $permission->item_ids : [];
                    
                    // آیتم‌های این ماژول (برای نمایش)
                    $items = [];
                    if ($moduleKey === 'cars') {
                        $items = \App\Models\Car::where('user_id', auth()->id())->get(['id', 'name']);
                    }
                    // برای ماژول‌های دیگر هم می‌توان آیتم‌ها را گرفت
                @endphp

                <div class="module-row">
                    <div class="module-header">
                        <div>
                            <div class="module-title">{{ $moduleName }}</div>
                            <div style="font-size:13px; color:#6b7280;">
                                @if($access && $itemIds === null)
                                    <span style="color:#059669;">✅ دسترسی کامل به همه</span>
                                @elseif($access && is_array($itemIds) && count($itemIds) > 0)
                                    <span style="color:#d97706;">🔶 دسترسی به {{ count($itemIds) }} آیتم</span>
                                @else
                                    <span style="color:#dc2626;">❌ بدون دسترسی</span>
                                @endif
                            </div>
                        </div>
                        <div class="toggle-switch {{ $access ? 'active' : '' }}" onclick="toggleSwitch(this, 'module_{{ $moduleKey }}')">
                            <input type="hidden" name="permissions[{{ $moduleKey }}][module]" value="{{ $moduleKey }}">
                            <input type="hidden" name="permissions[{{ $moduleKey }}][access]" id="module_{{ $moduleKey }}" value="{{ $access ? 1 : 0 }}">
                            <div class="slider"></div>
                        </div>
                    </div>

                    <div class="item-selector" style="{{ $access ? 'display:block;' : 'display:none;' }}" id="items_{{ $moduleKey }}">
                        <div style="font-size:14px; color:#6b7280; margin-bottom:6px;">آیتم‌های قابل دسترس:</div>
                        
                        <div class="all-items">
                            <input type="checkbox" id="all_items_{{ $moduleKey }}" 
                                   onchange="toggleAllItems('{{ $moduleKey }}', this)"
                                   {{ $itemIds === null ? 'checked' : '' }}>
                            <label for="all_items_{{ $moduleKey }}">دسترسی به همه آیتم‌ها</label>
                        </div>

                        <div class="items-grid" id="items_grid_{{ $moduleKey }}">
                            @forelse($items as $item)
                                <label class="{{ in_array($item->id, $itemIds ?? []) ? 'selected' : '' }}">
                                    <input type="checkbox" 
                                           name="permissions[{{ $moduleKey }}][item_ids][]" 
                                           value="{{ $item->id }}"
                                           {{ in_array($item->id, $itemIds ?? []) ? 'checked' : '' }}
                                           {{ $itemIds === null ? 'disabled' : '' }}>
                                    {{ $item->name }}
                                </label>
                            @empty
                                <span style="color:#9ca3af; font-size:14px;">هیچ آیتمی برای این ماژول وجود ندارد</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="form-actions" style="margin-top:30px;">
                <a href="{{ route('permissions.index', $user) }}" class="btn-cancel">بازگشت</a>
                <button type="submit" class="btn-submit">ذخیره دسترسی‌ها</button>
            </div>
        </form>

    </div>

    <script>
        function toggleSwitch(element, inputId) {
            const isActive = element.classList.contains('active');
            const input = document.getElementById(inputId);
            const itemsDiv = document.getElementById('items_' + inputId.replace('module_', ''));
            
            if (isActive) {
                element.classList.remove('active');
                input.value = 0;
                if (itemsDiv) itemsDiv.style.display = 'none';
            } else {
                element.classList.add('active');
                input.value = 1;
                if (itemsDiv) itemsDiv.style.display = 'block';
            }
        }

        function toggleAllItems(moduleKey, checkbox) {
            const itemsGrid = document.getElementById('items_grid_' + moduleKey);
            const checkboxes = itemsGrid.querySelectorAll('input[type="checkbox"]');
            
            if (checkbox.checked) {
                checkboxes.forEach(cb => {
                    cb.checked = false;
                    cb.disabled = true;
                });
                // تنظیم item_ids به null برای دسترسی به همه
                // این کار در بک‌اند انجام می‌شود
            } else {
                checkboxes.forEach(cb => {
                    cb.disabled = false;
                });
            }
        }

        function toggleAll(checkbox) {
            const moduleCheckboxes = document.querySelectorAll('.toggle-switch input[type="hidden"]');
            const toggles = document.querySelectorAll('.toggle-switch');
            
            moduleCheckboxes.forEach((input, index) => {
                if (checkbox.checked) {
                    toggles[index].classList.add('active');
                    input.value = 1;
                    const moduleKey = input.id.replace('module_', '');
                    const itemsDiv = document.getElementById('items_' + moduleKey);
                    if (itemsDiv) itemsDiv.style.display = 'block';
                } else {
                    toggles[index].classList.remove('active');
                    input.value = 0;
                    const moduleKey = input.id.replace('module_', '');
                    const itemsDiv = document.getElementById('items_' + moduleKey);
                    if (itemsDiv) itemsDiv.style.display = 'none';
                }
            });
        }

        // تغییر وضعیت آیتم‌ها هنگام کلیک روی "همه آیتم‌ها"
        document.querySelectorAll('.all-items input[type="checkbox"]').forEach(cb => {
            cb.addEventListener('change', function() {
                const moduleKey = this.id.replace('all_items_', '');
                const itemsGrid = document.getElementById('items_grid_' + moduleKey);
                const checkboxes = itemsGrid.querySelectorAll('input[type="checkbox"]');
                
                if (this.checked) {
                    checkboxes.forEach(cb => {
                        cb.checked = false;
                        cb.disabled = true;
                        cb.closest('label').classList.remove('selected');
                    });
                } else {
                    checkboxes.forEach(cb => {
                        cb.disabled = false;
                    });
                }
            });
        });
    </script>

</body>
</html>