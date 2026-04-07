## 🧩 DataTable Architecture (Reusable System)

All tables MUST use a reusable `<DataTable />` component.

---

### 🎯 Goals

* Single reusable table system across entire app
* Consistent UI & behavior
* Clean SaaS look (light theme, sky-600)
* Fully customizable per page

---

### 🧱 Component Architecture

Create a shared component:

```tsx
<DataTable
  columns={columns}
  data={data}
  searchPlaceholder="Search members..."
  actions={<AddButton />}
 />
```

---

### ⚙️ Tech Stack

* TanStack Table → table logic (sorting, pagination, filtering)
* Tailwind CSS → UI
* NO prebuilt table UI libraries

---

### 📦 Props Design

#### columns

* Defines table structure
* Includes header + cell renderer
* Supports sorting

#### data

* Array of objects

#### actions

* React node (Add button, filters, export)

#### searchPlaceholder

* Custom placeholder per page

---

### 🔍 Features (MANDATORY)

Every DataTable MUST include:

* 🔎 Search (global filter)
* 🔢 Pagination (client-side)
* 🔽 Sorting (on key columns)
* 📱 Responsive layout
* ⚡ Fast rendering

---

### 🎨 UI Structure

1. Top Bar

   * Search input (left)
   * Actions (right)

2. Table Container

   * Clean header
   * Hover rows
   * Proper spacing

3. Footer

   * Pagination
   * Row info

---

### 🎯 Tailwind Styling Rules

#### Wrapper

```tailwind
bg-white border border-gray-200 rounded-xl overflow-hidden
```

#### Search Input

```tailwind
border border-gray-200 rounded-lg px-3 py-2 w-64 focus:ring-2 focus:ring-sky-500 outline-none
```

#### Table Head

```tailwind
bg-gray-50 text-xs uppercase text-gray-500
```

#### Table Cell

```tailwind
px-4 py-3 text-sm text-gray-700
```

#### Row

```tailwind
hover:bg-gray-50 transition
```

#### Pagination

```tailwind
flex items-center justify-between px-4 py-3 border-t border-gray-200
```

Buttons:

```tailwind
px-3 py-1 border border-gray-200 rounded-md hover:bg-gray-100
```

---

### 🧠 Behavior Rules

* Default page size: 10
* Search filters instantly
* Sorting toggles asc/desc
* Keep UI minimal
* No unnecessary borders

---

### 🧬 Reusability Rules

* NO page-specific logic inside DataTable
* All customization via props
* Columns define rendering
* Actions passed from parent

---

### 🚫 Avoid

* Hardcoded table UI per page
* Different table styles across pages
* External UI-heavy table libraries

---

### 🧑‍💻 Copilot Instruction

When generating tables:

* ALWAYS create or reuse `<DataTable />`
* Use TanStack Table for logic
* Use Tailwind for UI
* Follow sky-600 theme strictly
* Keep code reusable and clean

Return production-ready React code only