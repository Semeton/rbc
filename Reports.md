# Reports (Expanded)

Reports will provide insightful summaries and analysis for decision-making. These reports should be filterable (by date range, customer, driver, depot, truck) and exportable to Excel and PDF. Charts and tables should be included if possible for visual clarity.

## 1. Customer Balance Report

-   **Purpose:** Show balances per customer.
-   **Fields:**
    -   Customer Name
    -   Total ATC Value (Naira)
    -   Total Payments (Naira)
    -   Outstanding Balance (Naira)
-   **Filters:**
    -   Date range
    -   Customer name
-   **Export:** Excel/PDF

## 2. Outstanding Balances

-   **Purpose:** List customers who owe money.
-   **Fields:**
    -   Customer Name
    -   Last Payment Date
    -   Outstanding Amount
-   **Additional Feature:** Highlight those with overdue balances.

## 3. Monthly Sales Report

-   **Purpose:** Show monthly cement sales.
-   **Fields:**
    -   Month
    -   Total Metric Tons Delivered
    -   Total ATC Cost
    -   Total Transport Fees
-   **Visuals:**
    -   Bar chart for monthly revenue
    -   Pie chart for cement type distribution

## 4. Customer Payment History

-   **Purpose:** Track all payments per customer.
-   **Fields:**
    -   Payment Date
    -   Customer Name
    -   Amount Paid
    -   Payment Type (Cash/Transfer)
    -   Bank Name (if transfer)
-   **Filters:**
    -   Date range
    -   Payment type

## 5. Depot Performance Report

-   **Purpose:** Analyze depot-wise activity.
-   **Fields:**
    -   Depot Name
    -   Total Dispatches
    -   Total Revenue (ATC + Transport)
-   **Visual:**
    -   Bar chart comparing depot performance.

## 6. Driver Performance Report

-   **Purpose:** Track driver activity per month.
-   **Fields:**
    -   Driver Name
    -   Number of Trips
    -   Total Fare Earned
-   **Visual:**
    -   Line graph showing trip trends.

## 7. Truck Utilization Report

-   **Purpose:** Track how often each truck is used.
-   **Fields:**
    -   Truck (Cab Number)
    -   Total Trips
    -   Total Income Generated
    -   Total Maintenance Cost
-   **Visual:**
    -   Bar chart comparing trucks’ performance.

## 8. Truck Maintenance Cost Report

-   **Purpose:** Track maintenance expenses by month.
-   **Fields:**
    -   Truck (Cab Number)
    -   Date
    -   Maintenance Cost
-   **Visual:**
    -   Line graph for maintenance cost trends.

## 9. Pending ATC Report

-   **Purpose:** Show unassigned ATCs.
-   **Fields:**
    -   ATC Number
    -   ATC Type
    -   Status
-   **Use Case:** Helps in ensuring ATCs are fully utilized.

## 10. Cash Flow Report

-   **Purpose:** Monitor incoming and outgoing cash.
-   **Incoming:**
    -   Customer Payments
-   **Outgoing:**
    -   Truck Maintenance
    -   Gas & Chop
    -   Fare
-   **Visual:**
    -   Cash inflow vs outflow graph.

## 11. Daily Activity Summary

-   **Purpose:** Show a daily overview of transactions and movements.
-   **Fields:**
    -   Date
    -   Number of Transactions
    -   Total Sales
    -   Total Payments
-   **Visual:**
    -   Dashboard widget summary.

## 12. Profit Estimate Report

-   **Purpose:** Estimate profit based on revenue and key costs.
-   **Calculation:**
    -   (ATC Cost + Transport Fee) – (Gas & Chop + Maintenance + Fare)
-   **Visual:**
    -   Trend chart showing monthly profit margin

---

## Implementation Plan for Reports Module

This plan outlines the steps for implementing the reports feature as described above. Each report will strictly adhere to the specified fields, and all reports will be accessible via the sidebar, including an "All Reports" quick access menu.

### 1. Sidebar Integration

-   **Add a "Reports" section to the sidebar** in `resources/views/components/layouts/app/sidebar.blade.php`.
    -   Each report (1–12) should have its own menu item.
    -   Add an "All Reports" menu item at the top of the section for quick access to a consolidated reports dashboard.

### 2. Routing

-   **Define named routes** for each report in `routes/web.php` (e.g., `reports.maintenance`, `reports.cash-flow`, etc.).
-   Add a route for the "All Reports" dashboard (e.g., `reports.index`).

### 3. Report Pages

-   **Create a Livewire Volt component for each report** in `resources/views/reports/` (e.g., `maintenance.blade.php`, `cash-flow.blade.php`, etc.).
    -   Each Volt component should:
        -   Display only the fields specified for that report.
        -   Include the required visual/chart if specified.
        -   Use Flux UI components for consistency.
-   **Create an "All Reports" dashboard** as a Livewire Volt component that provides quick links and summary widgets for each report.

### 4. Data Handling

-   **Fetch and display data** for each report using Eloquent models and relationships.
-   Ensure each report only queries and displays the fields listed in the requirements.
-   For reports requiring calculations (e.g., Profit Estimate), implement the calculation logic in the corresponding Volt component.

### 5. Visualizations

-   **Integrate charts/graphs** using a preferred charting library (e.g., Chart.js via a Blade component or Livewire integration).
-   Ensure visuals match the requirements (line graph, trend chart, dashboard widget, etc.).

### 6. Permissions & Access

-   **Restrict access** to reports based on user roles/permissions if required by the application.

### 7. Testing

-   **Write feature tests** for each report page to ensure:
    -   The correct fields are displayed.
    -   The sidebar menus link to the correct pages.
    -   Visualizations render as expected.

### 8. Audit Trail

-   **Log report views** in the activity log for audit purposes.

---

**Summary Table of Reports and Sidebar Menu Items**

| Report Name                           | Sidebar Menu Item | Route Name              | Fields/Visuals Strictly As Listed            |
| ------------------------------------- | ----------------- | ----------------------- | -------------------------------------------- |
| All Reports                           | All Reports       | reports.index           | Quick links/summary for all                  |
| Maintenance Report                    | Maintenance       | reports.maintenance     | Truck, Date, Maintenance Cost, Line Graph    |
| Pending ATC Report                    | Pending ATCs      | reports.pending-atc     | ATC Number, ATC Type, Status                 |
| Cash Flow Report                      | Cash Flow         | reports.cash-flow       | Incoming/Outgoing, Graph                     |
| Daily Activity Summary                | Daily Activity    | reports.daily-activity  | Date, #Transactions, Sales, Payments, Widget |
| Profit Estimate Report                | Profit Estimate   | reports.profit-estimate | Calculation, Trend Chart                     |
| ... (add all other reports as needed) | ...               | ...                     | ...                                          |

---

**Next Steps:**

-   Confirm the above plan with the team.
-   Begin with sidebar and routing setup, then implement each report page following the strict field requirements.
