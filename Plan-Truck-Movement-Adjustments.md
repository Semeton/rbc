## Truck Movement Form & Reporting – Implementation Plan

### 1. Confirm Domain Formulas & Field Behaviour
- **New/updated business rules**
  - **Customer Cost (input)**: add numeric field `customer_cost` to the truck movement form; value entered manually.
  - **ATC Cost (existing)**: confirm source (likely from related `Atc` record); expose as read-only on the form if needed.
  - **Fare (auto)**: change from manual entry to derived value:  
    - **Formula**: `fare = customer_cost - atc_cost`.
  - **Gas (existing)**: keep `gas_chop_money` as is (manual entry).
  - **Haulage (existing)**: keep as manual entry (can be negative), but include in totals.
  - **Incentive (input)**: add numeric field `incentive` to the form; value entered manually.
  - **Total (auto)**: change stored/derived field meaning to:  
    - **Formula**: `total = fare - gas + haulage`  
      (i.e. `total = fare - gas_chop_money + haulage`).
  - **Total + Incentive (auto)**: new derived value:  
    - **Formula**: `total_plus_incentive = total + incentive`.
  - **Salary Contribution (input)**: add numeric field `salary_contribution` to the form; value entered manually per movement.
- **Reporting rules**
  - **Expenses (per truck, per month)**: use “total maintenance per truck per month” (confirm existing maintenance source/model – probably a maintenance or expense model keyed by truck and date).
  - **Balance (per month)**:  
    - **Formula**: `monthly_balance = sum(total_plus_incentive) - sum(expenses)` grouped by truck and month.
  - **Salary (per month)**:  
    - **Formula**: `monthly_salary = sum(salary_contribution)` grouped by driver (or global) and month – confirm expected grouping (by driver, truck, or company-wide).

> **Open questions to confirm before implementation**
> - Is **ATC cost** always available on the `Atc` model (e.g. `atc_cost`, `amount`, or similar), and should it be pulled automatically when an ATC is selected? - Yesß
> - Should **Total + Incentive** and **Salary Contribution** be persisted in the database as fields, or always computed on the fly from stored primitives? - compute
> - For **monthly salary**, should we report per **driver**, per **truck**, or a global monthly total (or all three)? All three

---

### 2. Data Model Changes
- **DailyTruckRecord model / database**
  - **Add columns** (via new migration):
    - `customer_cost` – `decimal(15, 2)`; nullable=false with default 0.00.
    - `incentive` – `decimal(15, 2)`; nullable=true with default 0.00.
    - `salary_contribution` – `decimal(15, 2)`; nullable=true with default 0.00.
    - Optionally `total_plus_incentive` if we choose to persist instead of compute.
  - **Update `$fillable`/casts** in `DailyTruckRecord`:
    - Add new attributes to `$fillable` and `casts()`/`$casts` as `decimal:2`.
  - **Derived attributes**
    - Add accessors:
      - `getTotalAttribute()` if we want `total` as accessor: `fare - gas_chop_money + haulage`.
      - `getTotalPlusIncentiveAttribute()` for `total_plus_incentive`.
    - Decide whether to:
      - Keep `balance` column as **“Total”**, repurpose its semantics, or
      - Introduce a new stored `total`/`total_plus_incentive` field and deprecate `balance` in UI (to avoid confusion).
  - **Maintenance expenses source**
    - Locate/introduce maintenance/expense model(s) keyed by truck and date (read-only for now).
    - If missing, plan a separate small module to record maintenance expenses per truck with:
      - `truck_id`, `maintenance_date`, `amount`, `description`, etc.

---

### 3. Service Layer Adjustments (`TruckMovementService`)
- **Create/update logic**
  - Modify `createTruckMovement()` and `updateTruckMovement()` to:
    - Accept and pass through new keys: `customer_cost`, `incentive`, `salary_contribution`.
    - Replace existing balance formula with:
      - Compute `fare` if not already computed by caller: `fare = customer_cost - atc_cost`.
      - Compute `total = fare - gas_chop_money + haulage`.
      - Compute `total_plus_incentive = total + incentive`.
      - Decide:
        - Either persist `balance` as `total_plus_incentive` (rename conceptually), **or**
        - Add explicit `total` and `total_plus_incentive` fields and stop using `balance` for new UI.
  - Ensure strict typing and minimal business logic duplication between create/update.
- **Statistics & reporting methods**
  - Extend `getTruckMovementStatistics()`:
    - Include aggregates for `total_plus_incentive` (and maybe `total`, `salary_contribution`) if useful on the dashboard.
  - Introduce dedicated methods for reporting:
    - `getMonthlyTruckReport(int $year, int $month): Collection|LengthAwarePaginator`
      - Returns per-truck summary with:
        - `sum_total`, `sum_total_plus_incentive`, `sum_expenses`, `monthly_balance`.
    - `getMonthlySalaryReport(int $year, int $month): Collection`
      - Returns per-driver (or global) salary contribution totals.

---

