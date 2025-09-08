# Product Requirements Document (PRD)

## Forms

### 1. Customer Form

-   **Purpose:** Add/edit customer details.
-   **Fields:**
    -   Customer Name
    -   Phone Number
    -   Email Address
    -   Customer Note
    -   Status

### 2. Driver Form

-   **Purpose:** Manage driver details, photos, and contact information.
-   **Fields:**
    -   Driver Name
    -   Photo Upload
    -   Phone Number
    -   Company Name
    -   License Number
    -   Status

### 3. ATC Entry Form

-   **Purpose:** Enter new ATC records.
-   **Fields:**
    -   ATC Number
    -   ATC Type
    -   Amount
    -   Issued By
    -   Tons
    -   Status

### 4. Payment Entry Form

-   **Purpose:** Record customer payments.
-   **Fields:**
    -   Customer Name
    -   Payment Date
    -   Amount Paid
    -   Payment Type
    -   Bank Name
    -   Note

### 5. Daily Transaction Form

-   **Purpose:** Input daily customer transactions.
-   **Fields:**
    -   Date
    -   Customer Name
    -   ATC Number
    -   Origin Depot
    -   Depot Details
    -   Cement Type
    -   Destination
    -   Driver
    -   Cab Number
    -   ATC Cost
    -   Transport Fee
    -   Metric Tons
    -   Transaction Note

### 6. Daily Truck Movement Form

-   **Purpose:** Detail daily truck movements.
-   **Fields:**
    -   Driver Name
    -   Cab Number
    -   ATC Collection Date
    -   Dispatch Date
    -   Receiving Customer
    -   Destination
    -   Fare
    -   Gas & Chop Money
    -   Balance

### 7. Truck Maintenance Form

-   **Purpose:** Log truck maintenance details.
-   **Fields:**
    -   Cab Number
    -   Maintenance Description
    -   Date
    -   Cost
    -   Workshop
    -   Attachment

### 8. Truck Form

-   **Purpose:** Manage truck records.
-   **Fields:**
    -   Cab Number
    -   Registration Number
    -   Truck Model
    -   Year of Manufacture
    -   Status

### 9. Main Dashboard/Form

-   **Purpose:** Access all major functionalities, link to other forms, and commonly used reports.

---

## Queries

-   **Customer Balance:** Calculates the total worth of ATC received by each customer and subtracts total payments to get the balance.
-   **Total Debt:** Returns the aggregate debt owed by all customers.
-   **Top 20 Destinations:** Lists the top 20 most frequent destinations for deliveries.
-   **Monthly Truck Maintenance Cost:** Provides monthly maintenance costs for each truck.
-   **Driver Monthly Trips:** Counts the number of trips made by each driver every month.
-   **Unassigned ATC Numbers:** Returns ATC numbers that have been recorded but not yet linked to a customer.
-   **Etc.**

---

## New Features for the Web App

-   Responsive UI (Desktop, Mobile, Tablet)
-   Role-Based User Authentication (Admin, Manager, Accountant, Data Entry)
    -   **Admin:** Full control (CRUD on all modules, reports, user management)
    -   **Accountant:** Manage payments, view balances, generate financial reports
    -   **Operations Manager:** Handle transactions, assign ATCs, dispatch trucks
    -   **Data Entry Staff:** Limited to adding records, no deletions
-   Simultaneous Multi-User Access
-   Activity Logs & Audit Trail
-   Dashboard with Charts: Monthly Sales, Depot Performance, Outstanding Debts, Truck Maintenance Trends
-   Advanced Search & Filters
-   Export to Excel / PDF
-   Notifications: Pending ATCs, Overdue Balances, Scheduled Maintenance

---

## Additional Reports to Include

9. **Customer Payment History** (Filter by date/type)
10. **Depot Performance Report**
11. **Driver Trip & Fare Summary**
12. **Truck Utilization Report** (Trips vs Maintenance)
13. **Customer Transaction Summary** (ATC, Metric Tons, Cost)
14. **Pending ATCs Report**
15. **Cash Flow Report** (Incoming vs Outgoing)
16. **Daily Activity Log**
17. **Profit Estimate Report** (Revenue vs Key Costs)

---

## Reports (Expanded)

Reports provide insightful summaries and analysis for decision-making. All reports should be filterable (by date range, customer, driver, depot, truck) and exportable to Excel and PDF. Charts and tables should be included for visual clarity.

### 18. Customer Balance Report

-   **Purpose:** Show balances per customer.
-   **Fields:**
    -   Customer Name
    -   Total ATC Value (Naira)
    -   Total Payments (Naira)
    -   Outstanding Balance (Naira)
-   **Filters:** Date range, Customer name
-   **Export:** Excel/PDF

### 19. Outstanding Balances

-   **Purpose:** List customers who owe money.
-   **Fields:**
    -   Customer Name
    -   Last Payment Date
    -   Outstanding Amount
-   **Additional Feature:** Highlight those with overdue balances.

### 20. Monthly Sales Report

-   **Purpose:** Show monthly cement sales.
-   **Fields:**
    -   Month
    -   Total Metric Tons Delivered
    -   Total ATC Cost
    -   Total Transport Fees
-   **Visuals:**
    -   Bar chart for monthly revenue
    -   Pie chart for cement type distribution

### 21. Customer Payment History

-   **Purpose:** Track all payments per customer.
-   **Fields:**
    -   Payment Date
    -   Customer Name
    -   Amount Paid
    -   Payment Type (Cash/Transfer)
    -   Bank Name (if transfer)
-   **Filters:** Date range, Payment type

### 22. Depot Performance Report

-   **Purpose:** Analyze depot-wise activity.
-   **Fields:**
    -   Depot Name
    -   Total Dispatches
    -   Total Revenue (ATC + Transport)
-   **Visual:** Bar chart comparing depot performance

### 23. Driver Performance Report

-   **Purpose:** Track driver activity per month.
-   **Fields:**
    -   Driver Name
    -   Number of Trips
    -   Total Fare Earned
-   **Visual:** Line graph showing trip trends

### 24. Truck Utilization Report

-   **Purpose:** Track how often each truck is used.
-   **Fields:**
    -   Truck (Cab Number)
    -   Total Trips
    -   Total Income Generated
    -   Total Maintenance Cost
-   **Visual:** Bar chart comparing trucks’ performance

### 25. Truck Maintenance Cost Report

-   **Purpose:** Track maintenance expenses by month.
-   **Fields:**
    -   Truck (Cab Number)
    -   Date
    -   Maintenance Cost
-   **Visual:** Line graph for maintenance cost trends

### 26. Pending ATC Report

-   **Purpose:** Show unassigned ATCs.
-   **Fields:**
    -   ATC Number
    -   ATC Type
    -   Status
-   **Use Case:** Helps in ensuring ATCs are fully utilized.

### 27. Cash Flow Report

-   **Purpose:** Monitor incoming and outgoing cash.
-   **Incoming:** Customer Payments
-   **Outgoing:** Truck Maintenance, Gas & Chop, Fare
-   **Visual:** Cash inflow vs outflow graph

### 28. Daily Activity Summary

-   **Purpose:** Show a daily overview of transactions and movements.
-   **Fields:**
    -   Date
    -   Number of Transactions
    -   Total Sales
    -   Total Payments
-   **Visual:** Dashboard widget summary

### 29. Profit Estimate Report

-   **Purpose:** Estimate profit based on revenue and key costs.
-   **Calculation:** (ATC Cost + Transport Fee) – (Gas & Chop + Maintenance + Fare)
-   **Visual:** Trend chart showing monthly profit margin
