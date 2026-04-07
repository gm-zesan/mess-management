## 🧩 DataTable Architecture (Laravel + Alpine.js)

All tables MUST use a reusable Blade component:

```blade
<x-datatable 
    :columns="$columns" 
    :data="$data" 
    searchPlaceholder="Search..." 
/>
```

---

### ⚙️ Tech Stack

* Laravel Blade → structure
* Alpine.js → interactivity (search, pagination, sorting)
* Tailwind CSS → UI

---

### 🎯 Features (MANDATORY)

* Search (client-side)
* Pagination
* Sorting
* Lightweight and fast

---

### 🧠 Behavior Rules

* No page reload for search
* Keep interactions smooth
* Minimal JS (Alpine only)

---

### 🧬 Reusability Rules

* Single Blade component for all tables
* Columns and data passed dynamically
* No hardcoded UI

---

### 🧑‍💻 Copilot Instruction

When building tables:

* Use Blade component `<x-datatable>`
* Use Alpine.js for logic
* Use Tailwind for styling
* Follow sky-600 theme
* Keep UI minimal and clean

Return production-ready Blade + Alpine code only
