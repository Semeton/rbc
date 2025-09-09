# Comprehensive Reports Implementation Plan

## Overview

This document details a comprehensive implementation plan for all reports required by the RBC Trucking Management System, as specified in the PRD.md. The plan ensures each report is highly comprehensive, featuring rich data tables, interactive and static charts, and robust export options (PDF and Excel) tailored to the report type.

## Report Categories

Reports are grouped into the following categories, each supporting advanced visualizations and export features:

1. **Financial Reports** – Revenue, payments, balances, cash flow
2. **Operational Reports** – Transactions, movements, performance
3. **Asset Reports** – Truck utilization, maintenance, ATC management
4. **Analytical Reports** – Trends, summaries, insights

## Complete Report Inventory

Each report below will provide:

-   **Tabular Data:** Detailed tables with all relevant fields
-   **Charts/Visuals:** Bar, line, or pie charts, and dashboard widgets as appropriate
-   **Export Options:** PDF and Excel (xlsx) for all reports

### 1. Financial Reports

#### 1.1 Customer Balance Report ✅ (Implemented)

-   **Purpose:** Show balances per customer
-   **Fields:** Customer Name, Total ATC Value (₦), Total Payments (₦), Outstanding Balance (₦)
-   **Filters:** Date range, Customer name
-   **Visuals:** Table, summary widget
-   **Export:** PDF, Excel
-   **Status:** ✅ Completed

#### 1.2 Outstanding Balances Report

-   **Purpose:** List customers who owe money
-   **Fields:** Customer Name, Last Payment Date, Outstanding Amount, Days Overdue
-   **Features:** Highlight overdue balances (>30 days)
-   **Filters:** Date range, Customer name, Overdue status
-   **Visuals:** Table with conditional formatting, bar chart for overdue distribution
-   **Export:** PDF, Excel
-   **Priority:** High

#### 1.3 Customer Payment History Report

-   **Purpose:** Track all payments per customer
-   **Fields:** Payment Date, Customer Name, Amount Paid, Payment Type (Cash/Transfer), Bank Name
-   **Filters:** Date range, Customer name, Payment type
-   **Visuals:** Table, line chart for payment trends
-   **Export:** PDF, Excel
-   **Priority:** High

#### 1.4 Cash Flow Report

-   **Purpose:** Monitor incoming and outgoing cash
-   **Fields:** Date, Incoming Amount, Outgoing Amount, Net Cash Flow, Running Balance
-   **Visuals:** Table, cash inflow vs outflow line/bar chart
-   **Filters:** Date range
-   **Export:** PDF, Excel
-   **Priority:** High

#### 1.5 Profit Estimate Report

-   **Purpose:** Estimate profit based on revenue and key costs
-   **Calculation:** (ATC Cost + Transport Fee) – (Gas & Chop + Maintenance + Fare)
-   **Fields:** Date, Revenue, Total Costs, Net Profit, Profit Margin %
-   **Visuals:** Table, trend chart for monthly profit margin
-   **Filters:** Date range, Customer, Driver, Truck
-   **Export:** PDF, Excel
-   **Priority:** Medium

### 2. Operational Reports

#### 2.1 Monthly Sales Report ✅ (Implemented)

-   **Purpose:** Show monthly cement sales
-   **Fields:** Month, Total Metric Tons Delivered, Total ATC Cost, Total Transport Fees
-   **Visuals:** Table, bar chart for monthly revenue, pie chart for cement type distribution
-   **Filters:** Date range, Customer, Driver
-   **Export:** PDF, Excel
-   **Status:** ✅ Completed

#### 2.2 Daily Activity Summary Report

-   **Purpose:** Show a daily overview of transactions and movements
-   **Fields:** Date, Number of Transactions, Total Sales, Total Payments, Active Trucks, Active Drivers
-   **Visuals:** Table, dashboard widgets, line chart for daily activity
-   **Filters:** Date range
-   **Export:** PDF, Excel
-   **Priority:** Medium

#### 2.3 Driver Performance Report ✅ (Implemented)

-   **Purpose:** Track driver activity per month
-   **Fields:** Driver Name, Number of Trips, Total Fare Earned, Average Trip Value
-   **Visuals:** Table, line graph for trip trends
-   **Filters:** Date range, Driver name
-   **Export:** PDF, Excel
-   **Status:** ✅ Completed

#### 2.4 Depot Performance Report