### 4. Livewire Form Components (Create/Edit)
- **Create component (`App\Livewire\TruckMovement\Create`)**
  - **State additions**:
    - Add public properties (with validation attributes):
      - `public float $customer_cost = 0.0;`
      - `public ?float $incentive = 0.0;`
      - `public ?float $salary_contribution = 0.0;`
  - **Validation rules**:
    - `customer_cost`: `required|numeric|min:0`.
    - `incentive`: `nullable|numeric`.
    - `salary_contribution`: `nullable|numeric|min:0`.
  - **Computed/derived behaviour**
    - Add Livewire lifecycle hook(s) or small methods to:
      - Recalculate `fare` whenever `customer_cost` or ATC changes:
        - Determine ATC cost from selected `atc_id`.
        - Update `$this->fare` in-memory using the formula.
      - Optionally show `total` & `total_plus_incentive` as read-only preview fields in the form using component state.
  - **Store action**
    - Pass new values into `createTruckMovement()` (`customer_cost`, `incentive`, `salary_contribution`).

- **Create Blade view (`resources/views/livewire/truck-movement/create.blade.php`)**
  - **Fields to keep as-is**: Driver, Truck, Customer, ATC, ATC collection date, Load dispatch date, Gas, Haulage, Status.
  - **Field changes**:
    - Replace manual `Fare Amount` input with:
      - `Customer Cost` input (`wire:model`).
      - Read-only `Fare` display (bound to computed `$fare`).
    - Add:
      - `Incentive` input.
      - `Salary Contribution` input.
    - Optionally add:
      - Read-only `Total` and `Total + Incentive` fields, reflecting live calculations.

- **Edit component & view**
  - Mirror changes from Create:
    - Load existing `customer_cost`, `incentive`, `salary_contribution` from model.
    - Maintain same auto-calculation rules but allow editing of these values.

---

### 5. Truck Reporting UI & Logic
- **Locate or create reporting screen**
  - If a dedicated truck reporting page already exists:
    - Extend it to show:
      - **Expenses (per truck, per month)**.
      - **Balance** = sum(`total_plus_incentive`) – sum(`expenses`) per month.
      - **Salary** = sum(`salary_contribution`) per month (decide by truck/driver/global).
  - If not present, add:
    - A new route and controller method OR Livewire page (following existing patterns).
    - Filters:
      - Month/year selector.
      - Optional truck and driver filters.
    - Table per truck:
      - Columns: Truck, Month, `sum_total`, `sum_total_plus_incentive`, `sum_expenses`, `monthly_balance`.
    - Optional salary summary table per driver/month.
- **Query strategy**
  - Use Eloquent aggregates grouped by:
    - `truck_id` and `YEAR(load_dispatch_date)`, `MONTH(load_dispatch_date)` (or `atc_collection_date` – confirm which date drives reporting).
  - Join or subquery maintenance expenses to compute monthly `expenses` per truck.

---

### 6. Validation, Tests, and Audit Trails
- **Form Requests**
  - Update `StoreTruckMovementRequest` and `UpdateTruckMovementRequest`:
    - Add rules/messages for `customer_cost`, `incentive`, `salary_contribution` (for non-Livewire controller paths).
  - Ensure rules/business logic are consistent between FormRequests and Livewire validations.
- **Audit & logging**
  - Ensure new financial fields are captured implicitly by existing audit descriptions (no changes needed if we keep generic messages).
  - If needed, extend descriptions to mention “totals” rather than only “fare/gas”.
- **Tests**
  - Extend `tests/Feature/TruckMovementTest.php`:
    - Cover create/update with the new fields.
    - Assert:
      - `fare` stored as `customer_cost - atc_cost`.
      - `total` and `total_plus_incentive` (or `balance` if reused) honour the specified formulas.
      - Monthly reporting endpoints/Livewire components compute:
        - Correct `Expenses`, `Balance`, `Salary` given controlled fixture data.

---

### 7. Incremental Rollout & Backwards Compatibility
- **Data migration**
  - For existing records, backfill:
    - `customer_cost` as `fare + atc_cost` where `atc_cost` is available; otherwise leave null or 0 with a note.
    - `incentive` and `salary_contribution` as 0 by default.
  - Carefully decide how to map old `balance` to the new definitions:
    - Option A: Leave historical records as-is, use new fields only for new data, and clearly label new UI fields.
    - Option B: Recompute `balance` into `total` for all historical rows if the original formula already matched `fare - gas + haulage`.
- **UI clarity**
  - Update labels and help texts to explicitly reference formulas:
    - Example: “Fare (auto-calculated as Customer Cost – ATC Cost, read-only)”.
    - “Total (Fare – Gas + Haulage)”, “Total + Incentive (Total + Incentive)”.

---

### 8. Next Steps / Approval Checklist
- [ ] Confirm data source and field name for **ATC cost**.
- [ ] Confirm reporting grain for **salary** (per driver/truck/global).
- [ ] Decide persistence strategy for `total` and `total_plus_incentive` vs. accessor-only.
- [ ] Approve model/migration changes.
- [ ] Approve Livewire form UX (which fields are editable vs read-only).
- [ ] Approve reporting layout and grouping (truck-level vs driver-level, per month).


