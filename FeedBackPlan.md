## Feedback Implementation Plan (Sep 30, 2025)

### Scope

Consolidated changes requested via chat for labels, required fields, field additions, calculation rules, and user permissions. This document will drive implementation and testing.

### Domain Clarifications (Decisions)

-   **Cost semantics**:
    -   **ATC amount (ATC table)**: price paid to the company (Dangote/BUA/Mangal).
    -   **Cost (Daily Transaction)**: selling price to the customer. Must be manually entered by staff; not auto-calculated; does not need to match ATC amount.
-   **Manual inputs**:
    -   **Cost**, **number of tons**, **fare amount**, **gas chop money**, **haulage** are all manually entered by staff.
    -   These amounts may be negative (e.g., −3,000,000).
-   **Company choices**: For new ATC, company must be one of `Dangote`, `BUA`, `Mangal` (dropdown/select, not free text).
-   **Tons field**: Keep as a free text/number input (manual); no auto calculation.

### Form/UI Changes

-   **Daily Transactions**

    -   Rename label: **“ATC Cost” → “Cost”**.
    -   Ensure value is manually entered by staff.

-   **Truck Movement (Create form)**

    -   Add a field to select **ATC** (position immediately after `Customer`).
    -   Add a field **“Haulage”** (position after `Gas Chop Money`).
    -   Ensure fields: `Fare Amount`, `Gas Chop Money`, `Haulage` accept negative numbers.
    -   Label note: “Drivers transport + haulage options (fare amount can be negative)”.

-   **Truck Maintenance (Create form)**

    -   Add a **Date** field (explicit maintenance date entry).

-   **New ATC (Create form)**

    -   Replace free-text **Company** with a required select: `Dangote`, `BUA`, `Mangal`.

-   **New Customer (Create form)**

    -   Required: **Customer Name**, **Phone Number**.
    -   Optional: **Email**, **Status**.

-   **Create Driver (form)**

    -   Required: **Driver Name**, **Phone Number**.
    -   Optional: **Company**, **Driver Photo**.

-   **Create Truck (form)**

    -   Required: **Registration Number** only.
    -   All other fields optional.

-   **Create New Transaction (form)**
    -   Rename field: **“ATC Cost” → “Cost”**; staff must enter manually.
    -   Remove field: **Transport Cost**.

### Calculation Rules (Truck Movement Balance)

-   Let:
    -   `Cost` = Daily Transaction cost (selling price).
    -   `ATC Amount` = amount from ATC table (purchase price).
    -   `Fare Amount`, `Gas Chop Money`, `Haulage` = staff-entered numbers (may be negative).
-   Computations:
    -   `Fare Amount = Cost − ATC Amount`.
    -   `Truck Movement Balance = Fare Amount − Gas Chop Money + Haulage`.

### Access Control & Seeding

-   Seed the following users (passwords will be securely hashed):

    -   `Chika@rbcnigeria.com` / `Passwq5689`
        -   Permissions: read/write everything **except** User Management.
    -   `Grace@rbcnigeria.com` / `Passw3478`
        -   Permissions: read/write **ATC** records and **access Reports only**.
    -   `Victoria@rbcnigeria.com` / `Past65457`
        -   Permissions: write everything **except** User Management and **Edit ATC records** (no ATC edit rights).
    -   `nnamdi@rbcnigeria.com` / `yuor5632`
        -   Permissions: write **Truck Movement** and **Truck Maintenance** and related reports only.
    -   `Rhoda@rbcnigeria.com` / `Timer5632`
        -   Permissions: write **Truck Movement** and **Truck Maintenance** and related reports only.

-   Suggested roles/abilities to implement (exact mapping to current policy/gate structure to be aligned during implementation):
    -   `all_but_user_management`
    -   `atc_rw_reports`
    -   `all_but_user_mgmt_and_atc_edit`
    -   `movement_maintenance_rw_reports`

### Implementation Tasks

-   Forms and Validation

    -   Daily Transactions: rename label to `Cost` and ensure manual entry.
    -   Truck Movement: add `ATC` select after `Customer`; add `Haulage` after `Gas Chop Money`; allow negatives for money fields.
    -   Truck Maintenance: add `date` field.
    -   New ATC: change `Company` to required select with options [Dangote, BUA, Mangal].
    -   New Customer: require only `name`, `phone`; make `email`, `status` optional.
    -   Create Driver: require only `name`, `phone`; make `company`, `photo` optional.
    -   Create Truck: require only `registration_number`; others optional.
    -   Create Transaction: rename `ATC Cost` → `Cost`; remove `Transport Cost`.

-   Calculations & Persistence

    -   Ensure `Fare Amount` formula: `Cost − ATC Amount`.
    -   Ensure `Truck Movement Balance` formula: `Fare Amount − Gas Chop Money + Haulage`.
    -   Permit negative values for monetary inputs and store precisely (decimal type).

-   Access Control & Seeding

    -   Define roles/permissions according to above matrix using policies/gates.
    -   Seed specified users with roles/permissions attached.

-   UI/UX

    -   Use selects for constrained fields (Company on ATC).
    -   Field order adjustments as specified.
    -   Update labels to match naming.

-   Tests
    -   Add/adjust feature tests for required/optional validation changes.
    -   Add tests for negative monetary inputs and calculation correctness.
    -   Add authorization tests for each seeded user’s permissions.

### Acceptance Criteria

-   Labels and required/optional fields match this plan across forms.
-   New ATC company is a dropdown with exactly: `Dangote`, `BUA`, `Mangal`.
-   Truck Movement accepts negative values for `Fare Amount`, `Gas Chop Money`, `Haulage` and computes balances per formulas.
-   Maintenance records include a date field and persist correctly.
-   Transaction form shows `Cost` (manual), with `Transport Cost` removed.
-   Users seeded with defined permissions; authorization behaves as specified.

### Open Questions (none blocking)

-   Should negative values trigger special highlighting or validation messages in UI?
-   Should we display computed `Fare Amount`/`Balance` live as staff types?