-   **Purpose:** Analyze depot-wise activity
-   **Fields:** Depot Name (Origin), Total Dispatches, Total Revenue (ATC + Transport), Average Transaction Value
-   **Visuals:** Table, bar chart comparing depot performance
-   **Filters:** Date range, Depot name
-   **Export:** PDF, Excel
-   **Priority:** Medium

#### 2.5 Top 20 Destinations Report

-   **Purpose:** List the top 20 most frequent destinations for deliveries
-   **Fields:** Destination, Number of Deliveries, Total Revenue, Average Revenue per Delivery
-   **Visuals:** Table, bar chart for top destinations
-   **Filters:** Date range
-   **Export:** PDF, Excel
-   **Priority:** Low

### 3. Asset Reports

#### 3.1 Truck Utilization Report ✅ (Implemented)

-   **Purpose:** Track how often each truck is used
-   **Fields:** Truck (Cab Number), Total Trips, Total Income Generated, Total Maintenance Cost, Utilization %
-   **Visuals:** Table, bar chart comparing trucks' performance
-   **Filters:** Date range, Truck
-   **Export:** PDF, Excel
-   **Status:** ✅ Completed

#### 3.2 Truck Maintenance Cost Report ✅ (Implemented)

-   **Purpose:** Track maintenance expenses by month
-   **Fields:** Truck (Cab Number), Date, Maintenance Cost, Description
-   **Visuals:** Table, line graph for maintenance cost trends
-   **Filters:** Date range, Truck
-   **Export:** PDF, Excel
-   **Status:** ✅ Completed

#### 3.3 Monthly Truck Maintenance Cost Report

-   **Purpose:** Provides monthly maintenance costs for each truck
-   **Fields:** Month, Truck (Cab Number), Total Maintenance Cost, Number of Maintenance Records
-   **Visuals:** Table, bar/line chart for monthly costs
-   **Filters:** Date range, Truck
-   **Export:** PDF, Excel
-   **Priority:** Medium

#### 3.4 Pending ATC Report

-   **Purpose:** Show unassigned ATCs
-   **Fields:** ATC Number, ATC Type, Amount, Tons, Status, Days Since Created
-   **Visuals:** Table, dashboard widget for pending ATCs
-   **Filters:** ATC Type, Status, Date range
-   **Export:** PDF, Excel
-   **Priority:** High

#### 3.5 Unassigned ATC Numbers Report

-   **Purpose:** Returns ATC numbers that have been recorded but not yet linked to a customer
-   **Fields:** ATC Number, ATC Type, Amount, Tons, Created Date, Days Unassigned
-   **Visuals:** Table, bar chart for unassigned ATCs by type
-   **Filters:** ATC Type, Date range
-   **Export:** PDF, Excel
-   **Priority:** High

### 4. Analytical Reports

#### 4.1 Driver Monthly Trips Report

-   **Purpose:** Counts the number of trips made by each driver every month
-   **Fields:** Month, Driver Name, Number of Trips, Total Revenue, Average Revenue per Trip
-   **Visuals:** Table, line/bar chart for trip trends
-   **Filters:** Date range, Driver name
-   **Export:** PDF, Excel
-   **Priority:** Medium

#### 4.2 Customer Transaction Summary Report

-   **Purpose:** Summary of ATC, Metric Tons, Cost per customer
-   **Fields:** Customer Name, Total ATCs, Total Metric Tons, Total ATC Cost, Total Transport Cost, Average Transaction Value
-   **Visuals:** Table, pie/bar chart for customer distribution
-   **Filters:** Date range, Customer name
-   **Export:** PDF, Excel
-   **Priority:** Medium

#### 4.3 Total Debt Report

-   **Purpose:** Returns the aggregate debt owed by all customers
-   **Fields:** Total Outstanding Debt, Number of Customers with Debt, Average Debt per Customer, Largest Single Debt
-   **Visuals:** Table, summary widgets, bar chart for debt distribution
-   **Filters:** Date range
-   **Export:** PDF, Excel
-   **Priority:** High

## Implementation Plan

All reports will be implemented to render comprehensive tables and relevant charts, with export options for both PDF and Excel.

### Phase 1: Core Financial Reports (Week 1)

**Priority:** Critical  
**Estimated Time:** 3-4 days

#### Tasks:

1. **Outstanding Balances Report**

    - Create `app/Reports/OutstandingBalancesReport.php`
    - Implement overdue calculation logic and conditional formatting in tables
    - Render bar chart for overdue balances
    - Enable PDF and Excel export
    - Create Livewire component and views

2. **Customer Payment History Report**

    - Create `app/Reports/CustomerPaymentHistoryReport.php`
    - Implement payment type filtering and bank grouping
    - Render payment trend line chart
    - Enable PDF and Excel export
    - Create Livewire component and views

3. **Cash Flow Report**
    - Create `app/Reports/CashFlowReport.php`
    - Implement incoming/outgoing calculations and running balance
    - Render cash flow line/bar chart
    - Enable PDF and Excel export
    - Create Livewire component and views

#### Deliverables:

-   3 new report service classes
-   3 new Livewire components
-   3 new report views (with tables and charts)
-   Comprehensive tests
-   Export functionality (PDF, Excel)

### Phase 2: Asset Management Reports (Week 2)

**Priority:** High  
**Estimated Time:** 3-4 days

#### Tasks:

1. **Pending ATC Report**

    - Create `app/Reports/PendingATCReport.php`
    - Implement ATC assignment status logic and days since created
    - Render dashboard widget and table
    - Enable PDF and Excel export
    - Create Livewire component and views

2. **Unassigned ATC Numbers Report**

    - Create `app/Reports/UnassignedATCReport.php`
    - Implement ATC-customer relationship checking and days unassigned
    - Render bar chart for unassigned ATCs
    - Enable PDF and Excel export
    - Create Livewire component and views

3. **Monthly Truck Maintenance Cost Report**
    - Create `app/Reports/MonthlyTruckMaintenanceReport.php`
    - Implement monthly aggregation and maintenance count
    - Render line/bar chart for costs
    - Enable PDF and Excel export
    - Create Livewire component and views

#### Deliverables:

-   3 new report service classes
-   3 new Livewire components
-   3 new report views (with tables and charts)
-   Comprehensive tests
-   Export functionality (PDF, Excel)

### Phase 3: Operational Analytics (Week 3)

**Priority:** Medium  
**Estimated Time:** 3-4 days

#### Tasks:

1. **Daily Activity Summary Report**

    - Create `app/Reports/DailyActivitySummaryReport.php`
    - Implement daily aggregation and active trucks/drivers calculation
    - Render dashboard widgets and line chart
    - Enable PDF and Excel export
    - Create Livewire component and views

2. **Depot Performance Report**

    - Create `app/Reports/DepotPerformanceReport.php`
    - Implement depot-wise aggregation and performance metrics
    - Render bar chart for depot comparison
    - Enable PDF and Excel export
    - Create Livewire component and views

3. **Driver Monthly Trips Report**
    - Create `app/Reports/DriverMonthlyTripsReport.php`
    - Implement monthly trip counting and revenue calculations
    - Render line/bar chart for trip trends
    - Enable PDF and Excel export
    - Create Livewire component and views

#### Deliverables:

-   3 new report service classes
-   3 new Livewire components
-   3 new report views (with tables and charts)
-   Comprehensive tests
-   Export functionality (PDF, Excel)

### Phase 4: Advanced Analytics (Week 4)

**Priority:** Low  
**Estimated Time:** 2-3 days

#### Tasks:

1. **Profit Estimate Report**

    - Create `app/Reports/ProfitEstimateReport.php`
    - Implement profit and margin calculations
    - Render trend chart for profit margin
    - Enable PDF and Excel export
    - Create Livewire component and views

2. **Top 20 Destinations Report**

    - Create `app/Reports/TopDestinationsReport.php`
    - Implement destination aggregation and ranking
    - Render bar chart for top destinations
    - Enable PDF and Excel export
    - Create Livewire component and views

3. **Customer Transaction Summary Report**

    - Create `app/Reports/CustomerTransactionSummaryReport.php`
    - Implement customer aggregation and metric tons calculations
    - Render pie/bar chart for customer distribution
    - Enable PDF and Excel export
    - Create Livewire component and views

4. **Total Debt Report**
    - Create `app/Reports/TotalDebtReport.php`
    - Implement debt aggregation and statistics
    - Render bar chart and summary widgets
    - Enable PDF and Excel export
    - Create Livewire component and views

#### Deliverables:

-   4 new report service classes
-   4 new Livewire components
-   4 new report views (with tables and charts)
-   Comprehensive tests
-   Export functionality (PDF, Excel)

## Technical Implementation Details

### Report Service Structure

Each report service will:

-   Generate comprehensive tabular data
-   Provide summary statistics
-   Generate chart data for visualizations (bar, line, pie, widgets)
-   Support export to PDF and Excel

Example structure:
